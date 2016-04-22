<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

global $CFG, $DB, $USER;
$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... videogallery instance ID - it should be named as the first character of the module.

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

$event = \mod_videogallery\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $videogallery);
$event->trigger();

// Print the page header.

$PAGE->set_pagelayout('frametop');
$PAGE->set_url('/mod/videogallery/recorder/index.php', array('id' => $cm->id));
$PAGE->set_title(format_string($videogallery->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo "<h2>Video Recorder</h2>";

echo " <link rel='stylesheet' href='$CFG->wwwroot/mod/videogallery/recorder/css/style.css'>";

echo " <script src='https://cdn.webrtc-experiment.com/RecordRTC.js'></script>
       <script src='https://cdn.webrtc-experiment.com/gif-recorder.js'></script>
  	   <script src='https://cdn.webrtc-experiment.com/getScreenId.js'></script>
   	   <script src='https://cdn.webrtc-experiment.com/gumadapter.js'></script>";

//echo $_SERVER['HTTP_USER_AGENT'];
      
      
echo " <section class='experiment recordrtc'>                      
         <br>";

         $browser = $_SERVER['HTTP_USER_AGENT'];

        if(preg_match('/Firefox/i',$browser)){
          echo "<div style='text-align:left;' class='alert alert-warning alert-dismissible' role='alert'>
         <b>Note:</b> After you install the share screen browser extension in Mozilla Firefox browser, please type about:config on the url address bar.
         Then add your site domain on media.getusermedia.screensharing.allowed_domains (it is comma-separated).
         </div><br><br>";
        }else if(preg_match('/Chrome/i',$browser)){
          echo "<div style='text-align:left;' class='alert alert-warning alert-dismissible' role='alert'>
         <b>Note:</b> Chrome is incapable of recording both audio and video in a single file.
         </div><br><br>";
        }
        
         
echo  " <div id='container'>
         <br>
           <div id='script'>
              <textarea cols='80' rows='15' id='content' name='content'></textarea>
                </div>
                 <div id='videoscreen'>
                   <video controls muted></video>
                	<br><br>
                    <select class='recording-media'>
                        <option value='record-video'>Video</option>
                        <option value='record-audio'>Audio</option>
                         <option value='record-screen'>Screen</option>
                       
                    </select>
                    
                    <font color='#A5A5A5'>into</font>
                    <select class='media-container-format'>
                        <option>WebM</option>
                        <option disabled>Mp4</option>
                        <option disabled>WAV</option>
                        <option disabled>Ogg</option>
                        <option>Gif</option>
                    </select>
                
                <button class='btn btn-danger'>Start Recording</button>
          

            <div style='text-align: center; display: none;'>
            	<br>
              
                <div class='form-group'>
                  <font color='gray'><strong> Record Filename: </strong></font><input type='text' name='recordfilename' class='forminput' id='recordfilename' required>
                </div>
                <div class='form-group'>
                 <font color='gray'><strong> Record Description: </strong></font><input type='text' name='recorddescription' class='forminput' id='recorddescription' required>
                </div>
                <div class='form-group'>
                    <font color='gray' style='margin-left: -25%;'><strong> Thumbnail: </strong> </font><input type='file' name='recordthumbnail' id='recordthumbnail' class='formupload'>     
                </div> 
               
                <button id='save-to-disk' class='btn btn-primary'>Save To Disk</button>
                <button id='open-new-tab' class='btn btn-primary'>Open New Tab</button>                  
                <button id='upload-to-server' class='btn btn-success'>Upload To Server</button>
                <br>
            </div> 
                    
                 </div>
            </div>
        </section> 

        <div class='col-md-3 col-md-offset-8'>
        <br>
        <button onClick='window.location.reload()' class='btn btn-primary'>Cancel</button>
        <button class='btn btn-primary' id='backtovidegallery'>Back to Videogallery</button>
        </div>";
        
      echo "  <script type='text/javascript'>
    			document.getElementById('backtovidegallery').onclick = function () {
       		    location.href = '$CFG->wwwroot/mod/videogallery/view.php?id=$cm->id';};
			 </script>

        <script type='text/javascript' src='js/URIComponent.js'></script>;
        <script type='text/javascript' src='js/recorder.js'></script>;
        <script type='text/javascript' src='ckeditor/ckeditor.js'></script>
        <script type='text/javascript'>
        CKEDITOR.replace( 'content' );
        </script>"; 



echo $OUTPUT->footer(); 