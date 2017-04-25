	// variables globales
	var liste_annee; // sera construite à partir du contenu de tab_parametre
	var annee_selectionnee ;
	var table_perimetre ;
	var schema_perimetre ;

	var x_pos=0;
	var y_pos=0;
	var extent="";
	var larg_image_web=800;
	var larg_image_mapserver = 800;
	var base_image = new Image();

	var canvas = document.getElementById('carte');
	var context = canvas.getContext('2d');
	var imageData;
	
	// fonctions remplissage des DIVs 
	function mes_perimetres() {
		// menu déroulant PERIMETRE
		$('#commune').hide();
		var x = document.getElementById("annee").selectedIndex;
		var y = document.getElementById("annee").options;
		
		annee_selectionnee = y[x].value;
		
		var perimetres_millesime_selectionne = tab_parametre[annee_selectionnee];
		var liste_nom_perimetre = '<option value="" selected>Choisir un périmètre</option>'+"\n";
		for (p in perimetres_millesime_selectionne) { // p = id du périmètre
			liste_nom_perimetre += '<option value="'+ p+'">'+perimetres_millesime_selectionne[p][1] +'</option>'+"\n";
			}
		$('#perimetre').html(liste_nom_perimetre);
		}

	function mes_communes() {
		// menu déroulant LISTE DES COMMUNES
		var z = document.getElementById("perimetre").selectedIndex;
		ma_carte_perimetre() ;
		if (z>0) {
			$('#commune').load('liste_communes.php?nom_schema_perim='+schema_perimetre+'&nom_table_perim='+table_perimetre,'');
			$('#commune').show() ;
			}
		
		}	
		
	function init() {
		if (base_image.height*canvas.width<base_image.width*canvas.height) {
			l=$('#id_carte').width();
			h=Math.round(base_image.height * l/base_image.width);
			} else {
			h=$('#id_carte').height();
			l=Math.round(base_image.width * h/base_image.height);
		}
		larg_image_web = l;
		$('#carte').attr('width',$('#id_carte').width());
		$('#carte').attr('height',$('#id_carte').height());
		context.drawImage(base_image,0,0,l,h);
		imageData = context.getImageData(0, 0,l,h);
		x_pos = 0;
		y_pos = 0;
		document.getElementById("val_x").value='';
		document.getElementById("val_y").value='';
		document.getElementById("occupation").value='';
	}

	function make_base(url) {
		var a1=url.indexOf("tmp/");
		url = url.substring(a1); // élimine les caractères parasites devant tmp/
		a1=url.indexOf(repertoire_image_perimetre);//"<?php echo $rep_images ?>/");
		url = url.substring(a1); // élimine les caractères parasites devant tmp/
		base_image.src = url;
		$.get(url, function(data) {
			init();
			});
	}

	function ma_carte_perimetre(){
		var perimetres_millesime_selectionne = tab_parametre[annee_selectionnee];
		var z = document.getElementById("perimetre").selectedIndex;
		var w = document.getElementById("perimetre").options;
		var p; // identifiant du perimetre_selectionné
		if (z>0) {
			p = w[z].value;
			schema_perimetre = perimetres_millesime_selectionne[p][2];
			table_perimetre = perimetres_millesime_selectionne[p][3];
			image_perimetre = perimetres_millesime_selectionne[p][5];
			extent = perimetres_millesime_selectionne[p][6];
			url = repertoire_image_perimetre + image_perimetre;
			make_base(url);
			}
	}
	function ma_carte() {
		var x = document.getElementById("commune").selectedIndex;
		var y = document.getElementById("commune").options;
		var ratio =larg_image_mapserver/larg_image_web;
		x_pos =  x_pos*ratio;
		y_pos =  y_pos*ratio;
		document.getElementById("val_x").value=x_pos;
		document.getElementById("val_y").value=y_pos;
		var mon_zoom = document.getElementById("mon_zoom").value;
		commune_selectionnee ="";
		mon_url="";

		if (x>0) {
			commune_selectionnee = y[x].value;
			}

		mon_url='url_image.php?table='+schema_perimetre+'.'+ table_perimetre +'&image_perimetre='+image_perimetre +'&extent='+extent;
		mon_url=mon_url+'&mon_zoom='+mon_zoom;
		mon_url=mon_url+'&mapa_x='+x_pos+'&mapa_y='+y_pos ;
		mon_url=mon_url+'&INSEE='+commune_selectionnee ;
		$.get(mon_url, function(data) {
			data= data.split("|");
			url = data[0];
			extent = data[1];
			make_base(url);
			});
	}
		
	function clicked(e){
		e.preventDefault();
		x_pos = e.clientX;
		y_pos = e.clientY;
		document.getElementById("val_x").value=x_pos;
		document.getElementById("val_y").value=y_pos;
		ma_carte();	
	} 

	function m_over(e){
		e.preventDefault();
		document.getElementById("val_x").value=e.clientX;
		document.getElementById("val_y").value=e.clientY;
		getPixelXY(e.clientX, e.clientY);
	} 
		

	function getPixelXY(x, y) {
		var data= imageData.data;
		var red = data[((l * y) + x) * 4];
		var green = data[((l * y) + x) * 4 + 1];
		var blue = data[((l * y) + x) * 4 + 2];
		var color = 'rgb(' + red + ',' + green + ',' + blue + ')';
		var c = red + ' ' + green + ' ' + blue;
		if (tab_color[c]==undefined) {//
			document.getElementById("occupation").value = "";
			document.getElementById("occupation").style.backgroundColor="";
			} else {
			document.getElementById("occupation").value = tab_color[c];
			document.getElementById("occupation").style.backgroundColor=color;
			}
	}

	// menu déroulant LISTE DES MILLESIMES 
	for (i in tab_parametre) {
		liste_annee += '<option value="'+ i+'">'+i +'</option>'+"\n";
		}

	$('#annee').html(liste_annee);		
	mes_perimetres();
	mes_communes();

	var Macarte = document.getElementById("carte");
	Macarte.addEventListener("mousedown", clicked, false);
	Macarte.addEventListener("mousemove", m_over, false);

