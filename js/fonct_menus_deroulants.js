// fonctions remplissage des DIVs 
function mes_perimetres() {
	// menu déroulant PERIMETRE
	$('#commune').hide();
	var x = document.getElementById("annee").selectedIndex;
	var y = document.getElementById("annee").options;
	
	//$('#perimetre').hide();
	$('#id_perimetre').hide();
	
	ref_tableau.id_fic = '';
	ref_tableau.annee='';
	ref_tableau.schema = '';
	ref_tableau.perimetre = '';
	ref_tableau.var_schema = '';
	ref_tableau.var_perimetre = '';
	ref_tableau.nom_perimetre='';
	ref_tableau.insee = '';
	ref_tableau.commune = '';
	ref_tableau.type='';
	ref_tableau.filter = '';
	// On ne ré-initialise pas ref_tableau.flagcontour car l'utilisateur peut conserver son choix sur toute la session
	initialisation() ;
	if (x>=0) {
		annee_selectionnee = y[x].value;
		var bloc="";
		var perimetres_millesime_selectionne = tab_parametre[annee_selectionnee];
		var liste_nom_perimetre = '<option value="" selected>Choisir un périmètre</option>'+"\n";
		/*
		var bloc="";
		var optgroup="";
		for (p in perimetres_millesime_selectionne) { // p = id du périmètre
			
			if (bloc!=perimetres_millesime_selectionne[p][0]){
				bloc = perimetres_millesime_selectionne[p][0];
				//alert("ici " + bloc_lib[bloc])
				liste_nom_perimetre += optgroup + '<optgroup label="'+ bloc_lib[bloc]+'">';
				optgroup = '</optgroup>' ;
				}
			liste_nom_perimetre += '<option value="'+ p+'">'+perimetres_millesime_selectionne[p][1] +'</option>'+"\n";
			}
		liste_nom_perimetre += optgroup ;
		*/
		var mes_options=[];
		for (i = 0; i < bloc_lib.length; i++) {
			mes_options[i]="";
		}
		var indice_bloc=0;
		for (p in perimetres_millesime_selectionne) { // p = id du périmètre
			indice_bloc = perimetres_millesime_selectionne[p][0];
			mes_options[indice_bloc]+= '<option value="'+ p+'">'+perimetres_millesime_selectionne[p][1] +'</option>'+"\n";
		}
		
		for (i = 0; i < bloc_lib.length; i++) {
			if (mes_options[i]!="") {
				liste_nom_perimetre += '<optgroup label="'+ bloc_lib[i]+'">';
				liste_nom_perimetre +=mes_options[i];
				liste_nom_perimetre +='</optgroup>';			
			}
		}	
		$('#perimetre').html(liste_nom_perimetre);
		//$('#perimetre').show();
		
		ref_tableau.annee = annee_selectionnee;
		
		$('#id_perimetre').show();
		$.get('sources/sources_'+annee_selectionnee+'.html', function(data) {
		  $('#panneau-source').html(data);
		});
		$.get('perimetres_personnalises.php', function(data) {
		  $('#panneau-newperimetre').html(data);
		});
		$.get('metadonnees.php?millesime='+annee_selectionnee, function(data) {
			$('#panneau-metadonnees').html(data);
		});	
	}
}
	
function mes_communes() {
// menu déroulant LISTE DES COMMUNES
	var z = document.getElementById("perimetre").selectedIndex;
	var monfiltre=ref_tableau.filter;
	ref_tableau ={};
	ref_tableau.filter=monfiltre;
	
	onglet_03('info-legende');
	//$('#info-legende').show();
	//$('#comparaisons').hide();
	
	ma_carte_perimetre() ;
	//alert('liste_communes.php?nom_schema_perim='+schema_perimetre+'&nom_table_perim='+table_perimetre);
	if (z>0) {
		// met à jour les menus déroulant liste de communes
		$('#commune').load('liste_communes.php?nom_schema_perim='+schema_perimetre+'&nom_table_perim='+table_perimetre,'');
		$('#commune').show() ;
		$('#id_commune').show();
		$('#compare_commune').load('liste_communes.php?nom_schema_perim='+schema_perimetre+'&nom_table_perim='+table_perimetre,'');
		}
}

