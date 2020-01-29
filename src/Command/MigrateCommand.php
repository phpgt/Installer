<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;

class MigrateCommand extends AbstractWebEngineCommand {
	public function run(ArgumentValueList $arguments = null):void {
		$this->executeScript(
			$arguments,
			["db-migrate"]
		);
	}

	public function getName():string {
		return "migrate";
	}

	public function getDescription():string {
		return "Run the database migrations";
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		return [];
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		return [
			new Parameter(
				false,
				"force",
				"f"
			),
		];
	}
}