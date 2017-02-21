<?php 
namespace Spectroscope;

// Turn off output buffering
ini_set('output_buffering', 'off');
// Turn off PHP output compression
ini_set('zlib.output_compression', false);
         
//Flush (send) the output buffer and turn off output buffering
//ob_end_flush();
while (@ob_end_flush());
         
// Implicitly flush the buffer(s)
ini_set('implicit_flush', true);
ob_implicit_flush(true);
 
//prevent apache from buffering it for deflate/gzip
//header("Content-type: text/plain");
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
 
 

require_once"vendor/autoload.php";
// require_once"css_color_parser.php";
// require_once"css_color_object.php";


?>
<!DOCTYPE html>
<html>
<head>
	<title>CSS Parser</title>
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<header>
	<img id="logo" src="new_logo.png">
</header>
<!-- <img src="design inspiration/tumblr_nq1q3waC6b1r7g5u4o1_1280.jpg">
 --><?php 


// NEW CODE!!!!!


/*
 * Interface list

 // Initiate
		analyse($url|css)
 		analyseURL($url)
 			loads the urls, analyse css from files, embedded and line
 		analyseCSS(css)

 // Settings
 		$settings 	array

 // Getters
 		getAllResults()		text, colors, layout, box model, animation, backgroundImages, prefixes 
 		getResults(array)	e.g. ("text","colors")
 		getResultText()	calls getResults("text")

 // Statistics
 		getAllStats()
 		getStats(array)		layout, colors, text, declarations, rules, sources(how much from external, % of embedded)
 		getStatsLayout()	calls getStats("layout")		
*/




$parser = Parser::getInstance();


// which URL to analyse (get ?url=)
	$url = "http://www.cchobby.dk";
	if(isset($_GET["url"]) && !empty($_GET["url"])) {
	    $url = rtrim($_GET["url"],"/");
	}
	if (!starts_with($url,"http") && !starts_with($url,"//")) {
	    $url = "http://".$url;
	    //echo "added http://<br>";
	} 
	define("URL",$url);
	//elseif (starts_with($url,"//")) {

	//}
	//var_dump($url);
	echo "<h5>".URL."</h5>";


	?>
<h2>
	Sources</h2>
	<?php


// load content from URL
	$urlContent = getFile($url);

// generate DOM for content
	$dom = $parser->generateDom($urlContent);
	//$dom->load($urlContent);

// find all css files on page
	$cssFiles = $parser->findCssFiles($dom);
	echo "<br>number of css files: ". count($cssFiles) ."<br>";
	foreach ($cssFiles as $fileUrl) {
        echo $fileUrl."<br><br>";
/*                ob_flush();
                flush();
*/
	}

// parse all rules in css files
	$selectors = $declarations = array();
	foreach ($cssFiles as $file) {
		$css = $parser->getFile($file);
		$css = $parser->prepareCSS($css);
		$selectors = array_merge($selectors,$parser->findSelectors($css));
		$declarations = array_merge($declarations,$parser->findDeclarations($css));
	}	
	echo " (" . count($selectors) ." rulesets with " . count($declarations) . " declarations)<br>";


/*echo "\n\n";
var_dump(count($selectors));
var_dump(count($declarations));
*/
// find all inline styling
	$inline = array();
	$inlineStyling = $parser->findInlineStyling($dom);
	foreach ($inlineStyling as $declaration) {
		$declaration = $parser->prepareCSS($declaration);
		$inline = array_merge($inline,$parser->findDeclarations($declaration));
	}
echo "<br>inline styles: ". count($inline) ." declarations<br>";

// add inline declarations to the ones found in css files
	$declarations = array_merge($declarations, $inline);


// find all embedded styling
	$embeddedStyling = "";
	$embeddedStylings = $parser->findEmbeddedStyling($dom);
	foreach ($embeddedStylings as $style) {
		$embeddedStyling .= $style;
	}
	$embeddedStyling = $parser->prepareCSS($embeddedStyling);


// add embedded styles to those already found
	//var_dump($selectors);
	//var_dump($parser->findSelectors($embeddedStyling));