function ma_carte_perimetre(){
	var perimetres_millesime_selectionne = tab_parametre[annee_selectionnee];
	var z = document.getElementById("perimetre").selectedIndex;
	var w = document.getElementById("perimetre").options;
	var x = document.getElementById("annee").selectedIndex;
	var y = document.getElementById("annee").options;
	var p; // identifiant du perimetre_selectionné
	
	//alert (url_geoide_internet);
	if (z>0) {
		p = w[z].value;
		code_bloc = perimetres_millesime_selectionne[p][0];
		libelle_perimetre = perimetres_millesime_selectionne[p][1];
		schema_perimetre = perimetres_millesime_selectionne[p][2];
		table_perimetre = perimetres_millesime_selectionne[p][3];
		image_perimetre = perimetres_millesime_selectionne[p][5];
		extent = perimetres_millesime_selectionne[p][6].split(" ");
		//$("#versGeoide").attr("src", url_geoide_internet+'?extent='+extent[0]+','+extent[1]+','+extent[2]+','+extent[3]+'&REQUEST=GETLegendGraphic&STYLE=');
		//$("#map").html('<image src="'+rep_images+'/'+image_perimetre+'" name="versIMG" id="versIMG">');
		ref_tableau.nom_perimetre=perimetres_millesime_selectionne[p][1];
		$('#commune').prop('selectedIndex', 0);
		$('#Intranet').show();
		$('#Internet').show();
		$('#accueil_gauche').hide();
		$('#Intranet').html('<a>Périmètre '+libelle_perimetre+'</a>');
		ma_carte();
		onglet_02("mytableaux") ;	
		onglet_01("Internet") ;
		$('#accueil').hide();
		$('#mytableaux').show();	
		$('#panneau-mytableaux').show();
		
		$.get('affiche_legende.php', function(data) {
		  $('#panneau-info-legende').html(data);
		  $("#tabref").html(table_perimetre); 
		  $("#schref").html(schema_perimetre); 
		  $('#info-legende').show();
		  
		});
		$('#panneau-Evolution').html("");
		$('#Evolution').hide();
		if (code_bloc > 2) {
			// Affiche le tableau d'évolution des surfaces des communes sur le périmètre sélectionné
			// sauf pour la région et les départements (temps trop long)
			// mais le module variations.php le permet si on le souhaite
			annee_selectionnee = y[x].value;
			url_evolution='variations.php?millesime='+annee_selectionnee+'&id='+p+'&annee=&code=23';
			monText  = "<p>Calcul de l'évolution sur le périmètre "+libelle_perimetre +"<p>" ;
			monText += "<iframe name='versVariation' id='versVariation'></iframe>"
			$('#panneau-Evolution').html(monText);
			$("#versVariation").attr("src", url_evolution);
			$('#Evolution').show();
		}
		init_var_annee();
		evolution_perimetre();
		
		// oscomsurfdtl
		$("#oscomsurfdtl_schsrc").val(schema_perimetre);
		$("#oscomsurfdtl_tabsrc").val(table_perimetre);
		$("#oscomsurfdtl_nom").val(libelle_perimetre);
		$("#oscomsurfdtl_annee").val(ref_tableau.annee);
		$('#Surfaces').show();
	}
}

