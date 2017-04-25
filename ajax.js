
function affiche(IdDiv,SQL) {
// fonction ajax d'affichage dans un div des résultats d'une requête
	var Objhttp=new XMLHttpRequest();
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		document.getElementById(IdDiv).innerHTML=Objhttp.responseText;
    		}
  	}
	Objhttp.open("GET",SQL,true);
	Objhttp.send();
}

function affiche_perim(IdDiv,SQL) {
// variante fonction ajax [affiche(IdDiv,SQL)] d'affichage dans un div des résultats d'une requête
// permet de synchroniser l'affichage de listecom
	var Objhttp=new XMLHttpRequest();
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		document.getElementById(IdDiv).innerHTML=Objhttp.responseText;
			listecom();
    		}
  	}
	Objhttp.open("GET",SQL,true);
	Objhttp.send();
}

function httpMainframe(SQL) {
// fonction ajax met à jour la page mainFrame avec l'url geoide après nettoyage des caractères parasites avant [http://]
	var Objhttp=new XMLHttpRequest();
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		url=Objhttp.responseText;
			v = url.indexOf('http://');
			document.getElementById("urlgeoide").value=url.substring(v);
			document.getElementById("urlgeoideregion").value=url.substring(v);
			parent.mainFrame.location.href=document.getElementById("urlgeoide").value;			
			document.getElementById("ide").value="Mode Internet";
			document.getElementById("ide").setAttribute('title', 'Cliquez pour passer en mode Intranet (affichage de la commmune seule)');
    		}
  	}
	Objhttp.open("GET",SQL,true);
	Objhttp.send();
}

function listeperim() {
var x = document.getElementById("millesime").selectedIndex;
var y = document.getElementById("millesime").options;

affiche_perim("perimetres","ajax_liste_perimetres.php?millesime=" + y[x].value);
httpMainframe("ajax_millesime.php?millesime=" + y[x].value);
}

function listecom() {
// on initialise la ligne d'entête sur un périmmètre (région, département, créa, ..)
// on affiche la table récapitulative au niveau du périmètre sélectionné
// affiche la liste des communes correspondant au périmètre
var x = document.getElementById("perimetres").selectedIndex;
x = (x==0)?x=1:x;
var y = document.getElementById("perimetres").options;
var schema_nom = y[x].value.split(',');
affiche("insee_comm","ajax_communes.php?perimetre=" + y[x].value); //insee_comm
affiche("myTab","ajax_liste_groupe.php?perimetre=" + y[x].value+"&lib_long="+ y[x].text);
affiche("myannee","ajax_bouton_annee.php?perimetre=" + y[x].value); 

// indique le nom de la table de référence et renseigne les variables cachées pour l'appel de la page locale
document.getElementById("tabref").innerHTML= schema_nom[0]+'.'+schema_nom[1] ;
document.getElementById("go_table").value= schema_nom[0]+'.'+schema_nom[1] ;
document.getElementById("go_perimetre").value=(schema_nom[5]=="CANTON")? "Canton de " + y[x].text : y[x].text ;
document.getElementById("go_extension").value=schema_nom[0]+'_'+schema_nom[1];
document.getElementById("go_insee_comm").value='';
document.getElementById("go_image_ref").value=schema_nom[3];
document.getElementById("go_nom_schema_ref").value=schema_nom[0];
document.getElementById("go_nom_table_ref").value=schema_nom[1];
document.getElementById("go_extent").value=schema_nom[4];
url= schema_nom[6];
v = url.indexOf('http://');
flag=0; // flag = 1 mettre à jour la page geoide
if (v >-1) {
	document.getElementById("urlgeoide").value=url.substring(v);
	flag=1;
	}else{
		if (document.getElementById("urlgeoide").value != document.getElementById("urlgeoideregion").value) {
			document.getElementById("urlgeoide").value = document.getElementById("urlgeoideregion").value;
			flag=1;
			}
	}
	
if ((document.getElementById("ide").value=="Mode Internet") && (flag==1)) {
	//parent.mainFrame.location.href=document.getElementById("urlgeoide").value;
	document.getElementById("appel_mainFrame").action = "ajax_extent.php";
		};
//met à jour la page Mapserver si on est en local 
if (document.getElementById("ide").value=="Mode Local") {
    document.getElementById("appel_mainFrame").action = "ajax_mapserver.php";
	}
go_mainFrame.submit();
}

function tabcom() {
// on affiche la table récapitulative de la commune sélectionnée 
var x = document.getElementById("perimetres").selectedIndex;
var y = document.getElementById("perimetres").options;
var z = document.getElementById("insee_comm").selectedIndex;
var w = document.getElementById("insee_comm").options;
affiche("myTab","ajax_liste_groupe.php?insee_comm=" + w[z].value+"&perimetre="+ y[x].value+"&lib_long="+ y[x].text);
affiche("myannee","ajax_bouton_annee.php?perimetre=" + y[x].value); 
// on renseigne les variables cachées pour l'appel de la page locale avec le couple codeinsee/nomcommune
document.getElementById("go_insee_comm").value= w[z].value ;//+ "," + w[z].text ;

//met à jour la page Mapserver si on est en local
if (document.getElementById("ide").value=="Mode Local") {
	document.getElementById("appel_mainFrame").action = "ajax_mapserver.php";
	//alert('ajax_mapserver.php');
	} else {
	document.getElementById("appel_mainFrame").action = "ajax_extent.php";
	//alert('ajax_extent.php');
	}
//document.getElementById("mestest").innerHTML=document.getElementById("appel_mainFrame").action;
go_mainFrame.submit();
}

