<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

global $CFG, $DB, $USER;


if(isset($_POST['urls'])){
  $titles = $_POST['title'];
	$descs = $_POST['desc'];
	$urls = $_POST['urls'];
  $id = $_POST['id'];

  $dataPoints = array();
  for($i=0; $i < count($urls); $i++) {
    $data = new stdClass();

    $dataPoints[] = array( array('Title' => $titles[$i], 'Description' => $descs[$i], 'URL' => $urls[$i], 'ID' => $id ));
  }

  foreach($dataPoints as $data => $value) {
    foreach($value as $val) {
      $vid = new stdClass();
    $vid->title = $val['Title'];
    $vid->description = $val['Description'];
    $vid->url = $val['URL'];
    $vid->cmid = $val['ID'];

    $id = $DB->insert_record('videogallery_links', $vid);
    
    echo $id;

    }
    
  }


}
?>