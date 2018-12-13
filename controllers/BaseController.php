<?php

namespace controllers;

use lib\Config;

abstract class BaseController {

	protected $title = '';
	protected $descriptionPage = '';
	protected $keyWordsPage = '';
	protected $content = [];
	protected $pagen = [];
	protected $loginLinkClass = 'login-admin';
	protected $loginLinkTitle = 'login by admin';

	public function __construct() {

		$this->title = Config::get('site_name');
		$this->descriptionPage = Config::get('description');
		$this->keyWordsPage = Config::get('keywords');

		if (!empty($_SESSION['isAuthorize']) && !empty($_SESSION['user_name'])) {

			$this->loginLinkClass = 'logout';
			$this->loginLinkTitle = 'logout';
		}
	}

	public function render() {

		$vars = [
			'pageTitle' => $this->title,
			'pageKeywords' => $this->descriptionPage,
			'pageDescription' => $this->keyWordsPage,
			'content' => $this->content,
			'pagen' => $this->pagen,
			'loginClass' => $this->loginLinkClass,
			'linkTitle' => $this->loginLinkTitle,
			'menu' =>  'left men'
		];
		$page = $this->template('layout.phtml', $vars);

		echo $page;
	}

	protected final function template($template, $vars) {

		foreach ($vars as $key => $value) {

			$$key = $value;
		}

		ob_start();

		require_once TEMPLATES_DIR. $template;

		return ob_get_clean();
	}

	public function isPost() {

		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	public function isAjax() {

		return $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
	}

	public function isGet() {

		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}
}