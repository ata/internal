<div class="accountingAccounts index page">
	<!--left column -->
	<div id="column_left" class="grid_3 alpha">
		<div class="grid_3 alpha omega">
			<?php echo $this->renderElement("nav_accounting_report")?>
		</div>
	</div>
	<!--end left column-->
	<div id="column_right" class="grid_13 omega">
		<h2><?php __('Buku Besar');?></h2>
		
		<!-- START FILTER -->
		<?php echo $form->create('FilterBukuBesar',array('url' => '/accounting_reportings/buku_besar','id'=>'filters')); ?>
		
		<table cellpadding="0" cellspacing="0">
			<tr>				
				<th>Account </th>
				<td><?php echo $form->select('FilterBukuBesar.account',$accounts,$account_id,null,true)?></td>
	
				<th>Month </th>
				<td><?php echo $form->month('FilterBukuBesar',$selected_month,null,false)?></td>

				<th>Year </th>
				<td><?php echo $form->year('FilterBukuBesar',2006,null,$selected_year,null,false)?></td>
				
				<th><?php echo $form->submit('Show',array('value'=>'Show'))?></th>
			</tr>
		</table>
		<?php echo $form->end()?>
		<?php if(isset($bukubesars)):?>
			<table cellpadding="0" cellspacing="0" class="list" border=1>
				<thead>
					<tr>
						<th colspan="3"><?php echo $account['AccountingAccount']['name'] ?></th>
						<th colspan="3">No. Perkiraan: <?php echo $account['AccountingAccount']['nomor'] ?></th>
					</tr>
					<tr>
						<th align="center">Tgl</th>
						<th align="center">Reff</th>
						<th align="center">Debit</th>
						<th align="center">Kredit</th>
						<th align="center">B-Debit</th>
						<th align="center">B-Kredit</th>
					</tr>
				</thead>
				<tbody>					
					<?php $saldo = 0;foreach($bukubesars as $buku):?>
					<tr>
						<td>
							<?php echo $time->format('d/m/y',$buku['Journal']['created']) ?>
						</td>
						<td>
							<?php echo ((isset($buku['hasChild'])&&$buku['hasChild'])||(isset($buku['hasTransaction'])&&$buku['hasTransaction']))?$html->link("(".$buku['nomor'].") ".$buku['Journal']['note'],array('action'=>'buku_besar',$buku['id'],$selected_month,$selected_year)):$buku['Journal']['note']?>
						</td>
						<td class="money">
							<?php if($buku['JournalTransaction']['type']=='DEBIT'){
								echo $number->currency($buku['JournalTransaction']['amount']);
								$saldo += $buku['JournalTransaction']['amount'];
							}?>
						</td>
						<td class="money">
							<?php if($buku['JournalTransaction']['type']=='CREDIT'){
								echo $number->currency($buku['JournalTransaction']['amount']);
								$saldo -= $buku['JournalTransaction']['amount'];
							}?>
								
						</td>
						<td class="money"><?php if($saldo>=0) echo $number->currency($saldo);?></td>
						<td class="money"><?php if($saldo<0) echo str_replace(')','',str_replace('(','',$number->currency($saldo)));?></td>
					</tr>
					<?php endforeach?>					
				</tbody>
			</table>
		<?php endif?>		
	</div>
</div>

