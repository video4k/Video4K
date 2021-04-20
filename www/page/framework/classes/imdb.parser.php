<?php
	class IMDBParser
	{
		private $XPath = array ('en_combined' => NULL, 'de_combined' => NULL, 'releaseinfo' => NULL);
		
		public function GetEntryInformation ($ID, $Full = TRUE)
		{
			$ID = substr ($ID, 2);
			
			if (!empty ($ID))
			{
				if (($tDOM = $this->provideDOM ("http://www.imdb.com/title/tt{$ID}/combined", 'en-us')) != FALSE) $this->XPath['en_combined'] = new DOMXPath ($tDOM);
				if (($tDOM = $this->provideDOM ("http://www.imdb.com/title/tt{$ID}/combined", 'de-de')) != FALSE) $this->XPath['de_combined'] = new DOMXPath ($tDOM);
				
				if ($this->XPath['en_combined'] != NULL && $this->XPath['de_combined'] != NULL)
				{
					if (!$Full)
					{
						return array
						(
							'ID' => "tt{$ID}",
							'TYPE' => $this->readType (),
							'TITLE' => array ('EN' => $this->readTitle ('en'), 'DE' => $this->readTitle ('de')),
							'STATUS' => TRUE
						);
					} else {
						if (($tDOM = $this->provideDOM ("http://www.imdb.com/title/tt{$ID}/releaseinfo", 'en-us')) != FALSE) $this->XPath['releaseinfo'] = new DOMXPath ($tDOM);
						
						$TMDBData = $this->provideTMDB ($ID);
						
						return array
						(
							'ID' => "tt{$ID}",
							'TYPE' => $this->readType (),
							'SERIES' => $this->readSeriesLink (),
							'TITLE' => array ('EN' => $this->readTitle ('en'), 'DE' => $this->readTitle ('de')),
							'YEAR' => $this->readYear (),
							'RATING' => $this->readRating (),
							'DURATION' => $this->readDuration (),
							'RELEASED' => strtotime ($this->readReleaseDate ()),
							'GENRES' => $this->readGenres (),
							'POSTER' => $this->readPoster (),
							'PLOTS' => array ('EN' => $TMDBData['plot_en'], 'DE' => $TMDBData['plot_de']),
							'TRAILER' => array ('EN' => $TMDBData['trailer_en'], 'DE' => $TMDBData['trailer_de']),
							'DIRECTORS' => array_merge ($this->readDirectors (), $this->readProducers ()),
							'ACTORS' => $this->readActors (),
							'STATUS' => TRUE
						);
					}
				}
			}
			
			return array ('STATUS' => FALSE);
		}
	
		public function GetIMDBReleaseTag ($Tag)
		{
			global $Base, $DB;
			
			$Tag = trim (preg_replace ('/\\.[^.\\s]{3,4}$/', '', $Tag));
			$Check = $DB->Provide ("SELECT IMDB FROM releases WHERE tag = ?", array ('s', $Tag));
			
			if ($Check->num_rows)
			{
				$Check = $Check->fetch_assoc ();
				
				return $Check['IMDB'];
			} else {
				if (($Content = $Base->FetchContent ('https://www.xrel.to/search.html?mode=full', 'de-de', array ('xrel_search_query' => $Tag))) != NULL)
				{
					$DataSet = array ();
					
					if (preg_match ("/tt\\d{7}/i", $Content, $DataSet))
					{
						$DB->Provide ("INSERT INTO releases (tag, IMDB) VALUES (?, ?)", array ('ss', $Tag, trim ($DataSet[0])));
						return $DataSet[0];
					} else {
						$_XPath = new DOMXPath ($this->ProvideDOM (NULL, NULL, FALSE, $Content));					
						$Link = $_XPath->query ("//a[text()='Produktinformationen']/@href")->item (0)->nodeValue;
						
						if (!empty ($Link))
						{
							if (($Content = $Base->FetchContent ("https://www.xrel.to{$Link}")) != NULL)
							{
								if (preg_match ("/tt\\d{7}/i", $Content, $DataSet))
								{
									$DB->Provide ("INSERT INTO releases (tag, IMDB) VALUES (?, ?)", array ('ss', $Tag, trim ($DataSet[0])));
									return $DataSet[0];
								}
							}
						}
					}
				}
			}
			
			return NULL;
		}
	
		private function readType ()
		{
			return ($this->XPath['en_combined']->query ("//span[@class='tv-extra']")->length > 0 ? TRUE : FALSE);
		}

		private function readSeriesLink ()
		{
			$Matches = array ();
			
			@preg_match ('/(.*)\\/([^\\/]+)/', $this->XPath['en_combined']->query ("//h5[text()='TV Series:']/../div/a/@href")->item (0)->nodeValue, $Matches);
			if ($Matches) return trim ($Matches[2]);
		}
		
		private function readTitle ($Language)
		{
			return htmlspecialchars_decode (trim ($this->XPath["{$Language}_combined"]->query ("//div[@id='tn15title']/h1/text()[1]")->item (0)->nodeValue, " \t\n\r\0\x0B\""), ENT_QUOTES);
		}
		
		private function readYear ()
		{
			$tYear = intval ($this->XPath['en_combined']->query ("//a[contains(@href, '/year')]")->item (0)->nodeValue);
			if (!$tYear) $tYear = intval (str_replace (array ('(', ')'), '', $this->XPath['en_combined']->query ("//h1/span/text()[1]")->item (0)->nodeValue));
			
			return $tYear;
		}
		
		private function readRating ()
		{
			return floatval (substr ($this->XPath['en_combined']->query ("//div[@class='starbar-meta']/b")->item (0)->nodeValue, 0, -3));
		}
		
		private function readDuration ()
		{
			$Matches = array ();
			
			@preg_match ('/\d+/', $this->XPath['en_combined']->query ("//h5[text()='Runtime:']/..")->item (0)->nodeValue, $Matches);
			if (!empty ($Matches)) return intval ($Matches[0]); else return 0;
		}
		
		private function readReleaseDate ()
		{
			if ($this->XPath['releaseinfo'] != NULL)
			{
				$Date = NULL;
				$Results = $this->XPath['releaseinfo']->query ("//table[@id='release_dates']/tr");
				
				if ($Results->length > 0)
				{
					if ($Results->length > 1)
					{
						foreach ($Results AS $Item)
						{
							if (trim (strtoupper ($Item->childNodes->item (0)->nodeValue)) == 'USA')
							{
								$Date = trim ($Item->childNodes->item (2)->nodeValue);
								break;
							}
						}
					}
					
					if (!$Date) $Date = trim ($Results->item (0)->childNodes->item (2)->nodeValue);
					
					if (!empty ($Date))
					{
						if (substr_count ($Date, ' ') == 2)
						{
							$Date = date_create_from_format ('d M Y', $Date);
						} else $Date = date_create_from_format ('d M Y', "01 {$Date}");
						
						if ($Date != FALSE) return date_format ($Date, 'Y-m-d');
					}
				}
			}
			
			return NULL;
		}
		
		private function readGenres ()
		{
			$Genres = array ();
			
			foreach ($this->XPath['en_combined']->query ("//div[@class='info-content']/a[contains(@href, '/Sections/Genres')]") AS $Item)
				$Genres[] = trim ($Item->nodeValue);
			
			return $Genres;
		}
		
		private function readPoster ()
		{
			$URL = preg_replace ('/SX(\d+)_SY(\d+)_/i', 'SY0', trim ($this->XPath['en_combined']->query ("//head/meta[@property='og:image']/@content")->item (0)->nodeValue));
			
			return (filter_var ($URL, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) !== FALSE ? $URL : NULL);
		}
	
		private function readDirectors ()
		{
			$Directors = array ();
			
			foreach ($this->XPath['en_combined']->query ("//div[@id='director-info']/div/a") AS $Item)
				if (strpos (strtolower ($Item->nodeValue), '(more)') === FALSE) $Directors[] = trim ($Item->nodeValue);
			
			return $Directors;
		}

		private function readProducers ()
		{
			$Producers = array ();
			
			foreach ($this->XPath['en_combined']->query ("//a[@name='producers']/../../../../tr/td/a") AS $Item)				
				if (strpos (strtolower ($Item->nodeValue), 'producer') === FALSE) $Producers[] = trim ($Item->nodeValue);
			
			return $Producers;
		}
		
		private function readActors ()
		{
			$Count = 0;
			$Actors = array ();
			
			foreach ($this->XPath['en_combined']->query ("//table[@class='cast']/tr/td[@class='nm']") AS $Actor)
			{
				if (strpos (strtolower ($this->XPath['en_combined']->query ("//table[@class='cast']/tr/td[@class='char']")->item ($Count)->nodeValue), 'uncredited') === FALSE)
					if (strpos (strtolower ($Actor->nodeValue), '(more)') === FALSE) $Actors[] = trim ($Actor->nodeValue);
				
				$Count++;
			}
			
			return $Actors;
		}
		
		private function provideTMDB ($ID)
		{
			global $CONFIG, $Base;

			$Fetch = function ($ID, $Language)
			{
				global $CONFIG, $Base;
				
				$Response = $Base->FetchContent ("https://api.themoviedb.org/3/movie/{$ID}?language={$Language}&append_to_response=trailers&api_key=e5014425028fc8ef720e7c5aeda7a4ec", 'en-us');
				
				if ($Response != NULL)
				{
					$TrailerID = NULL;
					$Response = json_decode ($Response, TRUE);
					
					foreach ($Response['trailers']['youtube'] AS $Item)
					{
						if (strtolower ($Item['type']) == 'trailer')
						{
							if (!preg_match ('/^[a-z_A-Z0-9\-]{10,12}$/', $Item['source']))
							{
								if (filter_var ($Item['source'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) !== FALSE)
									$TrailerID = substr (parse_url ($Item['source'], PHP_URL_PATH), 1);
							} else $TrailerID = $Item['source'];
							
							break;
						}
					}
					
					return array (
						"plot_{$Language}" => trim ($Response['overview']),
						"trailer_{$Language}" => trim ($TrailerID)
					);
				}
				
				return array ();
			};
			
			$ID = str_pad ($ID, 7, '0', STR_PAD_LEFT);
			$Response = $Base->FetchContent ("https://api.themoviedb.org/3/find/tt{$ID}?external_source=imdb_id&api_key=e5014425028fc8ef720e7c5aeda7a4ec", 'en-us');

			if ($Response != NULL)
			{
				$Response = json_decode ($Response, TRUE);
				
				if (count ($Response['movie_results']) > 0)
					return array_merge ($Fetch ($Response['movie_results'][0]['id'], 'en'), $Fetch ($Response['movie_results'][0]['id'], 'de'));
			}
			
			return array ();
		}
		
		private function provideDOM ($URL, $AcceptLanguage = 'en-us')
		{
			global $Base;
			
			libxml_use_internal_errors (FALSE);
			
			$DOM = new DomDocument ();
			$DOM->recover = TRUE;
			$DOM->strictErrorChecking = FALSE;
			
			if (($Content = $Base->FetchContent ($URL, $AcceptLanguage)) != NULL)
			{
				return ($DOM->loadHTML ($Content) ? $DOM : FALSE);
			} else return FALSE;
		}
	}
?>