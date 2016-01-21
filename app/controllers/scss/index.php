<?php

namespace App\Controller;

class Scss extends \Tipsy\Controller {
	public function init($hi = null) {
		$this->inject(function($Params, $Request) {
			$scss = new \Leafo\ScssPhp\Compiler;
			$scss->setImportPaths($this->tipsy()->config()['path'].'public/assets/');
			$scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
			$file = $this->tipsy()->config()['path'].'public/'.$Request->path();
			$data = $scss->compile(file_get_contents($file));
			$mtime = filemtime($file);

			header('HTTP/1.1 200 OK');
			header('Date: '.date('r'));
			header('Last-Modified: '.gmdate('D, d M Y H:i:s',$mtime).' GMT');
			header('Accept-Ranges: bytes');
			header('Content-Length: '.strlen($data));
			header('Content-type: text/css');
			header('Vary: Accept-Encoding');
			header('Cache-Control: max-age=290304000, public');
			echo $data;

		});
	}
}