echo "<br>embedded styles: ". count($embeddedStylings) ." instances having " . count($parser->findSelectors($embeddedStyling)) ." declarations with ".count($parser->findDeclarations($embeddedStyling))." rules<br>";
	$selectors = array_merge($selectors,$parser->findSelectors($embeddedStyling));
	$declarations = array_merge($declarations,$parser->findDeclarations($embeddedStyling));


//var_dump($selectors);
//var_dump($declarations);
//die();
	//var_dump($declarations);

	$parser->addDeclarations($declarations);
	$parser->addSelectors($selectors);
	


// set up stats
$stats = array(
	"files" => array(),
	"inline" => array(),
	"ruleSets" => array(
		"count" => 0,
	),
	"declarations" => array(
		"count" => 0,
		"unique" => array(), 	
	),
	"fonts" => array(
		"font-size" => array(),
		"font-family" => array(),
		"sizes" => array(),
		"important" => array(),

	),
	"color" => array(),
	"background-color" => array(),
);

/* Get unique declarations */

$stats["declarations"]["unique"] = $result = array_unique($declarations);
//var_dump($stats);







// Inline styling
// TODO add stats to this
/*$inline = $dom->find('*[style]');
if(!empty($inline)){
	foreach ($inline as $tag) {
		//echo "<textarea>".$tag->outertext."</textarea>";
		//var_dump(debug_backtrace());
		echo $tag->style;
		$stats["inline"][] = $tag->style;
		// $css = prepareCSS($tag->style);
		// $ruleSets = findRuleSets($css);
		// $declarations = findDeclarations($css);

		//var_dump($tag);die();
	}
}*/
//var_dump($inline);
//die();

// // External Stylesheets
// foreach ($cssFiles as $file) {
// 	$css = getFile($file);
// 	$css = prepareCSS($css);
// 	$ruleSets = findRuleSets($css);
// 	$declarations = findDeclarations($css);
// 	$stats["files"][$file]["ruleSets"] = $ruleSets;
// 	$stats["files"][$file]["declarations"] = $declarations;
// 	$stats["declarations"]["unique"] = addToDeclarations($declarations,$stats["declarations"]["unique"]);
// 	$stats["ruleSets"]["count"] += count($ruleSets);
// 	$stats["declarations"]["count"] += count($declarations);
// 	//echo "found ".count($ruleSets)." ruleSets<br>";
// }

//var_dump($stats["declarations"]);
foreach ($stats["declarations"]["unique"] as $declaration) {
	if(starts_with($declaration,"font-size")>0) {
		//echo "<br>font found!<br>";
		$size = str_ireplace("font-size:","",$declaration);
		
		if(strpos($size, "!important")>0) {
			//echo "found !important";
			//var_dump($size);
			$stats["fonts"]["important"][] = $size;
			$size = str_ireplace("!important","",$size);
		}
		// Find unit and value
		if(!in_array($size,$stats["fonts"]["font-size"])){
			$stats["fonts"]["font-size"][] = $size;
			//$size = "small";
			$re = "/(\d+\.*\d*)(\D*)/"; 
 			$matches = array();
			preg_match_all($re, $size, $matches);
			//var_dump($size);
			//var_dump($matches);
			//echo "<br>";
			$re = "/(\d+\.*\d*)|(\D*)/"; 
 			$matches = array();
			preg_match_all($re, $size, $matches);
			//var_dump($size);
			//var_dump($matches);
			//echo "<br>";
			//echo "<br>";die();
			$unit = $matches[0][1];
			$value = $matches[0][0];
			if($unit === "") {
				$unit = "none";
			}
/*			var_dump($matches);
			echo "<br>";
			echo "size: ";
			var_dump($size);
			echo "<br>";
			echo "unit: ";
			var_dump($unit);
			echo "<br>";
			echo "value: ";
			var_dump($value);
			echo "<br>";
			echo "<br>";
*/			if(!is_array($stats["fonts"]["sizes"][$unit])) {
				//echo "<br>adding ".$unit;
				//$stats["fonts"]["sizes"][] = $unit;
			}
			if(!in_array($size,$stats["fonts"]["sizes"])){
				$stats["fonts"]["sizes"][$unit][] = $value;
				//echo "<br>adding ".$value;
			}
			//die();
		}
	} else {
		//echo "<br>no font<br>";
	}
}

