<?php
// Muaz Khan     - www.MuazKhan.com 
// MIT License   - https://www.webrtc-experiment.com/licence/
// Documentation - https://github.com/muaz-khan/WebRTC-Experiment/tree/master/RecordRTC
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');
global $CFG, $DB, $USER;
require_once($CFG->libdir . '/filelib.php');



foreach(array('video', 'audio') as $type) {
    if (isset($_FILES["${type}-blob"])) {
    
        echo 'uploads/';
        
		$fileName = $_POST['recordfilename'];
        $uploadDirectory = 'uploads/'.$fileName;
        
        
        if (!move_uploaded_file($_FILES["${type}-blob"]["tmp_name"], $uploadDirectory)) {
            echo(" problem moving uploaded file");
        }
		
		$context = context_module::instance(153);
		
        //$files = file_save_draft_area_files($fileName, 153, 'mod_videogallery', 'vidfiles', 0);
        //var_dump($files);
        var_dump($uploadDirectory);
      
        echo($fileName);


		echo $_POST['recordfilename'];
		echo $_POST['recorddescription'];
		var_dump($_POST['recordthumbnail']);
		
		$fs = get_file_storage();
 
		// Prepare file record object
		$fileinfo = array(
			'contextid' => $context->id, // ID of context
			'component' => 'mod_videogallery',     // usually = table name
			'filearea' => 'vidfiles',     // usually = table name
			'itemid' => 0,               // usually = ID of row in table
			'filepath' => '/',           // any path beginning and ending in /
			'filename' => $fileName); // any filename
		 
		// Create file containing text 'hello world' 

		$fs->create_file_from_pathname($fileinfo, $uploadDirectory);

		
    }
}
?>