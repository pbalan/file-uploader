<?php
	
	namespace pbalan\FileUploader;	
	use pbalan\FileUploader;
	
	class FileUploaderTest extends FileUploader
	{
		
		protected $allowedExtn = array();
		protected $dir = null;
		protected $recurse = false;
		
		public function testDirectoryParse()
		{
			$this->dir = parent::createDirectory($_SERVER['DOCUMENT_ROOT'].'/tests/upload',0777,true);
			return $this->testRead($this->dir);
		}
	}
	$newObj = new FileUploaderTest();
	$contentDir = $newObj->testRead();
	var_dump($contentDir);
?>