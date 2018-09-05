<?php
namespace Gt\Installer;

class CreateCliCommand extends CliCommand {
	public function __construct() {
		$this->setName("create");

		$this->setRequiredValueParameter("project-name");
		$this->setRequiredValueParameter("namespace");
		$this->setOptionalValueParameter("thingy");

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
}