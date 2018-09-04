<?php
namespace Gt\Installer;

class CliNamedOption {
	protected $optionName;

	public function __construct(string $option) {
		$this->optionName = $option;
	}
}