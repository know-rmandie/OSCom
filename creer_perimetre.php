<?php
	require_once("connexion.php");
	$oscom=new oscom;
	
	$listecommune= '';
	$listemillesime='';
	$virgule='';
	$nomliste='';
	$nomlistelong='';
	$sql = '';
    $userQgis = $oscom->get_value('userQgis');
	$admin = $oscom->get_value('user');
///--------------- NETTOYAGE de la table des paramètres --------------------------------

	$delai_nettoyage=$oscom->get_value('delai_nettoyage');
	
	$droptable='DROP TABLE IF EXISTS ';
	$sepdroptable='';
	// nettoyage des tables perso dans le schema utilisateur dans Postgresql
	
	$sql = 'select id, "schema", nom_table from '. $oscom->get_perimetre() .' where creation < current_date-'.$delai_nettoyage.' and permanent = false order by id';
	$result = $oscom->get_requete($sql);
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$droptable = $droptable .$sepdroptable.$row['schema'] .'.'. $row['nom_table'];
		$sepdroptable=',';
		}
	$droptable .= ' CASCADE';
	$result = $oscom->get_requete($droptable) ;
	// nettoyage des .map dans le dossier map sur le serveur
	$result = $oscom->nettoyage_map() ;
	// nettoyage de la table des paramètres
	
	$sql  = 'delete from '. $oscom->get_perimetre() .' where  creation < current_date-'.$delai_nettoyage.' and permanent = false ';
	$sql .= ' and ';
	$sql .= ' (nom_table,schema) not in (select tablename,schemaname from pg_tables where (tablename=nom_table)) ';
	
	$result = $oscom->get_requete($sql);

// -------------

	if (isset($_GET['commune'])&& !empty($_GET['commune'])) { 
			$tableau =explode(",", $_GET['commune']);
			foreach ($tableau as $insee) {
			$listecommune .= $virgule . $insee ;
			$virgule = ',';
			};
		};
		
	$virgule='';
	
	if (isset($_GET['millesime'])&& !empty($_GET['millesime'])) { 
			$tableau =$_GET['millesime'];
			foreach ($tableau as $millesime) {
			$listemillesime .= $virgule .$millesime;		
			$virgule = ',';
			};
		} else {
			$sql = 'select distinct millesime from '. $oscom->get_perimetre() ;
			$result = $oscom->get_requete($sql);
			while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
					$listemillesime .= $virgule .$row["millesime"];
					$virgule = ',';
					}
		}
			
	$nomliste = $_GET['nomliste'];
	$nomlistelong = $_GET['nomlistelong'];
	$schema_tab_dept=$oscom->get_value('schema_tab_dept');
	$schema_table_perso=$oscom->get_value('schema_table_perso');
	$prefix_table_perso=$oscom->get_value('prefix_table_perso');
	$message = '' ;
	$sql = "select * from ".$oscom->_creation_perimetre."('".$schema_tab_dept."','".$schema_table_perso."','".$prefix_table_perso."','".$nomliste."', '".str_replace("'","''''",$nomlistelong)."', '".$listecommune."', '".$listemillesime."','$admin','$userQgis')" ;

	$result = $oscom->get_requete($sql);
	if ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$message ='Création du périmètre "'.  $nomlistelong. '" terminée.</br>La page doit être actualisée pour mettre à jour le Menu "Choisir un périmètre"' ;
		}
	echo $message ;
?>
