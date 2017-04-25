<?php
require_once("param_server.php"); 
require_once("ajax_table_color.php"); 
$formperim ='';
$millesime ='';
$liste_codes ='';

function evolution_color($v) {
	if ($v<0) {
		$v = '<font color="#FF0000">'.$v.'</font>';
	}
	return $v;
}

//------ Préparation menu déroulant liste des périmètres
$bloc_lib = array(
	1=>'Région & Départ',
	2=>'Région & Départ',
	3=>'Canton',
	4=>'SCOT',
	5=>'Autre',
	6=>'Personnalisé'
);

// récupération des id et code
$id='';
$code='';

if (isset($_GET['id'])) {
	 if ($_GET['id']<>'') {
		$id=$_GET['id'];
	}
}

if (isset($_GET['code'])) {
	 if ($_GET['code']<>'') {
		$code=$_GET['code'];
	}
}

// récupération des paramètres du référent
// si id est vide on prend le premier dans la table des paramaètres
	if ( $id <>'')  {
		$select_param = "select id, libelle_court, libelle_long,schema,nom_table,millesime, sql from ". $nom_schema . "." . $table_perimetres . " where id =".$_GET['id'];
		} else {
		$select_param = "select id, libelle_court,libelle_long,schema,nom_table,millesime, sql from ". $nom_schema . "." . $table_perimetres . " order by id ";
	}
	$result = $connexion->prepare($select_param) ;
	$result->execute();
	$row=$result->fetch(PDO::FETCH_ASSOC);
	$id = $row["id"];
	$libelle_court_ref = $row["libelle_court"];
	$libelle_long_ref = $row["libelle_long"];
	$nom_schema_ref = $row["schema"] ;
	$nom_table_ref = $row["nom_table"] ;
	$annee_ref = $row["millesime"] ;
	$sql =  $row["sql"];
	
//------- Préparation liste des millésimes
		$sql_millesime = "select millesime from ". $nom_schema . "." . $table_perimetres . " group by millesime order by millesime desc" ;
		$result = $connexion->prepare($sql_millesime) ;
		$result->execute();
		
		$millesime = '<option value="">millésimes</option>';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["millesime"]==$annee_ref){
				$selection ='selected';
				} else {
				$selection ='';
				}
			$millesime .= '<option value="' . $row["millesime"] .'" ' . $selection .' >' . $row["millesime"] .'</option>';
		}

// récupération de l'année de comparaison
	if ( isset($_GET['annee']) and ($_GET['annee']<>'') ) {
		$annee_comp = $_GET['annee'];
	} else {
		$annee_comp  = $annee_ref ;
	};
	
//------- Préparation liste des années de comparaison
		$sql_comparaison = "select millesime from ". $nom_schema . "." . $table_perimetres . " where millesime <> ".$annee_ref . " group by millesime order by millesime desc" ;
		$result = $connexion->prepare($sql_comparaison) ;
		$result->execute();
		$comparaison = '<option value="">Choisir SVP</option>';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			if ($row["millesime"]==$annee_comp){
				$selection ='selected';
				} else {
				$selection ='';
				}
			$comparaison .= '<option value="' . $row["millesime"] .'" ' . $selection .' >' .  $row["millesime"] .'</option>';
		}
		
		
// récupération des paramètres de la table de l'année de comparaison
// la fonction init_comparaison($v1,$v2)
// retourne dans les variables globales $nom_schema_ref2 et $nom_table_ref2
//  les paramètres de la table d'identifiant $v2 pour l'année $v1 si $libelle_court_ref<>Region, Dept76 ou 27
//  les paramètres de la table régionale ou départementale pour l'année $v1 si $libelle_court_ref = Region, Dept76 ou 27
	if (($libelle_court_ref<>'Region') and ($libelle_court_ref<>'Dept76') and ($libelle_court_ref<>'Dept27')) {
		$sql_comp = "select * from oscom.oscom_perimetres where (millesime =". $annee_comp . ") and (sql='" . $sql."')";
		} else {
			$sql_comp = "select * from " . $nom_schema . ".retourne_table_comparaison('".$annee_comp."','".$libelle_court_ref."') "; 
			//retourne_table_comparaison('".$annee."','".$lib_court."') "; 
			$sql_comp .= "as (libelle_long character varying,";
			$sql_comp .= "nom_table character varying,";
			$sql_comp .= "schema character varying,";
			$sql_comp .= "geoide character varying,";
			$sql_comp .= "image character varying,";	
			$sql_comp .= "extent_to_html character varying)";
		} ;
    $result = $connexion->prepare($sql_comp) ;
	$result->execute(); 
	$row=$result->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_FIRST) ; // PDO::FETCH_ASSOC
		//$lib_long_ref2 = $row["libelle_long"] ;
		$nom_schema_ref2 = $row["schema"] ;
		$nom_table_ref2 =  $row["nom_table"] ;
		//$geoide_ref2 =  htmlspecialchars($row["geoide"],ENT_QUOTES) ;
		//$image_ref2 =  $row["image"] ;
		//$extent_to_html2 = $row["extent_to_html"] ;

