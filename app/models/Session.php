<?php

namespace App;

class Session implements \SessionHandlerInterface {
	public $ttl = 259200; // 3 days
	protected $db;
	protected $prefix;

	public function __construct($client, $prefix = 'PHPSESSID:') {
		$this->db = $client;
		$this->prefix = $prefix;
	}

	public function open($savePath, $sessionName) {
		// No action necessary because connection is injected
		// in constructor and arguments are not applicable.
		return true;
	}

	public function close() {
		$this->db = null;
		unset($this->db);
		return true;
	}

	public function read($id) {
		$id = $this->prefix . $id;
		$sessData = $this->db->get($id);
		$this->db->expire($id, $this->ttl);
		return $sessData;
	}

	public function write($id, $data) {
		$id = $this->prefix . $id;
		$this->db->set($id, $data);
		return $this->db->expire($id, $this->ttl);
	}

	public function destroy($id) {
		return $this->db->del($this->prefix . $id);
	}

	public function gc($maxLifetime) {
		// no action necessary because using EXPIRE
	}
}
