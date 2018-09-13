<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;

class CreateCommand extends Command {
	public function __construct() {
		$this->setName("create");
		$this->setDescription(
			"Create a new WebEngine application"
		);

		$this->setRequiredNamedParameter("name");
	}

	public function run(ArgumentValueList $arguments):void {

	}
}