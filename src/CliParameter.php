<?php
namespace Gt\Installer;

class CliParameter {
	protected $requireValue;
	protected $longOption;
	protected $shortOption;

	public function __construct(
		bool $requireValue,
		string $longOption,
		string $shortOption = null
	) {
		$this->requireValue = $requireValue;
		$this->longOption = $longOption;
		$this->shortOption = $shortOption;
	}

	public function __toString():string {
		$message = $this->longOption;

		if(!is_null($this->shortOption)) {
			$message .= " (";
			$message .= $this->shortOption;
			$message .= ")";
		}

		return $message;
	}

	public function getLongOption():string {
		return $this->longOption;
	}

	public function getShortOption():?string {
		return $this->shortOption;
	}

	public function isValueRequired():bool {
		return $this->requireValue;
	}
}