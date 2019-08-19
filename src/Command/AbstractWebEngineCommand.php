<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\Argument;
use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Stream;
use Gt\Daemon\Pool;
use Gt\Daemon\Process;

abstract class AbstractWebEngineCommand extends Command {
	public function executeScript(
		ArgumentValueList $arguments = null,
		string...$scriptsToRun
	):void {
		$argString = "";

		foreach($arguments as $arg) {
			$key = $arg->getKey();

			if($key !== Argument::USER_DATA) {
				$argString .= " ";
				$argString .= "--";
				$argString .= $key;
			}

			$value = $arg->get();
			if(!empty($value)) {
				$argString .= " ";
				$argString .= $value;
			}
		}

		$processPool = new Pool();

		foreach($scriptsToRun as $scriptName) {
			$gtCommand = implode(DIRECTORY_SEPARATOR, [
				"vendor",
				"bin",
				$scriptName,
			]);

			$spacePos = strpos($gtCommand, " ");
			$gtCommandWithoutArguments = $gtCommand;
			if($spacePos > 0) {
				$gtCommandWithoutArguments = substr(
					$gtCommand,
					0,
					$spacePos
				);
			}
			if(!file_exists($gtCommandWithoutArguments)) {
				$this->writeLine(
					"The current directory is not a WebEngine application.",
					Stream::ERROR
				);
				return;
			}

			if(!empty($argString)) {
				$gtCommand .= $argString;
			}

			$friendlyScriptName = $gtCommandWithoutArguments;
			$slashPos = strrpos($gtCommandWithoutArguments, "/");
			if($slashPos > 0) {
				$friendlyScriptName = substr(
					$gtCommandWithoutArguments,
					$slashPos + 1
				);
			}

			$process = new Process($gtCommand);
			$processPool->add($friendlyScriptName, $process);
		}

		$processPool->exec();

		do {
			$output = $processPool->read();
			$errorOutput = $processPool->readError();

			if(strlen(trim($output)) > 0) {
				$this->write($output);
			}
			if(strlen(trim($errorOutput)) > 0) {
				$this->write($errorOutput, Stream::ERROR);
			}

			usleep(100000);
		}
		while($processPool->numRunning() > 0);
	}
}