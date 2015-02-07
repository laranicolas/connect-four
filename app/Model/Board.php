<?php
/**
 * Board model
 */
App::uses('AppModel', 'Model');

class Board extends AppModel {

	public $name = 'Board';

	public function beforeSave($options = array()) {

		if (!empty($this->data['Board']['matriz'])) {
			$this->data['Board']['matriz'] = serialize($this->data['Board']['matriz']);
		}
		return true;
	}

	public function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if (isset($val['Board']['matriz'])) {
				$results[$key]['Board']['matriz'] = unserialize($val['Board']['matriz']);
			}
		}
		return $results;
	}
}