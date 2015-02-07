<?php
/**
 * Connect Four controller.
 */

App::uses('AppController', 'Controller');

class ConnectFourController extends AppController {

	public $helpers = array('Form', 'Html');

	public $components = array('RequestHandler');

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	protected $_columns = 6;

	protected $_rows = 5;

/**
 * Displays a view
 *
 * @return void
 */
	public function view($player = null) {
		if (isset($player)) {
			$this->_player = (int) $player;
		}
		unset($player);
		App::uses('BoardsController', 'Controller');

		$Boards = new BoardsController($this->_player);
		$Boards->build();

		$settings = [
			'columns' => $this->_columns,
			'rows' => $this->_rows,
			'board' => array_reverse($Boards->_board, true),
			'player' => $this->_player
		];

		$this->set(compact('settings'));
	}

	public function push($data) {

		$data['message'] = sprintf('Your turn player %d', $data['otherPlayer']);
		if (!empty($data['win'])) {
			$data['message'] = 'You win';
		}

		App::import(
			'Vendor',
			'Pusher',
			array('file' => 'pusher' . DS . 'lib' . DS . 'Pusher.php')
		);

		$app_id = '101927';
		$app_key = '0ac7b4b7f07ac4ecbd02';
		$app_secret = '4e29c805db84d2b699c5';

		$pusher = new Pusher($app_key, $app_secret, $app_id);

		$event = sprintf('turn_%d_event', $data['otherPlayer']);

		$pusher->trigger('connect_four_channel', $event, $data);
	}
}
