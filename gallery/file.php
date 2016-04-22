<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');


header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');

global $DB, $CFG;

$id = optional_param('id', 0, PARAM_INT);
$context = context_module::instance($id);
$contextid = $context->id;

$vfiles = $DB->get_records_sql('SELECT f.* FROM {files} as f WHERE f.contextid=? AND f.filename != ? AND f.component=? AND f.filearea=?',array($context->id,'.','mod_videogallery', 'vidfiles'));
$pfiles = $DB->get_records_sql('SELECT f.* FROM {files} as f WHERE f.contextid=? AND f.filename != ? AND f.component=? AND f.filearea=?',array($context->id,'.','mod_videogallery', 'vidpix'));
$embedvid = $DB->get_records_sql('SELECT v.* FROM {videogallery_links} as v WHERE v.cmid=? ',array($id));

function createElement($domObj, $tag_name, $value = NULL, $attributes = NULL)
{
    $element = ($value != NULL ) ? $domObj->createElement($tag_name, $value) : $domObj->createElement($tag_name);

    if( $attributes != NULL )
    {
        foreach ($attributes as $attr=>$val)
        {
            $element->setAttribute($attr, $val);
        }
    }

    return $element;
}

$dom = new DOMDocument('1.0', 'utf-8');
$xmlRoot = $dom->createElement("MultimediaGallery");
    /* append it to the document created */
$xmlRoot = $dom->appendChild($xmlRoot);




$i = 0;
foreach($pfiles as $pix){ /*put videopix into array*/
	$name = str_ireplace(" ","%20", $pix->filename);
	$img[$i] .= $CFG->wwwroot.'/pluginfile.php/'.$context->id.'/'.$pix->component.'/'.$pix->filearea.'/'.$pix->itemid.'/'.$name;
	$i++;

}
$count = 0;
foreach($vfiles as $video){ /*put videofiles into array*/
		$elm = createElement($dom, 'file', '', array('type'=>'video'));

		$xmlRoot->appendChild($elm);
		
		$thumb = createElement($dom, 'thumb', $img[$count]);
		$elm->appendChild($thumb);

		$source = createElement($dom, 'source', $CFG->wwwroot.'/pluginfile.php/'.$context->id.'/'.$video->component.'/'.$video->filearea.'/'.$video->itemid.'/'.$video->filename);
		$elm->appendChild($source);

		$description = createElement($dom, 'description', 'text');
		$elm->appendChild($description);
		$count++;

}

foreach($embedvid as $evid){ /*put videofiles into array*/
		$elm = createElement($dom, 'file', '', array('type'=>'video'));

		$xmlRoot->appendChild($elm);

		$img_url = substr($evid->url, strpos($evid->url, "=") + 1);
		$img_src = 'https://img.youtube.com/vi/' .$img_url. '/0.jpg';
		
		$thumb = createElement($dom, 'thumb', $img_src);
		$elm->appendChild($thumb);

		$source = createElement($dom, 'source', $evid->url);
		$elm->appendChild($source);

		$description = createElement($dom, 'description', 'text');
		$elm->appendChild($description);
		$count++;

}




$dom->formatOutput = true;
$test1 = $dom->saveXML();
echo $dom->saveXML();
$dom->save('xmlfile/'.$id.'.xml');
	