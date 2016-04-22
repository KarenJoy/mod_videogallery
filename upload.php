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
}

echo "<script type='text/javascript' src='jwplayer-7.2.2/jwplayer.js'>";

$PAGE->set_pagelayout('frametop');
$PAGE->set_url('/mod/videogallery/uploader.php', array('id' => $cm->id));
$PAGE->set_title(format_string($videogallery->name));
$PAGE->set_heading(format_string($course->fullname));

$options = array('subdirs' => false,
                         'maxbytes' => 0,
                         'maxfiles' => -1,
                         'accepted_types' => array('.*'));

echo $OUTPUT->header();
$vidurl = $DB->get_record('videogallery',array('cmid'=>$id));
$urls = explode(',', $vidurl->url);
foreach($urls as $eurl) {

  $entry->$url = $eurl;

}

echo '<link href="css/textbox.css" rel="stylesheet" type="text/css" media="screen">';
echo '<script src="js/removetextbox.js" type="text/javascript"></script>
    <script src="js/bootstrap.js" type="text/javascript"></script>';
echo "
<script type='text/javascript'>
jQuery(document).ready( function () {

$('#append').click( function() {
        $('.inc').append('<div class=\"controls\"><input type=\"text\"><a href=\"#\" class=\"remove_this btn btn-danger\">remove</a><br><br></div>');
        return false;
    });
    
jQuery('.remove_this').live('click', function() {
    jQuery(this).parent().remove();
    return false;
});
    

    });
</script>";
echo "<br><br>
    <form class='form-horizontal'>
        <div class='control-group'>
        <table id='myTable'>
            <label class='control-label'>URL</label>
            <tr>
                <div class='inc'>
                    <div class='controls'>
                    <td>
                        <input type='text'>
                    </td>
                    <td>
                    <input type='text'>
                    </td>
                    <td>
                        <button class='btn btn-info' type='submit' id='append' name='append'>Add More</button>
                    </td>
    <br>
    <br>
                    </div>
    
                 </div>
            </tr>
            </table>
             </div>
    <button class='btn btn-info' type='submit' id='addLink'>Submit</button>
  
    </form>";


 echo $OUTPUT->footer();

?>