<?php
namespace Spectroscope;
    use MatthiasMullie\Minify;
class CssParser {

	/**
     * @var Singleton The reference to instance of this class
     */
    private static $instance;
    
    /**
     * Returns the instance of this class.
     *
     * @return Singleton The instance.
     */
    public static function getInstance()
    {
        if (null === CssParser::$instance) {
            CssParser::$instance = new static();
        }
        
        return CssParser::$instance;
    }



    public function __get($name) {
        return $this->$name;
    }







    public function prepareCSS($css) 
    {
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
        //echo "<textarea>".$minified."</textarea>";


        // modify structure
        $minified = str_ireplace("{", "{\n", $minified);
        $minified = str_ireplace("}", "\n}\n", $minified);
        $minified = str_ireplace(";", ";\n", $minified);
        //*echo "<textarea>".$minified."</textarea>";
        return $minified;
    }



    public function generateDom($content) 
    {
        // load dom
        include_once('htmldom.php');
        include_once('htmldomnode.php');
        //use Yangqi\Htmldom;
        $dom = new \Yangqi\Htmldom\Htmldom();

        return $dom;
    }




    public function findCssFiles($dom) {
        $cssFiles = array();
        $linkTags = $dom->find('link[rel=stylesheet]');
        //var_dump($linkTags);
        //echo "<br>antal css filer: ". count($linkTags) ."<br>";
        foreach ($linkTags as $tag) {
            //*echo $tag->rel;
            // check if rel is stylesheet (and not e.g. canonical)
            if($this->starts_with($tag->rel,"stylesheet")) {
                //*echo "stylesheet found<br>";
                //echo $tag->href."<br>";
                $cssUrl = $tag->href;
                //var_dump($this->starts_with($cssUrl,"//"));
                if($this->starts_with($cssUrl,"//")){
                    //echo "TRUE";
                    $cssUrl = "http:" . $cssUrl;
                    //var_dump($cssUrl);
                }
                else if (!$this->starts_with($cssUrl,"http")) {
                    $cssUrl = URL."/".ltrim($cssUrl,'/');
                }
                //*echo $cssUrl."<br>";
                if(!in_array($cssUrl, $cssFiles)) {
                    //*echo "save in array<br>";
                    $cssFiles[] = $cssUrl;
                } else {
                    //*echo "already in array!<br>";
                }
                echo "<p>".$cssUrl."</p><br>";
                ob_flush();
                flush();
                //sleep(3);
            }
        }
        return $cssFiles;
    }



    public function findInlineStyling($dom)
    {
        $styles = array();
        $inline = $dom->find('*[style]');
        if(!empty($inline)){
            foreach ($inline as $tag) {
                $styles[] = $tag->style;
            }
        }
        return $styles;
    }

    public function findEmbeddedStyling($dom)
    {
        $styles = array();
        $embedded = $dom->find('style');
        if(!empty($embedded)){
            foreach ($embedded as $tag) {
                //var_dump($tag->innertext);
                $styles[] = $tag->innertext;
            }
        }
        return $styles;
    }




























    private $declarations = array();
    private $selectors = array();

    public function findSelectors($preparedCss) {
        //$preparedCSS;

        // Regex
        // ruleSets: ^(.+){
        // preg_match("/^(.+){/i", $input_line, $output_array);
        // declarations: ^(\S+:\S+|\S+\(\S+\)?)|\S+\.+\S+;$
        // preg_match("/^(\S+:\S+|\S+\(\S+\)?)|\S+\.+\S+;$/i", $input_line, $output_array);

        $selectors = $temp = array();
        $selectors = preg_grep("/^(.+){/i", explode("\n", $preparedCss));

        foreach ($selectors as $rule) { 
            //var_dump($rule);
            $rule = str_ireplace("  "," ",$rule);
            $rule = str_ireplace(" {","",$rule);
            $rule = str_ireplace("{ ","",$rule);
            $rule = str_ireplace("{","",$rule);
            $temp[] = $rule;
            //var_dump($rule);
            //echo "<br><br>";
        }
        //die();

        //var_dump($selectors);die();
        // 
        return $temp;
    }

    public function findDeclarations($preparedCSS)
    {
        $declarations = preg_grep("/^(\S+:\S+|\S+\(\S+\)?)|\S+\.+\S+;$/i", explode("\n", $preparedCSS));
        //var_dump($declarations);die();
        $temp = array();
        // remove the ;
        foreach ($declarations as $dec) {
            $temp[] = str_ireplace(";","",$dec);
        }
        //var_dump($temp); die();
        return $temp;
    }




    public function addDeclarations($arr) 
    {
      if(is_array($arr)) {
            $this->declarations = array_merge($this->declarations, $arr);
        } elseif (is_string($arr)) {
            $this->declarations[] = $arr;
        }
    }
    public function addSelectors($arr) 
    {
      if(is_array($arr)) {
            $this->selectors = array_merge($this->selectors, $arr);
        } elseif (is_string($arr)) {
            $this->selectors[] = $arr;
        }
    }



    public function getUnique($name) {
        return array_unique($this->$name);
    }





























     // from https://raw.githubusercontent.com/danielstjules/Stringy/master/src/Stringy.php
    public function starts_with($str,$substring, $caseSensitive = true)
    {
        $substringLength = \mb_strlen($substring);
        $startOfStr = \mb_substr($str, 0, $substringLength);

        if (!$caseSensitive) {
            $substring = \mb_strtolower($substring);
            $startOfStr = \mb_strtolower($startOfStr);
        }

        return (string) $substring === $startOfStr;
    }





    // From http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
    public function human_filesize($bytes, $decimals = 2) 
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }



    public function getFile($url) 
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    

} // end class