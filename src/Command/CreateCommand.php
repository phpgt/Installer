<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Stream;

class CreateCommand extends Command {
	public function __construct() {
		$this->setName("create");
		$this->setDescription(
			"Create a new WebEngine application"
		);

		$this->setRequiredNamedParameter("name");
	}

	public function run(ArgumentValueList $arguments = null):void {
		$cwd = getcwd();
		$appDir = implode(DIRECTORY_SEPARATOR, [
			$cwd,
			$arguments->get("name"),
		]);

		$this->writeLine(
			"Creating new application in: $appDir"
		);

		if(is_dir($appDir)) {
			$this->writeLine(
				"Directory already exists",
				Stream::ERROR
			);
		}

		exec("composer create-project --remove-vcs webengine-blueprints/empty:dev-master $appDir");
// TODO: Future release of php.gt/config:
// Use config-generate to set the correct namespace.
// Update project's composer.json to autoload the correct application classes (from namespace).
	}
}