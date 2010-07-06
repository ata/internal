<?php
class JournalTransaction extends AppModel {

	var $name = 'JournalTransaction';
	var $useTable = 'accounting_journal_transaction';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'Journal' => array(
			'className' => 'Journal',
			'foreignKey' => 'accounting_journal_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'AccountingAccount' => array(
			'className' => 'AccountingAccount',
			'foreignKey' => 'accounting_account_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    public function saveTransaction($data, $journal_id, $type)
    {
        $transactions = array();
        $type = strtoupper($type);
        foreach($data as $row) {
            if($row['accounting_account_id']){
                $temp = array();
                $temp['accounting_journal_id'] = $journal_id;
                $temp['accounting_account_id'] = $row['accounting_account_id'];
                $temp['amount'] = $row['amount'];
                $temp['type'] = $type;
                $transactions[] = $temp;
            }
        }
        return $this->saveAll($transactions);
    }

}
?>
