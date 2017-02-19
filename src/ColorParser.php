<?php
namespace Spectroscope;
class ColorParser {
	
	public static $instance;
	
	public static function getInstance()
	{
	    if (null === ColorParser::$instance) {
	        ColorParser::$instance = new ColorParser();
	    }
	    
	    return ColorParser::$instance;
	}


	/**
	  * This function is responsible for creating the ColorObject. It resolved the format of the received color 
	  *	and converts into other color formats.
	  *
	  */
	public function parse($color) {
		//echo "checking $color<br>";
		// remove all whitespace from color and set to lowercase
		$color = preg_replace('/\s+/', '', $color);
		$color = strtolower($color);

		// Find type of color value
		$type = false;
		$alpha = false;
		$important = false;
		$hex = false;

		if(preg_match('/\!important/', $color)) {
			$important = true;
			$color = str_replace("!important", "", $color);
		}
		$display = $color;
		// var_dump(preg_match('/\!important/',  $color));
		// var_dump(str_replace("!important", "", $color));

		// // get first 4 characters from color
		// $identifier = substr($color, 0, 4);

		// switch ($identifier) {
		// 	case 'value':
		// 		# code...
		// 		break;
			
		// 	default:
		// 		# code...
		// 		break;
		// }

		$color = $this->cleanColorString($color);
		//var_dump($color); return true;

		if (strpos($display, "#")===0) 
			{
				//echo "This is a HEX color";
				$type = "hex";
				
				// remove the hashtag
				
				// check for short hand hex values, e.g. #f3e
				//var_dump(strlen($color));
				if(strlen($color)===3) {
					//echo "this is shorthand";

					// double the values
					$hex =  $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
					//var_dump($color);
				} else {
					$hex =  $color;
				}

				$rgb = $this->convertHexToRgb($hex);
				$hsl = $this->convertHexToHsl($hex);


			} 
		elseif (strpos($display, "rgba")===0) 
			{
				//*echo "This is a RGBA color";
				$type = "rgba";
				$hex = $this->convertToHex($color,"rgba");
				$rgb = $color;
				$hsl = $this->convertHexToHsl($hex);
				if(is_array($color)){
					$alpha = $color[3];
				} else {
					$alpha = explode(",", $color);
					$alpha = str_replace(")", "", $alpha[3]);
				}
			} 
		elseif (strpos($display, "rgb")===0) 
			{
				//*echo "This is a RGB color";
				$type = "rgb";
				$hex = $this->convertToHex($color,"rgb");
				$rgb = $color;
				$hsl = $this->convertHexToHsl($hex);
			} 
		elseif (strpos($display, "hsla")===0) 
			{
				//var_dump($color);
				//*echo "This is a HSLA color";
				$type = "hsla";
				$hex = $this->convertToHex($color,"hsla");
				$rgb = $this->convertHexToRgb($hex);
				$hsl = $color;
				if(is_array($color)){
					$alpha = $color[3];
				} else {
					$alpha = explode(",", $color);
					$alpha = str_replace(")", "", $alpha[3]);
				}
			}
		elseif (strpos($display, "hsl")===0) 
			{
				//*echo "This is a HSL color";
				$type = "hsl";
				$hex = $this->convertToHex($color,"hsl");
				$rgb = $this->convertHexToRgb($hex);
				$hsl = $color;
			}
		elseif (strpos($display, "transparent")===0) 
			{
				//*echo "This is a TRANSPARENT color";
				$type  = "transparent";
				$alpha = 0;
				$hex = false;
				$rgb = false;
				$hsl = false;
			}
		elseif (strpos($display, "inherit")===0) 
			{
				//*echo "This is an INHERIT color";
				$type  = "inherit";
				$alpha = 0;
				$hex = false;
				$rgb = false;
				$hsl = false;
			}
		elseif (strpos($display, "none")===0) 
			{
				//*echo "This is an "NONE" color";
				$type  = "none";
				$alpha = 0;
				$hex = false;
				$rgb = false;
				$hsl = false;
			}
		elseif (strpos($display, "currentcolor")===0) 
			{
				//*echo "This is an "NONE" color";
				$type  = "currentcolor";
				$alpha = 0;
				$hex = false;
				$rgb = false;
				$hsl = false;
			}
		else 
			{
				if($this->resolveNamedColor($color)!==$color) {
					//*echo "This is a NAMED color";
					$type = "named";
					$hex = $this->resolveNamedColor($color);
					$rgb = $this->convertHexToRgb($hex);
					$hsl = $this->convertHexToHsl($hex);
				} 
				else {
					//*echo "what the hell is this color??";
					$type = "unknown";
					$hex = false;
					$rgb = false;
					$hsl = false;
				}
			}

		return new ColorObject(array(
				"display" 	=> 	$display,
				"hex" 		=> 	$hex,
				"rgb" 		=> 	$rgb,
				"hsl" 		=> 	$hsl,
				"type"		=>	$type,
				"alpha"		=>	$alpha,
				"important"	=>	$important,
			));
	}






