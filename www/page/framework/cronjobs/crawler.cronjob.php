<?php
	if (php_sapi_name () != 'cli' || !empty ($_SERVER['REMOTE_ADDR'])) die ();
	require_once (dirname ($argv[0]) . '/../common.php');
	
	$CoverPath = (MAIN_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'covers' . DIRECTORY_SEPARATOR);
	$Tasks = $DB->Provide ("SELECT c.ID, d.MID, d.type, d.name_de, d.name_en, d.cover FROM crawler_query AS c LEFT JOIN directory AS d ON (d.ID = c.ID) ORDER BY c.timestamp ASC, c.priority ASC", NULL);
	
	if ($Tasks->num_rows)
	{
		$_c = new PID ();
		
		while ($Data = $Tasks->fetch_assoc ())
		{
			if (!empty ($Data['MID']))
			{
				set_time_limit (120);
				$EntryInfo = $IMDB->GetEntryInformation ("tt{$Data['MID']}");
				$FetchCover = TRUE;
				$CoverID = NULL;
				
				if ($EntryInfo['STATUS'] == TRUE)
				{
					if (!empty ($EntryInfo['SERIES'])) // merge with main series
					{
						if ($S_Entry = $IMDB->ProvideEntry ($EntryInfo['SERIES'], 's'))
						{
							$Result = $DB->Provide ("SELECT LID FROM languages_index WHERE EID = ?", array ('d', $Data['ID']));
							
							if ($Result->num_rows)
							{
								while ($rLang = $Result->fetch_assoc ())
									$DB->Provide ("INSERT IGNORE languages_index (EID, LID) VALUES (?, ?)", array ('dd', $S_Entry['EID'], $rLang['LID']));
							}
							
							$DB->Provide ("UPDATE links SET EID = ? WHERE EID = ?", array ('dd', $S_Entry['EID'], $Data['ID']));
							$DB->Provide ("DELETE FROM directory WHERE ID = ?", array ('d', $Data['ID']));
							$DB->Provide ("DELETE FROM crawler_query WHERE ID = ?", array ('d', $Data['ID']));
							$DB->Provide ("DELETE FROM languages_index WHERE EID = ?", array ('d', $Data['ID']));
						}
					} else {
						switch ($EntryInfo['TYPE'])
						{
							case 0: $gType = ($Data['type'] == 0 ? 0 : 1); break; // MOVIE
							case 1: $gType = 2; break; // SERIES
						}
						
						if (!empty ($Data['cover']))
						{
							if (file_exists ($CoverPath . $Data['cover']))
							{
								$FetchCover = FALSE;
								$CoverID = $Data['cover'];
							}
						}
						
						if (!empty ($EntryInfo['POSTER']) && $FetchCover)
						{
							if ($CoverData = $Base->FetchContent ($EntryInfo['POSTER']))
							{
								$CoverID = $Base->GenUID ();
								
								while (file_exists ($CoverPath . $CoverID))
									$CoverID = $Base->GenUID ();
								
								$fF = @fopen (($CoverPath . $CoverID), 'wb+');
								@fwrite ($fF, $CoverData);
								@fclose ($fF);
								@chmod (($CoverPath . $CoverID), 0666);
								
								$Image = new Image ($CoverPath . $CoverID);
								
								if ($Image->GetWidth () >= 214)
								{
									$Image->ResizeToWidth (214);
									$Image->Save ();
									
									$Result = $DB->Provide ("SELECT cover FROM directory WHERE ID = ?", array ('d', $Data['ID']))->fetch_assoc ();
									
									if (!empty ($Result['cover']))
									{
										if (file_exists ($CoverPath . $Result['cover']))
											@unlink ($CoverPath . $Result['cover']);
									}
								} else {
									@unlink ($CoverPath . $CoverID);
									$CoverID = NULL;
								}
							}
						}
						
						$DB->Provide ("UPDATE directory SET type = ?, name_de = ?, name_en = ?, year = ?, released = ?, rating = ?, duration = ?, cover = ?, plot_de = ?, plot_en = ? WHERE ID = ?",
								array ('dssddddsssd', $gType, $EntryInfo['TITLE']['DE'], $EntryInfo['TITLE']['EN'], $EntryInfo['YEAR'], $EntryInfo['RELEASED'],
										$EntryInfo['RATING'], $EntryInfo['DURATION'], $CoverID, $EntryInfo['PLOTS']['DE'], $EntryInfo['PLOTS']['EN'], $Data['ID']));
						{
							$IMDB->ProvideGenres ($Data['ID'], $EntryInfo['GENRES']);
							$IMDB->ProvideActors ($Data['ID'], $EntryInfo['ACTORS']);
							$IMDB->ProvideDirectors ($Data['ID'], $EntryInfo['DIRECTORS']);
							
							$DB->Provide ("DELETE FROM crawler_query WHERE ID = ?", array ('d', $Data['ID']));
						}
					}
				}
			}
		}
	}
?>