<?php
namespace Spectroscope;

?><!DOCTYPE html>
<html>
<head>
	<title>Color Parser Test</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
	<style type="text/css">h1{display:block;}</style>
</head>
<body>
<?php




require_once"../css_color_parser.php";
require_once"../css_color_object.php";


echo "<pre>";
var_dump(CssColorParser::getInstance()->parse("hsl(299,86%,57%)"));
die();
/*
//hsl(299, 86%, 57%)

// generated HUE-based colors
$generatedHues = array();
$generatedHuesParsed = array();
for ($i=0; $i < 360; $i=$i+10) { 
	$generatedHues[] = "hsl($i,100%,80%)";
}
//var_dump($generatedHues);die();
foreach ($generatedHues as $color) {
	$color = CssColorParser::getInstance()->parse($color);
	$generatedHuesParsed[$color->hex] = $color;
	//var_dump($color);
}
?>
<pre><?php //print_r($generatedHuesParsed); ?></pre>
<h1><?php echo count($generatedHuesParsed);?> HUE-based generated colors</h1>
<?php
foreach ($generatedHuesParsed as $color) {
	//$color = CssColorParser::getInstance()->parse($color);
	?>
	<div class="colorExample" style="background-color: #<?php echo $color->hex; ?>"><em><?php echo $color->hex."<br>(".$color->display.")<br>Type: ".$color->type;?></em></div>

	<?php
}

die();

*/














 // Test suite for CssColorParser class
?>
<?php $test = CssColorParser::getInstance()->parse("#c3f");?>
<h3>HEX shorthand</h3>
<p>HEX: #cc33ff (<?php print_r($test->hex);?>)<br>
RGB: 204 51 255 (<?php print_r($test->rgb);?>)<br>
HSL: 285° 100% 60% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: #cc33ff;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>

<pre><?php print_r($test);?></pre><br>


<?php $test = CssColorParser::getInstance()->parse("#c3f42d");?>
<h3>HEX</h3>
<p>HEX: #c3f42d (<?php print_r($test->hex);?>)<br>
RGB: 195 244 45 (<?php print_r($test->rgb);?>)<br>
HSL: 75° 90% 57% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: #c3f42d;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>


<?php $test = CssColorParser::getInstance()->parse("#c3f42d!important");?>
<h3>HEX !important</h3>
<p>HEX: #c3f42d (<?php print_r($test->hex);?>)<br>
RGB: 195 244 45 (<?php print_r($test->rgb);?>)<br>
HSL: 75° 90% 57% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: #c3f42d!important;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>