//var_dump($stats["fonts"]["sizes"]);
// SORT AFTER SIZE
foreach ($stats["fonts"]["sizes"] as $key => $value) {
	sort($stats["fonts"]["sizes"][$key],SORT_NUMERIC);// = rsort($key);
}


//var_dump($stats["fonts"]["sizes"]);


	// TODO: sort font size units
	// em
	// rem
	// ex
	// %
	// px
	// cm
	// mm
	// in
	// pt
	// pc
	// ch
	// vh
	// vw
	// vmin
	// vmax
	// q
	// mozmm
	// filter out !important
	?>
	<section id="fonts">
	<header>
		<h2>Fonts</h2>
		<?php
		echo "<h3>Found ".count($stats["fonts"]["font-size"])." font-sizes</h3>";
		?><h4>Out of which <?php echo count($stats["fonts"]["important"]);?> where !important</h4>
	</header><?php
		//echo "<br><br><br>";
		//var_dump($stats["fonts"]["font-size"]);

asort($stats["fonts"]["sizes"]);
	ob_flush();
	flush();


// Print out all font sizes
foreach ($stats["fonts"]["sizes"] as $unit => $array) {
	///echo "<br><br><br>";

	//var_dump($unit); die();

	foreach ($array as $value) {
		//var_dump($value);
		$displayUnit = $unit;
		// if no unit is specified
		if($unit === "none") {$displayUnit = "";}
		?><h4 class="font-unit font-unit-<?php echo $unit; ?>" style="font-size:<?php //echo $value . $unit?>">Font size <?php echo $value.$displayUnit?></h4><?php
		ob_flush();
		flush();
	}
}
//die();

?>
</section>
<section id="color">

<?php
?>
<section id="background-color">

<?php
foreach ($stats["declarations"]["unique"] as $declaration) {
	if(starts_with($declaration,"background-color")>0) {
		//echo "<br>font found!<br>";
		$color = str_ireplace("background-color:","",$declaration);
		if(!in_array($color,$stats["background-color"])){
			$stats["background-color"][] = $color;
		}
	} else {
		//echo "<br>no font<br>";
	}
	//var_dump($declaration);
}	//die();

$sorted = (ColorParser::getInstance()->sortGreys($stats["background-color"]));
?>



<br><br><br><br><br><br><h1>Greys (<?php echo count($sorted["greys"]); ?>)</h1>
<?php

foreach ($sorted["greys"] as $color) {
	?>
	<div class="colorExample" style="background-color: #<?php echo $color->hex; ?>"><em><?php echo $color->hex."<br>(".$color->display.")<br>Type: ".$color->type;?></em></div>

	<?php
}
?><br><br><br><br><br><br><h1>Colors (<?php echo count($sorted["nonGreys"]); ?>)</h1>
<?php
foreach ($sorted["nonGreys"] as $color) {
	?>
	<div class="colorExample" style="background-color: #<?php echo $color->hex; ?>"><em><?php echo $color->hex."<br>(".$color->display.")<br>Type: ".$color->type;?></em></div>

	<?php
	ob_flush();
	flush();

}
/*foreach ($stats["declarations"]["unique"] as $declaration) {
	if(starts_with($declaration,"color")>0) {
		//echo "<br>font found!<br>";
		$color = str_ireplace("color:","",$declaration);
		if(!in_array($color,$stats["color"])){
			$stats["color"][] = $color;
		}
	} else {
		//echo "<br>no font<br>";
	}
	//var_dump($declaration);
}	//die();





$stats["sortedColors"] = array();
	
	foreach ($stats["color"] as $color) {
		//echo '"'.$color.'",\n';
		//echo "<br>Checking color: ".$color."<br>";
		$color = CssColorParser::getInstance()->parse($color);
		if(!is_null($color->hex)) {
			if(!in_array($color->hex, $stats["sortedColors"])){
				$stats["sortedColors"][] = $color->hex;
			}			
		}
		$stats["colorValues"][] = $color;
	}
	//echo "<pre>";print_r($stats["sortedColors"]);
	//$stats["sortedColors"] = cf_sort_hex_colors($stats["sortedColors"]);
	$stats["sortedColors"] = CssColorParser::getInstance()->sortColors($stats["sortedColors"]);
	//echo "<br><br>";print_r($stats["sortedColors"]);
 
	//var_dump($stats["sortedColors"]);die();
	?>
	<h3>Found <?php echo count($stats["sortedColors"]);?> font colors</h3>
	<?php
	//var_dump($stats["color"]);
	foreach ($stats["sortedColors"] as $color) {
		?><div class="color-block" style="background-color:<?php echo $color;?>;"><label><?php echo $color;?></label></div><?php
		ob_flush();
		flush();
	}

?>
</section>
<?php

// TODO: translate colors to rgba and compare
// 

?>
<section id="background-color">

<?php
foreach ($stats["declarations"]["unique"] as $declaration) {
	if(starts_with($declaration,"background-color")>0) {
		//echo "<br>font found!<br>";
		$color = str_ireplace("background-color:","",$declaration);
		if(!in_array($color,$stats["background-color"])){
			$stats["background-color"][] = $color;
		}
	} else {
		//echo "<br>no font<br>";
	}
	//var_dump($declaration);
}	//die();
	?>
	<h3>Found <?php echo count($stats["background-color"]);?> background-colors</h3>
	<?php
	//var_dump($stats["background-color"]);
	foreach ($stats["background-color"] as $color) {
		//echo '"'.$color.'",\n';
		?><div class="color-block" style="background-color:<?php echo $color;?>;"><label><?php echo $color;?></label></div><?php
	}

?>
</section>
<?php
*/





