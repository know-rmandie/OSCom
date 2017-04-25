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
	
// retourne les années de comparaison pour le périmètre séléctionné
	if ( isset($_GET['id']) and ($_GET['id']<>'') ) {
		$id = $_GET['id'];
		//------ Préparation menu déroulant liste des périmètres sur l'année de référence
		$select_annee_comp = "select distinct millesime from ". $table_perimetres . " where id <>". $id;
		$select_annee_comp .= " and libelle_long in (select libelle_long from ". $table_perimetres . " where id=". $id.")";
		$select_annee_comp .= "	order by millesime";
		$result = $oscom->get_requete($select_annee_comp) ;
		$comparaison = '<option value="">Choisir SVP</option>';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			$comparaison .= '<option value="' . $row["millesime"] .'" ' . $selection .' >' .  $row["millesime"] .'</option>';
		}
	}else {
		$comparaison = '<option value="" selected>choisir périmètre de référence svp</option>'; 
		};
	echo $comparaison;
	echo "<br>";
	echo $select_annee_comp;
?>