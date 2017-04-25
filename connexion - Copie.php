<?php
	class oscom {
		// paramètres de connexion
		private $_ip_serv;
		private $_port;
		private $_nom_bd;
		private $_user;
		private $_mdp;
		private $_dsn;
		private $_connexion;
		
		// alias des noms des tables dans la base de données
		private $_schema_fonctions;
		private $_schema_sources;
		private $_schema_user; // schéma de la table des périmètres
		private $_perimetres;
		private $_geompar;
		
		// tables ressources oscom - format conforme note de création
		private $_table_communes;
		private $_table_scots;
		private $_table_epci;
		
		// info sur la connexion
		private $_message;
		private $_conn_ok; // true --> 1 connexion réussie
		
		// alias mapserver
		private $_rep_map; // dossier sur le serveur avec un droit d'écriture
		private $_modele_map;
		private $_data_using_id;
		private $_data_using_srid;
		private $_user_map;
		private $_connexion_map;
		private $_images;
		
		// alias geoide (version web)
		private $_geoide;
		
		//site intranet
		private $_nom_site;
		private $_url_site;
		
		//groupes menus déroulant
		//private $_grp_perimetre_nb;
		//private $_grp_perimetre_groupe;
		
		public function __construct() { 
					$this->oscom();
			}

		public function oscom(){
		// initialise les paramètres à partir du fichier ini 
			$mes_parametres = parse_ini_file("param.ini");
			$this->_ip_serv=$mes_parametres['ip'];
			$this->_port=$mes_parametres['port'];	
			$this->_nom_bd=$mes_parametres['base_oscom'];			
			$this->_user=$mes_parametres['login'];
			$this->_mdp=$mes_parametres['mdp'];
			$this->_dsn='pgsql:host='.$this->_ip_serv.';dbname='.$this->_nom_bd;

			try {
				$this->_connexion = new PDO($this->_dsn, $this->_user, $this->_mdp);
				$this->_message = 'Connection OK';
				$this->_conn_ok = true;
				}
				catch (PDOException $e)
					{
				$this->_message = 'Connection failed: ' . $e->getMessage() ;
				$this->_conn_ok = false;
					} ;	

			$this->_schema_fonctions=$mes_parametres['schema_fonctions'];	
			$this->_schema_sources=$mes_parametres['schema_sources'];	
			$this->_schema_user=$mes_parametres['schema_user'];	
			$this->_perimetres=$mes_parametres['perimetres'];
			$this->_geompar=$mes_parametres['geompar'];
			
			$this->_table_communes=$mes_parametres['table_communes'];
			$this->_table_scots=$mes_parametres['table_scots'];
			$this->_table_epci=$mes_parametres['table_epci'];

			$this->_rep_map=$mes_parametres['rep_map'];
			$this->_modele_map=$mes_parametres['modele_map']; 
			$this->_user_map=$mes_parametres['user_map'];
			$this->_data_using_id=$mes_parametres['data_using_id'];
			$this->_data_using_srid=$mes_parametres['data_using_srid'];
			$this->_images=$mes_parametres['images'];
			
			$this->_connexion_map = 'user='.$this->_user.' dbname='.$this->_nom_bd.' host='.$this->_ip_serv.' port='.$this->_port.' password='.$this->_mdp;
			
			$this->_geoide=$mes_parametres['geoide'];
			$this->_nom_site=$mes_parametres['nom_site'];
			$this->_url_site=$mes_parametres['url_site'];
			
			//$this->_grp_perimetre_nb=$mes_parametres['grp_perimetre_nb'];
			/*
			for ($i = 1; $i <= $this->_grp_perimetre_nb; $i++) {
				$this->_grp_perimetre_groupe[$i]=$mes_parametres['grp_perimetre_'.$i];
			}
			*/
		}
		
	// section fixe - applicable sans modification à tous les projets
	
		public function get_conn() { // return  1 si connexion ok, sinon 0;
			return $this->_conn_ok ;
		}

		public function get_geoide() { // return  1 si connexion ok, sinon 0;
			return $this->_geoide ;
		}

		public function get_images() { // return  1 si connexion ok, sinon 0;
			return $this->_images ;
		}
		
		public function get_nomtable($nom_table) { 
			// renvoi le nom réel de la table à partir de son alias
			// retourne la table commençant par l'alias $table_name dans le schéma d'alias $this->_schema_sources
			// cela permet de changer les millésimes des tables paramètres sans avoir à modifier l'application WEB
			$sql_table = "SELECT tablename FROM pg_tables WHERE (Tablename like '". $this->{"_$nom_table"}."%') and (schemaname='".$this->_schema_sources."') ORDER BY Tablename DESC limit 1";
			$result = $this->get_requete($sql_table) ;
			$row=$result->fetch(PDO::FETCH_ASSOC) ; // PDO::FETCH_ASSOC
			return $row["tablename"] ;
		}
		
		public function get_message() {  // renvoi le type d'erreur de connexion ou ok
			return $this->_message;
		}
		
		public function get_schema_fonctions() {  // renvoi le schema des fonctions postgresql
			return $this->_schema_fonctions;
		}

		public function get_nom_site() {  // renvoi le schema des fonctions postgresql
			return $this->_nom_site;
		}
		
		public function get_url_site() {  // renvoi le schema des fonctions postgresql
			return $this->_url_site;
		}
		public function get_table($selection,$nom_table,$where) { // récupère la table d'alias $nom_table sur le serveur
			$result = $this->_message ; 
			if ($this->_conn_ok) {
				if ($this->_schema=='') {
					$marequete="select ".$selection." from " ;
					}
					else {
					$marequete="select ".$selection." from ".$this->_schema."."; 
					};
				$marequete .= $this->{"_$nom_table"} .' '.$where;
				$result = $this->_connexion->prepare($marequete) ;
				$result->execute();
				}
			return $result ;
		}

		public function lookfor_table($schema_name,$table_name) {
			// retourne la table commençant par $table_name dans le schéma $schema_name
			// cela permet de changer les millésimes des tables paramètres sans avoir à modifier l'application WEB
			// Attention : le gestionnaire de l'OS Communal doit veiller à ne laisser que le dernier millésime
			$sql_table = "SELECT tablename FROM pg_tables WHERE (Tablename like '". $table_name."%') and (schemaname='".$schema_name."') ORDER BY Tablename DESC limit 1";
			$result = get_requete($sql_table) ;
			$row=$result->fetch(PDO::FETCH_ASSOC) ; // PDO::FETCH_ASSOC
			return $row["tablename"] ;
		}

		public function affiche_table($selection,$larg_table,$nom_table,$where) { /* affiche le contenu HTML à mettre entre les balises <table> et </table>
																		 $selection est la liste des champs à prendre
																		 $where est la condition SQL
																	  */
			$result = $this->get_table($selection,$nom_table,$where) ;
			if ($result <> $this->_message) {
				$table='<thead>';
				$dimtable=explode(",", $larg_table); 
				 
				$row = $result->fetch(PDO::FETCH_ASSOC);
				$temp = '<tr><th width="AA">' . implode('</th><th width="AA">', array_keys($row)). '</th></tr>';
				for ($i=0;$i< count($dimtable);$i++){
					$temp = preg_replace('/width="AA"/', 'width="'.$dimtable[$i].'"', $temp, 1); 
				}
				$table .=  $temp."\n" ;
				$table .=  '</thead>';
				do {
					$temp =  '<tr><td  width="AA">'. implode('</td><td width="AA">', $row). '</td></tr>' ;
					for ($i=0;$i< count($dimtable);$i++){
						$temp = preg_replace('/width="AA"/', 'width="'.$dimtable[$i].'"', $temp, 1);
					}
					$table .=  $temp."\n" ;
				} while($row = $result->fetch(PDO::FETCH_ASSOC));
			}
			else {
			$table ='<p>'.$this->_message ;
			} ;
			return $table;
		}	

		public function affiche_select($selection,$nom_table,$where) { /* affiche le contenu HTML à mettre entre les balises <select> et </select>
		                                                                  $selection comprend le value et le libelléprendre
																		 $where est la condition SQL
																	  */
			$result = $this->get_table($selection,$nom_table,$where) ;
			if ($result <> $this->_message) {
				$row = $result->fetch(PDO::FETCH_ASSOC);
				$MySelect = "" ;
				do {
					$MySelect .='<option value="'. implode('">', $row).'</option>'."\n" ;
					} while($row = $result->fetch(PDO::FETCH_ASSOC));
				}
				else {
				$MySelect='<p>'.$this->_message ;
				} ;
			return $MySelect;
		}	

		public function cree_ficmap($id_fic, $schema, $perimetre, $insee, $filter) {
			// crée le fichier .map
			$reussite = '';
			try {
				$dir = dirname(__FILE__).'/';
				$rep = ($this->_rep_map=="")? "" : $this->_rep_map ."/" ;
				$table = "select * from ". $schema .".". $perimetre ;
				if ($insee<>"") {
					$id_fic=$insee;
					$table = $table ." WHERE insee_comm='".$insee."'" ;
				}
				
				$table = "(".$table.")";
				
				if ($id_fic=="") {
					$id_fic="test";
				} 
				
				$modele_map  = $this->_modele_map ;
				$user_map = $id_fic."_".$this->_user_map ;	
				
				$ficmap_mod = $dir.$rep.$modele_map;
				$ficmap_new = $dir.$rep.$user_map;
				
				// paramètres modifiable du fichier mapserver

				$ma_connexion = $this->_connexion_map;
				$mes_datas =  $this->_geompar. " from ".$table." as foo1 using unique ". $this->_data_using_id ." using srid=". $this->_data_using_srid ;

				$xy = "select st_xmin(foo.st_extent) as xmin, st_ymin(foo.st_extent) as ymin, st_xmax(foo.st_extent) as xmax, st_ymax(foo.st_extent) as ymax from ";
				$xy .= "(select st_extent(".$this->_geompar.") from ".$table." as foo2 ) as foo";
				
				$result = $this->_connexion->prepare($xy) ;
				$result->execute();
				while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
					$xmin = $row['xmin'] ;
					$ymin = $row['ymin'] ;
					$xmax = $row['xmax'] ;
					$ymax = $row['ymax'] ;
					}
				$mon_extent = $xmin. ' '. $ymin. ' '.$xmax. ' '.$ymax;
				
				//echo $mon_extent."</br>" ;
				
				$monfichier_modele = fopen($ficmap_mod, 'r');
				$monfichier_map = fopen($ficmap_new, 'w+');
				
				//echo $ficmap_mod."</br>" ;
				//echo $ficmap_new."</br>" ;
				$contents = fread($monfichier_modele, filesize($ficmap_mod));
				$contents = str_replace('[$EXTENT]', $mon_extent, $contents);
				$contents = str_replace('[$DATA]', $mes_datas, $contents);
				$contents = str_replace('[$CONNECTION]', $ma_connexion, $contents);
				$contents = str_replace('[$FILTER]', $filter, $contents);
				
				fwrite($monfichier_map , $contents) ;
				fclose($monfichier_map);
				fclose($monfichier_modele);	
				
				$reussite = $ficmap_new ."|".$mon_extent;
			}
			catch (PDOException $e)
			{
				$reussite = '';
			} ;	
			return $reussite ;
		}	// fin de cree_ficmap
		
		public function get_param(){
		/* Retourne la table des périmètres dans une variable dictionnaire de type :
		   tab_perimetre = {
			"millesime 1" : { 
				id : ["bloc","libelle_long", "schema","nom_table","geoide","image","extent_to_html"],
				id2 : ["bloc","libelle_long", "schema","nom_table","geoide","image","extent_to_html"],
				....
				},
			....
			"millesime 2" : { 
				id : ["bloc","libelle_long", "schema","nom_table","geoide","image","extent_to_html"],
				id2 : ["bloc","libelle_long", "schema","nom_table","geoide","image","extent_to_html"],
				....
				} 
			}
		*/
			$ma_tab_perimetre = 'select * from '. $this->_schema_user . '.' . $this->_perimetres . ' order by millesime,bloc, libelle_long' ;
			$result = $this->_connexion->prepare($ma_tab_perimetre) ;
			$result->execute();
			$millesime='';
			$tab_perimetre='{'."\n";
			$virgule_perim='';
			
			while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
				if ($millesime!=$row["millesime"]) {
					$virgule_perim='';
					$millesime =$row["millesime"] ;
					if ($tab_perimetre != '{'."\n"){
						$tab_perimetre .= "\n" .'},'."\n";
						}
					$tab_perimetre .= '"'.$row["millesime"].'": {'."\n";
					}
				$tab_perimetre .= $virgule_perim . '     "'.$row["id"].'": ["'.$row["bloc"].'","'.$row["libelle_long"].'","'.$row["schema"].'","'.$row["nom_table"].'","'.$row["geoide"].'","'.$row["image"].'","'.$row["extent_to_html"].'"]' ;
				$virgule_perim=',' . "\n";
			};
			if ($tab_perimetre != '{'."\n"){
				$tab_perimetre .= "\n" .'}'."\n" .'}';
				}
		return $tab_perimetre ;
		}
		
		public function get_requete($marequete) { // exécute une requête sans contrôle ni formatage
			$result = '' ; 
			if ($this->_conn_ok) {
				$result = $this->_connexion->prepare($marequete) ;
				$result->execute();
				}
			return $result ;
		}
		
		public function get_bloc_lib() {
			/* retourne dans un tableau array() les valeurs 
			1=>'Région & Départ',
			2=>'Région & Départ',
			3=>'Canton',
			4=>'SCOT',
			5=>'Autre',
			6=>'Personnalisé'
			*/
			$bloc='';
			$sep='';
			/*
			for ($i = 1; $i <= $this->_grp_perimetre_nb; $i++) {
				$bloc .= $sep . $i . "=>'". $this->_grp_perimetre_groupe[$i]."'";
				$sep=',';
			}
			*/
			return $bloc;
		};
	}
?>