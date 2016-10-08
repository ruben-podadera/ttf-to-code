<?php

class LanguageFactory {

	public static function createLanguage($lang, TtfToCode $ttf_to_code) {
		switch ($lang) {
			case 'c1':
				return new LanguageCArduinoProgmem($ttf_to_code);
				
			default:
				throw new Exception("Language '$lang' not handled yet");
		}
	}

}