<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;
use Gt\Daemon\Process;

class BuildCommand extends AbstractWebEngineCommand {
	public function run(ArgumentValueList $arguments = null):void {
		$this->executeScript($arguments, "build");
	}

	public function getName():string {
		return "build";
	}

	public function getDescription():string {
		return "Build the client-side files";
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
				"watch",
				"w"
			),
		];
	}
}