<?php
	require_once("connexion.php");
	$oscom=new oscom;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
		<link rel="stylesheet" href="css/main.css" />	
		<script src="http://openlayers.org/api/OpenLayers.js"></script> 
		<script src="js/jquery-1.12.0.min.js" charset="utf-8"></script> 
		<script src="js/initialisation.js" charset="utf-8"></script> 	
		<script src="js/fonct_appels_php.js" charset="utf-8"></script> 	
		<script src="js/fonct_onglets.js" charset="utf-8"></script> 	
		<script src="js/fonct_menus_deroulants.js" charset="utf-8"></script> 		
		<script>
			var tab_parametre = <?php echo $oscom->get_param(); ?> ;
			var geoide= "<?php echo $oscom->get_geoide(); ?>" ;
			var rep_images= "<?php echo $oscom->get_images(); ?>" ;
			//alert(tab_parametre["2009"].["6"].[0]);
			$(document).ready(function(){
				onglet_01(anc_onglet_01) ;
				onglet_02(anc_onglet_02) ;
				var liste_annee = '';
				var j=0;
				for (i in tab_parametre) {
					liste_annee += '<option value="'+ i+'">'+i +'</option>'+"\n";
					if (j<i) {j=i};
					}
				liste_annee = liste_annee.replace('<option value="'+ j+'">', '<option value="'+ j+'" selected>'); 
				$('#annee').html(liste_annee);
				//$('#perimetre').hide();
				$('#id_perimetre').hide();	
				$('#commune').hide();	
				$('#id_commune').hide();	
				$('#info-tableau').hide();
				$('#compare_commune').hide();
				mes_perimetres();
			});
			
			$( window ).resize(function() {
				ma_carte();
			});
			

		</script>
		<title>OSCOM 2016</title>
	</head>

<body >

<div class="container">
	<header class="cadre-bandeau-haut">
		<div class="myselect-01">OSCOM 2016</div>
		<div id="id_millesime" class="myselect-02" onchange="mes_perimetres()">								
			<select name="annee" id="annee"></select>
		</div>
		<div id="id_perimetre" class="myselect-03">								
			<select name="perimetre" id="perimetre"  onchange="mes_communes()"></select>
		</div>
		<div id="id_commune" class="myselect-04" onchange="ma_carte()">								
			<select name="commune" id="commune"></select>
		</div>
	</header>
	<section class="cadre-centre">
		<section class="cadre-interne-gauche" id="cadre-interne-gauche">
			<nav>
					<ul>
						<li id="Internet" onclick="javascript:onglet_01(this)"><a>Internet</a></li>
						<li id="Intranet" onclick="javascript:onglet_01(this)"><a>Intranet</a></li>
						<li id="Evolution" onclick="javascript:onglet_01(this)"><a>Evolution</a></li>
						<li id="metadonnees" onclick="javascript:onglet_01(this)"><a>Métadonneés</a></li>
						<li style="float:right" id="explication" onclick="javascript:onglet_01(this)"><a>Mode d'emploi</a></li>						
					</ul>
			</nav>
			<div>
					<div class="panneau-masque" id="panneau-Internet" style="overflow: hide;"><p>Bienvenue sur l'Observatoire des Sols à l'échelle COMmunale</p>
					<iframe name="versGeoide" id="versGeoide" src="Cabourg.gif"></iframe>
					</div>
					<div class="panneau-masque" id="panneau-Intranet" style="overflow: hide;">
						<div id="map"></div>
					</div>
					<div class="panneau-masque" id="panneau-Evolution">page évolution</div>
					<div class="panneau-masque" id="panneau-metadonnees">page Métadonnées</div>
					<div class="panneau-masque" id="panneau-explication">page Mode d'emploi</div>
			</div>
		</section>	
		<section class="cadre-interne-droit">
			<!--div id="menus"-->
				<nav>
						<ul>
							<li id="mytableaux" onclick="javascript:onglet_02(this)"><a>Répartition des surfaces</a></li>
							<li id="source" onclick="javascript:onglet_02(this)"><a>Source</a></li>
							<li id="newperimetre" onclick="javascript:onglet_02(this)"><a>Nouveau périmètre</a></li>
							<li id="graphiques" onclick="javascript:onglet_02(this)"><a>Graphiques</a></li>
							<li id="comparaisons" onclick="javascript:onglet_02(this)"><a>Comparer</a></li>
							<li style="float:right" id="contacts" onclick="javascript:onglet_02(this)"><a>Contacts</a></li>						
						</ul>
				</nav>
				<div>
						<div class="panneau-masque" id="panneau-mytableaux">
							<div id="info-tableau" class="myselect-info-tableau">
							<!-- Tableaux des valeurs -->
							<table id="start" width="100%" border="1" cellpadding="1" cellspacing="0" bordercolor="cccccc"  style="font-size:100%;">
								<tr><th colspan=4 align=center>surfaces en ha</th></tr>
								<tr>
									<td width="30%" id="col_perimetre" align="center"></td>								
									<td width="30%" id="col_commune" align="center"></td>									
									<td width="30%" id="col_autre" align="center">
										<select name="compare_commune" id="compare_commune" onchange="creation_tableau_comparaison()"></select>
									</td>
								</tr>
								<tr>
									<td width="30%" id="tab_perimetre" valign="top"></td>								
									<td width="30%" id="tab_commune" valign="top"></td>									
									<td width="30%" id="tab_autre" valign="top"></div>
									</td>
								</tr>
							</table>
							</div>
							<div id="info-legende" class="myselect-info-legende"><!--Légende du tableau--></div>
							<div id="info-point" class="myselect-info-point"><!-- info sur le point cliqué--></div>
						</div>
						<div class="panneau-masque" id="panneau-source">page sources</div>
						<div class="panneau-masque" id="panneau-newperimetre">page Nouveau périmètre</div>
						<div class="panneau-masque" id="panneau-graphiques">page graphiques</div>
						<div class="panneau-masque" id="panneau-comparaisons">page comparaisons</div>
						<div class="panneau-masque" id="panneau-contacts">page contacts</div>
				</div>
			<!--/div-->
		</section>
	</section>
	<footer class="cadre-bandeau-bas">
	bandeau bas
	</footer>
</body>

</html> 