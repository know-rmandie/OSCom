 <?php 
	require_once("connexion.php");
	$oscom=new oscom; 
	
	$table_communes_region = $oscom->get_nomtable('table_communes');
	$table_scots_region = $oscom->get_nomtable('table_scots');


if (isset($_GET['scot'])) {
	if ($_GET['scot']<>'') {
		$scot=$_GET['scot'];
		}
	};
	
$select_scot = "select distinct code_insee from ". $table_communes_region . ", ". $table_scots_region ." where " . $table_scots_region .".nom = '".$scot."' and ST_Within(ST_PointOnSurface(". $table_communes_region .".wkb_geometry),".$table_scots_region.".wkb_geometry) order by code_insee";

//------ Récupération des communes du SCOT de $scot
		
		$result = $oscom->get_requete($select_scot) ;
		$sep ='';
		$com = ''; //choisir un périmètre
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		    $com .= $sep . $row["code_insee"];
			$sep =', ';
			}
//
header("Content-Type: text/plain");
//header("Content-Type:text/html; charset=utf-8");
echo $com .',scot de '. $scot ;
?>


