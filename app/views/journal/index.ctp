<!-- START FILTER -->
		<?php echo $form->create('Filter',array('url' => '/journal/index','id'=>'filters')); ?>
		
		<table cellpadding="0" cellspacing="0">
			<tr>	
				<th>Month </th>
				<td><?php echo $form->month('Filter',$selected_month,null,false)?></td>

				<th>Year </th>
				<td><?php echo $form->year('Filter',2006,null,$selected_year,null,false)?></td>
				
				<th><?php echo $form->submit('Show',array('value'=>'Show'))?></th>
			</tr>
		</table>
<div class="actions">
        <ul>
                <li><?php echo $html->link("<span>".__('Tambah Jurnal', true)."</span>", array('action' => 'add'),array(), false, false); ?></li>
        </ul>
</div>
<br/><br/>
<?php
        $paginator->options(array('url' => $this->passedArgs));
        echo $paginator->prev('« Previous ', null, null);
        echo $paginator->numbers();        
	echo $paginator->next(' Next »', null, null);
?>
 menampilkan
<?php echo $paginator->counter(); ?>
<table cellpadding="0" cellspacing="0" class="list" border=1>
    <tr>
        <th><?php echo $paginator->sort('ID','Journal.id')?></th>
        <th><?php echo $paginator->sort('Tanggal','Journal.created')?></th>
        <th width='300'>Note</th>
        <th>Akun</th>
        <th>Debit</th>
        <th>Kredit</th>        
    </tr>
<?php foreach($journals as $journal):?>
    <?php if(!empty($journal['JournalTransaction'])):?>
        <tr>
        <td valign='middle' rowspan='<?php echo count($journal['JournalTransaction'])+1?>'><i><?php echo $journal['Journal']['id'];?></i></td>
        <td valign='middle' rowspan='<?php echo count($journal['JournalTransaction'])+1?>'><i><?php $arr = split(" ",$journal['Journal']['created']); echo $arr[0]?></i></td>
        <td valign='middle' rowspan='<?php echo count($journal['JournalTransaction'])+1?>'>
            <i><?php echo $journal['Journal']['note']?></i>
            (<?php echo $html->link('hapus journal', '/journal/delete/'.$journal['Journal']['id']);?>)
        </td>
        <td></td>
        <td></td>
        <td></td>        
        </tr>
        <?php foreach($journal['JournalTransaction'] as $transaction):?>
            <tr>
            <td><?php echo $html->link($transaction['AccountingAccount']['name'], '/accounting_reportings/buku_besar/'.$transaction['AccountingAccount']['id'].'/'.date('m').'/'.date('Y'))?></td>
            <?php if($transaction['type']=='DEBIT'):?>
                <td style="text-align:right"><?php echo $number->currency($transaction['amount'])?></td>
                <td></td>
            <?php else:?>
                <td></td>
                <td style="text-align:right"><?php echo $number->currency($transaction['amount'])?></td>
            <?php endif;?>            
            </tr>
        <?php endforeach;?>
    <?php endif;?>
<?php endforeach;?>
</table>
<br/>
       <?php
        echo $paginator->prev('« Previous ', null, null);
        echo $paginator->numbers();        
	echo $paginator->next(' Next »', null, null);
?>
 menampilkan
<?php echo $paginator->counter(); ?>
<div class="actions">
        <ul>
                <li><?php echo $html->link("<span>".__('Tambah Jurnal', true)."</span>", array('action' => 'add'),array(), false, false); ?></li>
        </ul>
</div>
