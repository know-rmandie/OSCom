<?php
	require_once("connexion.php");
	$oscom=new oscom;
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />  
		<meta http-equiv="Expires" content="0" />
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
			var bloc_lib =<?php echo $oscom->get_bloc_lib(); ?> ;
			var ip_serv="<?php echo $oscom->get_value('ip_serv'); ?>" ;
			//alert(tab_parametre["2009"].["6"].[0]);
			$(document).ready(function(){
				/*
				onglet_01(anc_onglet_01) ;
				onglet_02(anc_onglet_02) ;
				onglet_03(anc_onglet_03) ;
				*/
				var liste_annee = '';
				var j=0;
				for (i in tab_parametre) {
					liste_annee += '<option value="'+ i+'">'+i +'</option>'+"\n";
					if (j<i) {j=i};
					}
				liste_annee = liste_annee.replace('<option value="'+ j+'">', '<option value="'+ j+'" selected>'); 
				$('#annee').html(liste_annee);

				mes_perimetres();
			});
			
			$( window ).resize(function() {
				ma_carte();
			});
			
			function ouvre_fenetre(lien) {
				w = window.open(lien,"fiche","menubar=no, status=no, scrollbars=yes, width=800, height=700");
				w.focus();
			}
		</script>
		<title>OSCOM version 2016</title>
	</head>

