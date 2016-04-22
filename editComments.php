<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
global $DB, $USER;

 $action = $_POST['commentaction'];
 $data = $_POST['commentdata'];

//Saving comments
if(isset($action) && !empty($action)) {  
	 if($action=='editComment'){
	 	$dt = json_decode($data);
	 	print_r($dt); 
	 	
	 	$query = $DB->get_record_sql('SELECT * FROM {videocomments} WHERE fileid = ? AND commentcode = ?', array($dt->fileid, $dt->id));
	 	$editComment = new stdClass();
			$editComment->id = $query->id;
			$editComment->commentcode = $dt->id;
			$editComment->parent = $dt->parent;
			$editComment->timecreated = $dt->created;
			$editComment->timemodified = date("c",$dt->modified/1000);
			$editComment->content = $dt->content;
			$editComment->userid = $USER->id;
			$editComment->deleted = 0;
			$editComment->fileid = $dt->fileid;
			print_r($editComment);
			
			$updateComment = $DB->update_record('videocomments',$editComment);

			 if(isset($updateComment)){ 
		   	echo 'Comment updated!';
		   } 		
	 }

}