var larg ;
var haut ;

var anc_onglet_01 = 'Internet';
var anc_onglet_02 = 'accueil';
var anc_onglet_03 = 'info-legende';
	
var tab_parametre ;
var ref_tableau = {} ; // voir appels_php.js
var hauteur_tableau ;

// paramètres de la table pour construire la carte
var schema_perimetre; // schema de la table 
var table_perimetre;

var nom_commune; // référence de la commune sélectionnée
var insee_commune;

var compare_insee_commune // référence de la commune de comparaison
var compare_nom_commune
var image_perimetre;
var extent;
var url;

ref_tableau.id_fic = '';
ref_tableau.annee='';
ref_tableau.schema = '';
ref_tableau.perimetre = '';
ref_tableau.nom_perimetre='';
ref_tableau.insee = '';
ref_tableau.commune = '';
ref_tableau.type='';
ref_tableau.filter = '';
ref_tableau.flagcontour='';

flag_contour='';

//bloc_lib = ["","Région","Départements","Canton","SCOT","Autre","Personnalisé","Test"];
// permutation modele map
//var permute_map='<input type=button/>';

function initialisation(){
	var page_accueil = "Cabourg.gif";
	ref_tableau.id_fic = '';
	ref_tableau.annee='';
	ref_tableau.schema = '';
	ref_tableau.perimetre = '';
	ref_tableau.nom_perimetre='';
	ref_tableau.insee = '';
	ref_tableau.commune = '';
	ref_tableau.type='';
	ref_tableau.filter = '';
	
	onglet_01('accueil_gauche') ;
	onglet_02('accueil') ;
	onglet_03('info-legende') ;
	
	$('#id_perimetre').hide();	
	$('#commune').hide();	
	$('#id_commune').hide();	
	$('#panneau-mytableaux').hide();
	$('#compare_commune').hide();
	
	//$('#Evolution').html('');
	$('#Evolution').hide();
	$('#Surfaces').hide();
	//$('#change_contour_carte').hide();
	$('#map').html('');
	$('#Intranet').hide();
	$('#Internet').hide();
	$('#graphiques').hide(); // développement futur 
	
	$('#mytableaux').hide();
	$('#info-legende').hide();
	
	$('#comparaisons').hide(); // masque l'onglet évolution de la commune 
	// initialisation affichage WEB
	$('#accueil_gauche').show();
	//$("#versGeoide").attr("src", page_accueil);
	$('#accueil').show();
	$('#id_reload').hide();
}
