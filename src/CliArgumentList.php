<?php
namespace Gt\Installer;

use Iterator;

class CliArgumentList implements Iterator {
	const DEFAULT_COMMAND = "help";

	protected $script;
	/** @var CliArgument[] */
	protected $argumentList = [];
	protected $iteratorIndex;

	public function __construct(string $script, string...$arguments) {
		$this->script = $script;
		$this->buildArgumentList($arguments);
	}

	public function getCommandName():string {
		return $this->argumentList[0]->getValue();
	}

	protected function buildArgumentList(array $arguments):void {
		$commandArgument = array_shift($arguments);
		$this->argumentList []= new CliCommandArgument(
			$commandArgument ?? self::DEFAULT_COMMAND
		);

		$skipNextArgument = false;

		foreach ($arguments as $i => $arg) {
			if($skipNextArgument) {
				$skipNextArgument = false;
				continue;
			}

			if ($arg[0] === "-") {
				if(strstr($arg, "=")) {
					$name = substr(
						$arg,
						0,
						strpos(
							$arg,
							"="
						)
					);

					$value = substr(
						$arg,
						strpos(
							$arg,
							"="
						) + 1
					);
				}
				else {
					$skipNextArgument = true;
					$name = $arg;
					$value = $arguments[$i + 1];
				}

				if ($arg[1] === "-") {
					$this->argumentList []= new CliLongOptionArgument(
						$name,
						$value
					);
				}
				else {
					$this->argumentList []= new CliShortOptionArgument(
						$arg,
						$value
					);
				}
			} else {
				$this->argumentList []= new CliNamedArgument($arg);
			}
		}
	}

	/**
	 * @link http://php.net/manual/en/iterator.current.php
	 */
	public function current():CliArgument {
		return $this->argumentList[$this->iteratorIndex];
	}

	/**
	 * @link http://php.net/manual/en/iterator.next.php
	 */
	public function next():void {
		$this->iteratorIndex++;
	}

	/**
	 * @link http://php.net/manual/en/iterator.key.php
	 */
	public function key():int {
		return $this->iteratorIndex;
	}

	/**
	 * @link http://php.net/manual/en/iterator.valid.php
	 */
	public function valid():bool {
		return isset($this->argumentList[$this->iteratorIndex]);
	}

	/**
	 * @link http://php.net/manual/en/iterator.rewind.php
	 */
	public function rewind() {
		$this->iteratorIndex = 0;
	}

	public function contains(CliParameter $parameter):bool {
		$longOption = $parameter->getLongOption();
		$shortOption = $parameter->getShortOption();

		foreach($this->argumentList as $argument) {
			$key = $argument->getKey();

			if($argument instanceof CliLongOptionArgument) {
				if($key === $longOption) {
					return true;
				}
			}
			elseif($argument instanceof CliShortOptionArgument) {
				if($key === $shortOption) {
					return true;
				}
			}
		}

		return false;
	}

	public function getValueForParameter(CliParameter $parameter):?string {
		$longOption = $parameter->getLongOption();
		$shortOption = $parameter->getShortOption();

		foreach($this->argumentList as $argument) {
			$key = $argument->getKey();

			if($argument instanceof CliLongOptionArgument) {
				if($key === $longOption) {
					return $argument->getValue();
				}
			}
			elseif($argument instanceof CliShortOptionArgument) {
				if($key === $shortOption) {
					return $argument->getValue();
				}
			}
		}

		return null;
	}
}