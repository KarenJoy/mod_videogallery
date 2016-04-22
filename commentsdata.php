<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/filelib.php');

global $DB, $USER, $PAGE;

  //Displaying comments
$contextid = $_POST['fileid'];
$comments = $DB->get_records_sql('SELECT VC.id, VC.commentcode, VC.parent, VC.fileid, VC.timecreated, VC.timemodified, VC.content, VC.userid, VC.deleted, U.firstname, U.lastname FROM {videocomments} VC, {user} U, {files} F WHERE F.id=VC.fileid AND VC.userid = U.id AND VC.deleted<>1 AND F.contextid=?',array($contextid));

     // $commentsarray = array(); 
      foreach($comments as $comment){
            $displayComment = new stdClass();
            $displayComment->id = $comment->id; 
            $displayComment->parent = $comment->parent;
            $displayComment->created = $comment->timecreated;
            $displayComment->modified = $comment->timemodified;

            if (preg_match('/^\d{1}:\d{2}$/', $comment->content))
              $displayComment->content = "<a href=#>".$comment->content."</a>";
            else
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
              $displayComment->created_by_current_user = false;
            }
            $commentsarray[] = $displayComment;
      } 
            $commentjson = json_encode($commentsarray);
             
             echo $commentjson;