//------ Préparation menu déroulant liste des périmètres sur l'année de référence
		$select_perimetres = "select * from ". $nom_schema . "." . $table_perimetres . " where millesime =". $annee_ref . ' order by bloc, libelle_long';
		$result = $connexion->prepare($select_perimetres) ;
		$result->execute();
		$selection ='';
		$formperim = '<option value="">choisir un périmètre</option>'; //choisir un périmètre
		$bloc ='';
		$optgroup = '';
		$libelle_long = '';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		    if(($bloc <> $row["bloc"]) AND ($row["bloc"]<>2)) { // On ne change pas de bloc select entre Région et Département
				$bloc = $row["bloc"] ;
				$formperim .= $optgroup. '<optgroup label="'.$bloc_lib[$bloc].'">';
				$optgroup = '</optgroup>' ;
				}
			if ($libelle_long <> $row["libelle_long"]) {
				if ($row["id"]==$id) {
					$selection ='selected';
					} else {
					$selection ='';
				}
				$formperim .= '<option value="' .$row["id"].'" ' . $selection .'>' . $row["libelle_long"].'</option>';
				$selection ='';
				$libelle_long = $row["libelle_long"] ;
				}
			}
		$formperim .= $optgroup ;

//------- Préparation du menu déroulant des codes
		if ($code<>''){
				$liste_codes = '<option value="">Tous les codes</option>';
				} else {
				$liste_codes = '<option value=""  selected>Tous les codes</option>';
				}
		foreach ($tableau as $lit_code=>$tabcode) {
			if ($lit_code<>$code){
				$selection ='';
				} else {
				if ($code<>'') {$selection ='selected';} else {$selection ='';};
				}
			$liste_codes .= '<option value="' . $lit_code  .'" ' . $selection .' >' . $lit_code . ' - '. $tabcode[6] .'</option>'; 
			}

//--------------------------
function calcul_variation($schema1,$perim1,$schema2,$perim2,$code) {
    global $connexion,$table_communes_region,$annee_comp,$annee_ref,$libelle_long_ref,$libelle_court_ref ;
	$calc  = "select codinsee, codos, surface_m2, nom from (";
	$calc .= "select * from fct.deltoscom('',";
	$calc .= "'". $schema1 . "','". $perim1 . "',";
	$calc .= "'". $schema2 . "','". $perim2 . "',";
	$calc .= "'". $code ."',";
	$calc .= "true, true,700) as (codinsee character varying, codos character varying, surface_m2 bigint)"; 
	$calc .= ") as f00, ".$table_communes_region." WHERE codinsee=code_insee";
	$result = $connexion->prepare($calc) ;
	$result->execute();
	$tab = '<table width="75%" border="1" align="center" bordercolor="#eeeeee" cellspacing="0" style="font-size:100%;">';
	$titre_tab = ($libelle_court_ref!='CANTON')?'Périmètre "':'Canton "';
	$titre_tab = 'Evolution de '. $annee_comp. ' à ' . $annee_ref .' - ' . $titre_tab . $libelle_long_ref .'"';
	$tab .= '<tr><td colspan=4 align="center">'.$titre_tab . '</td></tr>';
	$tab .= '<tr align="center"><td width="10%">Insee</td><td width="50%">Nom Commune</td><td width="10%">code OS</td><td width="30%"> Surface en ha</td></tr>';
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$tab .= '<tr><td align="center">' . $row["codinsee"] .'</td><td>'. $row["nom"] .'</td><td align="center">'. $row["codos"] .'</td><td align="right">'. evolution_color(number_format($row["surface_m2"]/10000, 2, ',', ' ')) .'</td></tr>';
	};
	//evolution_color(number_format($row['surface_m2']/10000,0,'.',' ')) ;
	//$tab .= "<tr><td colspan=4>".$calc."</td></tr>";
	$tab .= "</table>";
	return $tab;
}