    /* ****************************************************************
     * Color Converters
     * ***************************************************************/




	public function convertToHex($color,$type) {
		$color = $this->cleanColorString($color);
		switch ($type) {
			case 'rgb':

				return $this->convertRgbToHex($color);
				break;
			
			case 'rgba':

				return $this->convertRgbToHex($color);
				break;
			
			case 'hsl':

				return $this->convertFromHslTo($color,"hex");
				break;
			
			case 'hsla':

				return $this->convertFromHslTo($color,"hex");
				break;
			
			default:
				# code...
				break;
		}
	}


	public function convertHexToHsl($hex) {
		// credit to https://github.com/mexitek/phpColors/blob/master/src/Mexitek/PHPColors/Color.php
		$R = hexdec($hex[0].$hex[1]);
        $G = hexdec($hex[2].$hex[3]);
        $B = hexdec($hex[4].$hex[5]);
        $HSL = array();
        $var_R = ($R / 255);
        $var_G = ($G / 255);
        $var_B = ($B / 255);
        $var_Min = min($var_R, $var_G, $var_B);
        $var_Max = max($var_R, $var_G, $var_B);
        $del_Max = $var_Max - $var_Min;
        $L = ($var_Max + $var_Min)/2;
        if ($del_Max == 0)
        {
            $H = 0;
            $S = 0;
        }
        else
        {
            if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
            else            $S = $del_Max / ( 2 - $var_Max - $var_Min );
            $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            if      ($var_R == $var_Max) $H = $del_B - $del_G;
            else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
            else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;
            if ($H<0) $H++;
            if ($H>1) $H--;
        }
        $HSL['H'] = round(($H*360),2);
        $HSL['S'] = round($S*100,2);
        $HSL['L'] = round($L*100,2);
        return $HSL;

	}

	public function convertHexToRgb($hex) {


		// Split hex input into pairs
		$hex = str_split($hex, 2);
		
		// Convert hex value pairs 
		$r = hexdec($hex[0]);
		$g = hexdec($hex[1]);
		$b = hexdec($hex[2]);

		return array($r,$g,$b);
	}


	public function convertRgbToHex($color) {
		// Accepts either a string or an array
		//  (string)	"rgb(13,255,0)"
		//  (array)		array(13,255,0)

			if(is_string($color)) {
				// prepare $color string
				$rgb = str_replace("rgba", "", $color);
				$rgb = str_replace("rgb", "", $rgb);
				$rgb = str_replace("(", "", $rgb);
				$rgb = str_replace(")", "", $rgb);

				// explode into array(r,g,b)
				$rgb = explode(",", $rgb);

			} elseif (is_array($color)) {
				if(is_int($color[0]) && is_int($color[1]) && is_int($color[2])) {
					$rgb = $color;
				} else {
					echo "Error: This array was in the wrong format for convertRgbToHex(). This array should contain integers, i.e. array(13,255,0)." . print_r($color,true);
					//var_dump($color);
				}
			} else {
				echo "error: trying to convertRgbToHex() but wrong format was supplied. Either send a string, e.g. 'rgb(13,255,0) or an array(13,255,0). " . print_r($color,true);
			}

			

			// Inspired/borrowed from http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml

			$r = intval($rgb[0]); $g = intval($rgb[1]);
		    $b = intval($rgb[2]);

		    $r = dechex($r<0?0:($r>255?255:$r));
		    $g = dechex($g<0?0:($g>255?255:$g));
		    $b = dechex($b<0?0:($b>255?255:$b));

		    $color = (strlen($r) < 2?'0':'').$r;
		    $color .= (strlen($g) < 2?'0':'').$g;
		    $color .= (strlen($b) < 2?'0':'').$b;

   			return $color;
	}

