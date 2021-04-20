<?php
	class PID
	{
		protected $qFile = NULL;
		private $bRunning = FALSE;
		
		public function __construct ($ID = FALSE)
		{
			$this->qFile = ('/var/run/' . basename ($_SERVER['PHP_SELF']) . ($ID ? ".{$ID}" : '') . '.pid');
			
			if (file_exists ($this->qFile))
			{
				$dPID = intval (trim (file_get_contents ($this->qFile)));
				
				if (posix_kill ($dPID, 0))
				{
					$this->bRunning = TRUE;
					die ();
				}
			}
			
			file_put_contents ($this->qFile, getmypid ());
		}

		public function __destruct ()
		{
			if (!$this->bRunning && file_exists ($this->qFile)) @unlink ($this->qFile);
		}
	}
?>