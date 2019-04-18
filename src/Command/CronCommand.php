<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;

class CronCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$gtCronCommand = implode(DIRECTORY_SEPARATOR, [
			"vendor",
			"bin",
			"cron",
		]);

		if(!file_exists($gtCronCommand)) {
			$this->writeLine(
				"The current directory is not a WebEngine application.",
				Stream::ERROR
			);
			return;
		}
	}

	public function getName():string {
		return "cron";
	}

	public function getDescription():string {
		return "Run any cron jobs that are scheduled to execute now";
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