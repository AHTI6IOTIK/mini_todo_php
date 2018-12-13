<?php

namespace controllers;

use models\TodoModel;

class Todos extends BaseController {

	public function changeTodo() {

		$result = ['error' => 'incorrect method'];

		if ($this->isAjax()) {

			if (!empty($_POST['edit_todo'])) {

				$result = ['error' => 'write all fields'];
				$temp = [];

				foreach ($_POST['edit_todo'] as $key => $value) {

					if (!empty($value)) {

						$temp[$key] = strip_tags($value);
					}
				}

				if (count($temp) === count($_POST['edit_todo'])) {

					$todoModel = new TodoModel('todos');
					$result = $todoModel->changeTodo($temp);
				}
			}
		}

		echo json_encode($result);
	}
}