?>
<section id="font-family">

<?php
foreach ($stats["declarations"]["unique"] as $declaration) {
	if(starts_with($declaration,"font-family")>0) {
		//echo "<br>font found!<br>";
		$color = str_ireplace("font-family:","",$declaration);
		if(!in_array($color,$stats["fonts"]["font-family"])){
			$stats["font-family"][] = $color;
		}
	} else {
		//echo "<br>no font<br>";
	}
	//var_dump($declaration);
}	//die();
	?>
	<h3>Found <?php echo count($stats["font-family"]);?> font-familys</h3>
	<?php
	asort($stats["fonts"]["sizes"]);
	//var_dump($stats["font-family"]);
	foreach ($stats["font-family"] as $font_family) {
		?><div class="font-family" style="font-family:<?php echo $font_family;?>;"><label><?php echo $font_family;?></label></div><?php
	}

?>
</section>
<section id="declarations">
<?php









	echo "<h3>Found ".$stats["ruleSets"]["count"]." ruleSets and ".$stats["declarations"]["count"]." declarations in <strong>".count($cssFiles)."</strong> files.</h3>";

	$stats["occurences"]["declarations"] = array();
	foreach ($stats["files"] as $file) {
		$occurences = array_count_values($file["declarations"]);
		//var_dump($occurences);
		arsort($occurences);
		foreach ($occurences as $key => $value) {
			$score = round($value/$stats["declarations"]["count"]*100,2);
			//*echo "<br>".$key . " (".$value.") | ".$score."%";
			if(in_array($key,$stats["occurences"]["declarations"])) {
				$stats["occurences"]["declarations"][$key] += $value;
			} else {
				$stats["occurences"]["declarations"][$key] = $value;
			}
		}
	}

	echo "<h4>position: absolute (". $stats["occurences"]["declarations"]["position:absolute"].")</h4>";
	echo "<h4>position: relative (". $stats["occurences"]["declarations"]["position:relative"].")</h4>";
	echo "<h4>position: static (". $stats["occurences"]["declarations"]["position:static"].")</h4>";
	echo "<h4>position: fixed (". $stats["occurences"]["declarations"]["position:fixed"].")</h4>";
	echo "<h4>float: left (". $stats["occurences"]["declarations"]["float:left"].")</h4>";
	echo "<h4>float: right (". $stats["occurences"]["declarations"]["float:right"].")</h4>";
	echo "<h4>clear: left (". $stats["occurences"]["declarations"]["clear:left"].")</h4>";
	echo "<h4>clear: right (". $stats["occurences"]["declarations"]["clear:right"].")</h4>";
	echo "<h4>clear: both (". $stats["occurences"]["declarations"]["clear:both"].")</h4>";
	echo "<h4>display: inline (". $stats["occurences"]["declarations"]["display:inline"].")</h4>";
	echo "<h4>display: inline-block (". $stats["occurences"]["declarations"]["display:inline-block"].")</h4>";
	echo "<h4>display: block (". $stats["occurences"]["declarations"]["display:block"].")</h4>";
	echo "<h4>display: flex (". $stats["occurences"]["declarations"]["display:flex"].")</h4>";
	//var_dump($stats["occurences"]);

	?><h3>Duplicated selectors</h3><?php
	$stats["occurences"]["ruleSets"] = array();
	$stats["occurences"]["id-selectors"] = 0;

	foreach ($stats["files"] as $file) {
		$occurences = array_count_values($file["ruleSets"]);
		//var_dump($occurences);
		arsort($occurences);
		foreach ($occurences as $key => $value) {
			//echo "count id".substr_count($key,"#");
			$stats["occurences"]["id-selectors"] += substr_count($key,"#");
			if($value>1) {
				echo "<br>".$key . " (".$value.")";
				if(in_array($key,$stats["occurences"]["ruleSets"])) {
					$stats["occurences"]["ruleSets"][$key] += $value;
				} else {
					$stats["occurences"]["ruleSets"][$key] = $value;
				}
			}
		}
	}

	echo "<h3>id selectors: ".$stats["occurences"]["id-selectors"]."</h3>";





	$stats["occurences"]["class-selectors"] = 0;

	foreach ($stats["files"] as $file) {
		$occurences = array_count_values($file["ruleSets"]);
		//var_dump($occurences);
		arsort($occurences);
		foreach ($occurences as $key => $value) {
			$stats["occurences"]["class-selectors"] += substr_count($key,".");
			if($value>1) {
				echo "<br>".$key . " (".$value.")";
				if(in_array($key,$stats["occurences"]["ruleSets"])) {
					$stats["occurences"]["ruleSets"][$key] += $value;
				} else {
					$stats["occurences"]["ruleSets"][$key] = $value;
				}
			}
		}
	}
	echo "<h3>class selectors: ".$stats["occurences"]["class-selectors"]."</h3>";

