<?php 
	require_once("connexion.php");
	$oscom=new oscom;
	

/////////////////// -------------------------------------------------------------------------------
// récupère les identifiants des tables de périmètre créé dans le module émetteur 
	$tabid = explode(',',$_GET["id"]);
	$maligne='<tr><td>Nom du périmètre</td><td>millésime</td><td><div id='.id.'>dessin réalisé</div></td></tr>';


	foreach ($tabid as $id) {
	////////////--------------- boucle à faire sur les identifiants id ---------------------------------------------------
		$schema_table = '';
		$nom_table ='';
		$millesime ='';
		
		$sql = 'select "schema", libelle_long, nom_table, millesime from '. $oscom->get_perimetre() .' where id ='.$id;
		$result = $oscom->get_requete($sql);
		if ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			$nom_table = $row['nom_table'] ;
			$millesime = $row['millesime'] ;
			$libelle_long = $row['libelle_long'] ;
			
			}
		$maligne = $maligne. '<tr><td>'.$libelle_long . '</td><td>'.$millesime. '</td><td><div id='.$id.'>Attendez SVP</div></td></tr>';
	}
		
	$maligne = '<TABLE border="0" cellpadding="0" cellspacing="0" style="font-size:65%; width:80%">'.$maligne.'<tr><td colspan=3><div id="carte"></div></td></tr></table>';

?>

<HTML>
<HEAD>
<TITLE>DRAAF HN - Connaissance des territoires - OS communale - exploitation</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</HEAD>
<body>
<?php 
	echo $maligne;
?>
<script language="JavaScript" type="text/JavaScript">

	parent.document.getElementById("Retour" ).disabled = false ;
	parent.document.getElementById("Retour" ).value="Retour";
	parent.document.getElementById("Retour" ).title="Cliquez ici pour revenir à la page principale";
</script>
</BODY>
</HTML>