	public function convertFromHslTo($color, $from = "hex") {
		// borrowed from http://stackoverflow.com/questions/20423641/php-function-to-convert-hsl-to-rgb-or-hex
		if(is_string($color)) {
				// prepare $color string
				$rgb = str_replace("hsla", "", $color);
				$rgb = str_replace("hsl", "", $rgb);
				$rgb = str_replace("(", "", $rgb);
				$rgb = str_replace(")", "", $rgb);

				// explode into array(r,g,b)
				$rgb = explode(",", $rgb);

			} elseif (is_array($color)) {
				if(is_int($color[0]) && is_int($color[1]) && is_int($color[2])) {
					$rgb = $color;
				} else {
					echo "Error: This array was in the wrong format for convertFromHslTo(). This array should contain integers, i.e. array(120, 100%, 50%).";
				}
			} else {
				echo "error: trying to convertFromHslTo() but wrong format was supplied. Either send a string, e.g. 'hsl(120, 100%, 50%) or an array(120, 100%, 50%). " . print_r($color,true);
		}
		//echo "<br><br>test:<br>";var_dump($color);
		$rgb[0] = intval($rgb[0]); $rgb[1] = intval($rgb[1]);
		$rgb[2] = intval($rgb[2]);
		//var_dump($rgb);



		// Borrowed from http://stackoverflow.com/a/31885018/1620719
		$toHex = true;

		$h = $rgb[0];
		$s = $rgb[1];
		$l = $rgb[2];

		$h /= 360;
	    $s /=100;
	    $l /=100;

	    $r = $l;
	    $g = $l;
	    $b = $l;
	    $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
	    if ($v > 0){
	          $m;
	          $sv;
	          $sextant;
	          $fract;
	          $vsf;
	          $mid1;
	          $mid2;

	          $m = $l + $l - $v;
	          $sv = ($v - $m ) / $v;
	          $h *= 6.0;
	          $sextant = floor($h);
	          $fract = $h - $sextant;
	          $vsf = $v * $sv * $fract;
	          $mid1 = $m + $vsf;
	          $mid2 = $v - $vsf;

	          switch ($sextant)
	          {
	                case 0:
	                      $r = $v;
	                      $g = $mid1;
	                      $b = $m;
	                      break;
	                case 1:
	                      $r = $mid2;
	                      $g = $v;
	                      $b = $m;
	                      break;
	                case 2:
	                      $r = $m;
	                      $g = $v;
	                      $b = $mid1;
	                      break;
	                case 3:
	                      $r = $m;
	                      $g = $mid2;
	                      $b = $v;
	                      break;
	                case 4:
	                      $r = $mid1;
	                      $g = $m;
	                      $b = $v;
	                      break;
	                case 5:
	                      $r = $v;
	                      $g = $m;
	                      $b = $mid2;
	                      break;
	          }
		    }
		    $r = round($r * 255, 0);
		    $g = round($g * 255, 0);
		    $b = round($b * 255, 0);
		    if ($from === "hex") {
		        $r = ($r < 15)? '0' . dechex($r) : dechex($r);
		        $g = ($g < 15)? '0' . dechex($g) : dechex($g);
		        $b = ($b < 15)? '0' . dechex($b) : dechex($b);
		        return "$r$g$b";
		    } elseif ($from === "rgb") {
		        return "rgb($r, $g, $b)";    
		    } else {
		    	echo "Error: invalid output format for convertFromHslTo(). 2. paramenter can be either 'hex' or 'rgb'.";
		    }

	}






    /* ****************************************************************
     * SORTING
     * ***************************************************************/




