<?php

namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Stream;

class ServeCommand extends Command {
	public function __construct() {
		$this->setName("serve");
		$this->setDescription(
			"Run a local HTTP server"
		);

		$this->setOptionalParameter(
			true,
			"port",
			"p",
			"8080"
		);
	}

	public function run(ArgumentValueList $arguments = null):void {
		$gtServeCommand = implode(DIRECTORY_SEPARATOR, [
			"vendor",
			"bin",
			"gt-serve",
		]);

		if(!file_exists($gtServeCommand)) {
			$this->writeLine(
				"The current directory is not a WebEngine application.",
				Stream::ERROR
			);
			return;
		}

		$cmd = implode(" ", [
			$gtServeCommand,
			"--port " . $arguments->get("port")
		]);
		passthru($cmd, $returnVar);
		exit($returnVar);
	}
}