// ----------- légende ---------------------
$ligne ='<table width="75%" border="1" cellpadding="2" cellspacing="0" bordercolor="cccccc"   style="font-size:60%;" align="center">';
$ligne .= '<tr align="center" valign="middle"><th >Code</th><th >Légende</th><th >Code</th><th >Légende</th></tr>';
	$pair = 0 ;
	foreach ($tableau as $code_os=>$tab) {
		$couleur = explode(" ", $tab[7]);
		$ligne .= ($pair==1)? '<tr>':''; // on commence une ligne de 2 colonnes
		
		if ($code_os<>'0' and $code_os<>'20' and $code_os<>'**') {
			$ligne .= '<td bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
			$ligne .= '<font color="'.$color_text_code[$code_os].'">' .$code_os.'</font></td>';
			$ligne .= '<td align="left">'.$tab[6].'</td>';
			}
		if ($code_os=='0' OR $code_os=='20') {
			if ($code_os=='0') {
				$infovent = '<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#eeeeee" style="font-size:100%;">';
				$infovent .= '<tr><td>Libellé Majic</td><td align="center">Affectation</td></tr>';
				$infovent .= "<tr><td>sols, jardins et terrains d'agrément </td><td align=\"center\">11</td></tr>";
				 $infovent .= "<tr><td>voies ferrées  </td><td align=\"center\">12</td></tr>";
				 $infovent .= "<tr><td>carrières</td><td align=\"center\">13</td></tr>"; 
				 $infovent .= "<tr><td>terrains à bâtir </td><td align=\"center\">15</td></tr>";
				 $infovent .= "<tr><td>terres </td><td align=\"center\">21</td></tr>"; 
				 $infovent .= "<tr><td>vergers et vignes </td><td align=\"center\">22</td></tr>";
				 $infovent .= "<tr><td>prés et landes </td><td align=\"center\">23</td></tr>"; 
				 $infovent .= "<tr><td>bois </td><td align=\"center\">31</td></tr>"; 
				 $infovent .= "<tr><td>eaux </td><td align=\"center\">51</td></tr>";
				$infovent .='</table>';
				$textvent = "Les surfaces issues de Majic et non localisables sont ventilées sur 11, 12, 13, 15, 21, 22, 23, 31, 51";
				$textvent = '<span class="bulle">'.$textvent.'<span>'.$infovent.'</span></span>';
				$ligne0 = '<tr>';
				$ligne0 .= '<td bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
				$ligne0 .= '<font color="'.$color_text_code[$code_os].'">' .$code_os.'</font></td><td align="left" colspan=3>'.$tab[6].'<br>'.$textvent.'</td></tr>';
			}
			else
			{
				$infovent = "";
				$textvent = "Les surfaces issues du RPG et non localisables sont ventilées sur 21 à 24";
				$ligne .= ($pair==1)? '<tr>':'';
				$ligne .= '<td bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
				$ligne .= '<font color="'.$color_text_code[$code_os].'">' .$code_os.'</font></td><td align="left">'.$tab[6].'<br>'.$textvent.'</td>';
			}
		}
	$ligne .= ($pair==0)? '</tr>':'';
	$pair = ($pair==1)? 0:1;	
	} ;
$ligne .= $ligne0 ;
$ligne .= '<tr style="font-size:72%;"><td><a href="http://intra.ddtm-seine-maritime.i2/occupation-des-sols-os-a-l-echelle-a14961.html" target="_blank"><img src="'.$rep_images.'icon-doc.gif" width="80%" border="0" title="Plus d\'information sur le site intranet de la DDTM76"></a></td><td colspan=3 align="right">';
$ligne .= 'Table de référence : '.$nom_schema_ref.'.'.$nom_table_ref.'</td></TR>';
$ligne .= '<tr style="font-size:72%;"><td colspan=4  title="Cliquez ici pour voir les sources des données" align="left" onclick="javascript:ouvre_fen_millesime();">';
$ligne .= 'Sources : BDCARTO® BDTOPO® BDPARCELLAIRE® BDFORET® ©IGN, MAJIC ©DGFiP, ASP-DDTM27&76-RPG</td></TR>';

?>

<html>
<script language="JavaScript" type="text/JavaScript">
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}

function init() {
document.body.style.fontSize = Math.min(window.innerHeight/35,window.innerWidth/35);
}

