<?php
namespace Gt\Installer;

class CreateCliCommand extends CliCommand {
	public function __construct() {
		parent::__construct();

		$this->setName("create");
		$this->setDescription("Set up a new WebEngine project.");

		$this->setRequiredNamedParameter("project-name");
		$this->setRequiredNamedParameter("namespace");
		$this->setOptionalNamedParameter("thingy");

		$this->setRequiredParameter(
			true,
			"directory",
			"d"
		);


		$this->setOptionalParameter(
			true,
			"blueprint",
			"b"
		);
	}

	public function run(CliArgumentList $arguments):void {

	}
}