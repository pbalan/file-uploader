<?php
	/*	DirectoryParser is a class that provides PHP actions inside a directory/folder
	 *	@Author: Prashant Balan
	 **/
	
	namespace pbalan\FileUploader;
	
	class FileUploader{
		private $destination = null;
		private $no_of_files = 5;
		private $uploadSize = 0;
		private $allowedExts = array("gif", "jpeg", "jpg", "png");
		private $mime_type = array('gif'=>array('image/gif'),'jpg'=>array('image/jpeg','image/jpg','image/pjpeg'),'jpeg'=>array('image/jpeg','image/jpg','image/pjpeg'),'png'=>array('image/image/png','image/x-png'));
		private $debug = false;
		
		public function __construct($destination='', $allowedExts='', $no_of_files=4, $uploadSize=0)
		{
			if(false===empty($destination) && true===is_dir($destination))
			{
				$this->destination = $destination;
			}
			if(false===empty($allowedExts) && true===is_array($allowedExts))
			{
				$this->allowedExts = $allowedExts;
			}
			if(false===empty($no_of_files) && true===is_numeric($no_of_files))
			{
				$this->no_of_files = $no_of_files;
			}
			if(true===is_numeric($uploadSize) && 0 < $uploadSize)
			{
				$this->uploadSize = $uploadSize;
			}
		}
		
		public function uploadForm($no_of_files=4)
		{
			if(false===empty($no_of_files) && true===is_numeric($no_of_files))
			{
				$this->no_of_files = $no_of_files;
			}
			$html = '';
			$html .= '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';
			for($i=0; $i < $this->no_of_files; $i++){
				$html .= '<label for="file'.$i.'">Filename:</label>
						  <input type="file" name="file[]" id="file'.$i.'"><br>';
			}
			$html .= '<input type="submit" name="submit" value="Submit">';
			$html .= '</form>';
			return $html;
		}
		
		public function checkSize($size=0){
			$flag = false;
			if($this->uploadSize > 0)
			{
				if($size < $this->uploadSize)
				{
					$flag = true;
				}
			} 
			else 
			{
				$flag = true;
			}
			return $flag;
		}
		
		public function uploadPictures($files, $allowedExts='', $destination='', $debug=false, $uploadSize=0)
		{
			if(false===empty($allowedExts) && true===is_array($allowedExts))
			{
				$this->allowedExts = $allowedExts;
			}
			if(false===empty($destination) && true===is_dir($destination))
			{
				$this->destination = $destination;
			}
			else
			{
				if(true===$this->debug)
				{
					echo $this->destination . "is not directory/folder";
				}
			}
			if(substr($this->destination, (strlen($this->destination)-2))!='/' || substr($this->destination, (strlen($this->destination)-2))!='\\')
			{
				$this->destination .= '/';
			}
			if(true==is_bool($debug))
			{
				$this->debug = $debug;
			}
			if(true===is_numeric($uploadSize) && 0 < $uploadSize)
			{
				$this->uploadSize = $uploadSize;
			}
			if(count($files)<=0)
			{
				if(true===$this->debug)
				{
					echo "No Files uploaded";
				}
				exit;
			}
			$tempMime = array();
			foreach($this->allowedExts as $ext)
			{
				$ext = strtolower($ext);
				if(false===empty($ext))
				{
					$tempMime = array_merge($tempMime,$this->mime_type[$ext]);
				}
			}
			$this->mime_type = $tempMime;
			
			for($count=0; $count <= count($files); $count++){
				if(false===empty($files["file"]["name"][$count]))
				{
					$temp = explode(".", $files["file"]["name"][$count]);
					$extension = end($temp);
					if (true===in_array($files["file"]["type"][$count],$this->mime_type) && true===$this->checkSize($files["file"]["size"][$count]) && true===in_array($extension, $this->allowedExts) )
					{
						if ($files["file"]["error"][$count] > 0) {
							if(true===$this->debug)
							{
								echo "Return Code: " . $files["file"]["error"][$count] . "<br>";
							}
						} else {
							if(true===$this->debug)
							{
								echo "Upload: " . $files["file"]["name"][$count] . "<br>";
								echo "Type: " . $files["file"]["type"][$count] . "<br>";
								echo "Size: " . ($files["file"]["size"][$count] / 1024) . " kB<br>";
								echo "Temp file: " . $files["file"]["tmp_name"][$count] . "<br>";
							}
							if(file_exists($this->destination . $files["file"]["name"][$count])) 
							{
								if(true===$this->debug)
								{
									echo $files["file"]["name"][$count] . " already exists. ";
								}
							} else {
								move_uploaded_file($files["file"]["tmp_name"][$count], $this->destination . $files["file"]["name"][$count]);
								if(true===$this->debug)
								{
									echo "Stored in: " . $this->destination . $files["file"]["name"][$count];
								}
							}
						}
					} else {
						if(true===$this->debug)
						{
							echo "Invalid file";
						}
					}
				} else {
					if(true===$this->debug)
					{
						echo "Invalid file - no filename <br/>";
					}
				}
			}
		}
	}
?>