<?php

namespace App;

use Tipsy\Tipsy;

class Mail extends \Tipsy\Service {
	public function __construct() {
		$this->_service = Tipsy::service('cfgr')->get('api-mailer');
		$this->_client = new \Mailgun\Mailgun(Tipsy::service('cfgr')->get('api-'.$this->_service.'-key'));
	}

	public function send($params) {
		return $this->_client->sendMessage(Tipsy::service('cfgr')->get('api-'.$this->_service.'-domain'), $params);
	}
}
