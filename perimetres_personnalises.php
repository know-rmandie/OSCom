<?php
	require_once("connexion.php");
	$oscom=new oscom; 
	
	$nom_schema=$oscom->get_value('schema_user');
	$schema_sources=$oscom->get_value('schema_sources');
	
	$table_communes_region = $schema_sources . '.' . $oscom->get_nomtable('table_communes');
	$table_scots_region = $schema_sources . '.' . $oscom->get_nomtable('table_scots');
	$table_epci_region = $schema_sources . '.' . $oscom->get_nomtable('table_epci');
	$table_perimetres=$nom_schema . '.' . $oscom->get_value('perimetres');


	$nom_champ_geom_table_communes = $oscom->get_value('nom_champ_geom_table_communes');
	$nom_champ_geom_table_scots = $oscom->get_value('nom_champ_geom_table_scots');
	$nom_champ_geom_table_epci = $oscom->get_value('nom_champ_geom_table_epci');
	
	$nom_champ_libelle_table_communes = $oscom->get_value('nom_champ_libelle_table_communes');
	$nom_champ_libelle_table_scots = $oscom->get_value('nom_champ_libelle_table_scots');
	$nom_champ_libelle_table_epci = $oscom->get_value('nom_champ_libelle_table_epci');
	
	$id_table_communes = $oscom->get_value('id_table_communes'); // code insee
	$id_table_scots = $oscom->get_value('id_table_scots'); // code scot
	$id_table_epci = $oscom->get_value('id_table_epci'); // code epci
	
//------ Préparation menu déroulant liste des communes
		$select_perim_communes='SELECT nom, code_insee  FROM '.$table_communes_region.' order by nom;';
		$result = $oscom->get_requete($select_perim_communes);
		$form = '<option value="">choisir une ou plusieurs communes</option>';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			$form .= '<option value="' . $row["code_insee"] .'" >' . $row["nom"].' - '. $row["code_insee"] .'</option>';
			}

//------ Préparation menu déroulant liste des scots
		$select_scot='SELECT '.$id_table_scots.' as id_scot, '. $nom_champ_libelle_table_scots .' as nom_scot FROM '.$table_scots_region.' order by nom_scot;';
		$result = $oscom->get_requete($select_scot) ;
		$formscot = '<option value="">choisir un SCOT</option>';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			$formscot .= '<option value="' . $row["id_scot"] .'" >' . $row["nom_scot"] .'</option>';
			}

//------ Préparation menu déroulant liste des epci
		$select_epci='SELECT id_epci,nom_epci  FROM '. $table_epci_region.' order by nom_epci;';
		$result = $oscom->get_requete($select_epci) ;
		$formepci = '<option value="">choisir un EPCI</option>';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			$formepci .= '<option value="' . $row["id_epci"] .'" >' . $row["nom_epci"] .'</option>';
			}
			
//------ Préparation menu déroulant liste des cantons
		$select_canton='SELECT Distinct canton, depart FROM '.$table_communes_region.' Order BY depart,canton;';
		$result = $oscom->get_requete($select_canton) ;
		$formcanton = '<option value="" selected>choisir un canton</option>';
		//$selection ='selected';
		$bloc ='';
		$optgroup = '';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			if($bloc <> $row["depart"]) { 
				$bloc = $row["depart"] ;
				$formcanton .= $optgroup. '<optgroup label="'.$bloc.'">';
				$optgroup = '</optgroup>' ;
				}
			$formcanton .= '<option value="' . $row["depart"] .','.$row["canton"].'" ' . $selection .'>' . $row["canton"].'</option>';
			$selection ='';
			}
		$formcanton .= $optgroup ;
		
//------- Préparation liste des millésimes
		$sql_millesime = "select millesime from ". $table_perimetres . " group by millesime order by millesime desc" ;
		$result = $oscom->get_requete($sql_millesime) ;
		$cptmillesime=0;
		$millesime = '<option value="">Tous les millésimes</option>';
		while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
			$millesime .= '<option value="' . $row["millesime"] .'" >' . $row["millesime"] .'</option>';
			$cptmillesime = $cptmillesime+1;
			}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Document sans titre</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="js/jquery-1.12.0.min.js" charset="utf-8"></script> 
<script language="JavaScript">

