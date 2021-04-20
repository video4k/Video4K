<?php
	require_once ('./framework/common.php');
	
	$User->CheckRights (array (USR_COADMIN, USR_ADMIN));
	
	if (intval ($_POST['SID']))
	{
		if (!empty ($_POST['imdb']) && (!empty ($_POST['name_de']) || !empty ($_POST['name_en'])) && isset ($_POST['type']) && !empty ($_POST['genres']))
		{
			$MID = intval (substr ($_POST['imdb'], 2));
			
			if ($MID)
			{
				if ($_POST['retail'] == 'on')
					$IMDB->AddEntryUpdates (intval ($_POST['SID']));
				
				if ($_POST['refetch'] == 'on')
					$DB->Provide ("INSERT INTO crawler_query (ID, timestamp, priority) VALUES (?, UNIX_TIMESTAMP(), ?)", array ('dd', intval ($_POST['SID']), intval ($_POST['type'])));
				
				if (filter_var ($_POST['cover'], FILTER_VALIDATE_URL))
				{
					if ($CoverData = $Base->FetchContent ($_POST['cover']))
					{
						$CoverID = $Base->GenUID ();
						$CoverPath = (MAIN_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'covers' . DIRECTORY_SEPARATOR);
						
						while (file_exists ($CoverPath . $CoverID))
							$CoverID = $Base->GenUID ();
						
						$fF = @fopen (($CoverPath . $CoverID), 'wb+');
						@fwrite ($fF, $CoverData);
						@fclose ($fF);
						
						$Image = new Image ($CoverPath . $CoverID);
						
						if ($Image->GetWidth () >= 214)
						{
							$Image->ResizeToWidth (214);
							$Image->Save ();
							
							$Result = $DB->Provide ("SELECT cover FROM directory WHERE ID = ?", array ('d', intval ($_POST['SID'])))->fetch_assoc ();
							
							if (!empty ($Result['cover']))
							{
								if (file_exists ($CoverPath . $Result['cover']))
									@unlink ($CoverPath . $Result['cover']);
							}
							
							$DB->Provide ("UPDATE directory SET cover = ? WHERE ID = ?", array ('sd', $CoverID, intval ($_POST['SID'])));
						} else @unlink ($CoverPath . $CoverID);
					}
				}
				
				$DB->Provide ("DELETE FROM genres_index WHERE EID = ?", array ('d', intval ($_POST['SID'])));
				
				foreach ($_POST['genres'] AS &$Genre)
					$DB->Provide ("INSERT INTO genres_index (EID, GID) VALUES (?, ?)", array ('dd', intval ($_POST['SID']), intval ($Genre)));
				
				// Updates Check
				$Update = $DB->Provide ("SELECT type FROM directory WHERE ID = ?", array ('d', intval ($_POST['SID'])))->fetch_assoc ();
				
				if ($Update['type'] != 0 && intval ($_POST['type']) == 0)
					$IMDB->AddEntryUpdates (intval ($_POST['SID']));
				
				$DB->Provide ("UPDATE directory SET type = ?, name_de = ?, name_en = ?, year = ?, released = ?, duration = ?, plot_de = ?, plot_en = ?, trailer_de = ?, trailer_en = ? WHERE ID = ?",
						array ('dssdddssssd', intval ($_POST['type']), $_POST['name_de'], $_POST['name_en'], intval ($_POST['year']), strtotime ($_POST['released']),
								intval ($_POST['duration']), $_POST['plot_de'], $_POST['plot_en'], $_POST['trailer_de'], $_POST['trailer_en'], intval ($_POST['SID'])));
				
				$Smarty->assign ('SUCCESS', TRUE);
			}
		}
	}
	
	if (!empty ($_GET['ID']))
	{
		$Entry = $DB->Provide ("SELECT * FROM directory WHERE MID = ?", array ('s', intval (substr ($_GET['ID'], 2))));
		
		if ($Entry->num_rows)
		{
			$Entry = $Entry->fetch_assoc ();
			$Entry['genres'] = array ();
			$Genres = array ();
			
			$GenresList = $DB->Provide ("SELECT * FROM genres", NULL);
			$GenresSelect = $DB->Provide ("SELECT GID FROM genres_index WHERE EID = ?", array ('d', $Entry['ID']));
			
			if ($GenresList->num_rows)
			{
				while ($Genre = $GenresList->fetch_assoc ())
					$Genres[] = $Genre;
			}
			
			if ($GenresSelect->num_rows)
			{
				while ($Genre = $GenresSelect->fetch_assoc ())
					$Entry['genres'][] = $Genre['GID'];
			}
			
			if (!empty ($Entry['released'])) $Entry['released'] = date ('d.m.Y', $Entry['released']);
				else $Entry['released'] = '00.00.0000';
			
			$Smarty->assign ('GENRES', $Genres);
			$Smarty->assign ('ENTRY', $Entry);
		}
	}
	
	$Smarty->display ('edit.tpl');
?>