	public function sortColors($colors = array()) {

		$sorted = array();
		$greys = array();
		foreach ($colors as $color) {
			//echo "<b><br>".$color."<br>";
			//$color = "#12abef";
			$color = str_replace("#", "", $color);
			$color = str_replace("!important", "", $color);
			$pair1 = substr($color, 0,2);
			$pair2 = substr($color, 2,2);
			$pair3 = substr($color, 4,2);
			//echo "<br>pairs: <br>";
			//var_dump($pair1);var_dump($pair2);var_dump($pair3);
			$set1 = $this->getDecimalFromHex($pair1[0]);
			$set1 += $this->getDecimalFromHex($pair1[1]);
			$set2 = $this->getDecimalFromHex($pair2[0]);
			$set2 += $this->getDecimalFromHex($pair2[1]);
			$set3 = $this->getDecimalFromHex($pair3[0]);
			$set3 += $this->getDecimalFromHex($pair3[1]);

			$set1 = str_pad($set1, 2, "0", STR_PAD_LEFT);
			$set2 = str_pad($set2, 2, "0", STR_PAD_LEFT);
			$set3 = str_pad($set3, 2, "0", STR_PAD_LEFT);
			//echo "<br>decimal converted<br>";
			//var_dump($set1);var_dump($set2);var_dump($set3); 

			$sortValue = $set1 . $set2 . $set3;
			//echo "<br>Sorted as<br>" .$sortValue;
			

			//echo "<br>HSL: " ;
			//print_r($this->hexToHsl($color));
			$hsl = $this->hexToHsl($color);
			//var_dump($hsl);

			$h = str_pad(round($hsl["H"]*100), 3, "0", STR_PAD_LEFT);
			$s = str_pad(round($hsl["S"]*100), 3, "0", STR_PAD_LEFT);
			$l = str_pad(round($hsl["L"]*100), 3, "0", STR_PAD_LEFT);
			$sortValue = $h.$s.$l;

			$sorted[$sortValue] = $color;
			//print_r($sortValue);
			//var_dump(count($sorted));
			//$sortValue = str_pad(round($hsl["H"]*100), 3, "0", STR_PAD_LEFT).str_pad(round($hsl["L"]*100), 3, "0", STR_PAD_LEFT).str_pad(round($hsl["S"]*100), 3, "0", STR_PAD_LEFT);


			if($s<0.5) {
				//echo "<br>!!!! grey-ish<br>";
				$greys[$h.$l.$s] = $color;
				//echo "<br>l: ".($s)." (".$color.")<br>";

			}
			//die();
		}

		/*
		$sortedGreys = array();

		foreach ($greys as $color) {
			// sort greys
			$pair1 = substr($color, 0,2);
			$pair2 = substr($color, 2,2);
			$pair3 = substr($color, 4,2);
			//echo "<br>pairs: <br>";
			//var_dump($pair1);var_dump($pair2);var_dump($pair3);
			$set1 = $this->hexMap[$pair1[0]];
			$set1 += $this->hexMap[$pair1[1]];
			$set2 = $this->hexMap[$pair2[0]];
			$set2 += $this->hexMap[$pair2[1]];
			$set3 = $this->hexMap[$pair3[0]];
			$set3 += $this->hexMap[$pair3[1]];

			$set1 = str_pad($set1, 2, "0", STR_PAD_LEFT);
			$set2 = str_pad($set2, 2, "0", STR_PAD_LEFT);
			$set3 = str_pad($set3, 2, "0", STR_PAD_LEFT);
			//echo "<br>decimal converted<br>";
			//var_dump($set1);var_dump($set2);var_dump($set3); 

			$sortValue = $set1 . $set2 . $set3;
			$sortedGreys[$sortValue] = "#". $color;
		}
		ksort($sortedGreys);*/

		ksort($greys);

		echo "<pre>";
		print_r($greys);
		echo "</pre>";
		foreach ($greys as $color) {
			?>
			<div class="colorExample" style="background-color: #<?php echo $color; ?>"><em><?php echo $color;?></em></div>

			<?php
		}
		//var_dump($sorted);
		ksort($sorted);
		//var_dump($sorted);
		return $sorted;
	}	




	public function sortGreys($colors = array()) {

		$greys = array();
		$sorted = array();
		$nonGreys = array();
		foreach ($colors as $color) {
			//print_r($color->hsl);
			// get HSL value from HEX
			$hsl = $color->hsl;
			//var_dump($hsl);
			$h = str_pad(round($hsl["H"]*100), 3, "0", STR_PAD_LEFT);
			$s = str_pad(round($hsl["S"]*100), 3, "0", STR_PAD_LEFT);
			$l = str_pad(round($hsl["L"]*100), 3, "0", STR_PAD_LEFT);
			$sortValue = $h.$s.$l;

			$sorted[$sortValue] = $color;



			if($hsl["S"]<20 || $hsl["S"]<20 && $hsl["L"]<10 ) {
				$greys[$h.$l.$s] = $color;
				//$greys[$hsl["S"]] = $color;
			} else {
				$nonGreys[$h.$l] = $color;
			}

		}
		ksort($greys);
		ksort($nonGreys);
		return array(
			"greys" 	=> 	$greys,
			"nonGreys"	=> 	$nonGreys,
			"sorted"	=>	$sorted,
		);

	}







