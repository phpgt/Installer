<?php
namespace Gt\Installer;

class CliShortOptionArgument extends CliArgument {
	protected function processRawKey(string $rawKey):string {
		return substr($rawKey, 1);
	}
}