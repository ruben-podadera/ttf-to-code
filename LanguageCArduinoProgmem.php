<?php

/*

// font height = 12
// font bpp = 2
// characters = N
// ASCII offset = 13

struct {
    uint8_t width;
    uint8_t height;
    uint8_t y_offset;
    uint16_t bitmap_pos;
    uint16_t bitmap_len;
} font_atlas [N] PROGMEM = 
{
    {5,0,2},
    {5,0,3}
};

const char font_bitmap[] PROGMEM = {0x00, 0x01, 0x02};    

*/

class LanguageCArduinoProgmem extends LanguageBase {
	public function render(array $characters) {
		$bitmap_data = array();
		
		$max_top = 0;
		$chars = array();

		foreach ($characters as $character) {
			$bitmap_offset = count($bitmap_data);
			$bitmap_length = count($character->getBitmap());
			$bitmap_data = array_merge($bitmap_data, $character->getBitmap());

			$box = $character->getBox();

			

			$max_top = max($max_top, $box["top"]);

			$chars[] = array(
				"bitmap_offset" => $bitmap_offset,
				"bitmap_length" => $bitmap_length,

				"char" => $character->getChar(),
				"width" => $box["width"],
				"top" => $box["top"],
				"height" => $box["height"]
			);
		}

		
		$height = 0;
		foreach ($chars as $char) {
			$height = max($height, $char["height"] + $max_top-$char["top"]);
		}

		$name = $this->getVariableName();

		$chars = array_map(function($char) use($height, $max_top) {
			return "/* ".$char["char"]." */ ".
				"{ " . 
					$char["width"]. ", " . 
					$char["height"]. ", " . 
					($max_top-$char["top"]) . ", " .
					$char['bitmap_offset'] . ", ".
					$char['bitmap_length'] .
				" }" ;
		}, $chars);

		$bitmap_str = join(
			", ", 
			array_map(
				function($val){
					return sprintf("0x%02s", base_convert($val, 10, 16));
				}, 
				$bitmap_data
			)
		);

		$ascii_offset = ord($characters[0]->getChar());


		return 
"// font height = ".($height )."
// font bpp = ".$this->ttf_to_code->getBitsPerPixel()."
// characters = ".count($chars)."
// ASCII offset = ".$ascii_offset."

const struct font_char_t {
    uint8_t width;
    uint8_t height;
    uint8_t y_offset;
    uint16_t bitmap_pos;
    uint16_t bitmap_len;
} ".$name."_atlas [".count($chars)."] PROGMEM = 
{
    ".join(",\n    ", $chars)."
};

const char ".$name."_bitmap[] PROGMEM = {".$bitmap_str."};

";
		
	}
}