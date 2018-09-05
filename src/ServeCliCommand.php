<?php
namespace Gt\Installer;

class ServeCliCommand extends CliCommand {
	public function __construct() {
		parent::__construct();
		$this->setName("serve");
	}

	public function run(CliArgumentList $arguments): void {
		// TODO: Implement run() method.
	}
}