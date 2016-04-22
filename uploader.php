<?php

//include simplehtml_form.php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('uploader_form.php');
require_once($CFG->libdir . '/filelib.php');


global $DB, $CFG;
$id = optional_param('id', 0, PARAM_INT); 
if($id == null){
	$id = $_POST['cmid'];
}
 if ($id) {
    $cm         = get_coursemodule_from_id('videogallery', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $videogallery  = $DB->get_record('videogallery', array('id' => $cm->instance), '*', MUST_EXIST);
 } else if ($n) {
    $videogallery  = $DB->get_record('videogallery', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $videogallery->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('videogallery', $videogallery->id, $course->id, false, MUST_EXIST);
 } else {
    error('You must specify a course_module ID or an instance ID');
 }

require_login($course, true, $cm);
if($id){
	$context = context_module::instance($id);
	//$PAGE->set_context( $context );
}
//$context = context_system::instance();


//include simplehtml_form.php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('uploader_form.php');
require_once($CFG->libdir . '/filelib.php');


global $DB, $CFG;
$id = optional_param('id', 0, PARAM_INT); 
if($id == null){
  $id = $_POST['cmid'];
}
 if ($id) {
    $cm         = get_coursemodule_from_id('videogallery', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $videogallery  = $DB->get_record('videogallery', array('id' => $cm->instance), '*', MUST_EXIST);
 } else if ($n) {
    $videogallery  = $DB->get_record('videogallery', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $videogallery->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('videogallery', $videogallery->id, $course->id, false, MUST_EXIST);
 } else {
    error('You must specify a course_module ID or an instance ID');
 }

require_login($course, true, $cm);
if($id){
  $context = context_module::instance($id);
  //$PAGE->set_context( $context );
}
//$context = context_system::instance();


echo "<script type='text/javascript' src='jwplayer-7.2.2/jwplayer.js'>";

$PAGE->set_pagelayout('frametop');
$PAGE->set_url('/mod/videogallery/uploader.php', array('id' => $cm->id));
$PAGE->set_title(format_string($videogallery->name));
$PAGE->set_heading(format_string($course->fullname));

$options = array('subdirs' => false,
                         'maxbytes' => 0,
                         'maxfiles' => -1,
                         'accepted_types' => array('.*'));


//var_dump($context->id);
//Instantiate simplehtml_form 
$mform = new mod_videogallery_uploader_form(null,array('id'=>$id,'option'=>$options));


$itemid = 0; // This is used to distinguish between multiple file areas, e.g. different student's assignment submissions, or attachments to different forum posts, in this case we use '0' as there is no relevant id to use

// Fetches the file manager draft area, called 'attachments' 
$viditemid = file_get_submitted_draft_itemid('vidfiles');
$pixitemid = file_get_submitted_draft_itemid('vidpix');
$vidurl = $DB->get_record('videogallery',array('cmid'=>$id));
//echo $vidurl->url;



// Copy all the files from the 'real' area, into the draft area
file_prepare_draft_area($viditemid, $context->id, 'mod_videogallery', 'vidfiles', $itemid, $options);
file_prepare_draft_area($pixitemid, $context->id, 'mod_videogallery', 'vidpix', $itemid, $options);
// Prepare the data to pass into the form - normally we would load this from a database, but, here, we have no 'real' record to load
$entry = new stdClass();
$entry->vidfiles = $viditemid; // Add the draftitemid to the form, so that 'file_get_submitted_draft_itemid' can retrieve it
$entry->vidpix = $pixitemid;



// --------- 

// Set form data
// This will load the file manager with your previous files
$mform->set_data($entry);

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
  redirect($CFG->wwwroot.'/mod/videogallery/view.php?id='.$id);
} else if ($fromform = $mform->get_data(true)) {
  
  //In this case you process validated data. $mform->get_data() returns data posted in form.
  $videogallery = $DB->get_record('videogallery',array('cmid'=>$fromform->cmid));
  //var_dump($videogallery);
  //die();
  $videogallery->viditemid = $fromform->vidfiles;
  $videogallery->pixitemid = $fromform->vidpix;
  $videogallery->url = $fromform->url;
  $context = context_module::instance($fromform->cmid);
  $draftitemid = $fromform->vidfiles;
        if ($draftitemid) {
      file_save_draft_area_files($draftitemid, $context->id, 'mod_videogallery', 'vidfiles', 0);
        }
  $draftpix = $fromform->vidpix;
        if ($draftpix) {
      file_save_draft_area_files($draftpix, $context->id, 'mod_videogallery', 'vidpix', 0);
        }

        //implode(",",$arr)

  $DB->update_record('videogallery',$videogallery);
    redirect($CFG->wwwroot.'/mod/videogallery/view.php?id='.$fromform->cmid);

    
}

$PAGE->set_pagelayout('frametop');
$PAGE->set_url('/mod/videogallery/uploader.php', array('id' => $cm->id));
$PAGE->set_title(format_string($videogallery->name));
$PAGE->set_heading(format_string($course->fullname));

$options = array('subdirs' => false,
                         'maxbytes' => 0,
                         'maxfiles' => -1,
                         'accepted_types' => array('.*'));


//var_dump($context->id);
//Instantiate simplehtml_form 
$mform = new mod_videogallery_uploader_form(null,array('id'=>$id,'option'=>$options));


$itemid = 0; // This is used to distinguish between multiple file areas, e.g. different student's assignment submissions, or attachments to different forum posts, in this case we use '0' as there is no relevant id to use

// Fetches the file manager draft area, called 'attachments' 
$viditemid = file_get_submitted_draft_itemid('vidfiles');
$pixitemid = file_get_submitted_draft_itemid('vidpix');
$vidurl = $DB->get_record('videogallery',array('cmid'=>$id));


// Copy all the files from the 'real' area, into the draft area
file_prepare_draft_area($viditemid, $context->id, 'mod_videogallery', 'vidfiles', $itemid, $options);
file_prepare_draft_area($pixitemid, $context->id, 'mod_videogallery', 'vidpix', $itemid, $options);
// Prepare the data to pass into the form - normally we would load this from a database, but, here, we have no 'real' record to load
$entry = new stdClass();
$entry->vidfiles = $viditemid; // Add the draftitemid to the form, so that 'file_get_submitted_draft_itemid' can retrieve it
$entry->vidpix = $pixitemid;



$urls = explode(',', $vidurl->url);
foreach($urls as $eurl) {
  $ctr = 1;
  $url = 'url'.$ctr;
  $entry->$url = $eurl;
  $ctr++;
  //echo $urls;
}


// --------- 

// Set form data
// This will load the file manager with your previous files
$mform->set_data($entry);

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
	redirect($CFG->wwwroot.'/mod/videogallery/view.php?id='.$id);
} else if ($fromform = $mform->get_data(true)) {
	
  //In this case you process validated data. $mform->get_data() returns data posted in form.
  $videogallery = $DB->get_record('videogallery',array('cmid'=>$fromform->cmid));
  //var_dump($videogallery);
  //die();

  $videogallery->viditemid = $fromform->vidfiles;
  $videogallery->pixitemid = $fromform->vidpix;

  $ctrl = $_POST['ctr'];
 
  for($i=1; $i>=$ctrl; $i++) {
    $url = 'url'.$i;
    $vidembed .= implode(',', $fromform->$url);
  }
  
$videogallery->url = $vidembed;
  
  $context = context_module::instance($fromform->cmid);
  $draftitemid = $fromform->vidfiles;
        if ($draftitemid) {
			file_save_draft_area_files($draftitemid, $context->id, 'mod_videogallery', 'vidfiles', 0);
        }
	$draftpix = $fromform->vidpix;
        if ($draftpix) {
			file_save_draft_area_files($draftpix, $context->id, 'mod_videogallery', 'vidpix', 0);
        }



        

	$DB->update_record('videogallery',$videogallery);
    redirect($CFG->wwwroot.'/mod/videogallery/view.php?id='.$fromform->cmid);

    
}
echo $OUTPUT->header();
$mform->display();
 echo $OUTPUT->footer();