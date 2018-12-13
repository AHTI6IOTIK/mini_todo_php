<?php

namespace controllers;

use models\UserModel;

class User extends BaseController {

	public function userAuth() {

		$result = ['error' => 'Insert login and password'];

		if ($this->isAjax() && !empty($_POST['login']) && !empty($_POST['pass'])) {

			$userModel = new UserModel('users');
			$result = $userModel->authUser($_POST);
		}

		echo json_encode($result);
	}

	public function logout() {

		$result = ['error' => 'unknown error'];

		if ($this->isAjax() && session_destroy()) {

			$result = ['success' => 'bye'];
		}

		echo json_encode($result);
	}
}