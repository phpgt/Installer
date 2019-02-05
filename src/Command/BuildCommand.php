<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Stream;

class BuildCommand extends Command {
	public function __construct() {
		$this->setName("build");
		$this->setDescription(
			"Build the client-side files"
		);
	}

	public function run(ArgumentValueList $arguments = null):void {
		$gtBuildCommand = implode(DIRECTORY_SEPARATOR, [
			"vendor",
			"bin",
			"gt-build",
		]);

		if(!file_exists($gtBuildCommand)) {
			$this->writeLine(
				"The current directory is not a WebEngine application.",
				Stream::ERROR
			);
			return;
		}

		$cmd = implode(" ", [
			$gtBuildCommand,
		]);
		passthru($cmd);
	}
}