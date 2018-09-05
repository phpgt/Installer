<?php

namespace Gt\Installer;

use SplFileObject;

abstract class CliCommand {
	protected $name;
	/** @var CliNamedParameter[] */
	protected $optionalNamedParameterList = [];
	/** @var CliNamedParameter[] */
	protected $requiredNamedParameterList = [];
	/** @var CliParameter[] */
	protected $optionalParameterList = [];
	/** @var CliParameter[] */
	protected $requiredParameterList = [];

	public function __construct() {
		$this->out = new SplFileObject(
			"php://stdout",
			"w"
		);
		$this->err = new SplFileObject(
			"php://stderr",
			"w"
		);
	}

	abstract public function run(CliArgumentList $arguments):void;

	public function write(string $message):void {
		$this->out->fwrite($message);
	}

	public function writeLine(string $message = ""):void {
		$this->write($message . PHP_EOL);
	}

	public function getName():string {
		return $this->name;
	}

	protected function setName(string $name):void {
		$this->name = $name;
	}

	public function checkArguments(CliArgumentList $argumentList):void {
		$requiredNamedParameters = count(
			$this->requiredNamedParameterList
		);

		$passedNamedArguments = 0;
		foreach($argumentList as $argument) {
			if($argument instanceof CliNamedArgument) {
				$passedNamedArguments ++;
			}
		}

		if($passedNamedArguments < $requiredNamedParameters) {
			throw new NotEnoughArgumentsException();
		}

		foreach($this->requiredParameterList as $parameter) {
			if(!$argumentList->contains($parameter)) {
				throw new MissingRequiredParameterException(
					$parameter
				);
			}

			if($parameter->isValueRequired()) {
				$value = $argumentList->getValueForParameter(
					$parameter
				);
				if(is_null($value)) {
					throw new MissingRequiredParameterValueException(
						$parameter
					);
				}
			}
		}
	}

	/**
	 * @return CliNamedParameter[]
	 */
	public function getRequiredNamedParameterList():array {
		return $this->requiredNamedParameterList;
	}

	protected function setRequiredNamedParameter(string $name):void {
		$this->requiredNamedParameterList []= new CliNamedParameter(
			$name
		);
	}

	/**
	 * @return CliNamedParameter[]
	 */
	public function getOptionalNamedParameterList():array {
		return $this->optionalNamedParameterList;
	}

	protected function setOptionalNamedParameter(string $name):void {
		$this->optionalNamedParameterList []= new CliNamedParameter(
			$name
		);
	}

	/**
	 * @return CliParameter[]
	 */
	public function getRequiredParameterList():array {
		return $this->requiredParameterList;
	}

	protected function setRequiredParameter(
		bool $requireValue,
		string $longOption,
		string $shortOption = null
	):void {
		$this->requiredParameterList []= new CliParameter(
			$requireValue,
			$longOption,
			$shortOption
		);
	}

	/**
	 * @return CliParameter[]
	 */
	public function getOptionalParameterList():array {
		return $this->optionalParameterList;
	}

	protected function setOptionalParameter(
		bool $requireValue,
		string $longOption,
		string $shortOption = null
	):void {
		$this->optionalParameterList []= new CliParameter(
			$requireValue,
			$longOption,
			$shortOption
		);
	}
}