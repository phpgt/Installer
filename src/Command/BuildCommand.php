<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;
use Gt\Daemon\Process;

class BuildCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$argString = "";

		foreach($arguments as $arg) {
			$argString .= " ";
			$argString .= "--";
			$argString .= $arg->getKey();

			$value = $arg->get();
			if(!empty($value)) {
				$argString .= " ";
				$argString .= $value;
			}
		}
		$gtBuildCommand = implode(DIRECTORY_SEPARATOR, [
			"vendor",
			"bin",
			"build",
		]);

		if(!file_exists($gtBuildCommand)) {
			$this->writeLine(
				"The current directory is not a WebEngine application.",
				Stream::ERROR
			);
			return;
		}

		if(!empty($argString)) {
			$gtBuildCommand .= $argString;
		}

		$cmd = implode(" ", [
			$gtBuildCommand,
		]);
		$process = new Process($cmd);
		$process->exec();

		do {
			$output = $process->getOutput();
			$errorOutput = $process->getErrorOutput();

			if(!empty($output)) {
				$this->write($output);
			}
			if(!empty($errorOutput)) {
				$this->write($errorOutput, Stream::ERROR);
			}
		}
		while($process->isRunning());
	}

	public function getName():string {
		return "build";
	}

	public function getDescription():string {
		return "Build the client-side files";
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
		return [
			new Parameter(
				false,
				"watch",
				"w"
			),
		];
	}
}