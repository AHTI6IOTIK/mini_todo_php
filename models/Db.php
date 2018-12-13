<?php

namespace models;

class Db {

	private static $_instance = null;

	private $db; // Ресурс работы с БД

	/*
	 * Получаем объект для работы с БД
	 */
	public static function getInstance() {

		if (self::$_instance == null) {

			self::$_instance = new Db();
		}
		return self::$_instance;
	}

	/*
	 * Запрещаем копировать объект
	 */
	private function __construct() {
	}

	private function __sleep() {
	}

	private function __wakeup() {
	}

	private function __clone() {
	}

	/*
	 * Выполняем соединение с базой данных
	 */
	public function connect($user, $password, $base, $host = 'localhost', $port = 3306) {

		// Формируем строку соединения с сервером
		$connectString = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $base . ';charset=UTF8;';
		setlocale(LC_ALL, 'ru_RU.UTF8');
		$this->db = new \PDO($connectString, $user, $password);
		$this->db->exec('SET NAMES UTF8');
		$this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
	}

	/*
	 * Выполнить запрос к БД
	 */
	public function query($query, $params = array()) {

		$res = $this->db->prepare($query);
		$res->execute($params);

		if ($res->errorCode() != \PDO::ERR_NONE) {

			$info = $res->errorInfo();
			throw new \Exception($info[2]);
		}

		return $res;
	}

	/*
	 * Выполнить запрос с выборкой данных
	 */

	public function select($query, $count = false, $params = []) {

		$res = $this->db->prepare($query);
		$res->execute($params);

		if ($res->errorCode() != \PDO::ERR_NONE) {

			$info = $res->errorInfo();
			throw new \Exception($info[2]);
		} else {

			if ($count) {

				return $res->rowCount();
			}
			return $res->fetchAll();
		}
	}

	public function sendTransaction($callback, $arFields = []) {

		$result = false;

		if (is_callable($callback)) {

			$this->db->beginTransaction();
			$result = $callback($arFields);

			if ($result && empty($result['error'])) {

				$this->db->commit();
			} else {

				$this->db->rollBack();
			}
		}

		return $result;
	}

	public function update($table,$object,$where) {

		$sets = array();
		$whr = array();

		foreach ($object as $key => $value) {

			$sets[] = "$key=:$key";

			if($value === NULL){

				$object[$key]='NULL';
			}
		}


		foreach ($where as $key => $value) {

			$whr[$key] = "$key=$value";
		}

		$whr_s = implode(',', $whr);
		$sets_s = implode(',',$sets);

		$query = "UPDATE $table SET $sets_s WHERE $whr_s";

		$rs = $this->db->prepare($query);
		$rs->execute($object);

		if($rs->errorCode() != \PDO::ERR_NONE) {

			$info = $rs->errorInfo();
			throw new \Exception($info[2]);
		}

		return $rs->rowCount();
	}

	public function insert($table , $object) {

		$columns = array();
		$masks = array();

		foreach($object as $key => $value){

			$columns[] = $key;
			$masks[] = ":$key";

			if($value === null){
				$object[$key] = 'NULL';
			}
		}

		$columns_s = implode(',', $columns);
		$masks_s = implode(',', $masks);

		$query = "INSERT INTO $table ($columns_s) VALUES ($masks_s)";

		$res = $this->db->prepare($query);
		$res->execute($object);

		if($res->errorCode() != \PDO::ERR_NONE) {

			$info = $res->errorInfo();
			throw new \Exception($info[2]);
		}

		return $this->db->lastInsertId();
	}
}

?>
