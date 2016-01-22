<?php

namespace App\Controller\Api;

use Tipsy\Tipsy;

class Forgot extends \Tipsy\Controller {
	public function init($args = null) {
		$this->inject(function($Request, $Mail, $View) {
			if ($Request->method() == 'POST' && $Request->email) {
				$user = \App\User::byEmail($Request->email);
				if (!$user->email) {
					http_response_code(403);
					return;
				}

				$reset = substr(base64_encode(password_hash(rand(1,999999), PASSWORD_BCRYPT)), 0, 50);
				$user->reset = $reset;
				$user->save();

				$link = $Request->host().'/reset-password/'.$reset;
				$View->config(['layout' => 'layouts/mail']);
				$template = $View->render('mail/forgot-password', ['link' => $link, 'title' => Tipsy::service('cfgr')->get('title')]);

				$status = $Mail->send([
					'to' => $user->email,
					'from' => Tipsy::service('cfgr')->get('title').' <postmaster@'.Tipsy::service('cfgr')->get('api-mailgun-domain').'>',
					'subject' => 'Password Recovery',
					'html' => $template
				]);

				echo json_encode(['status' => $status ? true : false]);

			}
			http_response_code(403);
		});
	}
}
