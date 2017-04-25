<?php
	require_once("connexion.php");
	$oscom=new oscom;
	$mafonction=$_GET['type'];
	
	// paramètres pour obtenir le contenu HTML à l'intérieur d'une balise <table> ou <select>
	$liste_select = $_GET['liste_select'];
	$larg_table = $_GET['larg_table'];
	$nom_table = $_GET['nom_table'];
	$condition = $_GET['condition'];

	
	// paramètres pour initialiser le fichier map - cree_ficmap
	$id_fic = $_GET['id_fic']; 			// 	préfixe à donner au fichier
	$schema = $_GET['schema']; 			/* 	schéma du fichier carte
											peut être différent du schéma dans param.ini 
											qui indique le schéma de la table des paramètres
										*/
	$perimetre = $_GET['perimetre']; 	// 	table périmètre selectionnée
	$insee = $_GET['insee'];			/*	code insee de la commune dans la table périmètre à prendre
											si non nul sert aussi de préfixe en priorité de id_fic
											si nul, tout le périmètre est retenu
										*/
	$filter = $_GET['filter'];			//  filtrage par FILTER dans le ficher MAP (optionnel non testé)
	
	$flag_contour = $_GET['flagcontour']; // si vide, on laisse les contours des zones
	
	// fonctions d'appel --------------------------------
	
	
	if ($mafonction=='affiche_table') { // affiche le HTML à inserer entre les balises <table> et </table>
		echo $oscom->affiche_table($liste_select,$larg_table,$nom_table,$condition);
	} ;
	
	if ($mafonction=='affiche_select') {// affiche le HTML à inserer entre les balises <select> et </select>
		echo $oscom->affiche_select($liste_select,$nom_table,$condition);
	} ;

	if ($mafonction=='cree_ficmap') {// création du fichier map
		echo $oscom->cree_ficmap($id_fic, $schema, $perimetre, $insee, $filter,$flag_contour);
		//echo $oscom->cree_ficmap("500", "oscom", "oscom_perso2009_0", "", "");
		//echo $oscom->cree_ficmap("1000", "oscom", "oscom_perso2009_0", "76095", "");
	} ;
	
	if ($mafonction=='get_param') {// Retourne la table des périmètres dans une variable dictionnaire
		echo $oscom->get_param();
	} ;
	
?>

