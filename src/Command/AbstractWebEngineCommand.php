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
		array...$scriptsToRun
	):void {
		$processPool = new Pool();

		foreach($scriptsToRun as $scriptParts) {
			$scriptName = array_shift($scriptParts);

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

			$friendlyScriptName = $gtCommand;
			$slashPos = strrpos($friendlyScriptName, "/");
			if($slashPos > 0) {
				$friendlyScriptName = substr(
					$friendlyScriptName,
					$slashPos + 1
				);
			}

			$process = new Process($gtCommand, ...$scriptParts);
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