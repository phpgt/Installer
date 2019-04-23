<?php

namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;
use Gt\Daemon\Process;

class ServeCommand extends AbstractWebEngineCommand {
	public function run(ArgumentValueList $arguments = null):void {
		$this->executeScript($arguments, "serve");
	}

	public function getName():string {
		return "serve";
	}

	public function getDescription():string {
		$baseCommand = new \Gt\Server\Command\StartCommand();
		return $baseCommand->getDescription();
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		$baseCommand = new \Gt\Server\Command\StartCommand();
		return $baseCommand->getRequiredNamedParameterList();
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		$baseCommand = new \Gt\Server\Command\StartCommand();
		return $baseCommand->getOptionalNamedParameterList();
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		$baseCommand = new \Gt\Server\Command\StartCommand();
		return $baseCommand->getRequiredParameterList();
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		$baseCommand = new \Gt\Server\Command\StartCommand();
		return $baseCommand->getOptionalParameterList();
	}
}