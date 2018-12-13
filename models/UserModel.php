<?php

namespace models;

class UserModel extends BaseModel {

	public function __construct($tableName) {

		parent::__construct($tableName);
	}

	//хешируем пароль
	private function hashing($str) {

		return md5(strlen($str) . $str);
	}


	public function authUser($data) {

		$result = ['error' => 'Incorrect login or password'];

		//Выбираем информацию о пользователе с сответствующим логином и паролем
		$query = 'SELECT id, login FROM users WHERE login="'.$data['login'].'" AND pass="'.$this->hashing($data['pass']).'"';
		$queryResult = $this->db->select($query, true);

		if (intval($queryResult) > 0) {

			$_SESSION = ['isAuthorize' => true, 'user_name' => $data['login']];
			$result = ['success' => 'auth success'];
		}

		return $result;
	}
}