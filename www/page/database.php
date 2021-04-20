<?php
	require_once ('./framework/common.php');
	
	$User->CheckRights (array (USR_COADMIN, USR_ADMIN));
	
	if (!empty ($_POST['sEcho']) && !isset ($_GET['LID']))
	{
		$CData = $DB->Provide ("SELECT COUNT(ID) AS entries FROM directory", NULL)->fetch_assoc ();
		$DataQuery = ("SELECT d.*, COUNT(l.ID) AS ActiveLinks FROM directory AS d LEFT JOIN links AS l ON (l.EID = d.ID AND l.active = 1)" . (!empty ($_POST['sSearch']) ? " WHERE CONCAT_WS('', d.MID, d.name_de, d.name_en) LIKE ?" : '') . " GROUP BY d.ID");
		
		$Data = array (
			'sEcho' => intval ($_POST['sEcho']),
			'iTotalRecords' => intval ($CData['entries']),
			'iTotalDisplayRecords' => 0,
			'aaData' => array ()
		);
		
		switch (intval ($_POST['iSortCol_0']))
		{
			case 1: $DataQuery .= ' ORDER BY d.type'; break;
			case 2: $DataQuery .= ' ORDER BY d.name_de'; break;
			case 3: $DataQuery .= ' ORDER BY d.name_en'; break;
			case 4: $DataQuery .= ' ORDER BY d.year'; break;
			case 5: $DataQuery .= ' ORDER BY ActiveLinks'; break;
			default: $DataQuery .= ' ORDER BY d.type'; break;
		} $DataQuery .= " {$_POST['sSortDir_0']}  LIMIT ?,?";
		
		if (!empty ($_POST['sSearch']))
		{
			$Result = $DB->Provide ($DataQuery, array ('sdd', "%{$_POST['sSearch']}%", $_POST['iDisplayStart'], $_POST['iDisplayLength']));
			$CountResult = $DB->Provide ("SELECT COUNT(d.ID) AS entries FROM directory AS d WHERE CONCAT_WS('', d.MID, d.name_de, d.name_en) LIKE ?", array ('s', "%{$_POST['sSearch']}%"))->fetch_assoc ();
		} else {
			$Result = $DB->Provide ($DataQuery, array ('dd', $_POST['iDisplayStart'], $_POST['iDisplayLength']));
			$CountResult['entries'] = $Data['iTotalRecords'];
		}
		
		while ($Entry = $Result->fetch_assoc ())
		{
			$Column[0] = "<a href=\"http://www.imdb.com/title/tt{$Entry['MID']}/\" target=\"{$Entry['MID']}\">tt{$Entry['MID']}</a>";
			
			switch ($Entry['type'])
			{
				case 0: $Column[1] = 'Cinema'; break;
				case 1: $Column[1] = 'Movie'; break;
				case 2: $Column[1] = 'Series'; break;
				default: $Column[1] = 'Unknown'; break;
			}
			
			$Column[2] = ("<a href=\"http://{$CONFIG['DOMAIN']}/#tt{$Entry['MID']}\" target=\"_{$Entry['MID']}\">" . (strlen ($Entry['name_de']) ? $Base->Truncate ($Entry['name_de'], 36) : '<i>No name given yet</i>') . "</a>");
			$Column[3] = ("<a href=\"http://{$CONFIG['DOMAIN']}/#tt{$Entry['MID']}\" target=\"_{$Entry['MID']}\">" . (strlen ($Entry['name_en']) ? $Base->Truncate ($Entry['name_en'], 28) : '<i>No name given yet</i>') . "</a>");
			$Column[4] = (intval ($Entry['year']) ? $Entry['year'] : '??');
			$Column[5] = $Entry['ActiveLinks'];
			$Column[6] = "<div id=\"{$Entry['ID']}\" class=\"table-options\"></div>";
		
			$Data['aaData'][] = $Column;
		}
		
		$Data['iTotalDisplayRecords'] = $CountResult['entries'];
		
		echo json_encode ($Data);
	}
	elseif (!empty ($_POST['sEcho']) && isset ($_GET['LID']))
	{
		$CData = $DB->Provide ("SELECT COUNT(ID) AS entries FROM links WHERE EID = ?", array ('d', $_GET['LID']))->fetch_assoc ();
		$DataQuery = ("SELECT l.*, la.text AS language_, u.name AS owner_ FROM links AS l
										LEFT JOIN languages AS la ON (la.ID = l.language)
										LEFT JOIN users AS u ON (u.ID = l.UID) WHERE l.EID = ?" . (!empty ($_POST['sSearch']) ? " AND CONCAT(l.URL, u.name) LIKE ?" : ''));
		
		$Data = array (
			'sEcho' => intval ($_POST['sEcho']),
			'iTotalRecords' => intval ($CData['entries']),
			'iTotalDisplayRecords' => 0,
			'aaData' => array ()
		);
		
		switch (intval ($_POST['iSortCol_0']))
		{
			case 1: $DataQuery .= ' ORDER BY u.name'; break;
			case 3: $DataQuery .= ' ORDER BY l.language'; break;
			case 4: $DataQuery .= ' ORDER BY l.season'; break;
			case 5: $DataQuery .= ' ORDER BY l.episode'; break;
			case 5: $DataQuery .= ' ORDER BY l.active'; break;
			default: $DataQuery .= ' ORDER BY u.name'; break;
		} $DataQuery .= " {$_POST['sSortDir_0']}  LIMIT ?,?";
		
		if (!empty ($_POST['sSearch']))
		{
			$Result = $DB->Provide ($DataQuery, array ('dsdd', $_GET['LID'], "%{$_POST['sSearch']}%", $_POST['iDisplayStart'], $_POST['iDisplayLength']));
			$CountResult = $DB->Provide ("SELECT COUNT(l.ID) AS entries FROM links AS l LEFT JOIN users AS u ON (u.ID = l.UID) WHERE l.EID = ? AND CONCAT(l.URL, u.name) LIKE ?", array ('ds', $_GET['LID'], "%{$_POST['sSearch']}%"))->fetch_assoc ();
		} else {
			$Result = $DB->Provide ($DataQuery, array ('ddd', $_GET['LID'], $_POST['iDisplayStart'], $_POST['iDisplayLength']));
			$CountResult['entries'] = $Data['iTotalRecords'];
		}
		
		while ($Entry = $Result->fetch_assoc ())
		{
			$Column[0] = "<input type=\"checkbox\" name=\"{$Entry['ID']}\" />";
			$Column[1] = "<i>{$Entry['owner_']}</i>";
			$Column[2] = ("<a href=\"{$Entry['URL']}\" target=\"blank_\">" . $Base->Truncate ($Entry['URL'], 55) . "</a>");
			$Column[3] = $Entry['language_'];
			$Column[4] = $Entry['season'];
			$Column[5] = $Entry['episode'];
			$Column[6] = ($Entry['active'] ? 'Active' : 'Inactive');
		
			$Data['aaData'][] = $Column;
		}
		
		$Data['iTotalDisplayRecords'] = $CountResult['entries'];
		
		echo json_encode ($Data);
	}
	elseif (!empty ($_POST['r']))
	{
		switch ($_POST['r'])
		{
			case 'entry':
			{
				$DB->Provide ("DELETE d, ai, di, gi, li, l, u, c FROM directory AS d
								LEFT JOIN actors_index AS ai ON (ai.EID = d.ID)
								LEFT JOIN directors_index AS di ON (di.EID = d.ID)
								LEFT JOIN genres_index AS gi ON (gi.EID = d.ID)
								LEFT JOIN languages_index AS li ON (li.EID = d.ID)
								LEFT JOIN links AS l ON (l.EID = d.ID)
								LEFT JOIN updates AS u ON (u.EID = d.ID)
								LEFT JOIN crawler_query AS c ON (c.ID = d.ID)
								WHERE d.ID = ?", array ('d', intval ($_POST['data'])));
			} break;
			
			case 'link':
			{
				foreach (json_decode (html_entity_decode ($_POST['data']), TRUE) AS $LID)
					$DB->Provide ("DELETE FROM links WHERE ID = ?", array ('d', intval ($LID)));
			} break;
		}
	} else {
		$Smarty->assign ('TABLE', FALSE);
		
		$Smarty->display ('header.tpl');
		$Smarty->display ('table.tpl');
		$Smarty->display ('contact.tpl');
		$Smarty->display ('database.tpl');
		$Smarty->display ('footer.tpl');
	}
?>