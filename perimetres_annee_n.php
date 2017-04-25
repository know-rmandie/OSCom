<?php
	require_once("connexion.php");
	require_once("table_color.php");
	$oscom=new oscom;

   	$nom_schema=$oscom->get_value('schema_user');
	$schema_sources=$oscom->get_value('schema_sources');
	
	$table_communes_region = $schema_sources . '.' . $oscom->get_nomtable('table_communes');
	$table_scots_region = $schema_sources . '.' . $oscom->get_nomtable('table_scots');
	$table_epci_region = $schema_sources . '.' . $oscom->get_nomtable('table_epci');
	$table_perimetres= $nom_schema . '.' .$oscom->get_value('perimetres');
	

	function evolution_color($v) {
		if ($v<0) {
			$v = '<font color="#FF0000">'.$v.'</font>';
		}
		return $v;
	}

//------ Préparation menu déroulant liste des périmètres
	$bloc_lib = $oscom->get_bloc_lib();

// retourne les périmètres de l'année de référence n prise en paramètre d'entrée
	if ( isset($_GET['annee_ref']) and ($_GET['annee_ref']<>'') ) {
		$annee_ref = $_GET['annee_ref'];
		//------ Préparation menu déroulant liste des périmètres sur l'année de référence
		$select_perimetres = "select * from ". $table_perimetres . " where millesime =". $annee_ref . ' order by bloc, libelle_long';

		$result = $oscom->get_requete($select_perimetres) ;

		$formperim = '<option value="" selected>choisir un périmètre ' . $annee_ref.'</option>'; //choisir un périmètre
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
				$formperim .= '<option value="' .$row["id"].'" >' . $row["libelle_long"].'</option>';
				$selection ='';
				$libelle_long = $row["libelle_long"] ;
				}
			}
		$formperim .= $optgroup ;
		} else {
		$formperim = '<option value="" selected>choisir une année de référence svp</option>'; 
		};
		echo $formperim;
?>