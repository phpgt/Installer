<?php
namespace Gt\Installer;

use Iterator;

class CliArgumentList implements Iterator {
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
		$this->argumentList []= new CliCommandArgument($commandArgument);

		foreach ($arguments as $i => $arg) {
			if ($arg[0] === "-") {
				$nextArg = $arguments[$i + 1];

				if ($arg[1] === "-") {
					$this->argumentList []= new CliLongOptionArgument($arg, $nextArg);
				} else {
					$this->argumentList []= new CliShortOptionArgument($arg, $nextArg);
				}
			} else {
				$this->argumentList []= new CliValueArgument($arg);
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

	public function contains(CliArgument $argument):bool {
		$shortKey = $argument->
		foreach($this->argumentList as $argument) {
			if($argument instanceof CliLongOptionArgument) {

			}
		}
	}
}