<?php
namespace Gt\Installer;

class ServeCliCommand extends CliCommand {
	public function __construct() {
		$this->setName("serve");
		$this->setDescription("Start a local webserver for the current project.");
	}

	public function run(CliArgumentValueList $arguments): void {
		// TODO: Implement run() method.
	}
}