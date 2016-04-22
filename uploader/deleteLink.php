<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

global $CFG, $DB, $USER;

if(isset($_POST['id'])){
    $id = $_POST['id'];

    $result = $DB->delete_records('videogallery_links', array('id' => $id));
    
    echo $result;
}
?>