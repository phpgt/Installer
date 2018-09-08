<?php
namespace Gt\Installer;

class CreateCliCommand extends CliCommand {
	public function __construct() {
		$this->setName("create");
		$this->setDescription("Set up a new WebEngine project.");

		$this->setRequiredNamedParameter("name");
		$this->setOptionalNamedParameter("namespace");


		$this->setOptionalParameter(
			true,
			"blueprint",
			"b",
			"blueprint_name"
		);
	}

	public function run(CliArgumentValueList $arguments):void {
		$cwd = getcwd();

		$dir = $arguments->get("name");
		$this->checkDirectoryNotExists($dir);

		$fullPath = implode(DIRECTORY_SEPARATOR, [
			$cwd,
			$dir,
		]);
		$this->streams->writeLine(
			"Creating new project in: $fullPath ..."
		);
	}

	protected function checkDirectoryNotExists(string $dir):void {
		if(is_dir($dir)) {
			die("TODO: Installer-specific (Non-CLI) exception");
		}
	}
}