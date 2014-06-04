<?php
	require_once dirname(dirname(__FILE__))."/src/pbalan/FileUploader/FileUploader.php";
	require_once '../vendor/autoload.php';
	require_once '../autoload.php';
	use pbalan\FileUploader;
	use pbalan\DirectoryParser;
	$destination = dirname(__FILE__).'/upload';
	$fileObj = new pbalan\FileUploader\FileUploader($destination);
	
	// if(class_exists('DirectoryParser'))
	// {
		// echo "found";
	// }
	// else
	// {	
		// echo "Not found"; exit;
	// }
	if(count($_POST)<=0)
	{
		
		$dirObj = new pbalan\DirectoryParser\DirectoryParser();
		$dirObj->createDirectory($destination);
		
		$form = $fileObj->uploadForm();
		echo $form;
	} 
	else 
	{
		$files = $_FILES;
		$fileObj->uploadPictures($files, array(), $destination, true, 0);
	}