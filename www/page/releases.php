<?php
	require_once ('./framework/common.php');
	
	$User->CheckRights (array (USR_COADMIN, USR_ADMIN));
	
	if (!empty ($_POST['sEcho']))
	{
		$CData = $DB->Provide ("SELECT COUNT(DISTINCT a.tag) AS entries FROM api_query AS a LEFT OUTER JOIN releases AS r ON (r.tag = a.tag) WHERE r.ID IS NULL", NULL)->fetch_assoc ();
		$DataQuery = ("SELECT a.tag, a.timestamp FROM api_query AS a LEFT OUTER JOIN releases AS r ON (r.tag = a.tag) WHERE r.ID IS NULL" . (!empty ($_POST['sSearch']) ? " AND a.tag LIKE ?" : '') . " GROUP BY a.tag");
		
		$Data = array (
			'sEcho' => intval ($_POST['sEcho']),
			'iTotalRecords' => intval ($CData['entries']),
			'iTotalDisplayRecords' => 0,
			'aaData' => array ()
		);
		
		switch (intval ($_POST['iSortCol_0']))
		{
			case 0: $DataQuery .= ' ORDER BY a.tag'; break;
			case 1: $DataQuery .= ' ORDER BY a.timestamp'; break;
			default: $DataQuery .= ' ORDER BY a.tag'; break;
		} $DataQuery .= " {$_POST['sSortDir_0']}  LIMIT ?,?";
		
		if (!empty ($_POST['sSearch']))
		{
			$Result = $DB->Provide ($DataQuery, array ('sdd', "%{$_POST['sSearch']}%", $_POST['iDisplayStart'], $_POST['iDisplayLength']));
			$CountResult = $DB->Provide ("SELECT COUNT(DISTINCT a.tag) AS entries FROM api_query AS a LEFT OUTER JOIN releases AS r ON (r.tag = a.tag) WHERE r.ID IS NULL AND a.tag LIKE ?", array ('s', "%{$_POST['sSearch']}%"))->fetch_assoc ();
		} else {
			$Result = $DB->Provide ($DataQuery, array ('dd', $_POST['iDisplayStart'], $_POST['iDisplayLength']));
			$CountResult['entries'] = $Data['iTotalRecords'];
		}
		
		while ($Entry = $Result->fetch_assoc ())
		{
			$Column[0] = "<div style=\"margin-left: 4px;\" class=\"checkbox\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$Entry['tag']}\" /></div>";
			$Column[1] = "<i>{$Entry['tag']}</i>";
			$Column[2] = date ('d.m.Y H:i', $Entry['timestamp']);
			$Column[3] = ("<input type=\"text\" name=\"" . base64_encode ($Entry['tag']) . "\" placeholder=\"ttXXXXXXX\" style=\"width: 75px; height: 14px; line-height: 14px; font-size: 14px; margin-bottom: 0;\" />");
		
			$Data['aaData'][] = $Column;
		}
		
		$Data['iTotalDisplayRecords'] = $CountResult['entries'];
		
		echo json_encode ($Data);
	} else {
		if (count ($_POST['delete']) > 0)
		{
			foreach ($_POST['delete'] AS &$Value)
				$DB->Provide ("DELETE FROM api_query WHERE tag = ?", array ('s', trim ($Value)));
		}
	
		foreach ($_POST AS $Key => $Value)
		{
			if (preg_match ('/^tt\\d/', $Value))
			{
				$DB->Provide ("INSERT INTO releases (tag, IMDB) VALUES (?, ?)", array ('ss', base64_decode ($Key), $Value));
				$DB->Provide ("UPDATE api_query SET tries = 0 WHERE tag = ?", array ('s', base64_decode ($Key)));
			}
		}

		$Smarty->assign ('TABLE', FALSE);
		
		$Smarty->display ('header.tpl');
		$Smarty->display ('table.tpl');
		$Smarty->display ('contact.tpl');
		$Smarty->display ('releases.tpl');
		$Smarty->display ('footer.tpl');
	}
?>