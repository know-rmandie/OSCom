 <?php
	require_once("connexion.php");
	require_once("table_color.php");
	$oscom=new oscom;
	//echo "essai";
	$ligne="";
	$ligne .='<table id="start" border="1" cellpadding="2" cellspacing="0" bordercolor="cccccc">';
	$ligne .= '<tr align="center" valign="middle"><th >Code</th><th >Légende</th><th >Code</th><th >Légende</th></tr>';
	$pair = 0 ;
	$ligne0 ='';
	foreach ($tableau as $code=>$tab) {
		$couleur = explode(" ", $tab[7]);
		$ligne .= ($pair==1)? '<tr>':''; // on commence une ligne de 2 colonnes
		
		if ($code<>'00' and $code<>'20' and $code<>'**') {
			$ligne .= '<td bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
			$ligne .= '<font color="'.$color_text_code[$code].'">' .$code.'</font></td>';
			$ligne .= '<td align="left">'.$tab[6].'</td>';
			}
		if ($code=='00' OR $code=='20') {
			if ($code=='00') {
				$infovent = '<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#eeeeee" style="font-size:100%;">';
				$infovent .= '<tr><td>Libellé Majic</td><td align="center">Affectation</td></tr>';
				$infovent .= "<tr><td>sols, jardins et terrains d'agrément </td><td align=\"center\">11</td></tr>";
				 $infovent .= "<tr><td>voies ferrées  </td><td align=\"center\">12</td></tr>";
				 $infovent .= "<tr><td>carrières</td><td align=\"center\">13</td></tr>"; 
				 $infovent .= "<tr><td>terrains à bâtir </td><td align=\"center\">15</td></tr>";
				 $infovent .= "<tr><td>terres </td><td align=\"center\">21</td></tr>"; 
				 $infovent .= "<tr><td>vergers et vignes </td><td align=\"center\">22</td></tr>";
				 $infovent .= "<tr><td>prés et landes </td><td align=\"center\">23</td></tr>"; 
				 $infovent .= "<tr><td>bois </td><td align=\"center\">31</td></tr>"; 
				 $infovent .= "<tr><td>eaux </td><td align=\"center\">51</td></tr>";
				$infovent .='</table>';
				$textvent = "Les surfaces issues de Majic et non localisables sont ventilées sur 11, 12, 13, 15, 21, 22, 23, 31, 51";
				$textvent = '<span class="bulle">'.$textvent.'<span>'.$infovent.'</span></span>';
				$ligne0 = '<tr>';
				$ligne0 .= '<td bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
				$ligne0 .= '<font color="'.$color_text_code[$code].'">' .$code.'</font></td><td align="left" colspan=3>'.$tab[6].'<br>'.$textvent.'</td></tr>';
			}
			else
			{
				$infovent = "";
				$textvent = "Les surfaces issues du RPG et non localisables sont ventilées sur 21 à 24";
				$ligne .= ($pair==1)? '<tr>':'';
				$ligne .= '<td bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
				$ligne .= '<font color="'.$color_text_code[$code].'">' .$code.'</font></td><td align="left">'.$tab[6].'<br>'.$textvent.'</td>';
			}
		}
			
		$ligne .= ($pair==0)? '</tr>':'';
		$pair = ($pair==1)? 0:1;	
	} ;
	$ligne .= $ligne0 ;
	$ligne .= '<tr style="font-size:72%;"><td><a href="'.$oscom->get_url_site().'" target="_blank"><img src="icon-doc.gif" width="80%" border="0" title="Plus d\'information sur le site '.$oscom->get_nom_site().'"></a></td><td colspan=3 align="right">';
	$ligne .= 'Table de référence : <b><span id="tabref"></span></b> - Schéma : <b><span id="schref"></span></b></td></TR>';
	$ligne .= '<tr style="font-size:72%;"><td colspan=4  title="Voir le détail des données dans Sources" align="left">';
	$ligne .= 'Sources : BDCARTO® BDTOPO® BDPARCELLAIRE® BDFORET® ©IGN, MAJIC ©DGFiP, RPG ASP-'.$oscom->get_value('sources').'</td></TR>'; 
	$ligne .= '</table>';
	$ligne .= '</br><div id="info-point" class="myselect-info-point"><!-- info sur le point cliqué--></div>';
	//-------------------------
	echo $ligne ;	
?>