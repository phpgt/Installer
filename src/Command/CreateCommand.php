<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;
use Gt\Daemon\Process;

class CreateCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$cwd = getcwd();
		$appDir = implode(DIRECTORY_SEPARATOR, [
			$cwd,
			$arguments->get("name"),
		]);

		if(is_dir($appDir)) {
			$this->writeLine(
				"Directory already exists",
				Stream::ERROR
			);
		}

		$blueprint = $arguments->get("blueprint", "empty");
		$this->writeLine(
			"Creating new application in: $appDir"
		);
		$this->writeLine(
			"Using blueprint: $blueprint"
		);

		$process = new Process("composer create-project --remove-vcs webengine-blueprints/$blueprint:dev-master $appDir");
		$process->exec();

		$this->write("Installing");
		do {
			sleep(1);
			$this->write(".");
		}
		while($process->isRunning());

		$this->writeLine();
		$this->writeLine("Installation complete. Have fun! https://www.php.gt/documentation");
// TODO: Future release of php.gt/config:
// Use config-generate to set the correct namespace.
// Update project's composer.json to autoload the correct application classes (from namespace).
	}

	public function getName():string {
		return "create";
	}

	public function getDescription():string {
		return "Create a new WebEngine application";
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		return [
			new NamedParameter("name")
		];
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		return [
			new Parameter(
				true,
				"blueprint",
				"b",
				"BLUEPRINT_NAME"
			),
		];
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		return [];
	}
}