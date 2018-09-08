<?php
namespace Gt\Installer;

class CliNamedParameter extends CliParameter {
	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct(string $optionName) {
		$this->longOption = $optionName;
	}

	public function getOptionName():string {
		return $this->longOption;
	}
}