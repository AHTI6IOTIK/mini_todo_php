<?php

namespace models;


class TodoModel extends BaseModel {

	public function __construct($tableName) {

		parent::__construct($tableName);
	}

	public function getCountTodos() {

		$query = 'SELECT id FROM todos';
		return $this->db->select($query, true);
	}

	public function getTodoList($arParams = []) {

		$query = 'SELECT TODOS.id, TODOS.user_email, TODOS.text, TODOS.user_name, STATUS.status_name, STATUS.id as status_id
					FROM '.$this->getTableName().' as TODOS 
					JOIN statuses as STATUS ON TODOS.status_id = STATUS.id';

		if (empty($arParams['sort']) &&  !empty($_COOKIE['by']) && !empty($_COOKIE['direction'])) {

			$arParams['sort']['by'] = $_COOKIE['by'];
			$arParams['sort']['direction'] =  $_COOKIE['direction'];
		}

		if (!empty($arParams['sort'])) {

			setcookie('by', $arParams['sort']['by']);
			setcookie('direction', $arParams['sort']['direction']);

			$query .= ' ORDER BY ' . $arParams['sort']['by'].' '.$arParams['sort']['direction'];
		}

		if (intval($arParams['navStartPos']) >= 0 && !empty($arParams['pageItemCount'])) {


			$query .= ' LIMIT ' . $arParams['navStartPos'] . ' ,' . $arParams['pageItemCount'];
		}


		return $this->db->select($query);
	}

	public function createTodo($arData) {

		$result = ['error' => 'Empty request or incorrect data'];

		if (!empty($arData) && is_array($arData)) {

			$todoID = $this->db->insert($this->getTableName(), $arData);

			if ($todoID > 0) {

				$result = ['succes' => 'successfully insert'];
			} else {

				$result = ['error' => 'Error insert todo'];
			}
		}

		return $result;
	}

	public function getTodoStatuses() {

		$result = ['error' => 'failed to get status'];

		$query = 'SELECT id, status_name FROM ' . $this->getTableName();
		$queryResult = $this->db->select($query);

		if (count($queryResult) > 0) {

			$result = ['statuses' => $queryResult];
		}

		return $result;
	}

	public function changeTodo($data) {

		$result = ['error' => 'There is nothing to update'];

		$query = 'SELECT status_id, text FROM ' . $this->getTableName() . ' WHERE id = "' . $data['id'].'"';
		$queryResult = $this->db->select($query)[0];

		$modifiedField = [];
		foreach ($queryResult as $key => $value) {

			if ($key == 'id') {
				continue;
			}

			if (trim($value) != trim($data[$key])) {

				$modifiedField[$key] = $data[$key];
			}
		}

		if (count($modifiedField) > 0) {

			$affectRow = $this->db->update($this->getTableName(), $modifiedField, ['id' => $data['id']]);

			if ($affectRow > 0) {

				$result = ['success' => 'successfully update'];
			} else {

				$result = ['error' => 'unknown error'];
			}
		}

		return $result;
	}
}