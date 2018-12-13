<?php

namespace models;

abstract class BaseModel {

	private $tableName;
	protected $db;

	public function __construct($tableName) {

		$this->tableName = $tableName;
		$this->db = Db::getInstance();
	}

	/**
	 * @param mixed $tableName
	 */
	public function setTableName($tableName) {

		$this->tableName = $tableName;
	}


	/**
	 * @return mixed
	 */
	public function getTableName() {

		return $this->tableName;
	}
}