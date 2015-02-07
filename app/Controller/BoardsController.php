<?php
/**
 * Board content controller.
 */
App::uses('ConnectFourController', 'Controller');

class BoardsController extends ConnectFourController {

	public $uses = array('Board');

	public $viewClass = 'Json';

	protected $_board = array();

	protected $_initValue = 0;

/**
 * Build a board
 *
 * @return void
 */
	public function build() {
		$data = $this->Board->findById(1);
		if (empty($data)) {
			$this->_board = array_fill($this->_initValue, $this->_rows, array_fill(0, $this->_columns, $this->_initValue));
		} else {
			$this->_board = $data['Board']['matriz'];
		}
	}

	public function add($colSelected) {
		$finish = false;
		foreach ($this->_board as $row => $cols) {
			foreach ($cols as $col => $val) {
				if ($col != $colSelected) {
					continue;
				}
				if ($val === 0) {
					$this->_board[$row][$col] = $this->_player;
					$finish = true;
					break;
				}
			}
			if ($finish) {
				break;
			}
		}
		$data = array('id' => '1', 'matriz' => $this->_board);
		$this->Board->save($data);
		return $row;
	}

	public function check($row, $col) {
		if (!isset($row) || !isset($col) || $row == $this->_rows || $col == $this->_columns) {
			return;
		}
		// Check for winner
		if ($this->_horizontalCheck($row, $col) || $this->_verticalCheck($row, $col)) {
			return true;
		}
		
		return false;
	}

	public function ajax_check() {
		if (empty($this->request->data)) {
			return false;
		}
		// $this->reset();die;
		// $this->request->data['player'] = 1;
		// $this->request->data['row'] = 1;
		// $this->request->data['col'] = 0;
		$players = [
			1 => 2,
			2 => 1
		];

		$this->_player = $this->request->data['player'];
		$this->_otherPlayer = $players[$this->_player];
		$this->build();

		$row = $this->request->data['row'];
		$col = $this->request->data['col'];

		$rowSaved = $this->add($col);
		$win = $this->check($rowSaved, $col);
		if ($win) {
			$this->reset();
		}

		$response = array(
			'rowSaved' => $rowSaved,
			'win' => $win,
			'otherPlayer' => $this->_otherPlayer
		);
		$this->push($response);
		$this->set('data', $response);
		$this->set('_serialize', 'data');
	}

	protected function _horizontalCheck($row, $col) {	
		if (!isset($row) || !isset($col)) {
			return false;
		}

		$board = $this->_board;
		$count = 0;

		// Count to left
		for ($i = $col; $i >= 0; $i--) {

			if ($board[$row][$i] !== $this->_player) {
				break;
			}
			$count += 1;
		}

		// Count to right
		for ($i = $col+1; $i < $this->_columns; $i++) {

			if ($board[$row][$i] !== $this->_player) {
				break;
			}

			$count += 1;
		}

		return ($count == 4) ? true : false;
	}

	protected function _verticalCheck($row, $col) {
		if (!isset($row) || !isset($col)) {
			return false;
		}

		$board = $this->_board;
		$count = 0;
		// Count to up
		for ($i = $row; $i >= 0; $i--) { 
			if ($board[$i][$col] !== $this->_player) {
				break;
			}

			$count += 1;
		}

		for ($i = $row + 1; $i < $this->_rows; $i++) {
			if ($board[$i][$col] !== $this->_player) {
				break;
			}

			$count += 1;
		}

		return ($count == 4) ? true : false; 
	}

	public function reset() {
		$this->Board->delete(1);
	}
}
