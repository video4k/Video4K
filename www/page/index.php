<?php
	require_once ('./framework/common.php');
	
	$Smarty->assign ('TABLE', TRUE);
	
	if (isset ($_GET['_escaped_fragment_']))
	{
		$Result = $DB->Provide ("SELECT name_de, name_en, year, plot_de, plot_en FROM directory WHERE MID = ?", array ('d', substr ($_GET['_escaped_fragment_'], 2)));
		
		if ($Result->num_rows)
		{
			$Smarty->assign  ('ENTRY', $Result->fetch_assoc ());
			$Smarty->display ('snapshot.tpl');
		} else header ('HTTP/1.1 404 Not Found');
	} else {
		$Smarty->display ('header.tpl');
		$Smarty->display ('table.tpl');
		$Smarty->display ('contact.tpl');
		$Smarty->display ('footer.tpl');
	}
?>