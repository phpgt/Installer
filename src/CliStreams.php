<?php
namespace Gt\Installer;

use SplFileObject;

class CliStreams {
	/** @var SplFileObject */
	protected $error;
	/** @var SplFileObject */
	protected $out;
	/** @var SplFileObject */
	protected $in;

	public function __construct(string $in, string $out, string $error) {
		$this->setStreams($in, $out, $error);
	}

	public function setStreams(string $in, string $out, string $error) {
		$this->in = new SplFileObject(
			$in,
			"r"
		);
		$this->out = new SplFileObject(
			$out,
			"w"
		);
		$this->error = new SplFileObject(
			$error,
			"w"
		);
	}

	public function getInStream():SplFileObject {
		return $this->in;
	}

	public function getOutStream():SplFileObject {
		return $this->out;
	}

	public function getErrorStream():SplFileObject {
		return $this->error;
	}
}