function compare() {
// on affiche la table comparative entre millesime et annee 
var k = document.getElementById("annee").selectedIndex;
var t = document.getElementById("annee").options;
var x = document.getElementById("perimetres").selectedIndex;
var y = document.getElementById("perimetres").options;
var z = document.getElementById("insee_comm").selectedIndex;
var w = document.getElementById("insee_comm").options;
//affiche("myTab","ajax_evolution.php?insee_comm=" + w[z].value+"&perimetre="+ y[x].value+"&annee=" + annee+"&lib_court=" + lib_court+"&millesime=" + t[k].value);
affiche("myTab","ajax_evolution.php?insee_comm=" + w[z].value+"&perimetre="+ y[x].value+"&lib_long="+ y[x].text+"&annee=" + t[k].value);
//alert("ajax_evolution.php?insee_comm=" + w[z].value+"&perimetre="+ y[x].value+"&lib_long="+ y[x].text+"&annee=" + t[k].value);
}

function ide_change() {
	//alert(document.getElementById("ide").value);
    if (document.getElementById("ide").value=="Mode Internet") {
		document.getElementById("appel_mainFrame").action = "ajax_mapserver.php";
		go_mainFrame.submit();
		document.getElementById("ide").value="Mode Local";
		document.getElementById("ide").setAttribute('title', 'Cliquez pour passer en mode Internet avec Géo-Ide');
		}else{
		//parent.mainFrame.location.href=document.getElementById("urlgeoide").value;
		document.getElementById("appel_mainFrame").action = "ajax_extent.php";
		go_mainFrame.submit();
		document.getElementById("ide").value="Mode Internet";
		document.getElementById("ide").setAttribute('title', 'Cliquez pour passer en mode Intranet (affichage de la commmune seule)');
	}
}

function fond_change() {
	//alert(document.getElementById("ide").value);
    if (document.getElementById("fond").value=="ON") {
		document.getElementById("go_ficmap").value="modele1.map" ;
		document.getElementById("fond").value="OFF";
		document.getElementById("go_valfond").value="OFF";
		document.getElementById("go_titrefond").value="Cliquez pour passer en mode affichage des contours";
		document.getElementById("fond").setAttribute('title', 'Cliquez pour passer en mode affichage des contours');
		go_mapserver.submit();		
		}else{
		document.getElementById("go_ficmap").value="modele2.map" ;
		document.getElementById("fond").value="ON";
		document.getElementById("go_valfond").value="ON";
		document.getElementById("go_titrefond").value="Cliquez pour masquer les contours";
		document.getElementById("fond").setAttribute('title', 'Cliquez pour masquer les contours');
		go_mapserver.submit();
	}
}

function test() {
	//var x = parent.mainFrame.document.getElementById("combo0").selectedIndex;
	//var z = parent.mainFrame.document.getElementById("combo0").options;
	//var x = document.getElementById("myframe");
	var x = parent.bottomFrame.document ;
	var y = (x.contentWindow || x.contentDocument);
	if (y.document)y = y.document;
	y.body.style.backgroundColor="red";
	//alert("Index: " + y[x].index + " is " + y[x].text + " et value is " + y[x].value);
}

<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}

function init() {
document.body.style.fontSize = Math.min(window.innerHeight/13,window.innerWidth/14);
}

function ouvre_fenetre(lien) {
	w = window.open(lien,"fiche","menubar=no, status=no, scrollbars=yes, width=800, height=700");
	w.focus();
}

function ouvre_fen_millesime() {
	var k = document.getElementById("annee").selectedIndex;
	var t = document.getElementById("annee").options;
	var x = document.getElementById("millesime").selectedIndex;
	var y = document.getElementById("millesime").options;
    lien = 'sources_'+y[x].value+'.html';
	w = window.open(lien,"fiche","menubar=no, status=no, scrollbars=yes, width=800, height=700");
	w.focus();
}

function go(){
    var x = document.getElementById("perimetres").selectedIndex;
	var y = document.getElementById("perimetres").options;
	var z = document.getElementById("annee").selectedIndex;
	var w = document.getElementById("annee").options;
	var perim = y[x].value;
	perim = perim.split(",");
	id = perim[perim.length-1];
	if (w[z].value==""){
		alert("choississez un millésime SVP");
		} else {
		lien = "ajax_variations.php?id="+ id + "&annee="+ w[z].value + "&code=" ;
		ouvre_fenetre(lien);
	}
}
