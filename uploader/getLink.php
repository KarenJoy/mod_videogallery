<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

global $CFG, $DB, $USER;

if(isset($_POST['id'])){
    $id = $_POST['id'];

    $embedvid = $DB->get_record_sql('SELECT v.* FROM {videogallery_links} as v WHERE v.id=? ',array($id));
    
    echo json_encode($embedvid); 
}
?>