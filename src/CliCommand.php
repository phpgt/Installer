<?php

namespace Gt\Installer;

abstract class CliCommand {
	protected $name;
	protected $requiredValueArguments = [];
	protected $optionalNamedArguments = [];
	/** @var CliOption[] */
	protected $optionalArguments = [];
	/** @var CliOption[] */
	protected $requiredArguments = [];

	public function __construct(string $name) {
		$this->name = $name;
	}

	public function getName():string {
		return $this->name;
	}

	public function checkArguments(CliArgumentList $argumentList):void {
		$requiredValueArgumentCount = count(
			$this->requiredValueArguments
		) + 1;
		$passedValueArguments = 0;
		foreach($argumentList as $argument) {
			if($argument instanceof CliValueArgument) {
				$passedValueArguments ++;
			}
		}

		if($passedValueArguments < $requiredValueArgumentCount) {
			throw new NotEnoughArgumentsException();
		}

		foreach($this->requiredValueArguments as $argument) {
			// TODO: Check
		}

		foreach($this->requiredArguments as $argument) {
			// TODO: Check
		}

		var_dump($options);die();
	}

	protected function setRequiredValueArgument(string $option):void {
		$this->requiredValueArguments []= new CliNamedOption(
			$option
		);
	}

	protected function setOptionalNamedOption(string $option):void {
		$this->optionalNamedArguments []= new CliNamedOption(
			$option
		);
	}

	protected function setRequiredArgument(
		bool $requireValue,
		string $longOption,
		string $shortOption = null
	):void {
		$this->requiredArguments []= new CliOption(
			$requireValue,
			$longOption,
			$shortOption
		);
	}

	protected function setOptionalArgument(
		bool $requireValue,
		string $longOption,
		string $shortOption = null
	):void {
		$this->optionalArguments []= new CliOption(
			$requireValue,
			$longOption,
			$shortOption
		);
	}
}