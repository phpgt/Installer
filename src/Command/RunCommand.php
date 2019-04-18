<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;

class RunCommand extends Command {
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

	public function getName():string {
		return "run";
	}

	public function getDescription():string {
		return "Start a local server, cron and build runner";
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		return [];
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		return [];
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