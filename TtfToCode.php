<?php

class TtfToCode {

	protected $font_file;

	protected $font_size;

	protected $bits_per_pixel;

	protected $language;

	protected $range_from;
	protected $range_to;

	protected $font;

	public function getFontFile() {
		return $this->font_file;
	}

	public function setFontFile($font_file) {
		$this->font_file = $font_file;
	}

	public function getFontSize() {
		return $this->font_size;
	}

	public function setFontSize($font_size) {
		$this->font_size = $font_size;
	}

	public function getBitsPerPixel() {
		return $this->bits_per_pixel;
	}

	public function setBitsPerPixel($bits_per_pixel) {
		$this->bits_per_pixel = $bits_per_pixel;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function setLanguage($language) {
		$this->language = LanguageFactory::createLanguage($language, $this);
	}

	public function setCharRange($from, $to) {
		$this->range_from = $from;
		$this->range_to = $to;
	}

	public function getCharString() {
		$str = "";
		for ($i = $this->range_from; $i <= $this->range_to; $i++ ) {
			$str .= chr($i);
		}
		return $str;
	}

	public function process() {
		$chars = $this->getCharString();
		$this->font = array();

		for($i = 0; $i < strlen($chars); $i++) {
			$this->font[] = new CharacterImage($chars[$i], $this);
		}

		return $this->getLanguage()->render($this->font);

	}

	public function getFontAsInlineImage() {
		$box = imagettfbbox($this->getFontSize(), 0, $this->getFontFile(), $this->getCharString());
		$w = abs($box[6] - $box[2]);
		$h = abs($box[7] - $box[1]);

		$im = imagecreatetruecolor($w, $h);
		$black = imagecolorallocate($im, 0, 0, 0);
		$white = imagecolorallocate($im, 255, 255, 255);

		imagefilledrectangle($im, 0, 0, $w, $h, $white);

		imagettftext($im, $this->getFontSize(), 0, 0, $h-$box[1], $black,  $this->getFontFile(), $this->getCharString());

		imagetruecolortopalette ( $im , false , pow(2, $this->getBitsPerPixel()) );

		ob_start (); 
		//header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);

		$image_data = ob_get_contents (); 
		ob_end_clean (); 
		
		return "data:image/png;base64," . base64_encode ($image_data) ;
	}
	


}