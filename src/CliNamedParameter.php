<?php
namespace Gt\Installer;

class CliNamedParameter {
	protected $optionName;

	public function __construct(string $optionName) {
		$this->optionName = $optionName;
	}

	public function getOptionName():string {
		return $this->optionName;
	}
}