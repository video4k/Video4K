<?php
	class Image
	{
		protected $File;
		private $Path;

		public function __construct ($FilePath)
		{
			$Info = getimagesize ($FilePath);
			$this->Path = $FilePath;
			
			switch ($Info[2])
			{
				case IMAGETYPE_JPEG: $this->File = imagecreatefromjpeg ($this->Path); break;
				case IMAGETYPE_PNG: $this->File = imagecreatefrompng ($this->Path); break;
			}
		}
		
		public function Save ()
		{
			imagejpeg ($this->File, $this->Path, 80);
			@chmod ($this->File, 0666);
		}
		
		public function GetWidth ()
		{
			return imagesx ($this->File);
		}
		
		public function GetHeight ()
		{
			return imagesy ($this->File);
		}
		
		public function ResizeToWidth ($dWidth)
		{
			$dHeight = round (($dWidth / $this->GetWidth ()) * $this->GetHeight ());
			$this->Resize ($dWidth, $dHeight);
		}
		
		private function Resize ($dWidth, $dHeight)
		{
			$nImage = imagecreatetruecolor ($dWidth, $dHeight);
			imagecopyresampled ($nImage, $this->File, NULL, NULL, NULL, NULL, $dWidth, $dHeight, $this->GetWidth (), $this->GetHeight ());
			$this->File = $nImage;
		}
	}
?>