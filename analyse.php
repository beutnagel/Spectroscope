<?php
    namespace Spectroscope;

    // register to file the timestamp for this operation
    include("register_operation.php");

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


use Danmichaelo\Coma\ColorDistance,
    Danmichaelo\Coma\sRGB;

$color1 = new sRGB(255, 255, 255);
$color2 = new sRGB(73, 187, 187);
$color3 = new sRGB(1, 255, 255);
$color4 = new sRGB(255, 0, 255);
?>
<div style="display:block;width: 10px;height: 10px;background-color: rgb(255,255,255);"></div>
<div style="display:block;width: 10px;height: 10px;background-color: rgb(73, 187, 187);"></div>
<div style="display:block;width: 10px;height: 10px;background-color: rgb(1, 255, 255);"></div>
<div style="display:block;width: 10px;height: 10px;background-color: rgb(255, 0, 255);"></div>
<?php

$cd = new ColorDistance;
$cie94 = $cd->cie94($color1, $color2);
echo 'The CIE94 ∆E is ' . $cie94 . ' between ' . $color1->toHex() . ' and ' . $color2->toHex() . '.<br><br>';

$cd = new ColorDistance;
$cie94 = $cd->cie94($color1, $color3);
echo 'The CIE94 ∆E is ' . $cie94 . ' between ' . $color1->toHex() . ' and ' . $color3->toHex() . '.<br><br>';

$cd = new ColorDistance;
$cie94 = $cd->cie94($color1, $color4);
echo 'The CIE94 ∆E is ' . $cie94 . ' between ' . $color1->toHex() . ' and ' . $color4->toHex() . '.<br><br>';


$cd = new ColorDistance;
$cie76 = $cd->cie76($color1, $color2);
echo 'The CIE76 ∆E is ' . $cie76 . ' between ' . $color1->toHex() . ' and ' . $color2->toHex() . '.<br><br>';


$colors = [
    new sRGB(102,   102,    102),
    new sRGB(73,    187,    187),
    new sRGB(14,    233,    1),
    new sRGB(217,   89,     142),
    new sRGB(2,     5,      19),
    new sRGB(88,    130,    29),
    new sRGB(156,   0,      210),
    new sRGB(45,   10,      210),
    new sRGB(24,   10,      110),
    new sRGB(46,   10,      190),
    new sRGB(7,   10,      170),
    new sRGB(156,   40,      100),
];
$results = array();
$cd = new ColorDistance;
$white = new sRGB(255,0,255);
foreach ($colors as $color) {
    //$color = new sRGB($color);
    $diff = $cd->cie76($white,$color);
    $results[] = [
        "difference"    =>  $diff,
        "color"         =>  $color->toHex(), 
        "light-diff"    =>  $cd->cie76(new sRGB(255,255,255),$color),
        "dark-diff"     =>  $cd->cie76(new sRGB(0,0,0),$color),
        "red-diff"      =>  $cd->cie76(new sRGB(255,0,0),$color),
        "green-diff"    =>  $cd->cie76(new sRGB(0,255,0),$color),
        "blue-diff"     =>  $cd->cie76(new sRGB(0,0,255),$color),
       ];
     ?><br><div style="display:block;width: 30px;height: 30px;background-color: <?php echo $color->toHex();?>;"></div><?php
   echo "The color ".$color->toHex()."is ".round(($color->r/255)*100,2)."% red<br><br>";
    # code...
   //var_dump($color->r);
}
//asort($results);
var_dump($results);
foreach ($results as $result) {
    ?><div style="display:block;width: 30px;height: 30px;background-color: <?php echo $result["color"];?>;"></div><?php
}


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
    </header><pre>
    <?php


$url = "testserver.dev";
        //$url = "dr.dk";
//$url = "https://abetterhealthcareplan.com/";

        $parser = new CssParser();
        $result = $parser->analyse($url);

        var_dump($result);die();
        echo "Files: ".count($result["files"]) ."<br>";
       // var_dump($result);die();
       // var_dump(count($result["files"]));
        foreach ($result["files"] as $name => $file) {
            //var_dump($file["selectors"]);
            echo "<br>File name: ". $name . "<br>";
            echo "Selectors: ". count($file["selectors"]) ."<br>";
            echo "Declarations: ". count($file["declarations"]) ."<br>";
            # code...
        }





















    ?>
        </pre>
</body>
</html>
