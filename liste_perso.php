 <?php 
	require_once("connexion.php");
	$oscom=new oscom; 
	$schema_sources=$oscom->get_value('schema_sources');
	$table_communes_region = $oscom->get_nomtable('table_communes');
	$table_scots_region = $oscom->get_nomtable('table_scots');
	$table_epci_region = $oscom->get_nomtable('table_epci');
	
	$nom_champ_geom_table_communes = $oscom->get_value('nom_champ_geom_table_communes');
	$nom_champ_geom_table_scots = $oscom->get_value('nom_champ_geom_table_scots');
	$nom_champ_geom_table_epci = $oscom->get_value('nom_champ_geom_table_epci');
	
	$id_table_communes = $oscom->get_value('id_table_communes'); // code insee
	$id_table_scots = $oscom->get_value('id_table_scots'); // code scot
	$id_table_epci = $oscom->get_value('id_table_epci'); // code epci
	
if (isset($_GET['scot'])) {
	if ($_GET['scot']<>'') {
		$scot=$_GET['scot'];
		$select = "select distinct ". $table_communes_region . "." .$id_table_communes." from ". $schema_sources.".". $table_communes_region . ", ". $schema_sources.'.'.$table_scots_region ." where " . $table_scots_region.".".$id_table_scots." = '".$scot."' and ST_Within(ST_PointOnSurface(". $table_communes_region.".".$nom_champ_geom_table_communes."),".$table_scots_region.".".$nom_champ_geom_table_scots.") order by ".$id_table_communes;
		$msg=', SCOT de '.$scot;
		}
	};
	
if (isset($_GET['epci'])) {
	if ($_GET['epci']<>'') {
		$epci=$_GET['epci'];
		$select = "select distinct ". $table_communes_region . "." .$id_table_communes." from ".$schema_sources.'.'.$table_communes_region.", ".$schema_sources.'.'.$table_epci_region." where ".$table_epci_region.".".$id_table_epci." = '".$epci."' and ST_Within(ST_PointOnSurface(".$table_communes_region.".".$nom_champ_geom_table_communes."),".$table_epci_region.".".$nom_champ_geom_table_epci.") order by ".$id_table_communes;
		$msg=', EPCI de '.$epci;		
		}
	};

	
if (isset($_GET['canton'])) {
	if ($_GET['canton']<>'') {
		$canton=$_GET['canton'];
		$depart=$_GET['depart'];
		$select = "select distinct ".$id_table_communes." from ".$schema_sources.'.'.$table_communes_region." where depart = '".$depart."' and canton='".$canton."' order by ".$id_table_communes;
		$msg=', CANTON de '.$canton . ' ('.$depart.')';			
		}
	};


//------ Récupération des communes du périmètre
		
		$result = $oscom->get_requete($select) ;
		$sep ='';
		$com = ''; //choisir un périmètre
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		    $com .= $sep . $row[$id_table_communes];
			$sep =', ';
			}

header("Content-Type: text/plain");
//echo $select ;
//echo "</br>";
echo $com .$msg ;
?>


