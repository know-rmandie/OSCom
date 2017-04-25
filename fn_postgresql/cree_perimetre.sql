CREATE OR REPLACE FUNCTION fct_web.cree_perimetre(schema_tab_dept text,schema_tab_perso text, prefix_table text, nomliste text, nomlistelong text, list_insee text, list_millesime text)
  RETURNS text AS
$BODY$

DECLARE
	table_perimetre text :='oscom.oscom_perimetres';
	prefix_table_dep text :='oscom';

	flag boolean :=false;
	tab_millesime text[];
	tab_insee text[];
	tab_dept text[];
	tab_dept_oscom text[];
	insee text :='';
	dept text:='';
	list_dept text :='';
	

	var_temp text :='';
	sql_temp text :='';
	
	table_perso text;
	millesime integer;
	nom_table text;	
	
	i integer;
	bloc text;
	id text;
	idreturn text:='';

BEGIN
--
--  cette fonction crée une table temporaire perimetre à partir d'une liste de communes
--  prend en entrée
--       le schéma des tables départements : schema_tab_dept
--       le schéma des tables perimetre personnalisé : schema_tab_perso
--	 le prefix du nom des tables personnalisées : prefix_table
--       le nom court de la table perimetre personnalisé : nomliste 
--       le libellé de la table perimetre personnalisé : nomlistelong 
-- 	 la liste des numéros insee des communes constituant le périmètre personnalisé : list_insee
--	 les millésimes de calcul : list_millesime

--  select * from fct_web.cree_perimetre('oscom','tab_web_user','oscom_perso','BIH-BG', 'BIHOREL BOIS GUILLAUME', '76095,76108,27034','2009,2010') ;


-- recherche des tables départements concernées
 tab_insee:= string_to_array(list_insee,',');
 tab_dept:=string_to_array('',',');
 tab_dept_oscom:=string_to_array('',',');
 tab_millesime:= string_to_array(list_millesime,',');
 FOREACH insee in ARRAY tab_insee LOOP
	IF (left(insee,2)::int<97) THEN var_temp:=left(insee,2); ELSE var_temp:=left(insee,3); END IF;
	--raise notice 'insee = %',insee;
	flag:=true;
	FOREACH dept in ARRAY tab_dept LOOP
		IF var_temp=dept THEN flag:=false ;  END IF ;
		--raise notice 'var_temp = % - dept = %',var_temp, dept;
	END LOOP ;
	IF flag THEN 
		-- on ne garde que les tables existant dans la table des paramètres oscom
		tab_dept:=array_append(tab_dept,var_temp) ; 
		sql_temp := 'select tablename from pg_tables where schemaname='''||schema_tab_dept||''' and tablename like '''||prefix_table_dep||'%'' and right(tablename,3) = right('''||'000' ||var_temp||''',3)';
		FOR nom_table in EXECUTE sql_temp LOOP 
			tab_dept_oscom:=array_append(tab_dept_oscom,nom_table) ; 
			--raise notice 'ajout de %',nom_table;
		END LOOP ;
	END IF ;	
 END LOOP;

-- création de la table personnamisée
 FOREACH millesime in ARRAY tab_millesime LOOP
	-- cherche un nom de table disponible
	table_perso :=prefix_table||millesime||'_' ;
	i := 0;
	while exists (select * from pg_tables where (tablename=table_perso|| i) and (schemaname=schema_tab_perso)) loop
		i := i + 1;
	end loop;
	table_perso := table_perso || i;
	-- raise notice 'table_perso : %', table_perso;

	-- prend les tables départementale du millésime
	list_dept:='';
	FOREACH dept in ARRAY tab_dept_oscom LOOP
		IF position(prefix_table_dep||millesime in dept)>0 THEN 
			IF list_dept <>'' THEN list_dept:=list_dept || ' UNION '; END IF ;
			list_dept:= list_dept || 'select * from ' ||schema_tab_dept ||'.'|| dept; 
		END IF ;
	END LOOP;
	-- raise notice 'list_dept : %', list_dept;
	-- crée la table à partir des tables départementales retenues

	sql_temp := 'create table ' || schema_tab_perso||'.'|| table_perso ||' as (
				SELECT *
				FROM (' || list_dept ||') as f00
				where insee_comm::integer in (' ||  list_insee || '));' ;

        -- raise notice 'remplissage table_perso : %', sql_temp;
	
	EXECUTE sql_temp;

	-- raise notice 'table_perso OK';
	
	bloc:='6'; --- Périmètre personnalisé
	if nomliste = 'CANTON' then bloc='3'; end if;
	if nomliste = 'SCOT' then bloc='4'; end if;
	if nomliste = 'EPCI' then bloc='5'; end if;
	if nomliste = 'Autres' then bloc='7'; end if; --- Périmètre mise en place manuelle par administrateur
	
	sql_temp := 'Insert into ' || table_perimetre || '(
				libelle_court, libelle_long, 
				millesime, schema, nom_table, 
				sql,
				permanent,commentaire,
				auteur, 
				geoide,image,extent_to_html,bloc)
				VALUES
				(
				''' || nomliste ||''','''|| nomlistelong ||''',
				' || millesime || ','''|| schema_tab_perso ||''',''' || table_perso ||''', '''
				|| replace(list_insee,'''', '') ||''',
				false,''création interface web'',
				''user'','''','''','''','||bloc||')
				RETURNING id;
				;';
	EXECUTE sql_temp into id; 
	-- raise notice 'id %',id;
	IF idreturn <> '' THEN idreturn:=idreturn || ','; END IF ;
	idreturn := idreturn || id;
 END LOOP;	

 -------------- Droits
	
 execute 'ALTER TABLE ' || schema_tab_perso||'.'||table_perso|| ' OWNER TO draaf_admin' ;
 	
 return idreturn;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION fct_web.cree_perimetre(text, text, text, text, text, text, text) OWNER TO draaf_admin;