    /* ****************************************************************
     * Helpers
     * ***************************************************************/

	private function cleanColorString($string) {
		if(!is_array($string)){
			$string = str_replace("#", "", $string);
			$string = str_replace("rgba", "", $string);
			$string = str_replace("rgb", "", $string);
			$string = str_replace("hsla", "", $string);
			$string = str_replace("hsl", "", $string);
			$string = str_replace("(", "", $string);
			$string = str_replace(")", "", $string);
			$string = str_replace("!important", "", $string);
			$string = str_replace(";", "", $string);
			$string = str_replace("%", "", $string);
			$string = explode(",", $string);
		} else {
			//echo "<br>ERROR: was expecting string, but array was provided<br>";echo count($string)."<br>";var_dump($string);var_dump(count($string)>1);die();
		}
		if (count($string)>1) { // if array, make sure all values are (int)s
			$string[0] = intval($this->cleanColorString($string[0]));
			$string[1] = intval($this->cleanColorString($string[1]));
			$string[2] = intval($this->cleanColorString($string[2]));
			return $string;
		} else {
			return $string[0];
		}
	}




	private $namedColors = array(
		"aliceblue" => "f0f8ff",
		"antiquewhite" => "faebd7",
		"aqua" => "00ffff",
		"aquamarine" => "7fffd4",
		"azure" => "f0ffff",
		"beige" => "f5f5dc",
		"bisque" => "ffe4c4",
		"black" => "000000",
		"blanchedalmond" => "ffebcd",
		"blue" => "0000ff",
		"blueviolet" => "8a2be2",
		"brown" => "a52a2a",
		"burlywood" => "deb887",
		"cadetblue" => "5f9ea0",
		"chartreuse" => "7fff00",
		"chocolate" => "d2691e",
		"coral" => "ff7f50",
		"cornflowerblue" => "6495ed",
		"cornsilk" => "fff8dc",
		"crimson" => "dc143c",
		"cyan" => "00ffff",
		"darkblue" => "00008b",
		"darkcyan" => "008b8b",
		"darkgoldenrod" => "b8860b",
		"darkgray" => "a9a9a9",
		"darkgrey" => "a9a9a9",
		"darkgreen" => "006400",
		"darkkhaki" => "bdb76b",
		"darkmagenta" => "8b008b",
		"darkolivegreen" => "556b2f",
		"darkorange" => "ff8c00",
		"darkorchid" => "9932cc",
		"darkred" => "8b0000",
		"darksalmon" => "e9967a",
		"darkseagreen" => "8fbc8f",
		"darkslateblue" => "483d8b",
		"darkslategray" => "2f4f4f",
		"darkslategrey" => "2f4f4f",
		"darkturquoise" => "00ced1",
		"darkviolet" => "9400d3",
		"deeppink" => "ff1493",
		"deepskyblue" => "00bfff",
		"dimgray" => "696969",
		"dimgrey" => "696969",
		"dodgerblue" => "1e90ff",
		"firebrick" => "b22222",
		"floralwhite" => "fffaf0",
		"forestgreen" => "228b22",
		"fuchsia" => "ff00ff",
		"gainsboro" => "dcdcdc",
		"ghostwhite" => "f8f8ff",
		"gold" => "ffd700",
		"goldenrod" => "daa520",
		"gray" => "808080",
		"grey" => "808080",
		"green" => "008000",
		"greenyellow" => "adff2f",
		"honeydew" => "f0fff0",
		"hotpink" => "ff69b4",
		"indianred" => "cd5c5c",
		"indigo" => "4b0082",
		"ivory" => "fffff0",
		"khaki" => "f0e68c",
		"lavender" => "e6e6fa",
		"lavenderblush" => "fff0f5",
		"lawngreen" => "7cfc00",
		"lemonchiffon" => "fffacd",
		"lightblue" => "add8e6",
		"lightcoral" => "f08080",
		"lightcyan" => "e0ffff",
		"lightgoldenrodyellow" => "fafad2",
		"lightgray" => "d3d3d3",
		"lightgrey" => "d3d3d3",
		"lightgreen" => "90ee90",
		"lightpink" => "ffb6c1",
		"lightsalmon" => "ffa07a",
		"lightseagreen" => "20b2aa",
		"lightskyblue" => "87cefa",
		"lightslategray" => "778899",
		"lightslategrey" => "778899",
		"lightsteelblue" => "b0c4de",
		"lightyellow" => "ffffe0",
		"lime" => "00ff00",
		"limegreen" => "32cd32",
		"linen" => "faf0e6",
		"magenta" => "ff00ff",
		"maroon" => "800000",
		"mediumaquamarine" => "66cdaa",
		"mediumblue" => "0000cd",
		"mediumorchid" => "ba55d3",
		"mediumpurple" => "9370d8",
		"mediumseagreen" => "3cb371",
		"mediumslateblue" => "7b68ee",
		"mediumspringgreen" => "00fa9a",
		"mediumturquoise" => "48d1cc",
		"mediumvioletred" => "c71585",
		"midnightblue" => "191970",
		"mintcream" => "f5fffa",
		"mistyrose" => "ffe4e1",
		"moccasin" => "ffe4b5",
		"navajowhite" => "ffdead",
		"navy" => "000080",
		"oldlace" => "fdf5e6",
		"olive" => "808000",
		"olivedrab" => "6b8e23",
		"orange" => "ffa500",
		"orangered" => "ff4500",
		"orchid" => "da70d6",
		"palegoldenrod" => "eee8aa",
		"palegreen" => "98fb98",
		"paleturquoise" => "afeeee",
		"palevioletred" => "d87093",
		"papayawhip" => "ffefd5",
		"peachpuff" => "ffdab9",
		"peru" => "cd853f",
		"pink" => "ffc0cb",
		"plum" => "dda0dd",
		"powderblue" => "b0e0e6",
		"purple" => "800080",
		"rebeccapurple" => "663399",
		"red" => "ff0000",
		"rosybrown" => "bc8f8f",
		"royalblue" => "4169e1",
		"saddlebrown" => "8b4513",
		"salmon" => "fa8072",
		"sandybrown" => "f4a460",
		"seagreen" => "2e8b57",
		"seashell" => "fff5ee",
		"sienna" => "a0522d",
		"silver" => "c0c0c0",
		"skyblue" => "87ceeb",
		"slateblue" => "6a5acd",
		"slategray" => "708090",
		"slategrey" => "708090",
		"snow" => "fffafa",
		"springgreen" => "00ff7f",
		"steelblue" => "4682b4",
		"tan" => "d2b48c",
		"teal" => "008080",
		"thistle" => "d8bfd8",
		"tomato" => "ff6347",
		"turquoise" => "40e0d0",
		"violet" => "ee82ee",
		"wheat" => "f5deb3",
		"white" => "ffffff",
		"whitesmoke" => "f5f5f5",
		"yellow" => "ffff00",
		"yellowgreen" => "9acd32",
	);

	public function resolveNamedColor($color) {
		$color = strtolower($color);


		//var_dump(isset($this->namedColors[$color]));
		//var_dump($this->namedColors[$color]);
		if(isset($this->namedColors[$color])) {
			return $this->namedColors[$color];
		} else {
			echo "ERROR: Could not resolveNamedColor(".$color.")";
			return false;
		}
	}



/*    private function getDecimalFromHex($number){
    	// TODO redundant function, use php function hexdec() instead!
		$hexMap = array(
			'0' => 0,
			'1' => 1,
			'2' => 2,
			'3' => 3,
			'4' => 4,
			'5' => 5,
			'6' => 6,
			'7' => 7,
			'8' => 8,
			'9' => 9,
			'a' => 10,
			'b' => 11,
			'c' => 12,
			'd' => 13,
			'e' => 14,
			'f' => 15,
		);
		if (in_array($number, $hexMap)) {
			return $hexMap[$number];
		} else {
			return false;
		}

    }*/

} // end class