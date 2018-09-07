<?php
namespace Gt\Installer;

class BuildCliCommand extends CliCommand {
	public function __construct() {
		parent::__construct();
		$this->setName("build");
		$this->setDescription("Compile all client-side assets.");
	}

	public function run(CliArgumentList $arguments): void {
		// TODO: Implement run() method.
	}
}