function actualise_perimetre() {
var x = document.getElementById("millesime").selectedIndex;
var y = document.getElementById("millesime").options;
var Objhttp=new XMLHttpRequest();
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		document.getElementById("id").innerHTML=Objhttp.responseText;
    		}
  	}
	Objhttp.open("GET","ajax_perimetres_annee_n.php?annee_ref=" + y[x].value,true);
	Objhttp.send();
	document.getElementById("affiche_annee").style.visibility = "hidden";
	document.getElementById("annee").style.visibility = "hidden";
	document.getElementById("submit").style.visibility = "hidden";
	document.getElementById("newf").style.visibility = "hidden";
	document.getElementById("tableau").innerHTML="<br><br>Nouveau Calcul. Sélectionnez un périmètre";
}

function actualise_annee_comparaison(){
var x = document.getElementById("id").selectedIndex;
var y = document.getElementById("id").options;
var Objhttp=new XMLHttpRequest();
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		document.getElementById("annee").innerHTML=Objhttp.responseText;
    		}
  	}
	Objhttp.open("GET","ajax_annee_comparaison.php?id=" + y[x].value,true);
	Objhttp.send();
	document.getElementById("affiche_annee").style.visibility = "visible";
	document.getElementById("annee").style.visibility = "visible";
	document.getElementById("tableau").innerHTML="<br><br>Sélectionnez une année de comparaison et un code et lancer le calcul";
}

function affiche_bouton() {
	document.getElementById("submit").style.visibility = "visible";
	document.getElementById("newf").style.visibility = "visible";
}

function ouvre_fenetre(lien) {
	w = window.open(lien,"fiche","menubar=no, status=no, scrollbars=yes, width=800, height=700");
	w.focus();
}

function go(){
	var x = document.getElementById("id").selectedIndex;
	var y = document.getElementById("id").options;
	var z = document.getElementById("annee").selectedIndex;
	var w = document.getElementById("annee").options;
	var a = document.getElementById("code").selectedIndex;
	var b = document.getElementById("code").options;
	lien = "ajax_variations.php?id="+ y[x].value + "&annee="+ w[z].value + "&code="+ b[a].value ;
	ouvre_fenetre(lien);
}
// <input type="button" name="newf" id="newf" value="Ouvrir dans une nouvelle fenêtre" style="font-size:80%;visibility: hidden;" onclick="javascript:go();">
</script>
<head>
<title><?php echo $titre_tab ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
span.bulle {
  font-size : 100%;
  position : relative;
  border-bottom : 1px dotted #fa8;
}
span.bulle:hover {
  z-index : 100;
}
span.bulle span {
  font-size : 80%;
  text-align : left;
  display : none;
  border : 1px solid #fa8;
  background-color : #ffb;
  color : #000;
  text-decoration : none;
  white-space : nowrap;
}
span.bulle:hover span {
  position : absolute;
  top : 18px;
  left : 0px;
  padding : 5px;
  display : block;
}
</style>
<script language="JavaScript" type="text/JavaScript">
	MM_reloadPage(true);
	window.onload =init;
</script>
</head>
<body  bgcolor="#FFFFFF" onresize="init()">
<form name="form1" method="get" action="ajax_variations.php">
<table width="100%" border="0" cellspacing="2" cellpadding="0" style="font-size:64%;">
  <tr>
   <td width="20%" style="font-size:70%;">Année de référence :<select name="millesime" id="millesime" style="font-size:80%;visibility: visible;" onChange="actualise_perimetre();" >
		<?php echo $millesime ?>
   		</select>
   </td>
    <td width="70%" style="font-size:70%;"> 
		<select  name="id" id="id"  onChange="actualise_annee_comparaison();" style="font-size:80%;visibility: visible;"><?php echo $formperim ?></select>
		<span id="affiche_annee">année de comparaison : </span><select name="annee" id="annee" style="font-size:80%;visibility: visible;" onChange="affiche_bouton();"><?php echo $comparaison ?></select>
		<select name="code" id="code" style="font-size:80%;visibility: visible;"><?php echo $liste_codes ?></select>
			</td>	
	<td width="10%" style="font-size:70%;">
		<input type="submit" name="Submit" id="submit" value="Calculer" style="font-size:80%;visibility: visible;">
	</td>
  </tr><tr><td colspan=3 id="tableau"><br>
	<?php
	$tab = calcul_variation($nom_schema_ref,$nom_table_ref,$nom_schema_ref2,$nom_table_ref2,$code);
	echo $tab;
	?>
	</td>
  </tr></table></form>

<?php echo $ligne ?> 
</body>
</html>
<?php
$connexion=NULL;
?>
