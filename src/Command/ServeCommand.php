<?php

namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;

class ServeCommand extends Command {
	public function __construct() {
		$this->setName("serve");
		$this->setDescription(
			"Run a local HTTP server"
		);

		$this->setOptionalParameter(
			true,
			"port",
			"p",
			"8080"
		);

		$this->setOptionalNamedParameter("name");
	}

	public function run(ArgumentValueList $arguments = null):void {
// TODO: Run inbuilt server.
// If the "name" parmeter is passed, change working directory there first.
	}
}