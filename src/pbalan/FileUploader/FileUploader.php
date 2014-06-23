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
		private $mime_type = array('gif'=>array('image/gif'),'jpg'=>array('image/jpeg','image/jpg','image/pjpeg'),'jpeg'=>array('image/jpeg','image/jpg','image/pjpeg'),'png'=>array('image/png','image/x-png'));
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
		public function setMimeType($mime_type=array()){
            if(false===empty($mime_type)){
                $this->mime_type = array_merge($this->mime_type,$mime_type);
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
		
		public function uploadPictures($files, $allowedExts='', $destination='', $debug=false, $uploadSize=0, $forceFileName='')
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
            $this->destination = str_replace('\\','/',$this->destination);
			if(substr($this->destination, (strlen($this->destination)-1))!='/')
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
			} else {
                if(true===$this->debug)
                {
                    var_dump($files);
                }
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
			
			for($i=0; $i <= count($files["file"]["name"]); $i++)
            {
                if(true===$this->debug)
				{
                    echo "total: ".count($files["file"]["name"]);
                    echo "counting: ".$i."<br/>";
                }
				if(false===empty($files["file"]["name"][$i]))
				{
					$temp = explode(".", $files["file"]["name"][$i]);
					$extension = end($temp);
					if (true===in_array($files["file"]["type"][$i],$this->mime_type) && true===$this->checkSize($files["file"]["size"][$i]) && true===in_array($extension, $this->allowedExts) )
					{
						if ($files["file"]["error"][$i] > 0) {
							if(true===$this->debug)
							{
								echo "Return Code: " . $files["file"]["error"][$i] . "<br>";
							}
						} else {
							if(true===$this->debug)
							{
								echo "Upload: " . $files["file"]["name"][$i] . "<br>";
								echo "Type: " . $files["file"]["type"][$i] . "<br>";
								echo "Size: " . ($files["file"]["size"][$i] / 1024) . " kB<br>";
								echo "Temp file: " . $files["file"]["tmp_name"][$i] . "<br>";
							}
							if(file_exists($this->destination . $files["file"]["name"][$i]) && true===empty($forceFileName)) 
							{
								if(true===$this->debug)
								{
									echo $files["file"]["name"][$i] . " already exists. ";
								}
							} else if(false===empty($forceFileName) && file_exists($this->destination . $forceFileName)){
                                if(true===$this->debug)
								{
									echo "forceFileName: ".$forceFileName. " already exists. ";
								}
                            } else {
                                if(false===empty($forceFileName) && $i==0 && count($files)===1){
                                    move_uploaded_file($files["file"]["tmp_name"][$i], $this->destination . $forceFileName);
                                } else {
                                    move_uploaded_file($files["file"]["tmp_name"][$i], $this->destination . $files["file"]["name"][$i]);
                                }
								if(true===$this->debug)
								{
									echo "Stored in: " . $this->destination . $files["file"]["name"][$i];
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
        
        public function getImageDimesions($filename)
        {
            $dimensions = array('width' => 0, 'height' => 0);
            if(false===empty($filename) && true===file_exists($filename))
            {
                if(list($width, $height) = @getimagesize($filename))
                {
                    $dimensions = array('width' => $width, 'height' => $height);
                }
            }
            return $dimensions;
        }
        
        public function checkImageDimension(array $dimensions=array(), $set_width, $set_height)
        {
            if(isset($dimensions['width']))
            {
                $width = $dimensions['width'];
            } else {
                $width = 0;
            }
            
            if(isset($dimensions['height']))
            {
                $height = $dimensions['height'];
            } else {
                $height = 0;
            }
            
            if($set_width===$width && $set_height===$height)
            {
                return true;
            } else {
                return false;
            }
        }
        public function checkDirectoryFlow($dir='')
		{
            if(false===empty($dir))
            {
                $dir = str_replace('\\','/',$dir);
                if(substr($dir, (strlen($dir)-1))!='/')
                {
                    $dir .= '/';
                }
                return $dir;
            } else {
                $this->dir = str_replace('\\','/',$this->dir);
                if(substr($this->dir, (strlen($this->dir)-1))!='/')
                {
                    $this->dir .= '/';
                }
                return true;
            }
		}
        public function generateThumb($filename, $thumb_width, $thumb_height, $crop=false, $thumbDir='', $thumbFile='')
        {
            if(false===empty($thumbFile) && false===empty($thumbDir))
            {
                $thumbDir = $this->checkDirectoryFlow($thumbDir);
                $thumbFile = $thumbDir.$thumbFile;                
            }
            else if(false===empty($thumbFile) && true===empty($thumbDir))
            {
                $thumbDir = $this->checkDirectoryFlow(dirname($filename));
                $thumbFile =  $thumbDir.$thumbFile;
            }
            else if(true===empty($thumbFile) && false===empty($thumbDir))
            {
                $thumbDir = $this->checkDirectoryFlow($thumbDir);
                $thumbFile =  $thumbDir.basename($filename);
            }
            else if(true===empty($thumbFile) && true===empty($thumbDir))
            {
                $thumbDir = $this->checkDirectoryFlow(dirname($filename));
                $thumbFile = $thumbDir.basename($filename);
            }
            
            if($crop===true)
            {
                if(0!=$thumb_width && 0!=$thumb_height && true===file_exists($filename))
                {
                    $this->cropImage($filename, $thumb_width, $thumb_height, $thumbFile);
                }
            }
            else
            {
                if(0!=$thumb_width && 0!=$thumb_height && true===file_exists($filename))
                {
                    $this->resizeImage($filename, $thumb_width, $thumb_height, $filename);
                }
            }
        }
        
        public function cropImage($imgSrc, $cropWidth='', $cropHeight='', $filename='')
        {
            $dimensions = $this->getImageDimesions($imgSrc);
            $width=  $dimensions['width'];
            $height=  $dimensions['height'];
            $imageType = image_type_to_mime_type(exif_imagetype($imgSrc));
            if(true===empty($cropWidth) && true===empty($cropHeight))
            {
                ///--------------------------------------------------------
                //setting the crop size
                //--------------------------------------------------------
                if($width > $height) $biggestSide = $width;
                else $biggestSide = $height;
                
                //The crop size will be half that of the largest side
                $cropPercent = .5;
                $cropWidth   = $biggestSide*$cropPercent;
                $cropHeight  = $biggestSide*$cropPercent;
            }
            $myImage = $this->createImage($imageType, $imgSrc);
            //getting the top left coordinate
            $c1 = array("x"=>($width-$cropWidth)/2, "y"=>($height-$cropHeight)/2);
            //--------------------------------------------------------
            // Creating the thumbnail
            //--------------------------------------------------------
            $thumb = imagecreatetruecolor($cropWidth, $cropHeight);
            imagecopyresampled($thumb, $myImage, 0, 0, $c1['x'], $c1['y'], $cropWidth, $cropHeight, $cropWidth, $cropHeight);
            $this->saveImage($imageType, $thumb, $filename);
        }
        
        public function resizeImage($imgSrc, $cropWidth='', $cropHeight='', $filename='')
        {
            $dimensions = $this->getImageDimesions($imgSrc);
            $width=  $dimensions['width'];
            $height=  $dimensions['height'];
            if(file_exists($imgSrc) && exif_imagetype($imgSrc)){
                $imageType = image_type_to_mime_type(exif_imagetype($imgSrc));
            }
            if(true===empty($cropWidth) && true===empty($cropHeight))
            {
                ///--------------------------------------------------------
                //setting the crop size
                //--------------------------------------------------------
                if($width > $height) $biggestSide = $width;
                else $biggestSide = $height;
                
                //The crop size will be half that of the largest side
                $cropPercent = .5;
                $cropWidth   = $biggestSide*$cropPercent;
                $cropHeight  = $biggestSide*$cropPercent;
            }
            $myImage = $this->createImage($imageType, $imgSrc);
            
            //--------------------------------------------------------
            // Creating the thumbnail
            //--------------------------------------------------------
            
            $thumb = imagecreatetruecolor($cropWidth, $cropHeight);
            if(is_resource($myImage)){
                imagecopyresized($thumb, $myImage, 0, 0, 0, 0, $cropWidth, $cropHeight, $width, $height);
            }
            $this->saveImage($imageType, $thumb, $filename);
        }
        
        private function createImage($imageType, $imgSrc)
        {
            $myImage = '';
            if(true===is_resource($imgSrc)){
                //saving the image into memory (for manipulation with GD Library)
                switch($imageType)
                {
                    case 'image/jpeg' : $myImage = imagecreatefromjpeg($imgSrc); break;
                    case 'image/gif'  : $myImage = imagecreatefromgif($imgSrc); break;
                    case 'image/png'  : $myImage = imagecreatefrompng($imgSrc); break;
                    case 'image/bmp'  : $myImage = imagecreatefromwbmp($imgSrc); break;
                }
            }
            return $myImage;
        }
        
        private function saveImage($imageType, $thumb, $filename)
        {
            if(true===is_resource($filename)){
                switch($imageType)
                {
                    case 'image/jpeg' : imagejpeg($thumb, $filename); break;
                    case 'image/gif'  : imagegif($thumb, $filename); break;
                    case 'image/png'  : imagepng($thumb, $filename); break;
                    case 'image/bmp'  : imagewbmp($thumb, $filename); break;
                }
            }
        }
	}
?>