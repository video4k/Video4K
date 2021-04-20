<?php
	class Database
	{
		protected $CID;
		
		public function __construct ()
		{
			$this->Connect ();
		}
		
		public function __destruct ()
		{
			$this->Disconnect ();
		}
		
		private function Connect ()
		{
			global $CONFIG;
			
			$this->CID = new mysqli ($CONFIG['MYSQL_SERVER'], $CONFIG['MYSQL_USERNAME'], $CONFIG['MYSQL_PASSWORD'], $CONFIG['MYSQL_DATABASE']);
			
			if ($this->CID->connect_error)
			{
				error_log (("[" . date ('d.m.y|H:i') . "]DATABASE: {$this->CID->connect_error} ({$this->CID->connect_errno})\r\n"), 3, '/var/log/server.log');
				die (!empty ($this->CID->connect_error) ? $this->CID->connect_error : 'MySQL Connection Error');
			}
			
			$this->CID->set_charset ($CONFIG['MYSQL_ENCODING']);
		}
		
		private function Disconnect ()
		{
			if ($this->CID) $this->CID->close ();
		}
		
		private function RefValues ($Array)
		{
			$Refs = array ();
			
			if (is_array ($Array))
			{
				foreach ($Array AS $Key => $Value)
					$Refs[$Key] = &$Array[$Key];
			}
			
			return $Refs;
		}
		
		public function Provide ($Query, $Values)
		{
			$Query = $this->CID->prepare ($Query);

			if (is_array ($Values))
				call_user_func_array (array (&$Query, 'bind_param'), $this->RefValues ($Values));
			
			$Query->execute ();
			$Result = $Query->get_result ();
			
			return (empty ($Result) ? $Query : $Result);
		}
		
		public function Escape ($Value)
		{
			return $this->CID->real_escape_string ($Value);
		}
	}
?>