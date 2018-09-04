<?php
namespace Gt\Installer;

class CliApplication {
	protected $arguments;
	protected $commands;

	public function __construct(
		CliArgumentList $arguments,
		CliCommand...$commands
	) {
		$this->arguments = $arguments;
		$this->commands = $commands;
	}

	public function run():void {
		$commandName = $this->arguments->getCommandName();
		$command = $this->findCommandByName($commandName);
		$command->checkArguments($this->arguments);
	}

	protected function findCommandByName(string $name):CliCommand {
		foreach($this->commands as $command) {
			if($command->getName() !== $name) {
				continue;
			}

			return $command;
		}

		throw new InvalidCliCommandException($name);
	}
}