<?php

namespace Gt\Installer;

use SplFileObject;

abstract class CliCommand {
	/** @var CliStreams */
	protected $streams;

	protected $name;
	protected $description = "";
	/** @var CliNamedParameter[] */
	protected $optionalNamedParameterList = [];
	/** @var CliNamedParameter[] */
	protected $requiredNamedParameterList = [];
	/** @var CliParameter[] */
	protected $optionalParameterList = [];
	/** @var CliParameter[] */
	protected $requiredParameterList = [];

	public function setStreams(CliStreams $streams) {
		$this->streams = $streams;
	}

	abstract public function run(CliArgumentValueList $arguments):void;

	public function getName():string {
		return $this->name;
	}

	protected function setName(string $name):void {
		$this->name = $name;
	}

	public function getDescription():string {
		return $this->description;
	}

	protected function setDescription(string $description):void {
		$this->description = $description;
	}


	public function checkArguments(CliArgumentList $argumentList):void {
		$numRequiredNamedParameters = count(
			$this->requiredNamedParameterList
		);

		$passedNamedArguments = 0;
		foreach($argumentList as $argument) {
			if($argument instanceof CliNamedArgument) {
				$passedNamedArguments ++;
			}
		}

		if($passedNamedArguments < $numRequiredNamedParameters) {
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

	public function getUsage():string {
		$message = "";

		$message .= "Usage: ";
		$message .= $this->getName();

		foreach($this->requiredNamedParameterList as $parameter) {
			$message .= " ";
			$message .= $parameter->getOptionName();
		}

		foreach($this->optionalNamedParameterList as $parameter) {
			$message .= " [";
			$message .= $parameter->getOptionName();
			$message .= "]";
		}

		foreach($this->requiredParameterList as $parameter) {
			$message .= " --";
			$message .= $parameter->getLongOption();

			if($short = $parameter->getShortOption()) {
				$message .= "|-$short";
			}

			if($parameter->isValueRequired()) {
				$message .= " ";
				$message .= $parameter->getExample();
			}
		}

		foreach($this->optionalParameterList as $parameter) {
			$message .= " [--";
			$message .= $parameter->getLongOption();

			if($short = $parameter->getShortOption()) {
				$message .= "|-$short";
			}

			if($parameter->isValueRequired()) {
				$message .= " ";
				$message .= $parameter->getExample();
				$message .= "]";
			}
		}

		return $message;
	}

	public function getArgumentValueList(
		CliArgumentList $arguments
	):CliArgumentValueList {
		$namedParameterIndex = 0;
		/** @var CliNamedParameter[] */
		$namedParameterList = array_merge(
			$this->requiredNamedParameterList,
			$this->optionalNamedParameterList
		);

		$parameterIndex = 0;
		/** @var CliParameter[] $parameterList */
		$parameterList = array_merge(
			$this->requiredParameterList,
			$this->optionalParameterList
		);

		$argumentValueList = new CliArgumentValueList();

		foreach($arguments as $argument) {
			if($argument instanceof CliCommandArgument) {
				continue;
			}

			if($argument instanceof CliNamedArgument) {
				/** @var CliNamedParameter $parameter */
				$parameter = $namedParameterList[
					$namedParameterIndex
				];

				$argumentValueList->set(
					$parameter->getOptionName(),
					$argument->getValue()
				);
				$namedParameterIndex++;
			}

			if($argument instanceof CliArgument) {
				/** @var CliParameter $parameter */
				$parameter = $parameterList[
					$parameterIndex
				];

				$argumentValueList->set(
					$parameter->getLongOption(),
					$argument->getValue()
				);
			}
		}

		return $argumentValueList;
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
		string $shortOption = null,
		string $example = null
	):void {
		$this->requiredParameterList []= new CliParameter(
			$requireValue,
			$longOption,
			$shortOption,
			$example
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
		string $shortOption = null,
		string $example = null
	):void {
		$this->optionalParameterList []= new CliParameter(
			$requireValue,
			$longOption,
			$shortOption,
			$example
		);
	}
}