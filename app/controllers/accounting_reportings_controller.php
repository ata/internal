<?php

class AccountingReportingsController extends AppController {

	var $helpers = array('Html', 'Form','Time','Number');
	var $uses = array('JournalTransaction','AccountingTransaction','AccountingAccount','AccountingBukuBesar');
	var $components = array('Session');

	function __construct(){
		parent::__construct();
		$this->set("modul","accounting");
		$this->set("submodul","reporting");
		
	}
	
	function index(){
		$this->redirect(array('action'=>'buku_besar'));
	}	
		
	function buku_besar($account_id=0,$month=null,$year=null){		
		$selected_year = isset($year)?$year:date('Y');
		$selected_month = isset($month)?$month:date('m');
		if(isset($this->data['FilterBukuBesar'])){
			$date = $this->data['FilterBukuBesar']['year'] .'-'
					. $this->data['FilterBukuBesar']['month'];
					
			$account_id = $this->data['FilterBukuBesar']['account'];
			$selected_month = $this->data['FilterBukuBesar']['month'];
			$selected_year = $this->data['FilterBukuBesar']['year'];
		}
		else{
			$date = $year.'-'.$month;
		}
		$account = $this->AccountingAccount->read(null,$account_id);
		$this->set('account',$account);
						
		$bukubesars = $this->JournalTransaction->find('all',array(
			'conditions' => array(
				'JournalTransaction.accounting_account_id' => $account_id,
				'Journal.created >= ' => $date.'-1 00:00:00',
				'Journal.created <= ' => $date.'-31 23:59:59',
			)
		));
		$childs = array();
		if(!empty($account['Children'])){
			$ch = 0;
			foreach($account['Children'] as $child){
				$childs[$ch]['id'] = $child['id'];
				$childs[$ch]['nomor'] = $child['nomor'];
				$childs[$ch]['hasChild'] = $this->AccountingAccount->hasChild($child['id']);
				$childs[$ch]['hasTransaction'] = $this->AccountingAccount->hasTransaction($child['id']);
				$childs[$ch]['Journal']['created'] = $date."-".date('d');
				$childs[$ch]['Journal']['note'] = $child['name'];
				$childsum = $this->AccountingAccount->getChildSumNow($child['id'], $selected_month,$selected_year);
				if($childsum>=0){
					$childs[$ch]['JournalTransaction']['type'] = 'DEBIT';
				}
				else {
					$childsum *= -1;
					$childs[$ch]['JournalTransaction']['type'] = 'CREDIT';
				}
				$childs[$ch]['JournalTransaction']['amount'] = $childsum;
				$ch++;
			}
			$bukubesars = array_merge($childs,$bukubesars);
		}
		$this->set('bukubesars',$bukubesars);
		
		$month = (int) $this->data['FilterBukuBesar']['month'];
		$year = (int) $this->data['FilterBukuBesar']['year'];
		
		if($month - 1 == 0) {
			$month_before = 12;
			$year_before = $year - 1;
		} else {
			$month_before = $month - 1;
			$year_before = $year;
		}
		
		$date_before = $year_before . '-' . $month_before . '-31 23:59:59';
		$date_current_after = $date .'-1 00:00:00';
		$date_current_before = $date .'-31 23:59:59';
		
		$this->set('account_id',$account_id);
		$this->set('selected_month',$selected_month);
		$this->set('selected_year',$selected_year);
		$this->set('current','buku_besar');
		$this->set('accounts',$this->AccountingAccount->getChildesList());
	}
	
	function neraca_saldo(){
		$this->set('current','neraca_saldo');
		
		if(isset($this->data['FilterNeracaSaldo']))
		{
			$this->set('selected_month',$this->data['FilterNeracaSaldo']['month']);
			$this->set('selected_year',$this->data['FilterNeracaSaldo']['year']);
			
			$before = $this->data['FilterNeracaSaldo']['year'] . '-'
						. $this->data['FilterNeracaSaldo']['month']
						. '-31 23:59:59';
			$this->set('accounts', $this->AccountingAccount->getSaldo($this->data['FilterNeracaSaldo']['month'],$this->data['FilterNeracaSaldo']['year']));
			
			
		} else {
			$this->set('selected_month',date('m'));
			$this->set('selected_year',date('Y'));
			$this->set('accounts', $this->AccountingAccount->getSaldo(date('m'),date('Y')));
		}
		
	}
	
	function neraca(){
		$this->set('current','neraca');		
		if(isset($this->data['FilterNeraca'])){
			$this->set('selected_month',$this->data['FilterNeraca']['month']);
			$this->set('selected_year',$this->data['FilterNeraca']['year']);
			
			$month = $this->data['FilterNeraca']['month'];
			$year = $this->data['FilterNeraca']['year'];
			
		} else {
			$this->set('selected_month',date('m'));
			$this->set('selected_year',date('Y'));
			$month = date('m');
			$year = date('Y');
		}
		$this->set('neraca',$this->AccountingAccount->getNeraca($month,$year));
	}
	
	function laba_rugi(){
		
		$this->set('current','laba_rugi');
			
		if(isset($this->data['FilterLabaRugi']))
		{
			$selected_month = $this->data['FilterLabaRugi']['month'];
			$selected_year = $this->data['FilterLabaRugi']['year'];
			$pendapatan = $this->data['FilterLabaRugi']['pendapatan'];
			$beban = $this->data['FilterLabaRugi']['beban'];
		} else {
			$selected_month = date('m');
			$selected_year = date('Y');
			$pendapatan = null;
			$beban = null;
		}
		$accounts = $this->AccountingAccount->getPendapatanPengeluaran($selected_month,$selected_year,$pendapatan,$beban);		
		$a = $this->AccountingAccount->find('all',array('conditions'=>array('AccountingAccount.account_type'=>'PENDAPATAN','AccountingAccount.parent_id'=>0),'order'=>array('AccountingAccount.account_type','AccountingAccount.parent_id')));
		$b = $this->AccountingAccount->find('all',array('conditions'=>array('AccountingAccount.account_type'=>'BEBAN','AccountingAccount.parent_id'=>0),'order'=>array('AccountingAccount.account_type','AccountingAccount.parent_id')));
		foreach($a as $account){
			$accountPendapatans[$account['AccountingAccount']['id']] = $account['AccountingAccount']['nomor']." ".$account['AccountingAccount']['name'];
			if(!empty($account['Children'])){
				foreach($account['Children'] as $child){
					$accountPendapatans[$child['id']] = $child['nomor']." ".$child['name'];
				}
			}
		}
		$accountBebans = array();
		foreach($b as $account){
			$accountBebans[$account['AccountingAccount']['id']] = $account['AccountingAccount']['nomor']." ".$account['AccountingAccount']['name'];
			if(!empty($account['Children'])){
				foreach($account['Children'] as $child){
					$accountBebans[$child['id']] = $child['nomor']." ".$child['name'];
				}
			}
		}
		$this->set('accounts',$accounts);
		$this->set('selected_month',$selected_month);
		$this->set('selected_year', $selected_year);
		$this->set('accountPendapatans',$accountPendapatans);
		$this->set('accountBebans',$accountBebans);
	}
	
	

	
	
	
	
}
