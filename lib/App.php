<?php

namespace lib;

use models\Db;

class App {

	public static function Init() {

		date_default_timezone_set('Europe/Moscow');
		Db::getInstance()->Connect(
			Config::get('db_user'),
			Config::get('db_password'),
			Config::get('db_base')
		);

		if (php_sapi_name() !== 'cli' && isset($_SERVER) && isset($_GET)) {

			self::web(!empty($_GET['PAGE']) ? $_GET : 'index');
		}
	}

	protected static function web($url) {

		if (!is_array($url)) {

			$_GET['PAGE'] = 'index';
		}

		if (!empty($_GET['PAGE'])) {

			$controllerName = 'controllers\\'.ucfirst($_GET['PAGE']);
			$methodName = !empty($_GET['ACTION']) ? $_GET['ACTION'] : 'index';

			if (empty($_SESSION)) {

				session_start();
			}

			$controller = new $controllerName();
			$controller->$methodName();

			if (!$controller->isAjax() || !empty($_POST['sort'])) {

				$controller->render();
			}
		}
	}
}