<div id="tab" class="tab">
    <div class="spacer">
        <h2><?php echo upFirst($modul) ?></h2>
        <ul id="tab">
            <li><?php echo $html->link("Jurnal","/journal/index",array("class"=>($submodul=="journal")?"current":"")); ?></li>
            <li><?php echo $html->link("Reporting","/accounting_reportings",array("class"=>($submodul=="reporting")?"current":"")); ?></li>
            <li><?php echo $html->link("Admin","/accounting_accounts",array("class"=>($submodul=="admin")?"current":"")); ?></li>
        </ul>
    </div>
</div>
