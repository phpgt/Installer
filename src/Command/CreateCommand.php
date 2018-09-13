<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Command\CommandException;

class CreateCommand extends Command {
	public function __construct() {
		$this->setName("create");
		$this->setDescription(
			"Create a new WebEngine application"
		);

		$this->setRequiredNamedParameter("name");
	}

	public function run(ArgumentValueList $arguments):void {
		$cwd = getcwd();
		$appDir = implode(DIRECTORY_SEPARATOR, [
			$cwd,
			$arguments->get("name"),
		]);

		$this->stream->writeLine(
			"Creating new application in: $appDir"
		);

		if(is_dir($appDir)) {
			throw new CommandException("Directory already exists");
		}

		exec("composer create-project webengine-blueprint/empty $appDir");
	}
}