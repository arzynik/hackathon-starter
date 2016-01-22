<?php

namespace App\Controller;

use OAuth\OAuth2\Service\Facebook;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

class Auth extends \Tipsy\Controller {
	public function init($args = null) {
		$name = $this->tipsy()->request()->loc(1);

		if (!$this->tipsy()->config()['auth'][$name]) {
			die('no auth config for '.$name);
		}

		$storage = new Session();
		$credentials = new Credentials(
			Tipsy::service('cfgr')->get('api-'.$name.'-key'),
			Tipsy::service('cfgr')->get('api-'.$name.'-secret'),
			$this->tipsy()->request()->url()
		);

		$serviceFactory = new \OAuth\ServiceFactory();

		$service = $serviceFactory->createService($name, $credentials, $storage, []);

		if (!empty($_GET['code'])) {

			$state = isset($_GET['state']) ? $_GET['state'] : null;
			$token = $service->requestAccessToken($_GET['code'], $state);

			switch ($name) {
				case 'facebook':
					$data = json_decode($service->request('/me'), true);
					$result = [
						id => $data['id'],
						name => $data['name']
					];
					break;

				case 'github':
					$data = json_decode($service->request('user'), true);
					print_r($data);
					exit;
					$result = [
						id => $data['id'],
						name => $data['name']
					];
					break;
			}

			if ($result['id']) {
				$user = \App\User::byAuth($result['id'], $name);
				if (!$user) {
					$user = new \App\User([
						'name' => $result['name']
					]);
					$user->save();

					$auth = new \App\Auth([
						'value' => $result['id'],
						'type' => $name,
						'user' => $user->id
					]);
					$auth->save();
				}

				$_SESSION['user'] = $user->id;
				header('Location: /account');
			}

		} else {
			$url = $service->getAuthorizationUri();
			header('Location: ' . $url);
		}
	}
}