?>

</section>

<?php
// CSS analyser
// 
function prepareCSS($css) {
	//echo $url."<br>";
	//*echo "<textarea>".$css."</textarea>";

	// Minify (to get consistency)
	// https://github.com/matthiasmullie/minify/
	require_once 'minify/src/Minify.php';
	require_once 'minify/src/CSS.php';
	require_once 'minify/src/JS.php';
	require_once 'minify/src/Exception.php';
	require_once 'path-converter/src/Converter.php';

	$minifier = new Minify\CSS($css);
	$minified = $minifier->minify();
	//*echo "<textarea>".$minified."</textarea>";


	// modify structure
	$minified = str_ireplace("{", "{\n", $minified);
	$minified = str_ireplace("}", "\n}\n", $minified);
	$minified = str_ireplace(";", ";\n", $minified);
	//*echo "<textarea>".$minified."</textarea>";
	return $minified;

/* $url = 'https://cssminifier.com/raw';
	$postdata = array('http' => array(
		        'method'  => 'POST',
		        'header'  => 'Content-type: application/x-www-form-urlencoded',
		        'content' => http_build_query( array('input' => $css) ) ) );
	$minified =  file_get_contents($url, false, stream_context_create($postdata));
	*/
	/*	include_once("minifier.php");

	$js = array(
	    "js/application.js"     => "js/application.min.js",
	    "js/main.js"            => "js/main.min.js"
	);
	$css = array(
	    "css/application.css"   => "css/application.min.css",
	    "css/main.css"          => "css/main.min.css"
	);

	minifyJS($js);
	minifyCSS($css);


		//var_dump($css);
		// minify
	    $data = array(
	        'input' => $css,
	    );
	    $minifyUrl = 'https://cssminifier.com/raw';
		$ch = curl_init($minifyUrl);

	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $minified = curl_exec($ch);
	     if($minified === false)
	    {
	        echo "Error Number:".curl_errno($ch)."<br>";
	        echo "Error String:".curl_error($ch);
	    } else {
	    	echo 'Operation completed without any errors';
	    }
	    var_dump($minified);
	if(curl_exec($ch) === false)
	{
	    echo 'Curl error: ' . curl_error($ch);
	}
	else
	{
	    echo 'Operation completed without any errors';
	}

	    curl_close($ch);
*/
    // output the $minified

}
// Prepare css file


