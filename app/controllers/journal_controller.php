<?php
class JournalController extends AppController {

	var $name = 'Journal';
	var $helpers = array('Html', 'Form', 'Number');
	var $uses = array('Journal');
	var $component = array('Pagination');

	function __construct() {
		parent::__construct();
		$this->set("modul","accounting");
		$this->set("submodul","journal");
	}

    function add()
    {
        if($this->data) {
            $this->Journal->begin();
            $this->Journal->save($this->data['Journal']);
            $journal_id = $this->Journal->id;
            $journalTransaction = ClassRegistry::init('JournalTransaction');
            $journalTransaction->saveTransaction($this->data['Debit'], $journal_id, 'debit');
            $journalTransaction->saveTransaction($this->data['Credit'], $journal_id, 'credit');
            $this->Journal->commit();
            $this->redirect(array('action' => 'index'));
        }
        $accounts = ClassRegistry::init('AccountingAccount')->find('list');
        $this->set(compact(array('accounts')));
    }

    function index($month=null,$year=null,$page=1){	
	$selected_month = isset($month)?$month:date('m');
	$selected_year = isset($year)?$year:date('Y');	
	$this->paginate = array('conditions'=>array('MONTH(created)'=>$selected_month,'YEAR(created)'=>$selected_year));
	if(isset($this->data['Filter'])){		
		$selected_month = $this->data['Filter']['month'];
		$selected_year = $this->data['Filter']['year'];
		$this->redirect("/journal/index/$selected_month/$selected_year");
	}
	$this->Journal->recursive=2;
	$journals = $this->paginate();
	$jurnal_selected_from = "";
	$jurnal_selected_to = "";
	$this->set(compact(array('journals')));	
	$this->set('selected_month',$selected_month);
	$this->set('selected_year',$selected_year);
    }
    
    function delete($id){
	$this->Journal->delete($id);
	$this->redirect(array('action' => 'index'));
    }

}