function ma_carte() {
	var x = document.getElementById("commune").selectedIndex;
	var y = document.getElementById("commune").options;
	commune_selectionnee ="";
	mon_map="";
		
	
	if (x>0) {
		commune_selectionnee = y[x].value;
		commune_selectionnee=commune_selectionnee.split("|");
		insee_commune=commune_selectionnee[0];
		nom_commune=commune_selectionnee[1];
		id_fic='';
		insee=insee_commune;
		$('#Intranet').html('<a>Commune de '+nom_commune+'</a>');
		} else {
		id_fic=table_perimetre;
		insee='';
		}
	
	ref_tableau.var_schema = '';
	ref_tableau.var_perimetre = '';
	
	ref_tableau.id_fic = id_fic;
	ref_tableau.schema = schema_perimetre;
	ref_tableau.perimetre = table_perimetre;
	ref_tableau.insee = insee;
	ref_tableau.commune = nom_commune;
	ref_tableau.type='cree_ficmap';
	ref_tableau.filter = '';
		
	$('#tab_commune').html('');
	$('#tab_autre').html('');
	$("#col_commune").html('');
	$("#info-point").html('');
	
	$("#info-point").hide();
	$('#compare_commune').hide();
	$('#tab_commune').hide();
	$('#tab_autre').hide();
	$("#col_commune").hide();
	
    creation_carte_intranet();
	
	// création du tableau de répartition des surfaces	
		
	$('#col_perimetre').width('100%');	
	$('#col_commune').width('0%');	
	$('#col_autre').width('0%');
	
	if (insee!=""){
		ref_tableau.nature_tab="tab_commune" ;
		ref_tableau.titre_tab="Commune : " +insee +' - '+ ref_tableau.commune;
		$("#col_commune").show();	
		$('#tab_commune').show();	 
		//$("#col_commune").html('<b>'+ref_tableau.titre_tab+'</b>');	
		//on passe les paramètres à tab_param_commune et affiche_nom_commune pour tenir compte des délais de traitement différent entre $.get et le reste du code javascript
		tab_commentaire = "Surface en ha";
		tab_param_commune ='schema=' + ref_tableau.schema + '&perimetre='+ref_tableau.perimetre+'&nature_tab='+ref_tableau.nature_tab+'&titre_tab='+ref_tableau.titre_tab+'&insee='+ref_tableau.insee+'&nom_commune='+ref_tableau.nom_commune+'&tab_commentaire='+tab_commentaire;	
		affiche_nom_commune=ref_tableau.titre_tab;
		$.get('affiche_tableaux.php',ref_tableau, function(data) {
			$("#col_commune").html('<b><a href="export_excel.php?'+tab_param_commune+'"><img src="excel.png" border="0" style="vertical-align:middle;height:2.5vh"></a>'+affiche_nom_commune+'</b>');
			$('#tab_commune').html(data);
			$('#tab_autre').show();	 	 
			$('#compare_commune').show();	
			$('#col_perimetre').width('30%');	
			$('#col_commune').width('30%');	
			$('#col_autre').width('30%');
			evolution_commune(insee_commune,'commune',nom_commune);
		});
	} 
	ref_tableau.insee = '';
	if (ref_tableau.nom_perimetre!=""){
		ref_tableau.nature_tab="tab_perimetre" ;
		ref_tableau.titre_tab="Périmètre : " + ref_tableau.nom_perimetre;
		$.get('affiche_tableaux.php',ref_tableau, function(data) {
			tab_commentaire = "Surface en ha";
			tab_param ='schema=' + ref_tableau.schema + '&perimetre='+ref_tableau.perimetre+'&nature_tab='+ref_tableau.nature_tab+'&titre_tab='+ref_tableau.titre_tab+'&tab_commentaire='+tab_commentaire;
			$("#col_perimetre").html('<b><a href="export_excel.php?'+tab_param+'"><img src="excel.png" border="0" style="vertical-align:middle;height:2.5vh"></a>'+ref_tableau.titre_tab+'</b>');
			$('#tab_perimetre').html(data);
			$('#info-tableau').show() ;
		});	
	
	}
}

