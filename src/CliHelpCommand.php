<?php
namespace Gt\Installer;

class CliHelpCommand extends CliCommand {
	protected $applicationName;
	/** @var CliCommand[] */
	protected $applicationCommandList;

	/**
	 * @param CliCommand[] $applicationCommandList
	 */
	public function __construct(
		string $applicationName,
		array $applicationCommandList = []
	) {
		$this->setName("help");
		$this->setDescription("Display information about available commands");

		$this->applicationName = $applicationName;
		$this->applicationCommandList = $applicationCommandList;
	}

	public function run(CliArgumentValueList $arguments): void {
		$this->streams->writeLine($this->applicationName);
		$this->streams->writeLine();

		$this->streams->writeLine("Available commands:");

		foreach($this->applicationCommandList as $command) {
			$this->streams->writeLine(" • " .
				$command->getName()
				. "\t"
				. $command->getDescription()
			);
		}
	}
}