<?php
namespace Gt\Installer;

class ServeCliCommand extends CliCommand {
	public function __construct() {
		$this->setName("serve");
	}
}