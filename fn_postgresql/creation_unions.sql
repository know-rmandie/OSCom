-- Function: fct_web.creation_unions(text, text, text, text, text)

-- DROP FUNCTION fct_web.creation_unions(text, text, text, text, text);

CREATE OR REPLACE FUNCTION fct_web.creation_unions(prefix text, millesime text, schcom text, list_champ text, nom_index text)
  RETURNS void AS
$BODY$
declare 
	my_table text :=prefix ||'_reg_'||millesime;
	my_select text := 'create table '||schcom||'.'||my_table||' as ';
	my_union text :=' ';
	liste_tables text:='';
	nomtab text;

	
begin
	-- select fct_web.creation_unions('n_commune_bdc', '2012', 'tab_web','nom_comm,insee_comm,statut,insee_cant,nom_dept,insee_dept,geom', 'insee_comm')
	-- select fct_web.creation_unions('n_epci_zsup', '2015', 'tab_web','id_epci,nom_epci,type_epci,nature_epc,siren_epci,nbcom_epci,insee_dept,geom', 'id_epci')
	-- select fct_web.creation_unions('n_scot_zsup', '2016', 'tab_web','code_insee,dept,schema_nom,schema_id,geom', 'schema_nom')
	EXECUTE 'drop table if exists ' ||schcom||'.'||my_table || ' cascade';
	liste_tables := 'select tablename from pg_tables where schemaname='''||schcom||''' and position('''||prefix||''' in tablename) = 1  and position('''||millesime||''' in tablename) >0  order by tablename';
	-- Raise notice '%',liste_tables;
	for nomtab in execute liste_tables loop
		my_select := my_select || my_union || 'select ' || list_champ || ' from ' || schcom  || '.' || nomtab ;
		my_union := ' union ';
	end loop;

	EXECUTE my_select ;
	EXECUTE 'drop index if exists ' || schcom  || '.' || my_table ||'geom';
	EXECUTE '
		CREATE INDEX '|| my_table || 'geom
		ON ' || schcom  || '.' || my_table ||'
		USING gist
		(geom)
		';
	EXECUTE 'drop index if exists ' || schcom  || '.' || my_table ||nom_index;
	EXECUTE '
		CREATE INDEX '|| my_table || nom_index ||'
		ON ' || schcom  || '.' || my_table ||'
		USING btree
		('|| nom_index ||' DESC NULLS LAST)
		';
	EXECUTE 'ALTER TABLE ' || schcom  || '.' || my_table ||' OWNER TO draaf_admin';

end;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION fct_web.creation_unions(text, text, text, text, text)
  OWNER TO draaf_admin;
