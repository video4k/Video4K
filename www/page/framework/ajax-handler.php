<?php
	require_once ('common.php');
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (!empty ($_POST['sEcho'])) // DataTable Request
		{
			// Additional Security
			if ($_POST['iDisplayLength'] > 100) $_POST['iDisplayLength'] = 100;
			$_POST['sSortDir_0'] = $DB->Escape ($_POST['sSortDir_0']);
			$_POST['filter'] = $DB->Escape ($_POST['filter']);
			
			$CData = $DB->Provide ("SELECT COUNT(d.ID) AS entries FROM directory AS d WHERE (d.name_de <> '' OR d.name_en <> '')", NULL)->fetch_assoc ();
			
			$Data = array (
				'sEcho' => intval ($_POST['sEcho']),
				'iTotalRecords' => intval ($CData['entries']),
				'iTotalDisplayRecords' => 0,
				'aaData' => array ()
			);
			
			switch (intval ($_POST['iSortCol_0']))
			{
				case 1: $gSorting = "ORDER BY name {$_POST['sSortDir_0']}"; break;
				case 4: $gSorting = "ORDER BY d.rating {$_POST['sSortDir_0']}"; break;
				default: $gSorting = "ORDER BY d.year DESC"; break; // d.timestamp
			}
			
			if (!empty ($_POST['sSearch']))
			{
				$_POST['sSearch'] = html_entity_decode ($_POST['sSearch']);
				
				$Result = $DB->Provide ("SELECT d.*, {$CONFIG['S_NAME']} FROM directory AS d WHERE (d.name_de <> '' OR d.name_en <> '') AND CONCAT_WS('', d.name_de, d.name_en) LIKE ? {$gSorting} LIMIT ?, ?", array ('sdd', "%{$_POST['sSearch']}%", $_POST['iDisplayStart'], $_POST['iDisplayLength']));
				$CountResult = $DB->Provide ("SELECT COUNT(d.ID) AS entries FROM directory AS d WHERE (d.name_de <> '' OR d.name_en <> '') AND CONCAT_WS('', d.name_de, d.name_en) LIKE ?", array ('s', "%{$_POST['sSearch']}%"))->fetch_assoc ();
			} else {
				switch ($_POST['type'])
				{
					case 'cinema': $gType = 0; break;
					case 'movies': $gType = 1; break;
					case 'series': $gType = 2; break;
					case 'updates': $gType = -1; break;
					default: $gType = -1; break;
				}
				
				if ($gType == -1) // List Updates
				{
					$gSorting = str_replace ('ORDER BY', ',', $gSorting);
					$gDate = (strtotime ($_POST['filter']) ? strtotime ($_POST['filter']) : ($CONFIG['CURUPDATES'] ? time () : (time () - 0x15180)));
					
					$Result = $DB->Provide ("SELECT d.*, u.LID, u.season, u.episode, {$CONFIG['S_NAME']} FROM updates AS u LEFT JOIN directory AS d ON (d.ID = u.EID) WHERE (d.name_de <> '' OR d.name_en <> '')
												AND (u.season != 0 AND u.episode != 0) AND u.datestamp = FROM_UNIXTIME(?, '%Y-%c-%e') ORDER BY d.type ASC, u.LID ASC {$gSorting} LIMIT ?, ?",
													array ('ddd', $gDate, $_POST['iDisplayStart'], $_POST['iDisplayLength']));
					
					$CountResult = $DB->Provide ("SELECT COUNT(d.ID) AS entries FROM updates AS u LEFT JOIN directory AS d ON (d.ID = u.EID) WHERE (d.name_de <> '' OR d.name_en <> '')
													AND u.datestamp = FROM_UNIXTIME(?, '%Y-%c-%e')", array ('d', $gDate))->fetch_assoc ();
				} else {
					switch (strtoupper ($_POST['filter']))
					{
						case 'ALL': break;
						case '#': $gLetter = "HAVING name NOT REGEXP '^[[:alpha:]]'"; break;
						default: $gLetter = "HAVING name LIKE '{$_POST['filter']}%'"; break;
					}
					
					$Result = $DB->Provide ("SELECT d.*, {$CONFIG['S_NAME']} FROM directory AS d WHERE (d.name_de <> '' OR d.name_en <> '') AND d.type = ? {$gLetter} {$gSorting} LIMIT ?, ?", array ('ddd', $gType, $_POST['iDisplayStart'], $_POST['iDisplayLength']));
					$CountResult = $DB->Provide ("SELECT COUNT(q.ID) AS entries FROM (SELECT d.ID, {$CONFIG['S_NAME']} FROM directory AS d WHERE (d.name_de <> '' OR d.name_en <> '') AND d.type = ? {$gLetter}) AS q", array ('d', $gType))->fetch_assoc ();
				}
			}
			
			while ($Entry = $Result->fetch_assoc ())
			{
				if (($gType == -1) && !empty ($Data['aaData'][$Entry['ID']]))
				{
					if ($Entry['type'] == 2)
					{
						$SeriesData = array ();
						
						if (@preg_match ("/Season (\d+)\ Episode (\d+)/i", $Data['aaData'][$Entry['ID']][1], $SeriesData))
						{
							if (intval ($SeriesData[1]) == $Entry['season'])
							{
								$Data['aaData'][$Entry['ID']][1] = preg_replace ("/Episode (\d+)/i", "Episode {$SeriesData[2]} & {$Entry['episode']}", $Data['aaData'][$Entry['ID']][1]);
								continue;
							}
						}
					} else $Entry['ID'] .= '_';
				}
				
				switch ($Entry['type'])
				{
					case 0: $Column[0] = "<img class=\"type\" src=\"//{$CONFIG['STATIC_URL']}/images/cinema.png\" title=\"Cinema\" alt=\"Cinema\" />"; break;
					case 1: $Column[0] = "<img class=\"type\" src=\"//{$CONFIG['STATIC_URL']}/images/movie.png\" title=\"Movie\" alt=\"Movie\" />"; break;
					case 2: $Column[0] = "<img class=\"type\" src=\"//{$CONFIG['STATIC_URL']}/images/series.png\" title=\"Series\" alt=\"Series\" />"; break;
				}
				
				if (($gType == -1) && ($Entry['type'] == 2))
				{
					$Column[1] = "<span data-language=\"{$CONFIG['LANG'][$Entry['LID']]['symbol']}\" data-season=\"{$Entry['season']}\" data-episode=\"{$Entry['episode']}\" rel=\"#tt{$Entry['MID']}\">{$Entry['name']} ({$Entry['year']})</span><small>Season {$Entry['season']} Episode {$Entry['episode']}</small>";
				} else $Column[1] = "<span data-language=\"{$CONFIG['LANG'][$Entry['LID']]['symbol']}\" rel=\"#tt{$Entry['MID']}\">{$Entry['name']} ({$Entry['year']})</span>";
				
				if ($gType == -1)
				{
					$Language = $DB->Provide ("SELECT l.symbol FROM languages AS l WHERE l.ID = ?", array ('d', $Entry['LID']))->fetch_assoc ();
					$Column[2] = "<img src=\"//{$CONFIG['STATIC_URL']}/images/flags/" . strtolower ($Language['symbol']) . ".png\" />";
				} else {
					$Languages = $DB->Provide ("SELECT l.symbol FROM languages AS l LEFT JOIN languages_index AS i ON (i.LID = l.ID) WHERE i.EID = ?", array ('d', $Entry['ID']));
					
					$Column[2] = "<img src=\"//{$CONFIG['STATIC_URL']}/images/flags/de.png\" style=\"opacity: {DE};\" />&nbsp;<img src=\"//{$CONFIG['STATIC_URL']}/images/flags/en.png\" style=\"opacity: {EN};\" />";
					
					while ($Language = $Languages->fetch_assoc ())
						$Column[2] = str_replace ("{{$Language['symbol']}}", 1, $Column[2]);

					foreach ($CONFIG['LANG'] AS &$Value)
						$Column[2] = str_replace ("{{$Value['symbol']}}", '0.3', $Column[2]);
				}
				
				$Genres = $DB->Provide ("SELECT g.name FROM genres AS g LEFT JOIN genres_index AS i ON (i.GID = g.ID) WHERE i.EID = ? LIMIT 1", array ('d', $Entry['ID']))->fetch_assoc ();
				
				$Column[3] = (!empty ($Genres['name']) ? $Genres['name'] : 'Unknown');
				$Column[4] = '<div class="stars-background" title="IMDB Rating: ' . $Entry['rating'] . '" alt="IMDB Rating: ' . $Entry['rating'] . '"><div class="stars-active" style="width: ' . ($Entry['rating'] * 10) . '%;"></div></div>';
				
				$Data['aaData'][$Entry['ID']] = $Column;
			}

			$Data['aaData'] = array_values ($Data['aaData']);
			$Data['iTotalDisplayRecords'] = $CountResult['entries'];
			
			echo json_encode ($Data);
		}
		elseif (!empty ($_POST['mID'])) // IMDB Request
		{
			$Entry = $DB->Provide ("SELECT d.*, {$CONFIG['S_NAME']} FROM directory AS d WHERE d.MID = ? AND (d.name_de <> '' OR d.name_en <> '')", array ('d', $_POST['mID']));
			
			if ($Entry->num_rows)
			{
				$Links = array ();
				$Entry = $Entry->fetch_assoc ();
				
				if ($_POST['raw'])
				{
					if (isset ($_POST['season']) && isset ($_POST['episode']))
					{
						$Result = $DB->Provide ("SELECT l.ID, l.HID, l.URL, l.quality FROM links AS l
													LEFT JOIN hoster AS h ON (h.ID = l.HID)
													LEFT JOIN languages AS g ON (g.ID = l.language)
													WHERE h.active = 1 AND l.active = 1 AND l.EID = ? AND g.symbol = ? AND l.season = ? AND l.episode = ? ORDER BY h.priority DESC",
														array ('dsdd', $Entry['ID'], strtoupper ($_POST['language']), $_POST['season'], $_POST['episode']));
					} else {
						$Result = $DB->Provide ("SELECT l.ID, l.HID, l.URL, l.quality FROM links AS l
													LEFT JOIN hoster AS h ON (h.ID = l.HID)
													LEFT JOIN languages AS g ON (g.ID = l.language)
													WHERE h.active = 1 AND l.active = 1 AND l.EID = ? AND g.symbol = ? ORDER BY h.priority DESC", array ('ds', $Entry['ID'], strtoupper ($_POST['language'])));
					}
					
					while ($Data = $Result->fetch_assoc ())
					{
						if (!isset ($Links["{$CONFIG['HOSTER'][$Data['HID']]['priority']}-{$Data['HID']}"]))
							$Links["{$CONFIG['HOSTER'][$Data['HID']]['priority']}-{$Data['HID']}"] = array_merge ($CONFIG['HOSTER'][$Data['HID']], array ('links' => array ()));
						
						$Buffer = $Data;
						unset ($Buffer['HID']);
						$Links["{$CONFIG['HOSTER'][$Data['HID']]['priority']}-{$Data['HID']}"]['links'][] = $Buffer;
					}
					
					foreach ($Links AS &$Link)
					{
						unset ($Link['ID']);
						unset ($Link['URL']);
						unset ($Link['active']);
						unset ($Link['priority']);
						unset ($Link['removestring']);
					}
					
					$Entry = array (); // clear entry
				} else {
					$tLKey = -1;
					$Entry['genres'] = array ();
					$Entry['actors'] = array ();
					$Entry['directors'] = array ();
					$Entry['languages'] = array ();
					
					$Genres = $DB->Provide ("SELECT g.name FROM genres AS g LEFT JOIN genres_index AS i ON (i.GID = g.ID) WHERE i.EID = ?", array ('d', $Entry['ID']));
					$Actors = $DB->Provide ("SELECT a.name FROM actors AS a LEFT JOIN actors_index AS i ON (i.AID = a.ID) WHERE i.EID = ?", array ('d', $Entry['ID']));
					$Directors = $DB->Provide ("SELECT d.name FROM directors AS d LEFT JOIN directors_index AS i ON (i.DID = d.ID) WHERE i.EID = ?", array ('d', $Entry['ID']));
					$Languages = $DB->Provide ("SELECT l.ID, l.symbol, l.text FROM languages AS l LEFT JOIN languages_index AS i ON (i.LID = l.ID) WHERE i.EID = ?", array ('d', $Entry['ID']));
					
					while ($Genre = $Genres->fetch_assoc ())
						$Entry['genres'][] = $Genre['name'];
					
					while ($Actor = $Actors->fetch_assoc ())
						$Entry['actors'][] = $Actor['name'];
					
					while ($Director = $Directors->fetch_assoc ())
						$Entry['directors'][] = $Director['name'];
					
					while ($Language = $Languages->fetch_assoc ())
					{
						if (isset ($_POST['language']))
						{
							if ($Language['symbol'] == strtoupper ($_POST['language'])) $tLKey = count ($Entry['languages']);
						} else if ($Language['symbol'] == 'EN') $tLKey = count ($Entry['languages']);
						
						$Entry['languages'][] = $Language;
					}
					
					$Entry['released'] = (!empty ($Entry['released']) ? date ('d.m.Y', $Entry['released']) : 'Unknown');
					$Entry['cover'] = "//{$CONFIG['STATIC_URL']}/covers/{$Entry['cover']}";
					if ($tLKey != -1 && ($CONFIG['VLANG'] != 'DE' || isset ($_POST['language']))) $Base->MoveElementTop ($Entry['languages'], $tLKey);
					
					if ($Entry['type'] == 2) // SERIES
					{
						$Result = $DB->Provide ("SELECT DISTINCT l.season, l.episode FROM links AS l
													LEFT JOIN hoster AS h ON (h.ID = l.HID)
													LEFT JOIN languages AS g ON (g.ID = l.language)
													WHERE h.active = 1 AND l.active = 1 AND l.EID = ? AND g.symbol = UPPER(?) ORDER BY l.season ASC, l.episode ASC", array ('ds', $Entry['ID'], (isset ($_POST['language']) ? $_POST['language'] : $Entry['languages'][0]['symbol'])));
						
						if ($Result->num_rows)
						{
							$Entry['seasons'] = array ();
							
							while ($Data = $Result->fetch_assoc ())
							{
								if (!$Entry['seasons'][$Data['season']])
									$Entry['seasons'][$Data['season']] = array ();
								
								$Entry['seasons'][$Data['season']][] = $Data['episode'];
							}
							
							$_POST['season'] = key ($Entry['seasons']);
							$_POST['episode'] = reset ($Entry['seasons'][$_POST['season']]);
						}
					}
					
					if (strtoupper ((isset ($_POST['language']) ? $_POST['language'] : $Entry['languages'][0]['symbol'])) == 'DE') $Entry['plot'] = $Entry['plot_de'];
						else $Entry['plot'] = $Entry['plot_en'];
					
					unset ($Entry['ID']);
					unset ($Entry['MID']);
					unset ($Entry['plot_de']);
					unset ($Entry['plot_en']);
					unset ($Entry['name_de']);
					unset ($Entry['name_en']);
					unset ($Entry['timestamp']);
				}
				
				echo json_encode (array ($Entry, $Links));
			}
		}
		elseif (isset ($_POST['aLogin']) && !$User->Info['VALID']) // Login Request
		{
			echo json_encode ($User->CheckLoginRequest ($_POST['username'], $_POST['password']));
		}
		elseif (isset ($_POST['aRegister']) && !$User->Info['VALID']) // Create Account Request
		{
			echo json_encode ($User->CheckRegisterRequest ($_POST['name'], $_POST['password'], $_POST['challenge'], $_POST['response']));
		}
		elseif (isset ($_POST['aIMDBCheck']) && $User->CheckRights (array (USR_TRUSTED, USR_COADMIN, USR_ADMIN), FALSE))
		{
			echo json_encode ($IMDB->GetEntryInformation ($_POST['ID'], FALSE));
		}
		elseif (isset ($_POST['aContact']))
		{
			echo json_encode ($Base->CheckContactRequest ($_POST['name'], $_POST['mail'], $_POST['subject'], $_POST['message'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']));
		}
	} exit ();
?>