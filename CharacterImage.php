<?php

class CharacterImage {

	protected $char;

	protected $ttf_to_code;

	protected $box;

	protected $bitmap;

	public function __construct($char, TtfToCode $ttf_to_code) {
		$this->char = $char;
		$this->ttf_to_code = $ttf_to_code;
	}

	public function getChar() {
		return $this->char;
	}

	public function getBox()
	{
		if (!$this->box) {
			$this->box = $this->calculateTextBox();
		}
		return $this->box;
	}

	public function getBitmap()
	{
		if (!$this->bitmap) {
			$this->calculateBitmapData();	
			$this->groupPerPixel();
		}

		return $this->bitmap;
	}

	protected function groupPerPixel() {
		$bpp = $this->ttf_to_code->getBitsPerPixel();
		
		if ($bpp == 8) {
			return;
		}

		$divider_map = array(
			1 => 255, // 8 
			2 => 63,  // 4
			4 => 15,  // 2
			8 => 1 	  // 1 
		);
		$divider = $divider_map[$bpp];

		$groupeds = array();
		$val = 0;
		
		for ($pixel=0; $pixel < count($this->bitmap); $pixel +=  (8 / $bpp)) { 
			$group_val = 0;
			for($sub_pixel = 0; $sub_pixel < (8 / $bpp); $sub_pixel++) {
				$sub_pixel_val = $pixel + $sub_pixel >= count($this->bitmap) ?
					0 : // padding
					$this->bitmap[$pixel + $sub_pixel];
				
				
				$sub_pixel_val = min(
					round($sub_pixel_val / $divider),
					$bpp == 1 ? 1 : (pow(2, $bpp) - 1)
				);

				
				$group_val |= $sub_pixel_val << (8 - ($sub_pixel +1) * $bpp);
			}

			$groupeds[] = $group_val;
		}

		$this->bitmap = $groupeds;
	}

	protected function calculateBitmapData() {

		$box = $this->getBox();

		$im = imagecreatetruecolor($box["width"], $box["height"]);
		$black = imagecolorallocate($im, 0, 0, 0);
		$white = imagecolorallocate($im, 255, 255, 255);

		imagefilledrectangle($im, 0, 0, $box["width"], $box["height"], $black);

		imagettftext($im, $this->ttf_to_code->getFontSize(), 0,
			 $box["left"], $box["top"], 
			 $white, $this->ttf_to_code->getFontFile(), $this->char);

		$this->bitmap = array();
		for( $y = 0; $y < $box["height"]; $y++ ) {
			for( $x = 0; $x < $box["width"]; $x++ ) {
				$rbg = imagecolorat( $im, $x, $y );
				$val = $rbg & 0xFF;
				$this->bitmap[] = $val;
			}
		}

		imagedestroy($im);
		
	}

	protected function calculateTextBox() { 
		$box   = imagettfbbox($this->ttf_to_code->getFontSize(), 0, $this->ttf_to_code->getFontFile(), $this->char); 
		if( !$box ) 
			return false; 
		$min_x = min( array($box[0], $box[2], $box[4], $box[6]) ); 
		$max_x = max( array($box[0], $box[2], $box[4], $box[6]) ); 
		$min_y = min( array($box[1], $box[3], $box[5], $box[7]) ); 
		$max_y = max( array($box[1], $box[3], $box[5], $box[7]) ); 
		$width  = ( $max_x - $min_x ); 
		$height = ( $max_y - $min_y ); 
		$left   = abs( $min_x ) + $width; 
		$top    = abs( $min_y ) + $height; 
		// to calculate the exact bounding box i write the text in a large image 
		$img     = @imagecreatetruecolor( $width << 2, $height << 2 ); 
		$white   =  imagecolorallocate( $img, 255, 255, 255 ); 
		$black   =  imagecolorallocate( $img, 0, 0, 0 ); 
		imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black); 
		// for sure the text is completely in the image! 
		imagettftext( $img, $this->ttf_to_code->getFontSize(), 
		            0, $left, $top, 
		            $white, $this->ttf_to_code->getFontFile(), $this->char); 
		// start scanning (0=> black => empty) 
		$rleft  = $w4 = $width<<2; 
		$rright = 0; 
		$rbottom   = 0; 
		$rtop = $h4 = $height<<2; 
		for( $x = 0; $x < $w4; $x++ ) 
			for( $y = 0; $y < $h4; $y++ ) 
				if( imagecolorat( $img, $x, $y ) ){ 
					$rleft   = min( $rleft, $x ); 
					$rright  = max( $rright, $x ); 
					$rtop    = min( $rtop, $y ); 
					$rbottom = max( $rbottom, $y ); 
				} 
		// destroy img and serve the result 
		imagedestroy( $img ); 
		return array(
			"left"   => $left - $rleft, 
            "top"    => $top  - $rtop, 
            "width"  => $rright - $rleft + 1, 
            "height" => $rbottom - $rtop + 1
        ); 
	} 
}