function comscot() {
    var Objhttp=new XMLHttpRequest();
	/*
	var x = document.getElementById("scot").selectedIndex;
	var y = document.getElementById("scot").options;
	var SQL = "liste_perso.php?scot=" + y[x].value ;
	var nom_scot =  y[x].value ;
	*/
	var SQL = "liste_perso.php?scot=" +$("#scot").val()
	var nom_scot = $("#scot option:selected").text(); 
	var maliste = document.getElementById("liste");
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		maliste.value=Objhttp.responseText;
			ajust();
			nom_scot = nom_scot.replace("SCOT DU ", ""); 			
			nom_scot = nom_scot.replace("SCOT DE LA ", "");
			nom_scot = nom_scot.replace("SCOT LE ", "");
			nom_scot = nom_scot.replace("SCOT ", "");
			//document.getElementById("nomlistelong").value = nom_scot.toLowerCase();
			document.getElementById("nomlistelong").value = nom_scot ;//nom_scot.substr(0,1).toUpperCase()+	nom_scot.substr(1,nom_scot.length).toLowerCase()
			document.getElementById("nomliste").value = 'SCOT';
    		}
  	}
	maliste.value = 'Recherche des perim_communes du '+nom_scot.replace(/(^\s*)|(\s*$)/g,"");
	//alert("SQL SCOT : " + SQL);
	Objhttp.open("GET",SQL,true);
	Objhttp.send();
}

function comepci() {
    var Objhttp=new XMLHttpRequest();
	/*
	var x = document.getElementById("epci").selectedIndex;
	var y = document.getElementById("epci").options;
	var SQL = "liste_perso.php?epci=" + y[x].value ;
	var nom_epci =  y[x].value ;
	*/
	var SQL = "liste_perso.php?epci=" +$("#epci").val()
	var nom_epci = $("#epci option:selected").text(); 
	//alert(SQL);
	var maliste = document.getElementById("liste");
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		maliste.value=Objhttp.responseText;
			ajust();
			document.getElementById("nomlistelong").value = nom_epci ; // nom_epci.substr(0,1).toUpperCase()+	nom_epci.substr(1,nom_epci.length).toLowerCase()
			document.getElementById("nomliste").value = 'EPCI';
    		}
  	}
	maliste.value = 'Recherche des perim_communes du '+nom_epci.replace(/(^\s*)|(\s*$)/g,"");
	
	/*
	alert(SQL);
	alert($("#epci").val() + ' = ' + $("#epci option:selected").text());
	*/
	Objhttp.open("GET",SQL,true);
	Objhttp.send();
}
function comcanton() {
    var Objhttp=new XMLHttpRequest();
	var x = document.getElementById("canton").selectedIndex;
	var y = document.getElementById("canton").options;
	var nom_deptcanton =  y[x].value.split(",");
	var nom_dept = nom_deptcanton[0].replace(/(^\s*)|(\s*$)/g,"");
	var nom_canton = nom_deptcanton[1].replace(/(^\s*)|(\s*$)/g,"");
	var SQL = "liste_perso.php?canton=" + nom_canton + "&depart="+nom_dept ;
	var maliste = document.getElementById("liste");
	Objhttp.onreadystatechange=function()	{
 		 if (Objhttp.readyState==4 && (Objhttp.status==200|| Objhttp.status == 0))
    		{
    		maliste.value=Objhttp.responseText;
			ajust();
			document.getElementById("nomlistelong").value = nom_canton.substr(0,1).toUpperCase()+ nom_canton.substr(1,nom_canton.length).toLowerCase()
			document.getElementById("nomliste").value = 'CANTON';
    		}
  	}
	maliste.value = 'Recherche des perim_communes du canton de '+nom_canton + '('+ nom_dept +')';
	//alert(SQL);
	Objhttp.open("GET",SQL,true);
	Objhttp.send();
}

function majliste() {
	var SelBranch = document.getElementById("perim_commune");
	var options = new Array();
	var maliste = document.getElementById("liste");
	var SelBranchVal = "";
    var x = 0;
	var virgule='';
	maliste.value='';

	for (x=0;x<SelBranch.options.length;x++){
		if (SelBranch.options[x].selected) {
			SelBranchVal = SelBranchVal + virgule + SelBranch.options[x].value;
			virgule =',';
			}
		}
	//alert(SelBranchVal);
	maliste.value = SelBranchVal;
}

function ajust(){
	var maliste = document.getElementById("liste");
    var tab = maliste.value.split(",");
	var a = 0;
    var x = 0;
	var chaine ='';
	var SelBranch = document.getElementById("perim_commune");
	var options = new Array();

	for (x=0;x<SelBranch.options.length;x++){SelBranch.options[x].selected = false;}
	for (a=0;a<tab.length;a++){
		chaine=tab[a];
		chaine=chaine.replace(/ /g,"");
		for (x=0;x<SelBranch.options.length;x++){
			if (SelBranch.options[x].value==chaine) { //chaine.replace(/ /g,"");
				SelBranch.options[x].selected = true;
				x= SelBranch.options.length;
			}
		}
	}
	majliste();
}