function findRuleSets($preparedCss) {
	//$preparedCSS;

	// Regex
	// ruleSets: ^(.+){
	// preg_match("/^(.+){/i", $input_line, $output_array);
	// declarations: ^(\S+:\S+|\S+\(\S+\)?)|\S+\.+\S+;$
	// preg_match("/^(\S+:\S+|\S+\(\S+\)?)|\S+\.+\S+;$/i", $input_line, $output_array);

	$ruleSets = array();
	$ruleSets = preg_grep("/^(.+){/i", explode("\n", $preparedCss));

	foreach ($ruleSets as $rule) { 
		//var_dump($rule);
		$rule = str_ireplace("  "," ",$rule);
		$rule = str_ireplace(" {","",$rule);
		$rule = str_ireplace("{ ","",$rule);
		$rule = str_ireplace("{","",$rule);
		$ruleSets2[] = $rule;
		//var_dump($rule);
		//echo "<br><br>";
	}
	//die();

	//var_dump($ruleSets);die();
	// 
	return $ruleSets2;
}

function findDeclarations($preparedCSS)
{
	$declarations = preg_grep("/^(\S+:\S+|\S+\(\S+\)?)|\S+\.+\S+;$/i", explode("\n", $preparedCSS));
	//var_dump($declarations);die();

	// remove the ;
	foreach ($declarations as $dec) {
		$declarations2[] = str_ireplace(";","",$dec);
	}
	//var_dump($declarations2); die();
	return $declarations2;
}



//var_dump($cssFiles);
?>






<?php function getFile($url,$api = null) {
	$url = $url;
	if(isset($api)) {$url .="&key=".$api;}
	$ch = curl_init($url);
	//die($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);



	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
// From http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

// from https://raw.githubusercontent.com/danielstjules/Stringy/master/src/Stringy.php
function starts_with($str,$substring, $caseSensitive = true)
    {
        $substringLength = \mb_strlen($substring);
        $startOfStr = \mb_substr($str, 0, $substringLength);

        if (!$caseSensitive) {
            $substring = \mb_strtolower($substring);
            $startOfStr = \mb_strtolower($startOfStr);
        }

        return (string) $substring === $startOfStr;
    }

function addToDeclarations($arr,$stats) {
	foreach ($arr as $declaration) {
		if(!in_array($declaration,$stats)) {
			$stats[] = $declaration;
		}
	}

	return $stats;
		//var_dump($stats["declarations"]);die();
}

// https://gist.github.com/alexkingorg/2158428
// Sort hex color values
function cf_sort_hex_colors($colors) {
	//var_dump($colors); die();
	$colorParser = ColorParser::getInstance();
//die($colorParser->resolveNamedColor("white"));

	$map = array(
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
	$c = 0;
	$sorted = array();
	foreach ($colors as $color) {
		$color = $colorParser->resolveNamedColor($color);
		$color = strtolower(str_replace('#', '', $color));
		$color = strtolower(str_replace('!important', '', $color));
		if (strlen($color) == 6) {
			$condensed = '';
			$i = 0;
			foreach (preg_split('//', $color, -1, PREG_SPLIT_NO_EMPTY) as $char) {
				if ($i % 2 == 0) {
					$condensed .= $char;
				}
				$i++;
			}
			$color_str = $condensed;
		}
		$value = 0;
		foreach (preg_split('//', $color_str, -1, PREG_SPLIT_NO_EMPTY) as $char) {
			$value += intval($map[$char]);
		}
		$value = str_pad($value, 5, '0', STR_PAD_LEFT);
		$sorted['_'.$value.$c] = '#'.$color;
		$c++;
	}
	ksort($sorted);
	return $sorted;
}
?>
<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
</body>
</html>