<?php

// a cascading config grabber

namespace App;

class Cfgr extends \Tipsy\Service {
	protected $_config;

	public function __construct() {
		foreach (\App\Config::all() as $config) {
			$this->_config[$config->key] = $config->value;
		}
	}

	public function get($id) {
		// checks the database for a value
		if ($this->_config[$id]) {
			return $this->_config[$id];
		}

		// checks environment variables for a value
		$env = strtoupper(str_replace('-', '_', $id));
		if (getenv($env)) {
			return getenv($env);
		}

		$parts = explode('-', $id);
		$cfg = $this->tipsy()->config();

		for ($x=0; $x < count($parts); $x++) {
			$cfg = $cfg[$parts[$x]];
		}
		return $cfg;
	}
	public function set($key, $value = null) {
		// sets the config in the database
		\App\Config::write($key, $value);
	}
}
