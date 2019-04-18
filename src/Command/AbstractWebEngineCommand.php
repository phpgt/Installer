<?php
namespace Gt\Installer\Command;

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
			$argString .= " ";
			$argString .= "--";
			$argString .= $arg->getKey();

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

			if(!file_exists($gtCommand)) {
				$this->writeLine(
					"The current directory is not a WebEngine application.",
					Stream::ERROR
				);
				return;
			}

			if(!empty($argString)) {
				$gtCommand .= $argString;
			}

			$process = new Process($gtCommand);
			$processPool->add($scriptName, $process);
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
		}
		while($processPool->numRunning() > 0);

		$this->writeLine("All processes completed.");
	}
}