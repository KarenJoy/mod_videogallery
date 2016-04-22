<?php

$mg 		= new MultimediaGallery();
$op			= $_GET['op'];
if($op === 'display'){
	$req		= $_GET['req'];
	$cursor		= $_GET['cursor'];
	$cmid		= $_GET['cmid'];
	$mg->display($req,$cursor,$cmid);
}
elseif($op === 'getTotalFiles'){
	$cmid		= $_GET['cmid'];
	$mg->getTotalFiles($cmid);
}

class MultimediaGallery{
	private $xsl_file 		= 'multimedia2text.xsl';
	//private $xml_file 		= 'https://learnbook2.ph.learnbook.com.au/mod/videogallery/gallery/file.php';
	
	public function __construct(){
	}
	
	public function display($req,$cursor,$cmid){
		$doc = new DOMDocument();
		$xsl = new XSLTProcessor();

		$doc->load($this->xsl_file);
		$xsl->importStyleSheet($doc);
		
		$xsl->setParameter('', 'req', $req);
		$xsl->setParameter('', 'cursor', $cursor);

		$doc->load('xmlfile/'.$cmid.'.xml');
		$json_str 		= $xsl->transformToXML($doc);
		$json_str 		= str_replace('},]','}]',$json_str);
		echo $json_str;
	}
	
	public function getTotalFiles($cmid){
		$doc = new DOMDocument();
		$doc->load('xmlfile/'.$cmid.'.xml');
		$file = $doc->getElementsByTagName('file');
		$totalFiles = $file->length;
		echo $totalFiles;
	}
}