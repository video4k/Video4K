<?php
	require_once ('./framework/common.php');
	
	$Smarty->assign ('TABLE', FALSE);
	
	$Smarty->display ('header.tpl');
	$Smarty->display ('table.tpl');
	$Smarty->display ('contact.tpl');
	$Smarty->display ('terms.tpl');
	$Smarty->display ('footer.tpl');
?>