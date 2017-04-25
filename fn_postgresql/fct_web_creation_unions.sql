-- Function: fct_web.creation_unions(text, text, text, text, text, text, text, text, text, text, text)

-- DROP FUNCTION fct_web.creation_unions(text, text, text, text, text, text, text, text, text, text, text);

CREATE OR REPLACE FUNCTION fct_web.creation_unions(prefix text, millesime text, schcom text, list_champ text, nomindsrc text, nomindres text, nomlibsrc text, nomlibres text, nomdepsrc text, nomdepres text, nomgeomres text)
  RETURNS boolean AS
$BODY$

-- FONCTION DE CREATION D'UNE TABLE REGIONALE COMMUNES, EPCI OU SCOT A PARTIR DE TABLES DEPARTEMENTALES
-- MAJ le 12/08/2016 - XL

-- DEFINITION DES PARAMETRES
-- prefix     : préfixe du nom des tables départementales sources à unir...
-- millesime  : millesime à considérer pour la fusion...
-- schcom     : nom du schéma où se trouvent les couches départementales sources à unir
-- list_champ : liste des champs des tables sources départementales à retenir (le nom des champ y est séparé par une virgule)...
-- nomindsrc  : nom du champ identifiant dans les couches départementales sources...
-- nomindres  : nom du champ identifiant dans la table régionale résultat (si vide alors on ne renomme pas le nom du champ identifiant)...
-- nomlibsrc  : nom du champ libellé dans les couches départementales sources (si vide alors on doit générer un champ identifiant de manière automatique)...
-- nomlibres  : nom du champ libellé dans la couche régionale résultat(si vide alors on ne renomme pas le nom du champ identifiant)...
-- nomdepsrc  : nom du champ département dans les couches départementales sources...
-- nomdepres  : nom du champ département dans la couche régionale résultat(si vide alors on ne renomme pas le nom du champ departement)...
-- nomgeomres : nom du champ géométrique à créer en résultat dans la table régionale (le nom du champ géométrique des tables départementales sources est identifié automatiquement par la fonction)...

-- EXECUTION POUR COMMUNES BDT&BDC, EPCI, SCOT
-- select fct_web.creation_unions('n_commune_bdt', '2012', 'tab_web', 'nom,code_insee,statut,canton,depart,popul,geom'                              , 'code_insee', '', 'nom'       , '', 'depart'    , '', 'geom')
-- select fct_web.creation_unions('n_commune_bdc', '2012', 'tab_web', 'nom_comm,insee_comm,statut,insee_cant,nom_dept,insee_dept,geom'              , 'insee_comm', '', 'nom_comm'  , '', 'insee_dept', '', 'geom')
-- select fct_web.creation_unions('n_epci_zsup'  , '2015', 'tab_web', 'id_epci,nom_epci,type_epci,nature_epc,siren_epci,nbcom_epci,insee_dept,geom' , 'id_epci'   , '', 'nom_epci'  , '', 'insee_dept', '', 'geom')
-- select fct_web.creation_unions('n_scot_zsup'  , '2016', 'tab_web', 'id_scot,nom,schema_id,code_insee,nom_dept,dep_respon,geom'                   , 'id_scot'   , '', 'nom'       , '', 'dep_respon', '', 'geom')

declare 
	my_table text  :=prefix ||'_reg_'||millesime;
	my_select text := 'create table '||schcom||'.'||my_table||' as ';
	my_union text  :=' ';
	liste_tables text:='';
	nomtab text;
	liste_geoms text;
	nomgeomsrc  text;
	i	    integer;
	ok	    boolean;
	
begin

	ok := true;

	-- Union des tables pour la création d'une table régionale...
	EXECUTE 'drop table if exists ' ||schcom||'.'||my_table || ' cascade';
	liste_tables := 'select tablename from pg_tables where schemaname='''||schcom||''' and position('''||prefix||''' in tablename) = 1  and position('''||millesime||''' in tablename) >0  order by tablename';
	-- Raise notice '%',liste_tables;
	for nomtab in execute liste_tables loop
		my_select := my_select || my_union || 'select ' || list_champ || ' from ' || schcom  || '.' || nomtab ;
		my_union := ' union ';
	end loop;
	EXECUTE my_select ;

	-- Récupération du nom de l'objet géométrique de la table résultat régionale et renommage en nom_geom si besoin...
	liste_geoms := 'select f_geometry_column from geometry_columns where f_table_schema = '''||schcom||''' and f_table_name = '''||my_table||'''';
	execute liste_geoms into nomgeomsrc;
	if nomgeomsrc is null then i := 0;
	else
		-- Recherche de l'objet géométrique de dimension 2 (polygones)...
		for nomgeomsrc in execute liste_geoms loop
			if nomgeomsrc is null then
				i := 0;
				exit;
			else
				execute 'select min(st_dimension('||nomgeomsrc||')) from '||schcom||'.'||my_table into i;
				raise notice 'Le champ géométrique <%> de la table <%.%> est de dimension <%>...',nomgeomsrc,schcom,my_table,i;
				if i > 1 then exit; end if;
			end if;
		end loop;
	end if;
	if i < 2 then ok := false;
	else
		-- Renommage du champ geométrique...
		if nomgeomsrc <> nomgeomres then
			execute 'alter table '||schcom||'.'||my_table||' rename column '||nomgeomsrc||' to '||nomgeomres;
		end if;
	end if;

	-- Renommage du champ identifiant...
	if nomindsrc <> nomindres and nomindres <> '' then
		execute 'alter table '||schcom||'.'||my_table||' rename column '||nomindsrc||' to '||nomindres;
	end if;

	-- Renommage du champ libellé...
	if nomlibsrc <> nomlibres and nomlibres <> '' then
		execute 'alter table '||schcom||'.'||my_table||' rename column '||nomlibsrc||' to '||nomlibres;
	end if;

	-- Renommage du champ département...
	if nomdepsrc <> nomdepres and nomdepres <> '' then
		execute 'alter table '||schcom||'.'||my_table||' rename column '||nomdepsrc||' to '||nomdepres;
	end if;

	-- Initialisation de la table régionale en projection 2154
	execute 'select * from fct.initsrid ('''||schcom||''','''||my_table||''', '''', ''2154'', '''', true)';

	-- Création d'un index sur le champ gémétrique nomgeomres...
	if length(nomgeomres) > 0 then
		EXECUTE 'drop index if exists '||schcom ||'.'||my_table||nomgeomres;
		EXECUTE 'CREATE INDEX '||my_table||nomgeomres||' ON '||schcom ||'.'||my_table||' USING gist ('||nomgeomres||')';
	end if;

	-- Création d'un index sur le champ identifiant nomindres...
	if length(nomindres) > 0 then
		EXECUTE 'drop index if exists '||schcom||'.'||my_table||nomindres;
		EXECUTE 'cREATE INDEX '||my_table||nomindres||'	ON '||schcom||'.'||my_table||' USING btree ('||nomindres||' DESC NULLS LAST)';
	end if;

	-- Gestion des droits...
	EXECUTE 'ALTER TABLE ' ||schcom||'.'||my_table||' OWNER TO draaf_admin';

	return ok;

end;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION fct_web.creation_unions(text, text, text, text, text, text, text, text, text, text, text)
  OWNER TO draaf_admin;
