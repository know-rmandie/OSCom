 <?php 
	require_once("connexion.php");
	$oscom=new oscom;

	$select_communes = "select insee_comm, nom from (select insee_comm, count(insee_comm) "; 
	$select_communes .= " from ". $_GET["nom_schema_perim"] . "." . $_GET["nom_table_perim"] . " group by insee_comm) as foo, " . $oscom->get_value('schema_sources').'.'.$oscom->get_nomtable('table_communes');
	$select_communes .= " where count >2 and insee_comm = code_insee ";
	$select_communes .= " order by nom ";

	$result = $oscom->get_requete($select_communes) ;
	//------ Préparation menu déroulant liste des communes
	$form = '<option value="">choisir une commune</option>';
	
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$form .= '<option value="' . $row["insee_comm"] .'|'.$row["nom"].'" >' . $row["insee_comm"] .' - ' . $row["nom"] .'</option>';
		}
	echo $form ;
	?>