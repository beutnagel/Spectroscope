<?php
namespace Spectroscope;
class CssColorObject {

	private $hex, $rgb, $hsl, $display, $alpha, $type, $important;
	public function __construct($color = array()) {
		if(	isset($color["hex"]) &&
			isset($color["rgb"]) &&
			isset($color["hsl"]) &&
			isset($color["display"]) &&
			isset($color["alpha"]) &&
			isset($color["type"]) &&
			isset($color["important"]) 
			) {

				$this->hex = $color["hex"];
				$this->rgb = $color["rgb"];
				$this->hsl = $color["hsl"];
				$this->display = $color["display"];
				$this->alpha = $color["alpha"];
				$this->type = $color["type"];
				$this->important = $color["important"];
		} else {
			echo "Error! Color object missing parameters";
			var_dump($color);
		}
		return 	$this;
	}


	public function __get($name) {

        //echo "Get:$name";
        return $this->$name;
    }






}


