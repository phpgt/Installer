<?php

namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;
use Gt\Daemon\Process;

class ServeCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$gtServeCommand = implode(DIRECTORY_SEPARATOR, [
			"vendor",
			"bin",
			"serve",
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
			"--port " . $arguments->get("port", 8080)
		]);

		$process = new Process($cmd);
		$process->exec();

		while($process->isRunning()) {
			$this->output->write($process->getOutput());
		}
	}

	public function getName():string {
		return "serve";
	}

	public function getDescription():string {
		return "Run a local development HTTP server";
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		return [];
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		return [
			new Parameter(
				true,
				"port",
				"p"
			),
		];
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		return [];
	}
}