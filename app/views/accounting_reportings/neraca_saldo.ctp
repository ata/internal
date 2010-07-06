<?php


$jumlah_debit=0;
$jumlah_credit = 0;
function printChildren($children,$html,$number,&$jumlah_debit, &$jumlah_credit){	
	?>
	
<?php foreach ($children as $account):?>
	<tr>		
		<td><?php echo $account['nomor'] ?></td>
		<td><?php echo $account['name'] ?></td>	
		<td class="money"><?php echo $account['saldo'] > 0 ? $number->currency($account['saldo']): $number->currency(0)?></td>
		<td class="money"><?php echo $account['saldo'] < 0 ? $number->currency($account['saldo']): $number->currency(0) ?></td>
		<?php $jumlah_debit += $account['saldo'] > 0 ? $account['saldo']:0 ?>
		<?php $jumlah_credit += $account['saldo']<0 ? $account['saldo']:0 ?>
	</tr>		
	<?php
	if(!empty($account['children'])){
		printChildren($account['children'],$html,$number,$jumlah_debit,$jumlah_credit);	
	}?>
<?php endforeach;
}?>
<?php //pr($accounts);?>
<div class="accountingAccounts index page">
	<!--left column -->
	<div id="column_left" class="grid_3 alpha">
		<div class="grid_3 alpha omega">
			<?php echo $this->renderElement("nav_accounting_report")?>
		</div>
	</div>
	<!--end left column-->
	<div id="column_right" class="grid_13 omega">
		<h2><?php echo __('Neraca Saldo')?></h2>
		<!-- START FILTER -->
		<?php echo $form->create('FilterNeracaSaldo',array('url' => '/accounting_reportings/neraca_saldo')); ?>
		
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th>Per</th>
				<td><?php echo $form->month('FilterNeracaSaldo',$selected_month,null,false)?></td>
				<td><?php echo $form->year('FilterNeracaSaldo',2006,null,$selected_year,null,false)?></td>
				
				<th>
					<?php echo $form->end("Show")?>
				</th>
			</tr>
		</table>
		<!-- END FILTER -->
		
		
		<table cellpadding="0" cellspacing="0" class="list" border=1>
			<thead>
				<tr>
					<th>No.</th>
					<th>Account</th>
					<th>Debit</th>
					<th>Credit</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($accounts as $account):?>
				<?php if(empty($account['children'])):?>
				<tr>
					<td><?php echo $account['nomor'] ?></td>
					<td><?php echo $account['name'] ?></td>
					<td class="money"><?php echo $account['saldo'] > 0 ? $number->currency($account['saldo']):$number->currency(0) ?></td>
					<td class="money"><?php echo $account['saldo'] < 0 ? $number->currency($account['saldo']):$number->currency(0) ?></td>
					<?php $jumlah_debit += $account['saldo'] > 0 ? $account['saldo']:0 ?>
					<?php $jumlah_credit += $account['saldo']<0 ? $account['saldo']:0 ?>
				</tr>				
				<?php else:?>
				<tr>
					<th colspan=4><?php echo $account['nomor'] ?> : <?php echo $account['name'] ?></th>
				</tr>
				<?php printChildren($account['children'],$html,$number,$jumlah_debit,$jumlah_credit)?>
				<tr>
					<th colspan=4></th>
				</tr>
				<?php endif;?>
				
				<?php endforeach?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2">Saldo</th>
					<th class="money"><?php echo $number->currency($jumlah_debit) ?></th>
					<th class="money"><?php echo $number->currency($jumlah_credit) ?></th>
				</tr>
			</tfoot>
		</table>		
	</div>
</div>
