<?php

abstract class LanguageBase {

	protected $ttf_to_code;

	public function __construct(TtfToCode $ttf_to_code) {
		$this->ttf_to_code = $ttf_to_code;
	}

	abstract public function render(array $characters);

	public function getVariableName()
	{

		$path_parts = pathinfo($this->ttf_to_code->getFontFile());
		$name = $path_parts['filename'];
		$name = mb_ereg_replace("([^\w\s\d\_~,;\[\]\(\).])", '_', $name);
		$name = strtolower($name);

		$name .= "_" . $this->ttf_to_code->getFontSize() . "pt";

		return $name;
	}
}