<?php
namespace Gt\Installer;

class CreateCliCommand extends CliCommand {
	public function __construct() {
		parent::__construct("create");

// Here a "named" option is something that doesn't require tacks.
// For example: gt create projectname --key value
// The "projectName" is a "named" option. Needs renaming after implementation...
		$this->setRequiredArgument(
			true,
			"directory",
			"d"
		);

		$this->setRequiredValueArgument("project-name");
		$this->setRequiredValueArgument("namespace");

		$this->setOptionalArgument(
			true,
			"blueprint",
			"b"
		);
	}
}