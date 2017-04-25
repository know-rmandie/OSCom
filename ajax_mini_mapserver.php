<?php 
require_once("param_server.php"); 

/////////////////// -------------------------------------------------------------------------------
// récupère les identifiants des tables de périmètre créé dans le module émetteur 
$tabid = explode(',',$_GET["id"]);
$hauteur = $_GET["hauteur"] ;
//$tabid =array(220,221,222,223);
//$tabid =array(230);
//$hauteur = 800 ;
$maligne='<tr><td>Nom du périmètre</td><td>millésime</td><td><div id='.id.'>dessin réalisé</div></td></tr>';
$imagewidth = 'width="65%"';

foreach ($tabid as $id) {
////////////--------------- boucle à faire sur les identifiants id ---------------------------------------------------
	$schema_table = '';
	$nom_table ='';
	$millesime ='';
	
	$sql = 'select "schema", libelle_long, nom_table, millesime from '. $nom_schema.'.'.$table_perimetres.' where id ='.$id;
	$result = $connexion->prepare($sql) ;
			$result->execute();
			if ($row=$result->fetch(PDO::FETCH_ASSOC)) {
				$nom_table = $row['nom_table'] ;
				$millesime = $row['millesime'] ;
				$libelle_long = $row['libelle_long'] ;
				
				}
	$maligne = $maligne. '<tr><td>'.$libelle_long . '</td><td>'.$millesime. '</td><td><div id='.$id.'>Attendez SVP</div></td></tr>';
	//$image_url_distant = $rep_images.$schema_table.'_'.$nom_table .'.png';
	//$libelle_bas ="Table de référence : ".$table. " - ".$millesime;

}
		
$maligne = '<TABLE border="0" cellpadding="0" cellspacing="0" style="font-size:65%; width:80%">'.$maligne.'<tr><td colspan=3><div id="carte"></div></td></tr></table>';

$connexion=NULL;
?>

<HTML>
<HEAD>
<TITLE>DRAAF HN - Connaissance des territoires - OS communale - exploitation</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</HEAD>

<script language="JavaScript" type="text/JavaScript">
compteur = 0;
function dessin(IdDiv) {
// fonction ajax lance le calcul de la carte pour le périmètre ID
// retourne OK
	var Objhttp=new XMLHttpRequest();
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		 if (Objhttp.responseText!=''){
				document.getElementById(IdDiv).innerHTML='carte OK';
				document.getElementById("carte").innerHTML=Objhttp.responseText;
				compteur = compteur -1;
				if (compteur < 1) {
					active();
					}
				}
    		}
  	}
	Objhttp.open("GET","ajax_mini_mapserver_carto.php?id="+IdDiv,true);
	compteur = compteur + 1;
	Objhttp.send();
}

function active() {
	parent.document.getElementById("Retour" ).disabled = false ;
	parent.document.getElementById("Retour" ).value="Retour";
	parent.document.getElementById("Retour" ).title="Cliquez ici pour revenir à la page principale";
	}

function init() {
	// document.body.style.fontSize = Math.min(window.innerHeight/32,window.innerWidth/15);window.innerHeight / window.innerWidth
	document.getElementById('carte').style.height =<?php echo $hauteur ?>*0.8 ;
	document.getElementById('carte').style.Width = <?php echo $hauteur ?>*0.8 ;
	}
window.onload =init;

</script>
<body onresize="init()">

<?php 
	echo $maligne;
?>
<script language="JavaScript" type="text/JavaScript">
	<?php
		foreach ($tabid as $id) {
			echo "dessin($id);";
			}
	?>
</script>
</BODY>
</HTML>