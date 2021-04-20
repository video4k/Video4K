<?php
	class User
	{
		public $Info;
		
		public function __construct ()
		{
			global $DB, $Smarty;
			
			if (!empty ($_COOKIE['session']) && !empty ($_COOKIE['hash']))
			{
				$this->Info = $DB->Provide ("SELECT u.* FROM users AS u LEFT JOIN sessions AS s ON (s.ID = u.ID AND u.rights > ? AND s.name = 'login') WHERE s.value = ?", array ('ds', 0, $_COOKIE['session']))->fetch_assoc ();
				
				if (!empty ($this->Info) && ($_COOKIE['hash'] == $this->GetSecurityHash ()))
				{
					$this->Info['VALID'] = TRUE;
					$Smarty->assign ('USER', $this->Info);
				}
			}
		}
		
		public function CheckLoginRequest ($User, $Password)
		{
			global $DB;
			
			$Request = array ('status' => FALSE, 'code' => NULL);
			$Result = $DB->Provide ("SELECT * FROM users WHERE LOWER(name) = LOWER(?) AND password = ?", array ('ss', $User, md5 (sha1 ($Password))));
			
			if ($Result->num_rows)
			{
				$this->Info = $Result->fetch_assoc ();
				
				if ($this->Info['rights'])
				{
					$SessionID = md5 ($this->Info['ID'] . $this->Info['password'] . time ());
					
					if ($DB->Provide ("INSERT INTO sessions (ID, name, value, timestamp) VALUES (?, 'login', ?, UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE
									value = IF(((UNIX_TIMESTAMP() - timestamp) >= (7 * 24 * 60 * 60)), VALUES(value), value), timestamp = VALUES(timestamp)", array ('ds', $this->Info['ID'], $SessionID))->affected_rows)
					{
						$NData = $DB->Provide ("SELECT value FROM sessions WHERE name = 'login' AND ID = ?", array('d', $this->Info['ID']))->fetch_assoc ();
						$SessionID = $NData['value'];
					}
					
					$DB->Provide ("UPDATE users SET access = UNIX_TIMESTAMP() WHERE ID = ?", array ('d', $this->Info['ID']));					
					setcookie ('session', $SessionID, (time () + (7 * 24 * 60 * 60)), '/', $_SERVER['HTTP_HOST']);
					setcookie ('hash', $this->GetSecurityHash (), (time () + (7 * 24 * 60 * 60)), '/', $_SERVER['HTTP_HOST']);
					
					$Request = array ('status' => TRUE);
				} else {
					$Request['code'] = 1; // banned or inactive user
				}
			}
			
			return $Request;
		}
		
		public function CheckRegisterRequest ($User, $Password, $Challenge, $Response)
		{
			global $CONFIG, $Base, $DB;
			
			$Request = array ('status' => FALSE, 'code' => NULL);
			
			if (preg_match ('/^[a-zA-Z0-9_]{5,12}$/', $User) && preg_match ('/^.{6,20}$/', $Password) && !empty ($Challenge) && !empty ($Response))
			{
				$rCaptcha = recaptcha_check_answer ($CONFIG['CAPTCHA_KEY_PRIVATE'], $_SERVER['REMOTE_ADDR'], $Challenge, $Response);
				
				if ($rCaptcha->is_valid)
				{
					if (!($DB->Provide ("SELECT ID FROM users WHERE LOWER(name) = LOWER(?)", array ('s', $User))->num_rows))
					{
						$APIKey = $Base->GenUID ();
						
						while ($DB->Provide ("SELECT ID FROM users WHERE api = ?", array ('s', $APIKey))->num_rows)
							$APIKey = $Base->GenUID ();
					
						if ($DB->Provide ("INSERT INTO users (name, password, api, signup, access) VALUES (?, ?, ?, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())", array ('sss', $User, md5 (sha1 ($Password)), $APIKey))->affected_rows)
						{
							$Request['status'] = TRUE;
						} else $Request['code'] = 3; // Unknown Error
					} else $Request['code'] = 2; // Username already used
				} else $Request['code'] = 1; // Wrong Captcha
			} else $Request['code'] = 0; // Wrong Input Syntax
			
			return $Request;
		}
		
		public function GetSecurityHash ()
		{
			return crypt (($this->Info['ID'] . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE']), $this->Info['signup']);
		}
		
		public function CancelSession ()
		{
			global $CONFIG, $DB;
			
			if ($this->Info['VALID']) $DB->Provide ("DELETE FROM sessions WHERE name = 'login' AND ID = ?", array ('d', $this->Info['ID']));
			setcookie ('session', NULL, -1, '/', $_SERVER['HTTP_HOST']);
			setcookie ('hash', NULL, -1, '/', $_SERVER['HTTP_HOST']);
			
			unset ($_COOKIE['session']);
			unset ($_COOKIE['hash']);
			
			header ("Location: http://{$CONFIG['DOMAIN']}/");
			exit ();
		}
		
		public function CheckRights ($AllowedRights, $Redirect = TRUE)
		{
			global $CONFIG;
			
			if (!$this->Info['VALID'] || !in_array ($this->Info['rights'], $AllowedRights, TRUE))
			{
				if ($Redirect)
				{
					header ("Location: http://{$CONFIG['DOMAIN']}/");
					exit ();
				} else return FALSE;
			}
			
			return TRUE;
		}
	}
?>