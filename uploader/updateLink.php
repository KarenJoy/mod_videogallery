<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

global $CFG, $DB, $USER;


if(isset($_POST['eurl'])){
  $title = $_POST['etitle'];
	$desc = $_POST['edesc'];
	$url = $_POST['eurl'];
  $id = $_POST['eid'];


    $record = new stdClass();
    $record->id = $id;
    $record->title = $title;
    $record->description = $desc;
    $record->url = $url;

    $id = $DB->update_record('videogallery_links', $record);
    echo $id;
}
?>