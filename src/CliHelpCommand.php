<?php
namespace Gt\Installer;

class CliHelpCommand extends CliCommand {
	protected $applicationName;
	protected $applicationCommandList;

	/**
	 * @param CliCommand[] $applicationCommandList
	 */
	public function __construct(
		string $applicationName,
		array $applicationCommandList = []
	) {
		parent::__construct();

		$this->setName("help");

		$this->applicationName = $applicationName;
		$this->applicationCommandList = $applicationCommandList;
	}

	public function run(CliArgumentList $arguments): void {
		$this->writeLine($this->applicationName);
		$this->writeLine();

		$this->writeLine("Available commands:");

		foreach($this->applicationCommandList as $command) {
			$this->writeLine("\t" . $command->getName());
		}
	}
}