<?php
namespace Gt\Installer;

class CliLongOptionArgument extends CliArgument {
	protected function processRawKey(string $rawKey):string {
		return substr($rawKey, 2);
	}
}