function affiche_table(liste_select,larg_table, nom_table,condition,mondiv,nom_fonction_php) { 
/*  	
	liste_select : nom des champs à récupérer
	larg_table : largeur souhaitée pour chaque champ en %
	nom_table
	condition : condition après le where
	
	mondiv est de la forme "#montableau"
 
    valeur possible de nom_fonction_php :
	affiche_table : affiche le HTML à inserer entre les balises <table> et </table>
	affiche_select : affiche le HTML à inserer entre les balises <select> et </select>
*/

	ref_tableau.liste_select = liste_select;
	ref_tableau.larg_table = larg_table;
	ref_tableau.nom_table = nom_table;
	ref_tableau.condition = condition;
	ref_tableau.type=nom_fonction_php;

	mes_colonnes=[];
	mes_colonnes = liste_select.split(",");
	ma_liste_colonne = [];
	for (i=0;i<mes_colonnes.length; i++) {
		ma_liste_colonne.push({label:ma_liste_colonne[i]});
	}
	
	$.get("innerHTML.php",ref_tableau,function(data){
			$(mondiv).empty();
			$(mondiv).append(data);
			if (ref_tableau.type='affiche_table') {
				$("#"+nom_table).tableutils( {
					fixHeader: { width: "100%" ,height: hauteur_tableau}, 				 
					filter: true, 
					columns: ma_liste_colonne				
				} );
			};
	});
} ;