function envoi() {
	var message ="";
	var flag = 1;
	var go_perimetre = {};
	ajust();
	if (document.getElementById("liste").value=="") {
		flag=0;
		message = "Il faut choisir une ou plusieurs perim_communes\n";
		}
	if (document.getElementById("nomliste").value=="") {
		flag=0;
		message = message + "Il faut choisir un nom court pour la liste";
		}
	if (document.getElementById("nomlistelong").value=="") {
		flag=0;
		message = message + "Il faut choisir un nom long pour la liste";
		}	
	if (flag==1){
		document.getElementById("creation_perimetre" ).disabled = true ; 
		document.getElementById("reinitialisation" ).disabled = true ;
		$('#id_reload').hide();
		document.getElementById("creation_perimetre" ).value="creation périmetre en cours";
		go_perimetre.commune=$('#liste').val();
		go_perimetre.millesime=$('#millesime').val();
		go_perimetre.nomliste=$('#nomliste').val();
		go_perimetre.nomlistelong=$('#nomlistelong').val();
		$('#message').html("Début de création du périmètre</br>Patientez svp..");
		$.get('creer_perimetre.php',go_perimetre, function(data) {
			if (data=='') {
				$('#message').html('la création a échouée.  Recommencez ou contactez votre administrateur si le problème persiste');
				}
				else{
				$('#message').html(data);
				document.getElementById("reinitialisation" ).disabled = false ;
				$('#id_reload').show();
				}
			document.getElementById("creation_perimetre" ).disabled = false ; 
			document.getElementById("creation_perimetre" ).value="Créer le nouveau périmètre";
			$('#liste').val('');
			$('#nomliste').val('');
			$('#nomlistelong').val('');
			ajust();
		});	
		}else{
		alert(message);
		};
}
</script>
</head>
<body>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="6" bgcolor="#eeeeee">
    <tr align="center" valign="middle"> 
      <td rowspan="6" width="35%">
	    <select name="millesime[]" size="<?php echo $cptmillesime +1?>" id="millesime" multiple title="choisir un ou plusieurs millésimes (par défaut tous les millésimes sont sélectionnés)" style="width:100%" >
			<?php echo $millesime;?></select>
			<br>
        <select name="perim_commune[]" id="perim_commune" size="10" multiple onClick="majliste()" style="width:100%" title="Vous pouvez sélectionner ou désélectionner une ou plusieurs perim_communes (ctrl + click)">
			<?php echo $form ; ?>
		</select></td>
      <td align="left" valign="middle" width="10%" > nom court :</td>
	  <td align="left" valign="middle" width="55%"> 
        <input name="nomliste" type="text" id="nomliste" size="6" maxlength="10" title="Libéllé court 5 car" style="width:25%">
	  </td>
    </tr>
	<tr>
	  <td align="left" valign="middle" width="10%" title="ce libéllé sera visible dans la liste des périmètres"> nom long :</td>
	  <td align="left" valign="middle" width="55%"> 
        <input name="nomlistelong" type="text" id="nomlistelong" size="20" maxlength="30" title="Libéllé long 15 car (titre dans le menu déroulant 'périmètres')" style="width:75%">
	  </td>
    </tr>
    <tr> 
      <td align="left" valign="middle" width="10%"><p>Votre liste</p>
		 <input id="ajuster" name="Ajuster" type="button" onclick="ajust()" value="Ajuster" title="Cliquez ici pour validez vos modifications (vérification et ajustement avec la liste des perim_communes ci-contre" >
	  </td>
	  <td align="left" valign="middle" width="55%">  
        <textarea name="liste" id="liste" style="width:100%;" onChange="ajust()" title="Dans ce champ, vous pouvez ajouter ou supprimer directement des numéros insee de communes en les séparant par une virgule" >
		</textarea>
	  </td>
    </tr>
	<tr>
	  <td colspan=2><select name="canton" id="canton" title="choisir un CANTON" style="width:100%" onChange="comcanton()" >
			<?php echo $formcanton;?></select></td>
    </tr>
	<tr>
	  <td colspan=2><select name="epci" id="epci" title="choisir un EPCI" style="width:100%" onChange="comepci()" >
			<?php echo $formepci;?></select></td>
	</tr>
	<tr>
	  <td colspan=2><select name="scot" id="scot" title="choisir un SCOT" style="width:100%" onChange="comscot()" >
			<?php echo $formscot;?></select></td>
	</tr>
	 <tr> 
	 <td align="center" valign="middle"  id='message'></td>
     <td align="left" valign="middle" colspan="2"> 
        <input id="creation_perimetre" name="Input" type="button" value="Créer le nouveau périmètre" onclick="envoi();" title="Cliquez ici pour créer votre périmètre quand votre liste sera complète">
		<input id="reinitialisation" name="Input" type="button" value="Actualiser les périmètres" onclick="document.location.reload();" title="Cliquez ici pour mettre à jour le menu Périmètre" disabled>
		</td>
	<tr>
		</tr>
  </table>
</body>
</html>
