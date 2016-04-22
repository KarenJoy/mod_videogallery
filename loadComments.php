<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/filelib.php');

global $DB, $USER, $PAGE;

$fileid = $_POST['fileid'];
  //Displaying comments

$comments = $DB->get_records_sql('SELECT VC.id, VC.commentcode, VC.fileid, VC.parent, VC.timecreated, VC.timemodified, VC.content, VC.userid, VC.deleted, U.firstname, U.lastname FROM {videocomments} VC, {user} U WHERE VC.userid = U.id AND VC.deleted<>1 AND VC.fileid = ?',array($fileid));

     // $commentsarray = array();
      foreach($comments as $comment){
            $displayComment = new stdClass(); 
            $displayComment->id = $comment->id;
            $displayComment->parent = $comment->parent;
            $displayComment->created = $comment->timecreated;
            $displayComment->modified = $comment->timemodified;
            $displayComment->content = $comment->content;
            $displayComment->fileid = $comment->fileid; 
            if($comment->userid == 2){
              $displayComment->created_by_admin = true;  
            }else{
              $displayComment->created_by_admin = false;
            }            
            if($comment->userid == $USER->id){
               $displayComment->fullname = 'You';
               $displayComment->created_by_current_user = true;
            }else{
              $displayComment->fullname = $comment->firstname.' '.$comment->lastname;
              $displayComment->created_by_current_user = true;
            }
            $commentsarray[] = $displayComment;
      }
			//print_r($commentsarray);
            $commentjson = json_encode($commentsarray);
			       echo $commentjson;
            










