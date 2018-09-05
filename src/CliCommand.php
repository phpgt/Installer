<?php

namespace Gt\Installer;

abstract class CliCommand {
	protected $name;
	protected $requiredValueParameterList = [];
	protected $optionalNamedParameterList = [];
	/** @var CliOption[] */
	protected $optionalParameterList = [];
	/** @var CliOption[] */
	protected $requiredParameterList = [];

	public function getName():string {
		return $this->name;
	}

	protected function setName(string $name):void {
		$this->name = $name;
	}

	public function checkArguments(CliArgumentList $argumentList):void {
		$requiredValueArgumentCount = count(
			$this->requiredValueParameterList
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

		foreach($this->requiredValueParameterList as $parameter) {
			if(!$argumentList->contains($parameter)) {
				throw new MissingRequiredArgumentException(
					$parameter->getLongName()
				);
			}
		}

		foreach($this->requiredParameterList as $parameter) {
			// TODO: Check
		}

		var_dump($options);die();
	}

	protected function setRequiredValueParameter(string $option):void {
		$this->requiredValueParameterList []= new CliNamedOption(
			$option
		);
	}

	protected function setOptionalValueParameter(string $option):void {
		$this->optionalNamedParameterList []= new CliNamedOption(
			$option
		);
	}

	protected function setRequiredParameter(
		bool $requireValue,
		string $longOption,
		string $shortOption = null
	):void {
		$this->requiredParameterList []= new CliOption(
			$requireValue,
			$longOption,
			$shortOption
		);
	}

	protected function setOptionalParameter(
		bool $requireValue,
		string $longOption,
		string $shortOption = null
	):void {
		$this->optionalParameterList []= new CliOption(
			$requireValue,
			$longOption,
			$shortOption
		);
	}
}