function creation_carte_intranet() {
	// création du fichier map et affichage des cartes
	var url_geoide_internet =geoide.replace('aaaa',annee_selectionnee);
	var unicite = '&unicite='+Date();
	$.get("innerHTML.php",ref_tableau,function(data){
		data= data.split("|");
		mon_map = data[0];
		extent = data[1].split(" ");
		//alert("http://10.76.8.44/cgi-bin/mapserv?MAP="+ mon_map +"&LAYERS=oscom&mode=map"+ unicite);
		var mslayer = new OpenLayers.Layer.MapServer( "MapServer Layer",
			"http://"+ip_serv+"/cgi-bin/mapserv?MAP="+ mon_map +"&LAYERS=oscom&mode=map"+unicite, 
            {layers: 'oscom'},
            {singleTile: "true", ratio:1} 
			); 

		$("#map").empty();
		document.getElementById("map").style.width  = ($("#cadre-interne-gauche").width() -100)+ "px";
		document.getElementById("map").style.height = ($("#cadre-interne-gauche").height()-100)+ "px";
		var map = new OpenLayers.Map('map',
			 {maxExtent: new OpenLayers.Bounds(extent[0], extent[1], extent[2], extent[3] ), 
			  maxResolution: 75} );	
        map.addLayer(mslayer);
        map.zoomToMaxExtent();
		map.addControl(new OpenLayers.Control.MousePosition());
		
// ----------------- affichage GEOIDE ------------------
		$("#versGeoide").attr("src", url_geoide_internet+'?extent='+extent[0]+','+extent[1]+','+extent[2]+','+extent[3]+'&REQUEST=GETLegendGraphic&STYLE=');
		$('#Internet').html('<a>Carte GEO-IDE</a>');
		$('#adresse_web').html('<a href="'+url_geoide_internet+'" target="_blank">'+url_geoide_internet+'</a>');
		//$("#versGeoide").attr("src", geoide+'?extent='+extent[0]+','+extent[1]+','+extent[2]+','+extent[3]+'&REQUEST=GETLegendGraphic&STYLE=');
		/*
		map.events.register("mousemove", map, function(e) {
			
			Coordonnées X Y écran :
			position = this.events.getMousePosition(e);
			OpenLayers.Util.getElement("coords").innerHTML = position;
			pos_x=OpenLayers.Util.getElement("coords").innerHTML.split(",")[0].split("=")[1];
			pos_y=OpenLayers.Util.getElement("coords").innerHTML.split(",")[1].split("=")[1];
			OpenLayers.Util.getElement("coords").innerHTML = position  +" - " +  pos_x  +" - " + pos_y; 
			ou en jscript :
			$("#coords").html(position  +" - " +  pos_x  +" - " + pos_y);
			-----------------
			Coordonnées X Y réelles :
			
			mypoint = map.getLonLatFromViewPortPx(e.xy);
			$("#coords").html(" X(E): " + mypoint.lon + " - Y(N): " + mypoint.lat) ;

		});
		*/
// -------------------GEstion de la souris ---------------------------------------
		map.events.register("mousedown", map, function(e) {
			mypoint = map.getLonLatFromViewPortPx(e.xy);
			$("#info-point").html("Point cliqué. Patientez svp, je cherche sur le serveur") ;
			var ref_point_click = {};
			ref_point_click.lon = mypoint.lon;
			ref_point_click.lat = mypoint.lat;
			ref_point_click.schemaref = ref_tableau.schema
			ref_point_click.tableref = ref_tableau.perimetre;
			//
			$.get('get_point_click.php',ref_point_click, function(data) {
				//$("#info-point").empty();
				$("#info-point").html(data);
				$("#info-point").show();
				//$("#info-point").html(" X(E): " + ref_point_click.lon + " - Y(N): " + ref_point_click.lat + " code : " + data) ; 
			});
			return true;
		},true); 
	});

}
function creation_tableau_comparaison(){
	var x = document.getElementById("compare_commune").selectedIndex;
	var y = document.getElementById("compare_commune").options;
	compare_commune_selectionnee ="";
	var ref_tab = {} ;
	if (x>0) {
		compare_commune_selectionnee = y[x].value;
		compare_commune_selectionnee=compare_commune_selectionnee.split("|");
		compare_insee_commune=compare_commune_selectionnee[0];
		compare_nom_commune=compare_commune_selectionnee[1];
		ref_tab.schema = ref_tableau.schema; 			
		ref_tab.perimetre = ref_tableau.perimetre; 	
		ref_tab.insee = compare_insee_commune;		
		ref_tab.nature_tab = 'tab_autre_comparaison';
		ref_tab.titre_tab = compare_nom_commune;
		tab_libelle='Commune : '+compare_insee_commune +' - '+ compare_nom_commune;
		tab_commentaire = "Surface en ha";
		tab_param_commune ='schema=' + ref_tab.schema + '&perimetre='+ref_tab.perimetre+'&nature_tab='+ref_tab.nature_tab+'&titre_tab='+tab_libelle+'&insee='+compare_insee_commune+'&nom_commune='+compare_nom_commune+'&tab_commentaire='+tab_commentaire;	
		$("#autre_excel").html('<a href="export_excel.php?'+tab_param_commune+'"><img src="excel.png" border="0" style="vertical-align:middle;height:2.5vh"></a>');
		$.get('affiche_tableaux.php',ref_tab, function(data) {
			$("#tab_autre").html(data);
			evolution_commune(compare_insee_commune,'autre',compare_nom_commune);
		});		
	}
}

