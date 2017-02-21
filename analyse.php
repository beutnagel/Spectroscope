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
    <?php


$url = "testserver.dev";

        $parser = new CssParser();
        var_dump($parser);
        $parser->analyse($url);





















    ?>
</body>
</html>
