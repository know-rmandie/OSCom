 <?php
	require_once("connexion.php");
	require_once("table_color.php");
	echo "essai";
	$ligne="";
	$ligne .='<table id="start" border="1" cellpadding="2" cellspacing="0" bordercolor="cccccc"  style="font-size:30%;">';
	$ligne .= '<tr align="center" valign="middle"><th >Code</th><th >Légende</th><th >Code</th><th >Légende</th></tr>';
	$pair = 0 ;
	$ligne0 ='';
			if ($code=='0' OR $code=='20') {
			if ($code=='0') {
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
	foreach ($tableau as $code=>$tab) {
		$couleur = explode(" ", $tab[7]);
		$ligne .= ($pair==1)? '<tr>':''; // on commence une ligne de 2 colonnes
		
		if ($code<>'0' and $code<>'20' and $code<>'**') {
			$ligne .= '<td bgcolor="'.ColorConverter::toHTML($couleur).'" align="center">' ;
			$ligne .= '<font color="'.$color_text_code[$code].'">' .$code.'</font></td>';
			$ligne .= '<td align="left">'.$tab[6].'</td>';
			}

	$ligne .= ($pair==0)? '</tr>':'';
	$pair = ($pair==1)? 0:1;	
	} ;
	$ligne .= $ligne0 ;
	$ligne .= '<tr style="font-size:72%;"><td><a href="http://intra.ddtm-seine-maritime.i2/occupation-des-sols-os-a-l-echelle-a14961.html" target="_blank"><img src="icon-doc.gif" width="80%" border="0" title="Plus d\'information sur le site intranet de la DDTM76"></a></td><td colspan=3 align="right">';
	$ligne .= 'Table de référence : <span id="tabref"></span></td></TR>';
	$ligne .= '<tr style="font-size:72%;"><td colspan=4  title="Cliquez ici pour voir les sources des données" align="left" onclick="javascript:ouvre_fen_millesime();">';
	$ligne .= 'Sources : BDCARTO® BDTOPO® BDPARCELLAIRE® BDFORET® ©IGN, MAJIC ©DGFiP, ASP-DDTM27&76-RPG</td></TR>';
	$ligne .= '</table>';
	
	//-------------------------
	echo $ligne ;	
?>