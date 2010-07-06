<?php
class Journal extends AppModel {

	var $name = 'Journal';
	var $useTable = 'accounting_journal';
	var $hasMany = array(
			'JournalTransaction' => array(
				'className' => 'JournalTransaction',
				'foreignKey' => 'accounting_journal_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
}
?>