<?php

namespace controllers;


use models\TodoModel;
use traits\getArLinksNavPage;

class Index extends BaseController {

	use getArLinksNavPage;

	private $navStartPos = 0;
	private $pageItemCount = 3;

	public function __construct() {

		parent::__construct();

		$this->title .= '::Main page';
		$this->descriptionPage = 'descriptionPage Main page';
		$this->keyWordsPage .= ',Main page';
	}

	private function getTodoModel($tableName = 'todos') {

		return new TodoModel($tableName);
	}

	private function getPagen() {

		$todoModel = $this->getTodoModel();
		$num_page = 1;

		$todosCount = $todoModel->getCountTodos(); //узнаем кол-во элементов
		$pagesCount = ceil($todosCount / $this->pageItemCount); // расчитываем кол-во страниц


		if (!empty($_GET['num_page'])) {

			$num_page = (int)$_GET['num_page']; // Номер запрашиваемой страницы

			if ($num_page<1) {

				$num_page = 1;
			}

			if ($num_page > $pagesCount) {

				$num_page = $pagesCount;
			}
		}

		$this->navStartPos = ($num_page - 1) * $this->pageItemCount;

		return $this->template('pagen.phtml', ['pagen' => $this->getArLinksNavPage($num_page, $pagesCount)]);
	}

	public function index() {

		$this->pagen = $this->getPagen();

		$arParams = [
			'navStartPos' => empty($_COOKIE['navStartPos'])? $this->navStartPos : $_COOKIE['navStartPos'],
			'pageItemCount' => empty($_COOKIE['pageItemCount'])? $this->pageItemCount : $_COOKIE['pageItemCount'],
		];

		if (!empty($_POST['by']) && !empty($_POST['direction'])) {

			$arParams['sort']['by'] = htmlspecialchars($_POST['by']);
			$arParams['sort']['direction'] = htmlspecialchars($_POST['direction']);
		}

		$todoModel = $this->getTodoModel();

		if ($this->isPost() && !empty($_POST['form_data'])) {

			$result = ['error' => 'Writen all fields'];
			$tmp = [];

			foreach ($_POST['form_data'] as $key => $value) {

				if (strlen(strip_tags($value)) > 0) {

					$tmp[$key] = $value;
				}
			}

			if (count($tmp) === count($_POST['form_data'])) {

				array_merge($result, $todoModel->createTodo($tmp));
			}
		}

		$vars = [
			'todoList' => $todoModel->getTodoList($arParams),
		];



		$this->content = $this->template('index/index.phtml', $vars);
	}

}