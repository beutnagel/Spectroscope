<?php
namespace Spectroscope;
    /** Dependencies
     *  \MatthiasMullie\Minify
     *  \Sunra\PhpSimple\HtmlDomParser
    */


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

    /*
    HOW TO KEEP LINENUMBERS?
    1) Prepare source
        go through source files and change all linebreaks to ⚜️(number)⚜️, e.g. ⚜️1⚜️
    2) Prepare CSS
        prepare the CSS but keep the ⚜️(number)⚜️ in there
    3) Analyse the CSS
        make the analyser ignore ⚜️(number)⚜️
        look up ⚜️(number)⚜️ whenever need to output a line number
    */


/**
 * Class CssParser
 * @package Spectroscope
 */
/**
 * Class CssParser
 * @package Spectroscope
 */
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



    private $page_url;  // The URL of the page being analysed
    private $result;    // All the results from analysing



    /*
     * ---------------------------------------------------------------------------------------------------------------------
     *      Initiate
     * ---------------------------------------------------------------------------------------------------------------------
     */


    /**
     * Main entry point for the class. Determines whether 1subjec is a URL or a string of CSS
     * @param URL|CSS $subject
     */
    public function analyse($subject)
    {
        //  first check if CSS, does it contain a {
        if(strpos($subject,":")!==false) {
            $this->analyseCSS($subject);
        } else {
            $this->analyseUrl($subject);
        }
    }


        /**
         * @param $url
         */
        private function analyseUrl($url)
        {
            echo "analysing a URL";

            // TODO handle http/https
            $this->page_url = $url;


            $page = $this->getFile($url);
            $dom = $this->generateDom($page);
            $files = $this->findCssFiles($dom);
            var_dump($files);
            foreach($files as $file) {
                $this->result["files"][$file]["prepared_css"] = $this->prepareCSS($this->getFile($file));
            }
            var_dump($this->result);
        }


        /**
         * @param $css
         */
        private function analyseCSS($css)
        {
            echo "analysing CSS";
            $css = $this->prepareCSS($css);
        }





        /**
         * Prepare the CSS for the analyser by minimizing and ensuring uniformity in format
         * @param $css
         *
         * @return string preparedCss
         */
    private function prepareCSS($css)
    {
        // 1) Minify the css as to remove all coments and ensure uniformity in format
        $minifier = new \MatthiasMullie\Minify\CSS($css);
        $minified = $minifier->minify();

        // 2) Modify structure by adding linebreaks again
        $minified = str_ireplace("{", "{\n", $minified);    // make { stand on own line
        $minified = str_ireplace("}", "\n}\n", $minified);  // make double line break to next rule
        $minified = str_ireplace(";", ";\n", $minified);    // make each rule be on its onw line
        return $minified;
    }


        /**
         * @param $content string HTML
         *
         * @return \simplehtmldom_1_5\simple_html_dom
         */
    private function generateDom($content)
    {
        $dom = \Sunra\PhpSimple\HtmlDomParser::str_get_html( $content );
        if(is_object($dom)) {
            return $dom;
        } else {
            // TODO Add error handling
            echo "ERROR: NO DOM OBJECT";
        }
    }


        /**
         * Finds all CSS file references on page
         * @param $dom
         *
         * @return array list of css file urls
         */
    private function findCssFiles($dom) {
        $cssFiles = array();
        $linkTags = $dom->find('link[rel=stylesheet]');

        foreach ($linkTags as $tag) {
            // check if rel is stylesheet (and not e.g. canonical)
            if($this->starts_with($tag->rel,"stylesheet")) {
                echo "stylesheet found<br>";
                echo $tag->href."<br>";
                $cssUrl = $tag->href;

                // make absolute url of hrefs starting with //
                if($this->starts_with($cssUrl,"//")){
                    $cssUrl = "http:" . $cssUrl;
                }
                // add site url to relative files
                else if (!$this->starts_with($cssUrl,"http")) {
                    $cssUrl = $this->page_url."/".ltrim($cssUrl,'/');
                }

                // add to list of files
                if(!in_array($cssUrl, $cssFiles)) {
                    $cssFiles[] = $cssUrl;
                }
            }
        }
        return $cssFiles;
    }


        /**
         * @param $dom
         *
         * @return array
         */
    private function findInlineStyling($dom)
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

        /**
         * @param $dom
         *
         * @return array
         */
    private function findEmbeddedStyling($dom)
    {
        $styles = array();
        $embedded = $dom->find('style');
        if(!empty($embedded)){
            foreach ($embedded as $tag) {
                $styles[] = $tag->innertext;
            }
        }
        return $styles;
    }


        /**
         * @var array
         */
        private $declarations = array();
        /**
         * @var array
         */
        private $selectors = array();

        /**
         * @param $preparedCss
         *
         * @return array
         */
    private function findSelectors($preparedCss) {
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

        /**
         * @param $preparedCSS
         *
         * @return array
         */
    private function findDeclarations($preparedCSS)
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


        /**
         * @param $arr
         */
        private function addDeclarations($arr)
    {
      if(is_array($arr)) {
            $this->declarations = array_merge($this->declarations, $arr);
        } elseif (is_string($arr)) {
            $this->declarations[] = $arr;
        }
    }

        /**
         * @param $arr
         */
        private function addSelectors($arr)
    {
      if(is_array($arr)) {
            $this->selectors = array_merge($this->selectors, $arr);
        } elseif (is_string($arr)) {
            $this->selectors[] = $arr;
        }
    }


        /**
         * @param $name
         *
         * @return array
         */
    private function getUnique($name) {
        return array_unique($this->$name);
    }
























/*
 * ---------------------------------------------------------------------------------------------------------------------
 *      HELPER FUNCTIONS
 * ---------------------------------------------------------------------------------------------------------------------
 */




     // from https://raw.githubusercontent.com/danielstjules/Stringy/master/src/Stringy.php
        /**
         * @param           $str
         * @param           $substring
         * @param bool|true $caseSensitive
         *
         * @return bool
         */
    private function starts_with($str,$substring, $caseSensitive = true)
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
        /**
         * @param     $bytes
         * @param int $decimals
         *
         * @return string
         */
    private function human_filesize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }


        /**
         * @param $url
         *
         * @return mixed
         */
    private function getFile($url)
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


    /**
     * @param $selectors
     *
     * @return array
     */
    private function sortByParent($selectors)
    {
        //$selectors = array("h1 a","h1 p","h1 a, h1 p");
        $selector_parents = array();
        foreach($selectors as $selector) {
            echo "\n\n### ".$selector."\n";
    // TODO: move the following into functions, add loop to go through all multipart selectors
            // determine if multipart (e.g. h2 em, h3 em)
            $multi = array();
            // split multiple selectors into individual ones, based upon ,
            if(strpos($selector,",")>0) {
                $arr = explode(",",$selector);
                foreach ($arr as $item) {
                    $multi[] = trim($item); // trim to remove any whitespace, e.g. " h1 a"
                }
               // echo "is multi";
                $isMulti = true;
            } else {
                $isMulti = false;
                $multi[] = $selector;
            }
            foreach ($multi as $multi_selector) {
                // determine parent
                $parent = $this->determineParent($multi_selector); 

                //echo "\n Testing: ".$multi_selector."\n";
                // determine pseudo selectors for parent (e.g. .btn:active belongs to .btn parent)
                $pseudo = null;
                if (strpos($parent, ":") > 0) {
                    $pseudo = strstr($parent, ":");
                    $parent = str_ireplace($pseudo, "", $parent);
                }
/*                echo "WOW: ";
                var_dump($selector_parents[ $parent ][$multi_selector]);
                var_dump(!is_null($selector_parents[ $parent ][$multi_selector]["selector"]));
*/                ///var_dump($parent);
                if (!isset($selector_parents[ $parent ])) {
                    echo "\nWas new parent";
                    $selector_parents[ $parent ][$multi_selector] = array("selector"=>$multi_selector,"count"=>1);
                } else {
                    if (is_null($selector_parents[ $parent ][$multi_selector]["selector"])) {
                        echo "\nNew selector added to parrent";
                        //var_dump(in_array($multi_selector,$selector_parents[ $parent ]));
                        $selector_parents[ $parent ][$multi_selector] = array("selector"=>$multi_selector,"count"=>1);
                        ///var_dump($selector_parents[ $parent ]);
                    } else {
                        echo "selector already existed under parent\n";
                        //die("here");
                        //echo "hmm, it does not update the count...";
                        $selector_parents[ $parent ][$multi_selector]["count"]++;
                    }
                    //var_dump(in_array($multi_selector,$selector_parents[ $parent ]));

                }
                //$selector_parents[ $parent ][$multi_selector]["count"]++;
            }
        }
        arsort($selector_parents);
        var_dump($selector_parents);
        return $selector_parents;
    }


    /**
     * @param $selector
     *
     * @return string
     */
    private function determineParent($selector)
    {
        // if selector has a " " use the first part as parent
        if (strpos($selector, " ") > 0) {
            $parent = strstr($selector, " ", true);
        } else {
            // if there is only one selector
            $parent = $selector;
        }
        return $parent;
    }
} // end class