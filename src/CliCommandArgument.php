<?php
namespace Gt\Installer;

class CliCommandArgument extends CliArgument {
	public function __construct(string $commandName) {
		parent::__construct("", $commandName);
	}

	protected function processRawKey(string $rawKey): string {
		return "";
	}
}