<div class="accountingAccounts index page">
	<!--left column -->
	<div id="column_left" class="grid_3 alpha">
		<div class="grid_3 alpha omega">
			<?php echo $this->renderElement("nav_accounting_report")?>
		</div>
	</div>
	<!--end left column-->
	<div id="column_right" class="grid_13 omega">
		<h2><?php echo __('Laporan Laba Rugi')?></h2>
		
		<!-- START FILTER -->
		<?php echo $form->create('FilterLabaRugi',array('url' => '/accounting_reportings/laba_rugi')); ?>
		
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th>Pendapatan</th>
				<td><?php echo $form->input('pendapatan',array('type'=>'select','label'=>'','empty'=>true,'options'=>$accountPendapatans))?></td>				
				<th>Beban</th>
				<td><?php echo $form->input('beban',array('type'=>'select','label'=>'','empty'=>true,'options'=>$accountBebans))?></td>
				<th>Per</th>
				<td><?php echo $form->month('FilterLabaRugi',$selected_month,null,false)?></td>
				<td><?php echo $form->year('FilterLabaRugi',2006,null,$selected_year,null,false)?></td>
				
				<th>
					<?php echo $form->end("Show")?>
				</th>
			</tr>
		</table>
		<!-- END FILTER -->
		<table cellpadding="0" cellspacing="0" class="list">
			<tbody>
				<tr>
					<th colspan="3">PENDAPATAN</th>
				</tr>
				<?php $sum_pendapatan =0; foreach($accounts['pendapatans'] as $account):?>
				<tr>
					<td><b><?php echo $account['AccountingAccount']['name'] ?></b></td>
					<td>&nbsp;</td>
					<td class="money"><?php $sum_pendapatan +=$account['amount']; echo $number->currency($account['amount']) ?></td>					
				</tr>
				<?php if(!empty($account['Children'])):?>
					<?php foreach($account['Children'] as $child):?>
						<tr>
							<td><?php echo $child['name'] ?></td>
							<td class="money"><?php echo $number->currency($child['amount']) ?></td>
							<td>&nbsp;</td>
						</tr>	
					<?php endforeach;?>
				<?php endif;?>
				<?php endforeach;?>
				<tr>
					<th colspan="3">BEBAN</th>
				</tr>
				<?php $sum_beban = 0;foreach($accounts['bebans'] as $account):?>
				<tr>
					<td><b><?php echo $account['AccountingAccount']['name'] ?></b></td>
					<td>&nbsp;</td>
					<td class="money"><?php $sum_beban +=$account['amount']; echo $number->currency($account['amount']) ?></td>					
				</tr>
				<?php if(!empty($account['Children'])):?>
					<?php foreach($account['Children'] as $child):?>
						<tr>
							<td><?php echo $child['name'] ?></td>
							<td class="money"><?php echo $number->currency($child['amount']) ?></td>
							<td>&nbsp;</td>
						</tr>	
					<?php endforeach;?>
				<?php endif;?>
				<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan='2'>Total Pendapatan</th>
					<th class="money"><?php echo $number->currency($sum_pendapatan) ?></th>
				</tr>
				<tr>
					<th colspan='2'>Total Beban</th>
					<th class="money"><?php echo $number->currency($sum_beban) ?></th>
				</tr>
				<tr>
					<th colspan='2'>Laba/Rugi</th>
					<th class="money"><?php echo $number->currency($sum_pendapatan-$sum_beban) ?></th>
				</tr>
			</tfoot>
		</table>		
	</div>
</div>