<body >
<div class="container">
	<header class="cadre-bandeau-haut">
		<div class="myselect-01">&nbsp;&nbsp;&nbsp;Observatoire des Sols à l'échelle Communale (OSCOM)&nbsp;&nbsp;&nbsp;</div>
		<div id="id_millesime" class="myselect-02" onchange="mes_perimetres()">								
			<select name="annee" id="annee"></select>
		</div>
		<div id="id_perimetre" class="myselect-03">								
			<select name="perimetre" id="perimetre"  onchange="mes_communes()"></select>
		</div>
		<div id="id_commune" class="myselect-04" onchange="ma_carte()">								
			<select name="commune" id="commune"></select>
		</div>
		<div id="id_reload" class="myselect-05">								
			<input id="reinitialisation_bis" name="Input" type="button" value="Actualiser les périmètres" onclick="document.location.reload();" title="Cliquez ici pour mettre à jour le menu Périmètre" class="myselect-05">
		</div>
	</header>
	<section class="cadre-centre">
		<section class="cadre-interne-gauche" id="cadre-interne-gauche">
			<nav>
					<ul>
						<li id="accueil_gauche" onclick="javascript:onglet_01(this)"><a>Bienvenue sur l'OSCOM</a></li>
						<li id="Internet" onclick="javascript:onglet_01(this)"><a>Internet</a></li>
						<li id="Intranet" onclick="javascript:onglet_01(this)"><a>Intranet</a></li>
						<li id="Evolution" onclick="javascript:onglet_01(this)"><a>Evolution sur le périmètre</a></li>
						<li id="Surfaces" onclick="javascript:onglet_01(this)"><a>Surfaces communales</a></li>
						<li id="metadonnees" onclick="javascript:onglet_01(this)"><a>Métadonnées</a></li>
						<li style="float:right" id="explication" onclick="javascript:onglet_01(this)"><a>Mode d'emploi</a></li>						
					</ul>
			</nav>
			<div>
					<div class="panneau-masque" id="panneau-accueil_gauche" style="overflow: hide;"><p>Les étapes de la création de l'OSCOM en images</p>
							<img src="Cabourg.gif" width="100%"></img>
					</div>
					<div class="panneau-masque" id="panneau-Internet" style="overflow: hide;"><p>Cette carte est aussi disponible sur Internet à l'adresse <span id="adresse_web"></span></p>
					<iframe name="versGeoide" id="versGeoide" src="Cabourg.gif" width="100%"></iframe>
					</div>
					<div class="panneau-masque" id="panneau-Intranet" style="overflow: hide;">
						<div id="change_contour_carte">
						<!-- <p><img src="permute.gif" width="20vh" onclick="permute_map();" Title="Cliquez pour changer le mode d'affichage" > Mode contour</p>-->
						<input type="button" name="change_contour" id="change_contour" value="Mode contour" onclick="permute_map()" Title="Cliquez pour changer le mode d'affichage">
						</div>
						<div id="bouton-ajuster-carte"><input type="button" name="ajuster-carte" value="Emprise totale" onclick="creation_carte_intranet()"></div>
						<div id="map"></div>
					</div>
					<div class="panneau-masque" id="panneau-Evolution"><p>Evolution des codes sur le périmètre sélectionné</p>
					<iframe name="versVariation" id="versVariation"></iframe>
					</div>
					<div class="panneau-masque" id="panneau-Surfaces">
					<?php include("surfaces.html"); ?>
					<form id="oscomsurfdtl" name="oscomsurfdtl" action="excel_surfaces.php" target="_blank" style="font-size: 1.5vh;">
						<input type="hidden" name="oscomsurfdtl_schsrc" id="oscomsurfdtl_schsrc"> 
						<input type="hidden" name="oscomsurfdtl_tabsrc" id="oscomsurfdtl_tabsrc"> 
						<input type="hidden" name="oscomsurfdtl_nom" id="oscomsurfdtl_nom">
						<input type="hidden" name="oscomsurfdtl_annee" id="oscomsurfdtl_annee">
						<input type="submit" name="oscomsurfdtl_submit" id="oscomsurfdtl_submit" value="Télécharger le tableau des Surfaces communales sur le périmètre" Title="Cliquez pour importer le tableau complet des surfaces dans un tableur">	
					</form>
					</div>
					<div class="panneau-masque" id="panneau-metadonnees">page Métadonnées</div>
					<div class="panneau-masque" id="panneau-explication"><?php include("mode_emploi.html"); ?></div>
			</div>
		</section>	
		<section class="cadre-interne-droit">
			<!--div id="menus"-->
				<nav>
						<ul>
							<li id="accueil" onclick="javascript:onglet_02(this)"><a>OSCOM version 2016</a></li>
							<li id="mytableaux" onclick="javascript:onglet_02(this)"><a>Répartition des surfaces en ha</a></li>
							<li id="newperimetre" onclick="javascript:onglet_02(this)"><a>Ajouter un périmètre</a></li>
							<li id="source" onclick="javascript:onglet_02(this)"><a>Sources</a></li>
							<li style="float:right" id="contacts" onclick="javascript:onglet_02(this)"><a>Contacts ADL et Administration du Site</a></li>						
						</ul>
				</nav>
				<div class="panneau-masque" id="panneau-accueil"><?php include("presentation.html"); ?></div>
				<div class="panneau-masque" id="panneau-mytableaux">
						<div id="info-tableau" class="myselect-info-tableau">
						<!-- Tableaux des valeurs -->
						<table id="start" width="100%" border="1" cellpadding="1" cellspacing="0" bordercolor="cccccc"  style="font-size:100%;">
							<tr>
								<td id="col_perimetre" align="center"></td>								
								<td id="col_commune" align="center"></td>									
								<td id="col_autre" align="center">
									<span id="autre_excel"></span><select name="compare_commune" id="compare_commune" onchange="creation_tableau_comparaison()"></select>
								</td>
							</tr>
							<tr>
								<td id="tab_perimetre" valign="top"></td>								
								<td id="tab_commune" valign="top"></td>									
								<td id="tab_autre" valign="top">
								</td>
							</tr>
						</table>
						</div>
								 			
						<nav>
							<ul>
								<li id="info-legende" onclick="javascript:onglet_03(this)"><a>Légende</a></li>
								<li id="graphiques" onclick="javascript:onglet_03(this)"><a>Graphiques</a></li>
								<li id="comparaisons" onclick="javascript:onglet_03(this)"><a>Comparer <span id="annee_base"></span> à <select name="var_annee" id="var_annee" onchange="set_selection_annee()"></select></a></li>
							</ul>
						</nav>
						<div class="panneau-masque" id="panneau-info-legende"><!--Légende du tableau--></div>
						<div class="panneau-masque" id="panneau-graphiques">page graphiques</div>
						<div class="panneau-masque" id="panneau-comparaisons"  class="myselect-08">
								<!-- Tableaux des valeurs évolution sur la commune -->
								<table id="var_tableau" width="100%" border="1" cellpadding="1" cellspacing="0" bordercolor="cccccc"  style="font-size:100%;">
									<tr><td colspan=3 id="info_tab_comparaison"></td></tr>
									<tr>
										<td id="var_col_perimetre" align="center"></td>								
										<td id="var_col_commune" align="center"></td>									
										<td id="var_col_autre" align="center"></td>
									</tr>
									<tr>
										<td id="var_tab_perimetre" valign="top"></td>								
										<td id="var_tab_commune" valign="top"></td>									
										<td id="var_tab_autre" valign="top">
										</td>
									</tr>
								</table>						
						</div>
				</div>
													
				<div class="panneau-masque" id="panneau-newperimetre">page Nouveau périmètre</div>
				<div class="panneau-masque" id="panneau-source">page sources</div>
				<div class="panneau-masque" id="panneau-contacts"><?php include("contacts_administration_site.html"); ?></div>

			<!--/div-->
		</section>
	</section>
	<footer class="cadre-bandeau-bas">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:70%;">
			<tr>
				<td><H4>version 15-12-2016</H4></td>
				<td align="center"><H4>COPIL SIG DRAAF HN - DREAL HN - DDTM27 - DDTM76 (2012-2014)</H4></td>
				<td align="right" onClick="ouvre_fenetre('contacts.php')" title="Cliquez ici pour avoir les contacts"><H4>Equipe conception WEB</H4></td>
			</tr>
		</table>
	</footer>
</div>
</body>

</html> 