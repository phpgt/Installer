<?php
namespace Gt\Installer;

class CliOption {
	protected $requireValue;
	protected $longOption;
	protected $shortOption;

	public function __construct(
		bool $requireValue,
		string $longOption,
		string $shortOption
	) {
		$this->requireValue = $requireValue;
		$this->longOption = $longOption;
		$this->shortOption = $shortOption;
	}
}