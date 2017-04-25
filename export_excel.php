<?php
	require_once("connexion.php");
	require_once("table_color.php");
	$oscom=new oscom;
	$var_schema =''; 			
	$var_perimetre = ''; 
	
	$schema = $_GET['schema']; 			
	$perimetre = $_GET['perimetre']; 	
	$tab_commentaire = $_GET['tab_commentaire'];
	if (isset($_GET['var_schema'])) { $var_schema = $_GET['var_schema']; }			
	if (isset($_GET['var_perimetre'])) { $var_perimetre = $_GET['var_perimetre']; }	
	
	$insee = $_GET['insee'];		
	$nature_tab = $_GET['nature_tab'];	
	$nom_commune = $_GET['commune'];	
	$titre_tab = $_GET['titre_tab'];

	$select_tableau = "select * from " . $oscom->get_schema_fonctions().".surfoscom('".$insee ."','".$schema."','".$perimetre."','".$var_schema."','".$var_perimetre."',false) as (code character varying, surface_m2 bigint, part real)";
	$result = $oscom->get_requete($select_tableau) ;
	
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$tableau[$row['code']][0]=number_format($row['surface_m2']/10000,2,'.',' ') ;
		$tableau[$row['code']][3]=number_format($row['part']*100,2,'.',' ') . ' %';
		if ($row['surface_m2']<0) {
			$tableau[$row['code']][0]='<font color="#FF0000">'.$tableau[$row['code']][0].'</font>';
			$tableau[$row['code']][3]='<font color="#FF0000">'.$tableau[$row['code']][3].'</font>';
		}
	}
	
	$ligne ='<table id="start" width="100%" border="1" cellpadding="1" cellspacing="0" bordercolor="cccccc"  style="font-size:100%;">';
	$ligne .= '<tr><td colspan=3 align=center><b>'.$titre_tab.'</b></td></tr>';
	$ligne .= '<tr><td colspan=3 align=center><b>'.$tab_commentaire.'</b></td></tr>';
	
	if ($nature_tab!='tab_code'){
		foreach ($tableau as $code=>$tab) {
			$couleur = explode(" ", $tab[7]);
			$codetitle = (($code == '0') || ($code =='++')|| ($code =='--')|| ($code =='99'))? '': $code. ' - ';
			$codetitle .= $tab[6];
			if ($code<>'0' and $code<>'20' and $code<>'**') {
				$infocode = ($code=='++')? 'surface en ha' : (($code=='--')? 'surface en ha et % non traités' :'surface en ha et % de code '.$code. ' traités ');
				$ligne .= '<tr>' ;
				$ligne .= '<td title="'.$codetitle.'" bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
				$ligne .= '<font color="'.$color_text_code[$code].'">' .$code.'</font></td>';
				$ligne .= '<td title="'.$infocode.'" align="right">'.$tab[0].'</td><td title="'.$infocode.'" align="right">'.$tab[3].'</td>';
				$ligne .= '</tr>';
				}
		} ;
	} else { // tableau de code uniquement
		foreach ($tableau as $code=>$tab) {
			$couleur = explode(" ", $tab[7]);
			$codetitle = (($code == '0') || ($code =='++')|| ($code =='--')|| ($code =='99'))? '': $code. ' - ';
			$codetitle .= $tab[6];
			if ($code<>'0' and $code<>'20' and $code<>'**') {
				$infocode = ($code=='++')? 'surface en ha' : (($code=='--')? 'surface en ha et % non traités' :'surface en ha et % de code '.$code. ' traités ');
				$ligne .= '<tr>' ;
				$ligne .= '<td title="'.$codetitle.'" bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
				$ligne .= '<font color="'.$color_text_code[$code].'">' .$code.'</font></td>';
				$ligne .= '</tr>';
				}
		}	
	}
	if ($var_schema =='') {
		$ligne .= '<tr><td colspan=3 align=center><b>Table : '.$schema.'.'.$perimetre.'</b></td></tr>';
		} else {
		$ligne .= '<tr><td colspan=3 align=center><b>Table de base : '.$schema.'.'.$perimetre.'</br>Table de comparaison : '.$var_schema .'.'.$var_perimetre.'</b></td></tr>';		
	}
	$ligne .= '</table>';
    header("Content-disposition: attachment; filename=oscom.xls");
    header('Content-Type: text/html; charset=iso-8859-1');
    header("Content-Type: application/force-download");
    header("Content-Transfer-Encoding: application/vnd.ms-excel\n");
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
    header("Expires: 0");   
    echo utf8_decode($ligne) ;
?>