var anc_onglet_01;
var anc_onglet_02;
var anc_onglet_03;

function onglet_01(nom_onglet) {
	anc_onglet_01=onglet(nom_onglet,anc_onglet_01) ;
	}
function onglet_02(nom_onglet) {
	anc_onglet_02=onglet(nom_onglet,anc_onglet_02);
	}
function onglet_03(nom_onglet) {
	anc_onglet_03=onglet(nom_onglet,anc_onglet_03);
	}		
function onglet(nom_onglet,anc_onglet) {
	new_onglet = (nom_onglet.id == null)? nom_onglet : nom_onglet.id;
	document.getElementById(anc_onglet).className = '';
	document.getElementById(new_onglet).className = 'active';
	document.getElementById('panneau-'+anc_onglet).className = 'panneau-masque';
	document.getElementById('panneau-'+new_onglet).className = 'panneau-actif';
	$("#info-point").hide();
	return new_onglet;
	}
