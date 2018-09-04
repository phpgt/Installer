<?php
namespace Gt\Installer;

class CliValueArgument extends CliArgument {
	public function __construct($value) {
		parent::__construct("", $value);
	}

	protected function processRawKey(string $rawKey): string {
		return "";
	}
}