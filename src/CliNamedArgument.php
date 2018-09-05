<?php
namespace Gt\Installer;

class CliNamedArgument extends CliArgument {
	public function __construct($name) {
		parent::__construct("", $name);
	}

	protected function processRawKey(string $rawKey): string {
		return "";
	}
}