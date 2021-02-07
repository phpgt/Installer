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
		array...$scriptsToRun
	):void {
		$processPool = new Pool();

		foreach($scriptsToRun as $scriptParts) {
			/** @var string[] $scriptParts */

			$scriptName = $scriptParts[0];

			$scriptParts[0] = implode(DIRECTORY_SEPARATOR, [
				"vendor",
				"bin",
				$scriptParts[0],
			]);

			if(!file_exists($scriptParts[0])) {
				$this->writeLine(
					"The current directory is not a WebEngine application.",
					Stream::ERROR
				);
				return;
			}

			$process = new Process(...$scriptParts);
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

			usleep(100000);
		}
		while($processPool->numRunning() > 0);
	}
}
