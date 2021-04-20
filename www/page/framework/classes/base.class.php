<?php
	class Base
	{
		public function __construct ()
		{
			global $CONFIG, $DB;
			
			$this->DetectLanguage ();
			
			$Hoster = $DB->Provide ("SELECT * FROM hoster", NULL);
			$Languages = $DB->Provide ("SELECT * FROM languages", NULL);
			$TodaysUpdate = $DB->Provide ("SELECT COUNT(EID) AS entries FROM updates WHERE datestamp = CURDATE()", NULL)->fetch_assoc ();
			
			while ($Entry = $Hoster->fetch_assoc ())
				$CONFIG['HOSTER'][$Entry['ID']] = $Entry;
			
			while ($Entry = $Languages->fetch_assoc ())
			{
				$CONFIG['LANG'][$Entry['ID']] = $Entry;
				if ($Entry['symbol'] == 'EN') $CONFIG['LANG_DEFAULT_ID'] = $Entry['ID'];
			}
			
			$CONFIG['CURUPDATES'] = ($TodaysUpdate['entries'] >= 5 ? TRUE : FALSE);
			$CONFIG = array_merge ($CONFIG, $DB->Provide ("SELECT * FROM settings", NULL)->fetch_assoc ());
		}
		
		public function SecurityEscape (&$Array) 
		{
			reset ($Array);
			
			while (list ($Key, $Value) = each ($Array))
			{
				if (is_string ($Value))	$Array[$Key] = filter_var (strip_tags ($Value), FILTER_SANITIZE_STRING);
					elseif (is_array ($Value)) $Array[$Key] = $this->SecurityEscape ($Value);
			}
			
			return $Array;
		}
		
		public function DetectLanguage ()
		{
			global $CONFIG;
			
			preg_match_all ('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $LanguageData);
			
			if (count ($LanguageData[1]))
			{
				$LanguageData = array_combine ($LanguageData[1], $LanguageData[4]);
				foreach ($LanguageData AS $Language => $Value) if ($Value === '') $LanguageData[$Language] = 1;
				arsort ($LanguageData, SORT_NUMERIC);
			} else $LanguageData = array ('en' => 1);
			
			$CONFIG['VLANG'] = strtoupper (substr (key ($LanguageData), 0, 2));
			
			if ($CONFIG['VLANG'] == 'DE') $CONFIG['S_NAME'] = "IFNULL(d.name_de, d.name_en) AS name";
				else $CONFIG['S_NAME'] = "IFNULL(name_en, name_de) AS name";
		}
		
		public function GetSliderContent ()
		{
			global $CONFIG, $DB;
			
			$Data = array ();
			$Result = $DB->Provide ("SELECT d.MID, {$CONFIG['S_NAME']}, d.cover FROM directory AS d LEFT JOIN links AS l ON (l.EID = d.ID) WHERE (d.name_de <> '' OR d.name_en <> '') AND d.type = ? GROUP BY d.ID HAVING COUNT(l.ID) > 0 ORDER BY d.timestamp DESC", array ('d', 0));
			
			while ($Entry = $Result->fetch_assoc ())
			{
				$Entry['name'] = $this->Truncate ($Entry['name']);
				$Data[] = $Entry;
			}
			
			return $Data;
		}
		
		public function GenUID ()
		{
			mt_srand (crc32 (microtime ()));
			return md5 (sha1 (time () * mt_rand (0, 100)));
		}
		
		public function RandomString ($Length)
		{
			return substr (str_shuffle ('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $Length);
		}
		
		public function Truncate ($String, $Length = 38)
		{
			if (strlen ($String) > $Length) $String = (substr ($String, 0, $Length) . ' [...]');
			
			return $String;
		}
		
		public function URLf ($Data)
		{
			return rawurlencode (mb_convert_encoding ($Data, 'ISO-8859-4'));
		}
		
		private function CheckMailString ($Mail)
		{
			if (filter_var ($Mail, FILTER_VALIDATE_EMAIL) !== FALSE)
			{
				list ($User, $Domain) = explode ('@', $Mail);
				if (!checkdnsrr ($Domain, 'MX')) return FALSE;
				
				return TRUE;
			}
			
			return FALSE;
		}
		
		public function MoveElementTop (&$Array, $Key)
		{
			$tItem = $Array[$Key];
			unset ($Array[$Key]);
			
			array_unshift ($Array, $tItem);
		}
		
		public function FetchContent ($URL, $AcceptLanguage = 'de-de', $PostData = NULL)
		{
			global $CONFIG;
			
			$Content = NULL;
			$cURL = curl_init ();
			
			curl_setopt ($cURL, CURLOPT_URL, $URL);
			curl_setopt ($cURL, CURLOPT_HEADER, FALSE);
			curl_setopt ($cURL, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt ($cURL, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt ($cURL, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt ($cURL, CURLOPT_CONNECTTIMEOUT, 15);
			curl_setopt ($cURL, CURLOPT_HTTPHEADER, array ("Accept-Language: {$AcceptLanguage}"));
			curl_setopt ($cURL, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1');
			
			if ($PostData != NULL)
			{
				curl_setopt ($cURL, CURLOPT_POST, TRUE);
				curl_setopt ($cURL, CURLOPT_POSTFIELDS, $PostData);
			}
			
			if (isset ($CONFIG['PROXY_IP']))
			{
				curl_setopt ($cURL, CURLOPT_HTTPPROXYTUNNEL, TRUE);
				curl_setopt ($cURL, CURLOPT_PROXYPORT, $CONFIG['PROXY_PORT']);
				curl_setopt ($cURL, CURLOPT_PROXY, $CONFIG['PROXY_IP']);
			}
			
			$Content = @curl_exec ($cURL);
			if (@curl_getinfo ($cURL, CURLINFO_HTTP_CODE) != 200) $Content = NULL;
			@curl_close ($cURL);
			
			return $Content;
		}
		
		public function CheckContactRequest ($Name, $Mail, $Subject, $Message, $Challenge, $Response)
		{
			global $CONFIG, $DB;
			
			$Request = array ('status' => FALSE, 'code' => NULL);
			
			if (preg_match ('/^[a-zA-Z ]{5,30}$/', $Name) && !empty ($Subject) && (strlen ($Message) > 40) && (strlen ($Mail) ? $this->CheckMailString ($Mail) : TRUE) && !empty ($Challenge) && !empty ($Response))
			{
				$rCaptcha = recaptcha_check_answer ($CONFIG['CAPTCHA_KEY_PRIVATE'], $_SERVER['REMOTE_ADDR'], $Challenge, $Response);
				
				if ($rCaptcha->is_valid)
				{
					if ($DB->Provide ("INSERT INTO contact (name, mail, subject, message, timestamp) VALUES (?, ?, ?, ?, UNIX_TIMESTAMP())", array ('ssss', $Name, $Mail, $Subject, $Message))->affected_rows)
						$Request['status'] = TRUE;
				} else $Request['code'] = 1; // wrong captcha
			} else $Request['code'] = 0; // wrong input syntax
			
			return $Request;
		}
	}
?>