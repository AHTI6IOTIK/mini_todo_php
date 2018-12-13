<?php

namespace traits;


trait getArLinksNavPage {

	protected $link_arr = [];


	public function getArLinksNavPage($num_page, $page_count) {

		$uri = '';
		$prevKey = '';
		if ($_SERVER['QUERY_STRING']) {
			foreach ($_GET as $key => $value) {

				if ($key != 'num_page') {

					$uri .= "$key=$value&amp;";
				}

				if ($prevKey == $key) {
					continue;
				}

				$prevKey = $key;
			}
		}
		if ($num_page > 3) {

			$this->link_arr['to_start']['href'] = '?' . $uri . 'num_page=1';
			$this->link_arr['to_start']['title'] = 'to start';
		}

		if ($num_page > 1) {

			$this->link_arr['go_back']['href'] = '?' . $uri . 'num_page=' . ($num_page - 1);
			$this->link_arr['go_back']['title'] = '<< go back';
		}

		if (($num_page - 2) > 0) {

			$this->link_arr[$num_page - 2]['href'] = '?' . $uri . 'num_page=' . ($num_page - 2);
			$this->link_arr[$num_page - 2]['title'] = $num_page - 2;
		}

		if (($num_page - 1) > 0) {

			$this->link_arr[$num_page - 1]['href'] = '?' . $uri . 'num_page=' . ($num_page - 1);
			$this->link_arr[$num_page - 1]['title'] = $num_page - 1;
		}

		$this->link_arr['current']['title'] = $num_page;


		if (($num_page + 1) <= $page_count) {

			$this->link_arr[$num_page + 1]['href'] = '?' . $uri . 'num_page=' . ($num_page + 1);
			$this->link_arr[$num_page + 1]['title'] = $num_page + 1;
		}

		if (($num_page + 2) <= $page_count) {

			$this->link_arr[$num_page + 2]['href'] = '?' . $uri . 'num_page=' . ($num_page + 2);
			$this->link_arr[$num_page + 2]['title'] = $num_page + 2;
		}

		if ($num_page < $page_count) {

			$this->link_arr['go_next']['href'] = '?' . $uri . 'num_page=' . ($num_page + 1);
			$this->link_arr['go_next']['title'] = 'go next >>';
		}

		if ($num_page < ($page_count - 2)) {

			$this->link_arr['to_end']['href'] = '?' . $uri . 'num_page=' . $page_count;
			$this->link_arr['to_end']['title'] = 'to end';
		}

		return $this->link_arr;

	}
}