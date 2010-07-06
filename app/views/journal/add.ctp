<?php
$html->css("form",null,array(),false);
$html->css("datepicker",null,array(),false);

$javascript->link('prototype', false);
//$javascript->link('scriptaculous.js?load=effects', false);
//$javascript->link('ajaxupload', false);
//$javascript->link('autoExpandContract', false);
$javascript->link('prototype-date-extensions', false);
$javascript->link('datepicker', false);
?>

<div id="journal" class="form">
<h2><?php __('Tambah Jurnal');?></h2>
<?php echo $form->create('Journal', array('url' => array('controller' => 'journal', 'action' => 'add'), 'id' => 'frmJournal'));?>
	<fieldset>
		<ol>
			<li>
				<?php echo $form->input('created', array("readonly"=>"false","value"=>date('Y-m-d'),"type"=>"text","id"=>"created", "class"=>"inputDate", "label"=>"<abbr title='required'>*</abbr>".__("Date", true))); ?>
				<p class="extra_helper"><?php __("format: yyyy-mm-dd", false) ?></p>
			</li>
			<li><?php echo $form->input('note', array('label' => '<abbr title="required">*</abbr> Note'));?></li>
		</ol>
	</fieldset>

    <table style="width:100%">
        <thead>
            <tr>
                <th>Debit</th>
                <th>Credit</th>
            </tr>
        </thead>
        <tr>
            <td>
                <div style="height:40px">
                    <?php echo $form->input('Debit.1.accounting_account_id', array('type' => 'select','label'=>false, 'div' =>false, 'options' =>$accounts, 'empty' => '-- pilih --')) ?>
                    <?php echo $form->input('Debit.1.amount', array('label'=>false, 'div'=>false, 'class' => 'amount debit')) ?>
                </div>
                <div style="height:40px">
                    <?php echo $form->input('Debit.2.accounting_account_id', array('type' => 'select','label'=>false, 'div' =>false, 'options' =>$accounts, 'empty' => '-- pilih --')) ?>
                    <?php echo $form->input('Debit.2.amount', array('label'=>false, 'div'=>false, 'class' => 'amount debit')) ?>
                    <a href="#hapus" class='delRow' onclick='this.up().remove()'>hapus</a>
                </div>
                <div style="height:40px">
                    <?php echo $form->input('Debit.3.accounting_account_id', array('type' => 'select','label'=>false, 'div' =>false, 'options' =>$accounts, 'empty' => '-- pilih --')) ?>
                    <?php echo $form->input('Debit.3.amount', array('label'=>false, 'div'=>false, 'class' => 'amount debit')) ?>
                    <a href="#hapus" class='delRow' onclick='this.up().remove()'>hapus</a>
                </div>
                <div id="debitAdd" style="margin-left: 280px">
                    <a id="btnDebitAdd" href="#add">New Row</a>
                </div>
            </td>
            <td>
                <div style="height:40px">
                    <?php echo $form->input('Credit.1.accounting_account_id', array('type' => 'select','label'=>false, 'div' =>false, 'options' =>$accounts, 'empty' => '-- pilih --')) ?>
                    <?php echo $form->input('Credit.1.amount', array('label'=>false, 'div'=>false, 'class' => 'amount credit')) ?>
                </div>
                <div style="height:40px">
                    <?php echo $form->input('Credit.2.accounting_account_id', array('type' => 'select','label'=>false, 'div' =>false, 'options' =>$accounts, 'empty' => '-- pilih --')) ?>
                    <?php echo $form->input('Credit.2.amount', array('label'=>false, 'div'=>false, 'class' => 'amount credit')) ?>
                    <a href="#hapus" class='delRow' onclick='this.up().remove()'>hapus</a>
                </div>
                <div style="height:40px">
                    <?php echo $form->input('Credit.3.accounting_account_id', array('type' => 'select','label'=>false, 'div' =>false, 'options' =>$accounts, 'empty' => '-- pilih --')) ?>
                    <?php echo $form->input('Credit.3.amount', array('label'=>false, 'div'=>false, 'class' => 'amount credit')) ?>
                    <a href="#hapus" class='delRow' onclick='this.up().remove()'>hapus</a>
                </div>
                <div id="creditAdd" style="margin-left: 280px">
                    <a id="btnCreditAdd" href="#add">New Row</a>
                </div>
            </td>
        </tr>
    </table>
	<div class="buttons">
		<button type="submit" class="button">
			<?php echo $html->image("icon/add_16.png") ?> Add
		</button>
		<?php echo $html->link('cancel',array('action'=>'index'))?>
	</div>
</form>
</div>

<script type="text/javascript">
	var picker = new Control.DatePicker('created', { dateFormat:"yyyy-MM-dd", icon:"../img/icon/calendar.png"});
    var idx = 4;
    $('btnDebitAdd').observe('click', function(e){
        var optionTemplate = $('Debit1AccountingAccountId').innerHTML;

        var out = '';
        out += '<select name="data[Debit]['+idx+'][accounting_account_id]">' + optionTemplate + '</select>';
        out += '<input name="data[Debit]['+idx+'][amount]" type="text"  class="amount debit"/>';
        out += '<a href="#hapus" class="delRow" onclick="this.up().remove()">hapus</a> ';
        out = '<div style="height:40px">' + out + '</div>';
        new Insertion.Before("debitAdd", out);
        idx++;

        return false;
    });

    $('btnCreditAdd').observe('click', function(e){
        var optionTemplate = $('Debit1AccountingAccountId').innerHTML;

        var out = '';
        out += '<select name="data[Credit]['+idx+'][accounting_account_id]">' + optionTemplate + '</select>';
        out += '<input name="data[Credit]['+idx+'][amount]" type="text" class="amount credit" />';
        out += '<a href="#hapus" class="delRow" onclick="this.up().remove()">hapus</a> ';
        out = '<div style="height:40px">' + out + '</div>';
        new Insertion.Before("creditAdd", out);
        idx++;

        return false;
    });

    Event.observe('frmJournal', 'submit', function(event) {
        var debit = 0;
        var credit = 0;

        $$('.amount').each(function(elm){
            if(elm.getValue()){
                if(elm.hasClassName('debit')){
                    debit += parseInt(elm.getValue());
                }else if(elm.hasClassName('credit')) {
                    credit += parseInt(elm.getValue());
                }
            }
        });

        if(debit != credit) {
            alert('Jumlah debit dan kredit tidak sama!');
            Event.stop(event); // stop the form from submitting
        }
    });
</script>
