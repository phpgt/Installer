<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;

class BuildCommand extends AbstractWebEngineCommand {
	public function run(ArgumentValueList $arguments = null):void {
		$this->executeScript($arguments, ["build"]);
	}

	public function getName():string {
		return "build";
	}

	public function getDescription():string {
		$baseCommand = new \Gt\Build\Command\RunCommand();
		return $baseCommand->getDescription();
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		$baseCommand = new \Gt\Build\Command\RunCommand();
		return $baseCommand->getRequiredNamedParameterList();
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		$baseCommand = new \Gt\Build\Command\RunCommand();
		return $baseCommand->getOptionalNamedParameterList();
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		$baseCommand = new \Gt\Build\Command\RunCommand();
		return $baseCommand->getRequiredParameterList();
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		$baseCommand = new \Gt\Build\Command\RunCommand();
		return $baseCommand->getOptionalParameterList();
	}
}