function init_var_annee(){
	var perimetres_millesime_ = tab_parametre[annee_selectionnee]
	var liste_annee = '';
	var j=10000;
	ref_tableau.var_schema = ''; // ré-initialise les valeurs dans ref_tableau
	ref_tableau.var_perimetre = ''; // ré-initialise les valeurs dans ref_tableau
	
	ref_tableau.schema_tmp = '';
	ref_tableau.perimetre_tmp = '';
	
	$('#comparaisons').hide(); // masque l'onglet variation sur la commune
	$('#var_tableau').show() ;
	for (i in tab_parametre) {
		if (i!=annee_selectionnee){
			var perimetre_millesime_examen = tab_parametre[i] ;
			for (id_examen in perimetre_millesime_examen){
				if (perimetre_millesime_examen[id_examen][1]==ref_tableau.nom_perimetre) {
					ref_tableau.schema_tmp = perimetre_millesime_examen[id_examen][2];
					ref_tableau.perimetre_tmp = perimetre_millesime_examen[id_examen][3];
					liste_annee += '<option value="'+ ref_tableau.schema_tmp +'|'+ref_tableau.perimetre_tmp +'">'+i +'</option>'+"\n";
					if (j>i) {j=i};
				}
			}
		}
	}
	liste_annee = liste_annee.replace('>'+ j+'</option>', ' selected>'+ j+'</option>'); 
	if (liste_annee !=''){
		$('#var_annee').html(liste_annee);	
		$('#annee_base').html(annee_selectionnee);	
		$('#comparaisons').show(); // affiche l'onglet variation sur la commune
		$('#var_tableau').hide() ;
		
	}
}

function set_id_selection() {
	var x = document.getElementById("var_annee").selectedIndex;
	var y = document.getElementById("var_annee").options;
	var ref_perimetre_selectionne = '';
	
	if (x>=0) {
		ref_perimetre_selectionne = y[x].value;
		//alert(ref_perimetre_selectionne);
		my_tab_tmp =ref_perimetre_selectionne.split("|");
		ref_tableau.var_schema = ref_perimetre_selectionne.split("|")[0];
		ref_tableau.var_perimetre =ref_perimetre_selectionne.split("|")[1];
		//alert(ref_tableau.var_perimetre);
		}	
}

