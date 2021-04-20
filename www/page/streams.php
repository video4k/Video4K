<?php
	require_once ('./framework/common.php');
	
	$User->CheckRights (array (USR_TRUSTED, USR_COADMIN, USR_ADMIN));
	$Smarty->assign ('TABLE', FALSE);
	
	if (!empty ($_POST['search']))
	{
		if (strlen ($_POST['search']) >= 3)
		{
			$Search = $DB->Provide ("SELECT d.ID, d.type, {$CONFIG['S_NAME']}, d.year FROM directory AS d WHERE d.MID = ? OR CONCAT_WS('', d.name_de, d.name_en) LIKE ?", array ('ds', intval (substr ($_POST['search'], 2)), "%{$_POST['search']}%"));
			
			if ($Search->num_rows)
			{
				$DataSet = array ();
				
				while ($Entry = $Search->fetch_assoc ())
					$DataSet[] = array ('ID' => $Entry['ID'], 'TYPE' => $Entry['type'], 'name' => "{$Entry['name']} ({$Entry['year']})");
				
				$Smarty->assign ('SEARCH', $DataSet);
			}
		} else $Smarty->assign ('NOTIFICATION', '<div class="notification alert alert-warning">The search term needs to be at least <b>3</b> characters long.</div>');
	}
	
	if (!empty ($_POST['entry']) && !empty ($_POST['quality']) && !empty ($_POST['language']) && !empty ($_POST['links']))
	{
		$Entry = $DB->Provide ("SELECT d.MID FROM directory AS d WHERE d.ID = ?", array ('d', $_POST['entry']));
		
		if ($Entry->num_rows)
		{
			$Entry = $Entry->fetch_assoc ();
			$Result = $IMDB->ParseManual (array ('IMDB' => "tt{$Entry['MID']}", 'LID' => intval ($_POST['language']), 'LINKS' => $_POST['links'], 'RATED' => $_POST['quality'], 'SEASON' => $_POST['season'], 'EPISODE' => $_POST['episode']));
			
			if (intval ($Result['success']) > 0) $Smarty->assign ('SUCCESS_RESULT', "&rsaquo;&nbsp;{$Result['success']} link(s) were successfully added.");
			
			if (count ($Result['failed']) > 0)
			{
				$Message = NULL;
				
				foreach ($Result['failed'] AS &$LF)
				{
					switch ($LF[0])
					{
						case 1: $Message .= "&rsaquo;&nbsp;Link is corrupt or the hoster is not allowed ({$LF[1]}).<br />"; break;
						case 2: $Message .= "&rsaquo;&nbsp;Unknown error while recording or found duplicates ({$LF[1]}).<br />"; break;
					}
				} $Smarty->assign ('FAILED_RESULT', $Message);
			} elseif (intval ($Result['success']) == 0 && count ($Result['failed']) == 0) $Smarty->assign ('FAILED_RESULT', "Entry couldn't be found.");
		}
	}
	
	if (!empty ($_POST['imdb']) && (!empty ($_POST['name_de']) || !empty ($_POST['name_en'])) && !empty ($_POST['type']))
	{
		$Check = $DB->Provide ("SELECT d.ID, {$CONFIG['S_NAME']} FROM directory AS d WHERE d.MID = ?", array ('d', intval (substr ($_POST['imdb'], 2))));
		
		if ($Check->num_rows)
		{
			$Check = $Check->fetch_assoc ();
			$Smarty->assign ('RESULT', "The entry '{$Check['name']}' with the IMDB ID ({$_POST['imdb']}) already exists.");
		} else {
			$Entry = $IMDB->ProvideEntry ($_POST['imdb'], $_POST['type']);
			
			if ($Entry['EID'])
			{
				if ($DB->Provide ("UPDATE directory AS d SET d.name_de = ?, d.name_en = ? WHERE d.ID = ?", array ('ssd', $_POST['name_de'], $_POST['name_en'], $Entry['EID']))->affected_rows)
				{
					$Smarty->assign ('SUCCESS', TRUE);
					$Smarty->assign ('RESULT', "The entry is successfully created.");
				}
			}
			
			if (!$Smarty->getTemplateVars ('SUCCESS'))
				$Smarty->assign ('RESULT', "Unknown error while creating entry.");
		}
	}
	
	if (is_uploaded_file ($_FILES['file-bulk']['tmp_name']))
	{
		$Smarty->assign ('BULK', TRUE);
		
		if (($_FILES['file-bulk']['size'] >= 512) && ($_FILES['file-bulk']['size'] <= (1024 * 1024)) && ($_FILES['file-bulk']['type'] == 'text/plain'))
		{
			$BID = $Base->GenUID ();
			
			while (file_exists (BULK_PATH . DIRECTORY_SEPARATOR . $BID))
				$BID = $Base->GenUID ();
			
			if (move_uploaded_file ($_FILES['file-bulk']['tmp_name'], (BULK_PATH . DIRECTORY_SEPARATOR . $BID)))
			{
				@chmod ((BULK_PATH . DIRECTORY_SEPARATOR . $BID), 0666);
				
				if ($DB->Provide ("INSERT INTO bulk_query (UID, timestamp, BID) VALUES (?, UNIX_TIMESTAMP(), ?)", array ('ds', $User->Info['ID'], $BID))->affected_rows)
				{
					$Smarty->assign ('SUCCESS', TRUE);
					$Smarty->assign ('RESULT', "Your bulk file is successfully uploaded and queued in our system.");
				}
			}

			if (!$Smarty->getTemplateVars ('SUCCESS'))
				$Smarty->assign ('RESULT', "Unknown error while uploading bulk file.");
		} else $Smarty->assign ('RESULT', "Your bulk file is bigger than 1MB, smaller than 1KB or not a text document.");
	}
	
	$Smarty->display ('header.tpl');
	$Smarty->display ('table.tpl');
	$Smarty->display ('contact.tpl');
	$Smarty->display ('streams.tpl');
	$Smarty->display ('footer.tpl');
?>