<?php
namespace Gt\Installer\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;

class RunCommand extends AbstractWebEngineCommand {
	public function run(ArgumentValueList $arguments = null):void {
		$port = $arguments->get("port", 8080);
		$bind = $arguments->get("bind", "0.0.0.0");

		$serveScript = ["serve", "--port", $port, "--bind", $bind];
		if($arguments->contains("debug")) {
			array_push($serveScript, "--debug");
		}

		$this->executeScript(
			$arguments,
			$serveScript,
			["build", "--default", "vendor/phpgt/webengine/build.default.json", "--watch"],
			["cron", "--now", "--watch"]
		);
	}

	public function getName():string {
		return "run";
	}

	public function getDescription():string {
		return "Start a local server, cron and build runner";
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		return [];
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		return [
			new NamedParameter("debug")
		];
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		return [
			new Parameter(
				true,
				"port",
				"p"
			),
			new Parameter(
				true,
				"bind",
				"b"
			),
		];
	}
}