function evolution_perimetre() {
	// affiche le tableau de variation du périmètre par rapport à une année sélectionnée
	$('#var_tab_commune').html('');
	$('#var_tab_autre').html('');
	$("#var_col_commune").html('');
	$("#var_col_autre").html('');

	$('#var_tab_commune').hide();
	$('#var_tab_autre').hide();
	$("#var_col_commune").hide();
	$("#var_col_autre").hide();
	
	$('#var_col_perimetre').width($('#col_perimetre').width);	
	$('#var_col_commune').width($('#col_commune').width);	
	$('#var_col_autre').width($('#col_autre').width);
	
	$("#var_col_perimetre").html('<b>'+ref_tableau.titre_tab+'</b>');
	ref_tableau.var_schema = '';
	ref_tableau.var_perimetre = '';
	ref_tableau.insee = '';
	set_id_selection();
	mon_evol_perim_schema= ref_tableau.var_schema;
	mon_evol_perim_perimetre= ref_tableau.var_perimetre;
	$.get('affiche_tableaux.php',ref_tableau, function(data) {
		tab_commentaire="Surfaces en ha - variation entre les millésimes "+$("#var_annee option:selected").text() + ' - ' +$("#annee option:selected").text();
		tab_param ='schema=' + ref_tableau.schema + '&perimetre='+ref_tableau.perimetre+'&nature_tab='+ref_tableau.nature_tab+'&titre_tab='+ref_tableau.titre_tab+'&var_perimetre='+ref_tableau.var_perimetre+'&var_schema='+ref_tableau.var_schema+'&tab_commentaire='+tab_commentaire;
		$("#var_col_perimetre").html('<b><a href="export_excel.php?'+tab_param+'"><img src="excel.png" border="0" style="vertical-align:middle;height:2.5vh"></a>'+ref_tableau.titre_tab+'</b>');
		$('#var_tab_perimetre').html(data);
		$('#var_tableau').show() ;
		$('#info_tab_comparaison').html(tab_commentaire) ;
	});		
}

function evolution_commune(num_insee_commune,suffixe_tab_affichage,nom_commune_tab_affichage) {
	// affiche la comparaison sur les millésime pour la commune dans les champs du suffixe
	$('#var_col_perimetre').width($('#col_perimetre').width());	
	$('#var_col_commune').width($('#col_commune').width());	
	$('#var_col_autre').width($('#col_autre').width());
	set_id_selection();
	if (num_insee_commune>0) {
		//$("#var_col_"+suffixe_tab_affichage).html('<b>'+num_insee_commune + ' - ' + nom_commune_tab_affichage+'</b>');
		ref_tableau.insee = num_insee_commune;
		tab_libelle_commune = num_insee_commune + ' - ' + nom_commune_tab_affichage ;
		tab_commentaire="Surfaces en ha - variation entre les millésimes "+$("#var_annee option:selected").text() + ' - ' +$("#annee option:selected").text();
		
		tab_param_commune_var ='schema=' + ref_tableau.schema + '&perimetre='+ref_tableau.perimetre+'&nature_tab='+ref_tableau.nature_tab+'&titre_tab='+tab_libelle_commune+'&insee='+ref_tableau.insee+'&nom_commune='+ref_tableau.nom_commune+'&var_perimetre='+ref_tableau.var_perimetre+'&var_schema='+ref_tableau.var_schema+'&tab_commentaire='+tab_commentaire;	
		
		//affiche_nom_commune_var=ref_tableau.titre_tab;
		
		$("#var_col_"+suffixe_tab_affichage).html('<b><a href="export_excel.php?'+tab_param_commune_var+'"><img src="excel.png" border="0" style="vertical-align:middle;height:2.5vh"></a>'+tab_libelle_commune+'</b>');
		
		$.get('affiche_tableaux.php',ref_tableau, function(data) {
			 $('#var_tab_'+suffixe_tab_affichage).html(data);
			 $("#var_col_"+suffixe_tab_affichage).show();
			 $("#var_tab_"+suffixe_tab_affichage).show();
		});	
	}
}

function set_selection_annee() {
	evolution_perimetre();
	evolution_commune(insee_commune,'commune',nom_commune)
	evolution_commune(compare_insee_commune,'autre',compare_nom_commune)
}

function permute_map() {
	//var mode_encours='<p><img src="permute.gif" width="20vh" onclick="permute_map();" Title="Cliquez pour changer le mode d\'affichage"> Mode ';
	ref_tableau.flagcontour = (ref_tableau.flagcontour!='') ? '' : 'sans ';
	//mode_encours=mode_encours +  ref_tableau.flagcontour + 'contour</p>';
	//$("#change_contour_carte").html(mode_encours);
	//$('#change_contour').prop('title', 'Mode '+ref_tableau.flagcontour+'contour');
	$('#change_contour').val('Mode '+ref_tableau.flagcontour+'contour');
	$("#map").empty();
	//ma_carte();
	creation_carte_intranet();
}