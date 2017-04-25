<?php
$color_text_code = array(
	'00'=>'#FFFFFF',
	'++'=>'#000000',
	'11'=>'#FFFFFF',
	'12'=>'#FFFFFF',
	'13'=>'#FFFFFF',
	'14'=>'#000000',
	'15'=>'#000000',
	'21'=>'#000000',
	'22'=>'#FFFFFF',
	'23'=>'#000000',
	'24'=>'#000000',
	'30'=>'#FFFFFF',
	'31'=>'#000000',
	'51'=>'#000000'
);

$tableau = array(array(5)); 
/* $tableau est de la forme :
	tableau[1]=(valeur1 commune, valeur1 département, valeur1 région),
	etc ..
*/	

$tableau['++'][6] = "Surface totale";
$tableau['++'][7] = "255 255 255"; // couleur de MAPSERVER

$tableau['11'][6] = "Zones urbanisées et bâties";
$tableau['11'][7] = "230 0 77"; // 65 65 65 couleur de MAPSERVER

$tableau['12'][6] = "Zones industr/commerc, réseaux de comm, gds équipements";
$tableau['12'][7] = "204 77 242"; // 100 100 100 couleur de MAPSERVER

$tableau['13'][6] = "Mines, décharges, dépôts et chantiers";
$tableau['13'][7] = "166 0 204"; // 135 135 135 couleur de MAPSERVER

$tableau['14'][6] = "Espaces verts artificialisés non agricoles";
$tableau['14'][7] = "255 166 255"; // 150 170 150 - 170 170 170 couleur de MAPSERVER

$tableau['15'][6] = "Espaces non bâtis en attente de requalification";
$tableau['15'][7] = "205 205 205"; // 205 205 205 couleur de MAPSERVER

$tableau['20'][6] = "Terres agricoles mixtes";
$tableau['20'][7] = "170 170 0"; // couleur de MAPSERVER

$tableau['21'][6] = "Terres arables";
$tableau['21'][7] = "255 255 168"; // 255 255 0 couleur de MAPSERVER

$tableau['22'][6] = "Cultures permanentes";
$tableau['22'][7] = "230 128 0"; // 255 170 0 couleur de MAPSERVER

$tableau['23'][6] = "Prairies";
$tableau['23'][7] = "230 230 77"; // 170 255 0 couleur de MAPSERVER

$tableau['24'][6] = "Autres terres agricoles";
$tableau['24'][7] = "255 230 166"; // 208 154 138 couleur de MAPSERVER

//$tableau['30'][6] = "Milieux semi-naturels";
//$tableau['30'][7] = "0 90 0"; // 0 170 0 couleur de MAPSERVER

$tableau['31'][6] = "Forêts, bois, bosquets";
$tableau['31'][7] = "128 255 0"; // 0 90 0 couleur de MAPSERVER

$tableau['32'][6] = "Milieu à végétation arbustive et/ou herbacée";
$tableau['32'][7] = "204 242 077"; // 0 170 0 couleur de MAPSERVER  204-242-077

$tableau['51'][6] = "Eaux continentales";
$tableau['51'][7] = "0 204 242"; // 0 170 255 couleur de MAPSERVER

$tableau['00'][6] = "Espaces mixtes (src Majic)";
$tableau['00'][7] = "100 100 100"; // 255 170 255 couleur de MAPSERVER

class ColorConverter {
    /**
     * Obtenir la valeur hexadecimal d'une couleur
     * @param array $rgb array[R, G, B]
     * @return string
	 * http://www.fobec.com/php5/1032/convertir-une-couleur-rgb-valeur-hexadecimal-vice-versa.html
	 	la couleur HTML d'une vert clair
		$hexcolor=ColorConverter::toHTML(array(102,255,51));
		composante RGB d'une couleur
		$rgb=ColorConverter::toRGB('#66ff33');
     */
    public static function toHTML(array $rgb) {
        $hexcolor = '#';
        for($i=0; $i<3; $i++) {
            if( ($rgb[$i] > 255) || ($rgb[$i] < 0) ) {
                echo "Error bad value :".$rgb[$i];
                $hexcolor .= '00';
            } else {
                $hex = dechex($rgb[$i]);
                if(strlen($hex) ==2) {
                    $hexcolor.= $hex;
                } else {
                    $hexcolor .= "0". $hex;
                }
            }
        }
        return $hexcolor;
    }
 
    /**
     * Extraire les byte RGB d'une couleur format HTML
     * @param String $hex
     * @return array $rgb array[R, G, B]
     */
    public static function toRGB($hex) {
        if (strlen($hex)==7) { //enlever #
            $hex=substr($hex, 1);
        }
 
        $rgb=array();
        $rgb[]=hexdec(substr($hex,0,2));
        $rgb[]=hexdec(substr($hex,2,2));
        $rgb[]=hexdec(substr($hex,4,2));
        return $rgb;
    }
	
    /**
     * Calculer la couleur du texte sur un fond RGB
     * @param array $rgb array[R, G, B]
     * @return string
	 * 
     */
    public static function colortext(array $rgb) {
        
		$textcolor = '';
		$cpt=0 ;
        for($i=0; $i<3; $i++) {
            if ($rgb[$i] <= 130) {
                $textcolor = '#FFFFFF';
				$cpt = $cpt + 1 ;
            } 
        }
		if ($cpt > 1)  {
        	return $textcolor;
			}else{
			$textcolor='';
			return $textcolor;
			}
			
		
    }	
}
?>