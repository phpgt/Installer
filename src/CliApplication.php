<?php
namespace Gt\Installer;

class CliApplication {
	protected $applicationName;
	protected $arguments;
	protected $commands;

	public function __construct(
		string $applicationName,
		CliArgumentList $arguments,
		CliCommand...$commands
	) {
		$this->applicationName = $applicationName;
		$this->arguments = $arguments;
		$this->commands = $commands;

		$this->commands []= new CliHelpCommand(
			$this->applicationName,
			$this->commands
		);
	}

	public function run():void {
		$commandName = $this->arguments->getCommandName();
		$command = $this->findCommandByName($commandName);
		$command->checkArguments($this->arguments);
		$command->run($this->arguments);
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