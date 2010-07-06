<div id="side_nav">
    <ul id="nav-02">        
		<li <?php if($current=="buku_besar") echo "class='current'" ?>>
			<?php echo $html->link(__('Buku Besar', true),array(
				'action'=>'buku_besar'
			),array(), false, false); ?>
		</li>
		<li <?php if($current=="neraca_saldo") echo "class='current'" ?>>
			<?php echo $html->link(__('Neraca Saldo', true),array(
				'action'=>'neraca_saldo'
			),array(), false, false); ?>
		</li>
		<li <?php if($current=="neraca") echo "class='current'" ?>>
			<?php echo $html->link(__('Neraca', true),array(
				'action'=>'neraca'
			),array(), false, false); ?>
		</li>
		<li <?php if($current=="laba_rugi") echo "class='current'" ?>>
			<?php echo $html->link(__('Laba Rugi', true),array(
				'action'=>'laba_rugi'
			),array(), false, false); ?>
		</li>		
	</ul>
</div>