<?php $test = CssColorParser::getInstance()->parse("rgb(23, 255, 125)");?>
<h3>RGB rgb(23, 255, 125)</h3>
<p>HEX: #17FF7D (<?php print_r($test->hex);?>)<br>
RGB: 23 255 125 (<?php print_r($test->rgb);?>)<br>
HSL: 146° 100% 55% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: rgb(23, 255, 125);"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("rgba(255, 0, 0, 0.3)");?>
<h3>RGBA</h3>
<p>HEX: #FF0000 (<?php print_r($test->hex);?>)<br>
RGB: 255 0 0 (<?php print_r($test->rgb);?>)<br>
HSL: 0° 100% 50% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: rgba(255, 0, 0, 0.3);"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("hsl(299, 86%, 57%)");?>
<h3>HSL</h3>
<p>HEX: #ED33F0 (<?php print_r($test->hex);?>)<br>
RGB: 237 51 240 (<?php print_r($test->rgb);?>)<br>
HSL: 299° 86% 57% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: hsl(299, 86%, 57%);"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("hsla(299, 86%, 57%, 0.732)");?>
<h3>HSLA</h3>
<p>HEX: #ED33F0 (<?php print_r($test->hex);?>)<br>
RGB: 237 51 240 (<?php print_r($test->rgb);?>)<br>
HSL: 299° 86% 57% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: hsla(299, 86%, 57%, 0.732);"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("white");?>
<h3>white</h3>
<p>HEX: #FFFFFF (<?php print_r($test->hex);?>)<br>
RGB: 255 255 255 (<?php print_r($test->rgb);?>)<br>
HSL: 0° 0% 100% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: white;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("black");?>
<h3>black</h3>
<p>HEX: #000000 (<?php print_r($test->hex);?>)<br>
RGB: 0 0 0 (<?php print_r($test->rgb);?>)<br>
HSL: 0° 0% 0% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: black;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("darkslateblue");?>
<h3>darkslateblue</h3>
<p>HEX: #483D8B (<?php print_r($test->hex);?>)<br>
RGB: 72 61 139 (<?php print_r($test->rgb);?>)<br>
HSL: 248° 39% 39% (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: darkslateblue;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("transparent");?>
<h3>transparent</h3>
<p>HEX: NULL (<?php print_r($test->hex);?>)<br>
RGB: NULL (<?php print_r($test->rgb);?>)<br>
HSL: NULL (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: transparent"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("inherit");?>
<h3>inherit</h3>
<p>HEX: NULL (<?php print_r($test->hex);?>)<br>
RGB: NULL (<?php print_r($test->rgb);?>)<br>
HSL: NULL (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: inherit;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("none");?>
<h3>none</h3>
<p>HEX: NULL (<?php print_r($test->hex);?>)<br>
RGB: NULL (<?php print_r($test->rgb);?>)<br>
HSL: NULL (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: none;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("currentColor");?>
<h3>currentColor</h3>
<p>HEX: NULL (<?php print_r($test->hex);?>)<br>
RGB: NULL (<?php print_r($test->rgb);?>)<br>
HSL: NULL (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: currentColor;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>

<?php $test = CssColorParser::getInstance()->parse("thisdoesnotexist");?>
<h3>thisdoesnotexist</h3>
<p>HEX: NULL (<?php print_r($test->hex);?>)<br>
RGB: NULL (<?php print_r($test->rgb);?>)<br>
HSL: NULL (<?php print_r($test->hsl);?>)</p>
<div class="colorExample" style="background-color: thisdoesnotexist;"></div>
<div class="colorExample" style="background-color: #<?php echo $test->hex; ?>"></div>
<pre><?php print_r($test);?></pre><br>



<?php

$testColors = array( // real color examples from http://www.dr.dk
	"#000",
	"#4d4d4d",
	"#fff",
	"#666",
	"#FFF!important",
	"#fddd00!important",
	"#bbb",
	"#333",
	"#484848",
	"#343434",
	"#CCC",
	"#FFF",
	"#b3b2b2",
	"#7f7f7f",
	"rgba(0,0,0,.5)",
	"#2f2f2f",
	"#040707",
	"#E3E3E3",
	"#808080",
	"#777",
	"black",
	"white",
	"#eaeaea",
	"#BBB",
	"inherit",
	"#999",
	"#800",
	"#676767",
	"#ccc",
	"#00AEDE!important",
	"#38b4ef",
	"#01173e",
	"none",
	"rgba(0,0,0,0)",
	"#ef3f23",
	"#06c",
	"#cd3300",
	"#ffd703",
	"#6A6A6A",
	"#9c9c9c",
	"#06C",
	"#e6e6e6",
	"#CD3300",
	"#7d7d7d",
	"#a6a6a6",
	"#e69980",
	"#80b3e6",
	"#004884",
	"#fd0",
	"#ff1e00",
	"#6c6a70",
	"#4f4f4f",
	"#4F4F4F",
	"#fddd00",
	"#00bab8",
	"#a2faf9",
	"#C00",
	"#000",
	"#fff",
	"#F00",
	"#158647",
	"#f00",
	"#424242",
	"#16adfe",
	"#f57e1a",
	"transparent",
	"#bdbdbd",
	"#FFF",
	"#333",
	"black",
	"#e6e6e6",
	"#989898",
	"#ccc",
	"#999",
	"#E3E3E3",
	"#808080",
	"rgba(0,0,0,.5)",
	"rgba(0,0,0,.75)",
	"#E6E6E6",
	"rgba(0,0,0,.1)",
	"#CCC",
	"rgba(0,0,0,.2)",
	"rgba(0,0,0,.4)",
	"#DCDCDC",
	"#bfbfbf",
	"rgba(0,0,0,.25)",
	"#7f7f7f",
	"rgba(255,255,255,.5)",
	"white",
	"#f2f2f2",
	"#E5E5E5",
	"#e5e5e5",
	"#df0d8e",
	"#bcbcbc",
	"#3f3f3f",
	"#00c026",
	"#b2b2b2",
	"#343434",
	"#3b5998",
	"#00cefd",
	"rgba(200,200,200,.3)",
	"transparent!important",
	"#1a1a1a",
	"#38b4ef",
	"#0076ae",
	"#ff7522",
	"#b32722",
	"#723280",
	"#a1bc28",
	"#df0a78",
	"#22c8d1",
	"#8e8a76",
	"#f5d040",
	"#234263",
	"#ef801a",
	"#133d49",
	"none",
	"#2A2A2A",
	"#666",
	"#ef3f23",
	"inherit",
	"#96a4a5",
	"rgba(200,205,210,.7)",
	"#3dbded",
	"#cd3300",
	"#CD3300",
	"#ffd800",
	"#06c",
	"#727272",
	"#acacac",
	"#262626",
	"#3a3a3a",
	"#a6a6a6",
	"#661900",
	"#992600",
	"#e69980",
	"#036",
	"#004c99",
	"#80b3e6",
	"#6c6a70",
	"#dddcde",
	"#0d3945",
	"#cfcfcf",
	"rgba(0,0,0,.65)",
	"#fddd00",
	"#5da4df",
	"#555",
	"hsl(299,86%,57%)",
);
$parsedColors = array();
foreach ($testColors as $color) {
	$color = CssColorParser::getInstance()->parse($color);
	$parsedColors[$color->hex] = $color;
}
?>
<h1><?php echo count($parsedColors);?> unique colors</h1>
<?php










//var_dump($parsedColors);
?>
<h1><?php echo count($testColors);?> Test Colors (unsorted)</h1>
<?php
foreach ($parsedColors as $color) {
	//$color = CssColorParser::getInstance()->parse($color);
	?>
	<div class="colorExample" style="background-color: #<?php echo $color->hex; ?>"><em><?php echo $color->hex."<br>(".$color->display.")<br>Type: ".$color->type;?></em></div>

	<?php
}

$sorted = (CssColorParser::getInstance()->sortGreys($parsedColors));
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
}
?>
</body>
</html>