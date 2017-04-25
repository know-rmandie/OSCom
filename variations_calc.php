<?php
	require_once("connexion.php");
	require_once("table_color.php");
	$oscom=new oscom;

   	$nom_schema=$oscom->get_value('schema_user');
	$schema_sources=$oscom->get_value('schema_sources');
	
	$table_communes_region = $schema_sources . '.' . $oscom->get_nomtable('table_communes');
	$table_scots_region = $schema_sources . '.' . $oscom->get_nomtable('table_scots');
	$table_epci_region = $schema_sources . '.' . $oscom->get_nomtable('table_epci');
	$table_perimetres=$nom_schema . '.' . $oscom->get_value('perimetres');
	
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
	$bloc_lib = oscom->get_bloc_lib();
	
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
			$select_param = "select id, libelle_court, libelle_long,schema,nom_table,millesime, sql from ". $table_perimetres . " where id =".$_GET['id'];
			} else {
			$select_param = "select id, libelle_court,libelle_long,schema,nom_table,millesime, sql from ". $table_perimetres . " order by id ";
		}
		$result = $oscom->get_requete($select_param) ;

		$row=$result->fetch(PDO::FETCH_ASSOC);
		$id = $row["id"];
		$libelle_court_ref = $row["libelle_court"];
		$libelle_long_ref = $row["libelle_long"];
		$nom_schema_ref = $row["schema"] ;
		$nom_table_ref = $row["nom_table"] ;
		$annee_ref = $row["millesime"] ;
		$sql =  $row["sql"];
		
//------- Préparation liste des millésimes
		$sql_millesime = "select millesime from ". $table_perimetres . " group by millesime order by millesime desc" ;
		$result = $oscom->get_requete($sql_millesime) ;
		
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
		$sql_comparaison = "select millesime from ". $table_perimetres . " where millesime <> ".$annee_ref . " group by millesime order by millesime desc" ;
		$result = $oscom->get_requete($sql_comparaison) ;

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
	if (($libelle_court_ref<>'Region') and (strpos($libelle_court_ref, 'Dept')===false)) {
		$sql_comp = "select * from ". $table_perimetres . " where (millesime =". $annee_comp . ") and (sql='" . $sql."')";
		} else {
			$sql_comp = "select * from " . oscom->_retourne_table_comparaison . "('".$annee_comp."','".$libelle_court_ref."') "; 
			//retourne_table_comparaison('".$annee."','".$lib_court."') "; 
			$sql_comp .= "as (libelle_long character varying,";
			$sql_comp .= "nom_table character varying,";
			$sql_comp .= "schema character varying,";
			$sql_comp .= "geoide character varying,";
			$sql_comp .= "image character varying,";	
			$sql_comp .= "extent_to_html character varying)";
		} ;
    $result = $oscom->get_requete($sql_comp) ;

	$row=$result->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_FIRST) ; // PDO::FETCH_ASSOC
		$nom_schema_ref2 = $row["schema"] ;
		$nom_table_ref2 =  $row["nom_table"] ;

//------ Préparation menu déroulant liste des périmètres sur l'année de référence
		$select_perimetres = "select * from ". $table_perimetres . " where millesime =". $annee_ref . ' order by bloc, libelle_long';
		$result = $oscom->get_requete($select_perimetres) ;

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
    global $oscom,$table_communes_region,$annee_comp,$annee_ref,$libelle_long_ref,$libelle_court_ref ;
	$calc  = "select codinsee, codos, surface_m2, nom from (";
	$calc .= "select * from ".oscom->get_schema_fonctions().".deltoscom('',";
	$calc .= "'". $schema1 . "','". $perim1 . "',";
	$calc .= "'". $schema2 . "','". $perim2 . "',";
	$calc .= "'". $code ."',";
	$calc .= "true, true,700) as (codinsee character varying, codos character varying, surface_m2 bigint)"; 
	$calc .= ") as f00, ".$table_communes_region." WHERE codinsee=code_insee";
	$result = $oscom->get_requete($calc) ;

	$tab = '<table width="75%" border="1" align="center" bordercolor="#eeeeee" cellspacing="0" style="font-size:100%;">';
	$titre_tab = ($libelle_court_ref!='CANTON')?'Périmètre "':'Canton "';
	$titre_tab = 'Evolution de '. $annee_comp. ' à ' . $annee_ref .' - ' . $titre_tab . $libelle_long_ref .'"';
	$tab .= '<tr><td colspan=4 align="center">'.$titre_tab . '</td></tr>';
	$tab .= '<tr align="center"><td width="10%">Insee</td><td width="50%">Nom Commune</td><td width="10%">code OS</td><td width="30%"> Surface en ha</td></tr>';
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$tab .= '<tr><td align="center">' . $row["codinsee"] .'</td><td>'. $row["nom"] .'</td><td align="center">'. $row["codos"] .'</td><td align="right">'. evolution_color(number_format($row["surface_m2"]/10000, 2, ',', ' ')) .'</td></tr>';
	};
	$tab .= "</table>";
	return $tab;
}

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
	Objhttp.open("GET","perimetres_annee_n.php?annee_ref=" + y[x].value,true);
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
	Objhttp.open("GET","annee_comparaison.php?id=" + y[x].value,true);
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
	lien = "variations.php?id="+ y[x].value + "&annee="+ w[z].value + "&code="+ b[a].value ;
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
