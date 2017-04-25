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


	$nom_champ_geom_table_communes = $oscom->get_value('nom_champ_geom_table_communes');
	$nom_champ_geom_table_scots = $oscom->get_value('nom_champ_geom_table_scots');
	$nom_champ_geom_table_epci = $oscom->get_value('nom_champ_geom_table_epci');
	
	$nom_champ_libelle_table_communes = $oscom->get_value('nom_champ_libelle_table_communes');
	$nom_champ_libelle_table_scots = $oscom->get_value('nom_champ_libelle_table_scots');
	$nom_champ_libelle_table_epci = $oscom->get_value('nom_champ_libelle_table_epci');
	
	$id_table_communes = $oscom->get_value('id_table_communes'); // code insee
	$id_table_scots = $oscom->get_value('id_table_scots'); // code scot
	$id_table_epci = $oscom->get_value('id_table_epci'); // code epci
	
	
	$marequete ='SELECT code_oscom,' . $oscom->get_nomtable('table_communes').'.'. $nom_champ_libelle_table_communes .' as libelle,' . $oscom->get_nomtable('table_communes').'.'. $id_table_communes .' as insee FROM '.$_GET['schemaref'].'.'.$_GET['tableref'].','.$table_communes_region;
	$marequete .=" where st_intersects(".$oscom->get_value('geompar') .",st_setsrid(ST_GeomFromText('POINT(". $_GET["lon"] . " " . $_GET["lat"] .")'),2154)) ";
	$marequete .=' AND "code_oscom"<>\'\' ';
	$marequete .=' AND ' . $oscom->get_nomtable('table_communes').'.'. $id_table_communes .' = '.$_GET['tableref'].'.insee_comm';
	
	$result=$oscom->get_requete($marequete) ;
	$row=$result->fetch(PDO::FETCH_ASSOC);
	$code=$row["code_oscom"];
	
	$couleur = explode(" ", $tableau[$code][7]);
	$ligne  = '<fieldset><legend>Information</legend>'; 
	$ligne .= '<table>';
	$ligne .= '<tr><td><b>Code</b></td>';
	$ligne .= '<td bgcolor="'.ColorConverter::toHTML($couleur).'" align="left">' ;
	$ligne .= '<font color="'.$color_text_code[$code].'">' . $code .' - ' . $tableau[$code][6].' </font></td>';
	$ligne .= '<tr>';
	$ligne .= '<tr><td><b>Coordonnées du Point : </b></td><td align="center"> X :'. $_GET["lon"] . ' - Y : ' . $_GET["lat"] .'</td></tr>';
	$ligne .= '<tr><td><b>Commune</b></td><td align="left">'. $row["insee"] .' - ' .strtoupper($row["libelle"]) .'</td></tr>';
	$ligne .= '</table>';
	$ligne .= '</fieldset>';
	echo $ligne;
?>