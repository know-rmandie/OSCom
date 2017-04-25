<?php
	require_once("connexion.php");
	$oscom=new oscom;
	
	$schsrc = $_GET['oscomsurfdtl_schsrc'] ;
	$tabsrc = $_GET['oscomsurfdtl_tabsrc'] ;
	$libelle_perimetre = $_GET['oscomsurfdtl_nom'] ;
	$annee = $_GET['oscomsurfdtl_annee'] ;
	$tabres = '_'.str_replace('.','_',$_SERVER["REMOTE_ADDR"]);
	$schres = $schsrc ;
	$pfxres = 'true';
	$schcom= $oscom->get_value(schema_sources);
	$tabcom= $oscom->get_nomtable('table_communes');
	$colinsee= $oscom->get_value('id_table_communes');
	$colnom= $oscom->get_value('nom_champ_libelle_table_communes');
	$ecrase= 'true';
	$select_surface= "select * from fct.oscomsurfdtl('".$schsrc."', '".$tabsrc."', '".$schres."', '".$tabres."', $pfxres, '".$schcom."', '".$tabcom."', '".$colinsee."', '".$colnom."', $ecrase)";

	$result = $oscom->get_requete($select_surface) ;
	
	$select_surface= '
	SELECT insee_comm as "INSEE commune", nom_comm as "Nom commune", 
		round(CAST(srf_tot_m2/10000 as numeric),2) as "surface totale", 
		round(CAST(srf_11_m2/10000 as numeric),2) as "code 11", 
		round(CAST(srf_12_m2/10000 as numeric),2) as "code 12",
		round(CAST(srf_13_m2/10000 as numeric),2) as "code 13", 
		round(CAST(srf_14_m2/10000 as numeric),2) as "code 14",
		round(CAST(srf_15_m2/10000 as numeric),2) as "code 15", 
		round(CAST(srf_21_m2/10000 as numeric),2) as "code 21", 
		round(CAST(srf_22_m2/10000 as numeric),2) as "code 22",
		round(CAST(srf_23_m2/10000 as numeric),2) as "code 23", 
        round(CAST(srf_31_m2/10000 as numeric),2) as "code 31",
		round(CAST(srf_32_m2/10000 as numeric),2) as "code 32", 
		round(CAST(srf_51_m2/10000 as numeric),2) as "code 51",
		round(CAST(srf_xx_m2/10000 as numeric),2) as "Indéterminées"
	FROM ' . $schres.'.'. $tabsrc.$tabres;

	$result = $oscom->get_requete($select_surface) ;
	$table ="<h3>Occupation des sols $annee</h3>";
	$table .="<h1>Répartition des Surfaces des communes du périmètre $libelle_perimetre (en hectare)</h1>";
	$table .='<table id="start" width="100%" border="1" cellpadding="1" cellspacing="0" bordercolor="cccccc"  style="font-size:100%;">';
	$table .='<thead>';
	
	$row = $result->fetch(PDO::FETCH_ASSOC);
	// array_keys prend les entêtes des colonnes
	$table .= '<tr><th align="center">' . implode('</th><th align="center">',array_keys($row)). '</th></tr>';
	$table .=  '</thead';
	// lecture de la table et mise en place des alignements
	
	do {
		$temp =  '<tr>';
		foreach (array_keys($row) as $titre) {
			$align =  'right';	
			if ($titre=='INSEE commune'){$align =  'center';}
			if ($titre=='Nom commune'){$align =  'left';}
			$temp .= '<td align="'.$align.'" title="'.$titre.'">'.$row[$titre].'</td>';
		}
		$temp .= '</tr>';
		$table .= $temp."\n" ;
	} while($row = $result->fetch(PDO::FETCH_ASSOC));
	$table .="</table>";
	
	$select_surface="DROP TABLE " . $schres.".". $tabsrc.$tabres . " CASCADE" ;
	$result = $oscom->get_requete($select_surface) ;
	
    header("Content-disposition: attachment; filename=oscom_surfaces_communales.xls");
    header('Content-Type: text/html; charset=iso-8859-1');
    header("Content-Type: application/force-download");
    header("Content-Transfer-Encoding: application/vnd.ms-excel\n");
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
    header("Expires: 0"); 
    echo utf8_decode($table) ;
?>