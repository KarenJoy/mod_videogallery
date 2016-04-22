<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
global $DB, $USER;

 $action = $_POST['commentaction'];
 $data = $_POST['commentdata'];
//Saving comments
if(isset($action) && !empty($action)) {  
	if($action=='postComment'){
 		$dt = json_decode($data);
 		
		   $addComment = new stdClass();
		   $addComment->commentcode = $dt->id;
	   	   $addComment->parent = $dt->parent;
		   $addComment->timecreated = $dt->created; 
		   $addComment->timemodified = $dt->modified;
		   $addComment->content = $dt->content; 
		   $addComment->userid = $USER->id; 
		   $addComment->deleted = 0;
		   $addComment->fileid = $dt->fileid;  
		   
		 	
		   $postComment = $DB->insert_record('videocomments',$addComment);
		   var_dump($addComment);
		   if(isset($postComment)){
		   	echo 'Comment added!';
		   	header("Refresh:0");
		   }
							  	
	}
}