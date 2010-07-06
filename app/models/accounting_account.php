<?php
class AccountingAccount extends AppModel {

	var $name = 'AccountingAccount';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'ParentAccount' => array(
			'className' => 'AccountingAccount',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $hasMany = array(
		'Children' => array(
			'className' => 'AccountingAccount',
			'foreignKey' => 'parent_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
	
	function getAccountTree($parent_id = 0){
		$this->recursive = -1;
		
		$accounts = $this->find('all',array(
			'conditions' => array(
				'AccountingAccount.parent_id' => $parent_id
			),
			'order' => array('AccountingAccount.nomor'),
		));
		
		for ($i = 0; $i < count($accounts); $i++) {
			$count = $this->find('count', array(
				'conditions' => array(
					'AccountingAccount.parent_id' => $accounts[$i]['AccountingAccount']['id']
				)
			));
			if($count != 0){
				$accounts[$i]['children'] = $this->getAccountTree($accounts[$i]['AccountingAccount']['id']);
			}
		}
		
		return $accounts;
	}
	
	function getChildes(){
		return $this->find('all',array(
			'conditions' => array(
				'(SELECT count(*) from accounting_accounts WHERE parent_id = AccountingAccount.id)' => 0
			)
		));
	}
	
	function getChildesList(){
		return $this->find('list',array(
			'conditions' => '',
			'order'=> array('AccountingAccount.nomor'),
		));
	}
	
	function getChildSum($child_id,$month, $year){
		$sum = $this->getSum($child_id,$month,$year);
		$account = $this->read(null,$child_id);		
		if(!empty($account['Children'])){
			foreach($account['Children'] as $child){
				$sum += $this->getChildSum($child['id'],$month,$year);				
			}
		}		
		return $sum;
	}

	function getChildSumNow($child_id,$month, $year){
		$sum = $this->getSumNow($child_id,$month,$year);
		$account = $this->read(null,$child_id);		
		if(!empty($account['Children'])){
			foreach($account['Children'] as $child){
				$sum += $this->getChildSumNow($child['id'],$month,$year);				
			}
		}		
		return $sum;
	}
	
	function getSum($account_id,$month,$year){
		$sql = "SELECT type as tipe, SUM(amount) as amount
		FROM accounting_journal_transaction jt
		JOIN accounting_journal journal on journal.id = jt.accounting_journal_id
		WHERE MONTH(journal.created) = '$month' and YEAR(journal.created) = '$year' and accounting_account_id = $account_id
		GROUP BY type";		
		$result = $this->query($sql);		
		$sum = 0;
		foreach($result as $r){
			if($r['jt']['tipe']=='DEBIT'){
				$sum += $r[0]['amount'];
			}
			else{
				$sum -= $r[0]['amount'];
			}
		}		
		return $sum;
	}
	
	function getSumNow($account_id, $month,$year){
		$sql = "SELECT type as tipe, SUM(amount) as amount
		FROM accounting_journal_transaction jt
		JOIN accounting_journal journal on journal.id = jt.accounting_journal_id
		WHERE MONTH(journal.created) <= '$month' and YEAR(journal.created) = '$year' and accounting_account_id = $account_id
		GROUP BY type";		
		$result = $this->query($sql);		
		$sum = 0;
		foreach($result as $r){
			if($r['jt']['tipe']=='DEBIT'){
				$sum += $r[0]['amount'];
			}
			else{
				$sum -= $r[0]['amount'];
			}
		}		
		return $sum;
	}
	
	function getSumByType($type, $month,$year){
		$account = $this->find('all',array());
		$sql = "SELECT type as tipe, SUM(amount) as amount
		FROM accounting_journal_transaction jt
		JOIN accounting_journal journal on journal.id = jt.accounting_journal_id
		WHERE MONTH(journal.created) = '$month' and YEAR(journal.created) = '$year'
		GROUP BY type;
		";		
		$result = $this->query($sql);
		$sum = 0;
		foreach($result as $r){
			if($r['jt']['tipe']=='DEBIT'){
				$sum += $r[0]['amount'];
			}
			else{
				$sum -= $r[0]['amount'];
			}
		}		
		return $sum;		
	}
	
	function getSumBefore($account_id, $type, $month,$year){
		$sql = "SELECT type as tipe, SUM(amount) as amount
		FROM accounting_journal_transaction jt
		JOIN accounting_journal journal on journal.id = jt.accounting_journal_id
		WHERE MONTH(journal.created) < '$month' and YEAR(journal.created) = '$year' and accounting_account_id = $account_id
		GROUP BY type";
		$result = $this->query($sql);
		$sum = 0;
		foreach($result as $r){
			if($r['jt']['tipe']=='DEBIT'){
				$sum += $r[0]['amount'];
			}
			else{
				$sum -= $r[0]['amount'];
			}
		}
		//pr($result);
		return $sum;
	}
	
	function getSaldo($month,$year){
		$accounts = $this->find('all',array('conditions'=>array('AccountingAccount.parent_id'=>0)));
		//pr($accounts);
		$result = array();
		foreach($accounts as $account){
			$result[] = $this->getNeracaSaldo($account,$month,$year);
		}		
		return $result;
		
	}
	
	function getNeracaSaldo($account,$month,$year){		
		$result['nomor'] = $account['AccountingAccount']['nomor'];
		$result['name'] = $account['AccountingAccount']['name'];
					
		$result['saldo'] = $this->getSumNow($account['AccountingAccount']['id'],$month,$year);		
		
		if(!empty($account['Children'])){
			foreach($account['Children'] as $child){											
				$result['children'][] = $this->getNeracaSaldo($this->read(null,$child['id']),$month,$year);
			}
		}
		return $result;		
	}
	
	function getPendapatanPengeluaran($month="",$year="",$pendapatan=null,$beban=null){
		$conditions = array('AccountingAccount.account_type'=>array('PENDAPATAN','BEBAN'));
		$arr_id = array();
		if($pendapatan){
			$arr_id[] = $pendapatan;
		}
		if($beban){
			$arr_id[] = $beban;
		}
		if(!empty($arr_id) && $pendapatan && $beban){			
			$conditions['AccountingAccount.id'] = $arr_id;
		}else if(!empty($arr_id) && $pendapatan && !$beban){
			$conditions['OR'] = array('AccountingAccount.id'=>$arr_id,'AccountingAccount.account_type'=>'BEBAN');
		}else if(!empty($arr_id) && !$pendapatan && $beban) {
			$conditions['OR'] = array('AccountingAccount.id'=>$arr_id,'AccountingAccount.account_type'=>'PENDAPATAN');
		}
		else{
			$conditions['AccountingAccount.parent_id']=0;
		}		
		$accountPendapatans = $this->find('all',array('conditions'=>$conditions,'order'=>array('AccountingAccount.account_type','AccountingAccount.parent_id')));
		$result = array();
		foreach($accountPendapatans as $account){
			$amount = $this->getChildSum($account['AccountingAccount']['id'],$month,$year);
			$account['amount'] = $amount<0?-1*$amount:$amount;
			if(!empty($account['Children'])){
				$i = 0;
				foreach($account['Children'] as $child) {
					$amount = $this->getChildSum($child['id'],$month,$year);
					$account['Children'][$i++]['amount'] = $amount<0?-1*$amount:$amount;
				}
			}
			if($account['AccountingAccount']['account_type']=='PENDAPATAN'){
				$result['pendapatans'][] = $account;
			}
			else{
				$result['bebans'][] = $account;
			}
			
		}
		return $result;
	}
	
	function hasChild($id){
		$result = $this->query("select (count(*)>0) hasChild from accounting_accounts where parent_id = ".$id);
		if(!empty($result))
			return $result[0][0]['hasChild'];
		return 0;
	}
	
	function hasTransaction($id){
		$result = $this->query("select (count(*)>0) hasChild from accounting_journal_transaction where accounting_account_id  = ".$id);
		if(!empty($result))
			return $result[0][0]['hasChild'];
		return 0;
	}
	
	function getLabaOrRugi($month,$year){
		$sql = "SELECT ajt.type, sum(amount) as amount
			from accounting_journal_transaction ajt
			join accounting_journal aj on aj.id = ajt.accounting_journal_id
			join accounting_accounts aa on aa.id = ajt.accounting_account_id
			WHERE
			MONTH(aj.created) <= '$month' and YEAR(aj.created) <= '$year'
			and aa.account_type in ('PENDAPATAN','BEBAN')
			GROUP BY ajt.type
			";		
		$result = $this->query($sql);
		$sum = 0;
		foreach($result as $r){
			if($r['ajt']['type']=='DEBIT'){
				$sum += $r[0]['amount'];
			}
			else{
				$sum -= $r[0]['amount'];
			}
		}		
		return $sum;
	}
	
	function getNeraca($month,$year){		
		$this->belongsTo = null;
		$neraca['AKTIVA'] = $this->find('all',array('conditions'=>array('AccountingAccount.parent_id'=>0,'AccountingAccount.account_type'=>'AKTIVA')));		
		for($i=0;$i<count($neraca['AKTIVA']);$i++){
			$neraca['AKTIVA'][$i]['amount'] = $this->getChildSumNow($neraca['AKTIVA'][$i]['AccountingAccount']['id'],$month,$year);
		}
		
		$neraca['KEWAJIBAN'] = $this->find('all',array('conditions'=>array('AccountingAccount.parent_id'=>0,'AccountingAccount.account_type'=>'KEWAJIBAN')));		
		for($i=0;$i<count($neraca['KEWAJIBAN']);$i++){
			$neraca['KEWAJIBAN'][$i]['amount'] = $this->getChildSumNow($neraca['KEWAJIBAN'][$i]['AccountingAccount']['id'],$month,$year);
		}
		
		$neraca['EKUITAS'] = $this->find('all',array('conditions'=>array('AccountingAccount.parent_id'=>0,'AccountingAccount.account_type'=>'EKUITAS')));		
		for($i=0;$i<count($neraca['EKUITAS']);$i++){
			$neraca['EKUITAS'][$i]['amount'] = $this->getChildSumNow($neraca['EKUITAS'][$i]['AccountingAccount']['id'],$month,$year);
		}
		$neraca['LABARUGI'] = $this->getLabaOrRugi($month,$year);		
		return $neraca;
	}
}
?>
