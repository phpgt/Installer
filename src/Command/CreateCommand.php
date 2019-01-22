<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Command\CommandException;
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
	}
}