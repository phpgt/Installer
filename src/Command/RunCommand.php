<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Stream;

class RunCommand extends Command {
	public function __construct() {
		$this->setName("run");
		$this->setDescription(
			"Start a local server, cron and build runner"
		);
	}

	public function run(ArgumentValueList $arguments = null):void {
		$gtRunCommand = implode(DIRECTORY_SEPARATOR, [
			"vendor",
			"bin",
			"gt-run",
		]);

		if(!file_exists($gtRunCommand)) {
			$this->writeLine(
				"The current directory is not a WebEngine application.",
				Stream::ERROR
			);
			return;
		}

		$cmd = implode(" ", [
			$gtRunCommand,
		]);
		passthru($cmd);
	}
}