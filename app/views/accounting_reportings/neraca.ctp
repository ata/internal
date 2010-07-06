<div class="accountingAccounts index page">
	<!--left column -->
	<div id="column_left" class="grid_3 alpha">
		<div class="grid_3 alpha omega">
			<?php echo $this->renderElement("nav_accounting_report")?>
		</div>
	</div>
	<!--end left column-->
	<div id="column_right" class="grid_13 omega">
		<h2><?php echo __('Neraca')?></h2>
		
		<!-- START FILTER -->
		<?php echo $form->create('FilterNeraca',array('url' => '/accounting_reportings/neraca')); ?>
		
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th>Per</th>
				<td><?php echo $form->month('FilterNeraca',$selected_month,null,false)?></td>
				<td><?php echo $form->year('FilterNeraca',2006,null,$selected_year,null,false)?></td>
				
				<th>
					<?php echo $form->end("Show")?>
				</th>
			</tr>
		</table>
		<!-- END FILTER -->
		<table cellpadding="0" cellspacing="0" class="list">
			<tbody>
				<tr>
					<th colspan="3">AKTIVA</th>
				</tr>
				<?php $aktiva = 0;foreach($neraca['AKTIVA'] as $n):?>
				<tr>
					<td><?php echo $n['AccountingAccount']['nomor']." ".$n['AccountingAccount']['name'] ?></td>
					<td class="money"><?php $aktiva += $n['amount'];echo $number->currency($n['amount']) ?></td>
					<td>&nbsp;</td>
				</tr>
				<?php endforeach?>
				<tr>
					<th colspan=2>Total Aktiva</th>
					<th><?php echo $number->currency($aktiva)?></th>
				</tr>
				
				<tr>
					<th colspan="3">KEWAJIBAN</th>
				</tr>
				<?php $aktiva = 0;foreach($neraca['KEWAJIBAN'] as $n):?>
				<tr>
					<td><?php echo $n['AccountingAccount']['nomor']." ".$n['AccountingAccount']['name'] ?></td>
					<td class="money"><?php $aktiva += $n['amount'];echo $number->currency($n['amount']) ?></td>
					<td>&nbsp;</td>
				</tr>
				<?php endforeach?>
				<tr>
					<th colspan=2>Total Kewajiban</th>
					<th><?php echo $number->currency($aktiva)?></th>
				</tr>
				
				<tr>
					<th colspan="3">EKUITAS</th>
				</tr>
				<?php $aktiva = 0;foreach($neraca['EKUITAS'] as $n):?>
				<tr>
					<td><?php echo $n['AccountingAccount']['nomor']." ".$n['AccountingAccount']['name'] ?></td>
					<td class="money"><?php $aktiva += $n['amount'];echo $number->currency($n['amount']) ?></td>
					<td>&nbsp;</td>
				</tr>
				<?php endforeach?>
				<tr>
					<td>Laba/rugi</td>
					<td class="money"><?php $aktiva += $neraca['LABARUGI'];echo $number->currency($neraca['LABARUGI']) ?></td>
					<td>&nbsp;</td>
				<tr>
					<th colspan=2>Total EKUITAS</th>
					<th><?php echo $number->currency($aktiva)?></th>
				</tr>
				
			</tbody>
		</table>		
	</div>
</div>
