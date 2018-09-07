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
		parent::__construct();

		$this->setName("help");
		$this->setDescription("Display information about available commands");

		$this->applicationName = $applicationName;
		$this->applicationCommandList = $applicationCommandList;
	}

	public function run(CliArgumentList $arguments): void {
		$this->writeLine($this->applicationName);
		$this->writeLine();

		$this->writeLine("Available commands:");

		foreach($this->applicationCommandList as $command) {
			$this->writeLine(" • " .
				$command->getName()
				. "\t"
				. $command->getDescription()
			);
		}
	}
}