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
$PAGE->set_url('/mod/videogallery/uploader/urluploader.php', array('id' => $cm->id));
$PAGE->set_title(format_string($videogallery->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

echo "<link rel='stylesheet' href='css/style.css'>
      <link rel='stylesheet' href='css/jquery.fileupload.css'>
      <link rel='stylesheet' href='css/jquery.fileupload-ui.css'>
      <noscript><link rel='stylesheet' href='css/jquery.fileupload-noscript.css'></noscript>
      <noscript><link rel='stylesheet' href='css/jquery.fileupload-ui-noscript.css'></noscript>
      <link href='//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.8/theme-default.min.css'
    rel='stylesheet' type='text/css' />
      ";

echo "<div class='container'>
        <h2>Learnbook Media Uploader</h2>
            <ul class='nav nav-tabs'>
                <li><a onclick=\"location.href='$CFG->wwwroot/mod/videogallery/uploader/urluploader.php?id=$cm->id'\">Video Files</a></li>
                <li class='active'><a href='#'>External Video Url</a></li>
             </ul><br>
            <blockquote>
                <p>Upload and share your video from external sites on Learnbook</p>
            </blockquote><br>
                <input type='hidden' id='cmid' value='$cm->id'>"; 

echo "<form id='fileupload' action='//jquery-file-upload.appspot.com/' method='POST' enctype='multipart/form-data'>";


echo "<div class='row fileupload-buttonbar'>
            <div class='col-lg-7'>
                <br>
                <span class='btn btn-success fileinput-button' data-toggle='modal' data-target='#myModal' id='myModalButton'>
                    <i class='glyphicon glyphicon-plus'></i>
                    <span>Add files...</span> 
                </span>
                <button type='button' onclick=\"location.href='$CFG->wwwroot/mod/videogallery/view.php?id=$cm->id'\" class='btn btn-primary start'>
                    <i class='glyphicon glyphicon-arrow-left'></i>
                    <span>Go back to Media Player</span>
                </button>
                
            </div>
            
        </div>
    </form>"; 

    $embedvid = $DB->get_records_sql('SELECT v.* FROM {videogallery_links} as v WHERE v.cmid=? ',array($id));


echo '<div id="box2" class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Uploaded Files</h3>
        </div>
        <div class="panel-body">
            <div id="bodyAlertMessage">
            </div>
            <table width="100%" class="table table-bordered">
            <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Url</th>
            <th>Edit</th>
            <th>Delete</th>
            </tr>';
            foreach($embedvid as $evid) {
            
            echo "<tr>
                <td>$evid->title</td>
                <td>$evid->description</td>
                <td>$evid->url</td>
                <td align='center'><button type='button' class='btn btn-success fileinput-button' id='$evid->id' data-toggle='modal' data-target='#editModal' id='editLink' >
                    <i class='glyphicon glyphicon-pencil'></i>
                </button></td>
                <td align='center'><button type='button' class='btn btn-danger delete' id='$evid->id' data-toggle='modal' data-target='#deleteModal'>
                    <i class='glyphicon glyphicon-remove'></i>
                </button></td>
            </tr>";
            }
            
           echo ' </table>
        </div>
    </div>
</div>';

echo '<div id="myModal" class="modal fade" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Embed Links</h4>
                </div>
                <div class="modal-body">
                    <div id="alertMessage"></div>
                    <br>
                    <button class="btn btn-info" type="submit" id="append" name="append">Add More</button>
                    <div class="control-group">
                        <div class="inc">
                            <div class="controls">
                                <br>
                                <form class="form-inline" id="addForm">
                                <input type="text" name="title" class="title" placeholder="Title" data-validation="required" data-validation-error-msg="Title is required.">
                                <input type="text" name="desc" placeholder="Description" data-validation="required" data-validation-error-msg="Description is required.">
                                <input type="text" name="url" placeholder="URL" data-validation="url required" data-validation-error-msg-required="URL is required.">
                                </form>
                                <br>
                                <br>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-info" id="addLink" name="addLink" data-dismiss="modal">Submit</button>
                </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
             </div>
        </div>
    </div>';

echo '<div id="editModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Link</h4>
                </div>
                <div class="modal-body">
                    <div class="control-group">
                        <div class="inc">
                            <div class="controls">
                                <br>
                                <form id="editForm">
                                <input type="hidden" id="idUpdate">
                                <table width="100%" class="table table-bordered">
                                <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>URL</th>
                                </tr>
                                <tr>
                                <td><input type="text" id="etitle" placeholder="Title" data-validation="required" data-validation-error-msg="Title is required."></td>
                                <td><input type="text" id="edesc" placeholder="Description" data-validation="required" data-validation-error-msg="Description is required."></td>
                                <td><input type="text" id="eurl" placeholder="URL" data-validation="url required" data-validation-error-msg="URL is required / Not a valid URL."></td>
                                </tr>
                                </table>
                                </form>
                                <br>
                                <br>
                            </div>
                        </div>
                    </div>
                        <button class="btn btn-info" id="updateLink" data-dismiss="modal">Update</button>
                </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
            </div>
        </div>
    </div>';

echo '<div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Link</h4>
                </div>
                <div class="modal-body edit-content">
                    <p>Are you sure you want to delete this record?</p>
                    <input type="hidden" id="deleteId">
                </div>
                      <div class="modal-footer">
                        <button class="btn btn-remove" id="confirmDelete" data-dismiss="modal">Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                      </div>
            </div>
        </div>
    </div>';

echo '<script src="js/removetextbox.js" type="text/javascript"></script>
    <script src="js/bootstrap.js" type="text/javascript"></script>';

echo "<script type='text/javascript'>
    jQuery(document).ready( function () {

         $.validate({
            form : '#addForm, #editForm'
        });

        $('#append').click( function() {
                $('.form-inline').append('<div class=\"controls\"><input type=\"text\" placeholder=\"Title\" name=\"title\" class=\"title\" data-validation=\"required\" data-validation-error-msg=\"Title is required.\"><input type=\"text\" placeholder=\"Description\" name=\"desc\"><input type=\"text\" placeholder=\"URL\" name=\"url\"><a href=\"#\" class=\"remove_this btn btn-danger\">Delete</a></div>');
                return false;
            });
       
        $('body').on('click', 'a', function(){

            jQuery(this).parent().remove();
            return false;
            });

     
        $('#deleteModal').on('show.bs.modal', function(e) {
           
            id = e.relatedTarget.id;
            $('#deleteId').val(id); 

        });

        $('#confirmDelete').click( function() {    
                var id = $('#deleteId').val();
                
                $.ajax({
                url:'deleteLink.php',
                data:{id:id},
                type:'post',
                success:function(data){
                    if(data){
                        $('#deleteModal').hide();
                        $('#box2').load(location.href + ' #box2');
                        $('#bodyAlertMessage').append('<div class=\"alert alert-success fade in \"><strong>Success!</strong> Record successfully deleted.</div>');
                        $('#bodyAlertMessage').delay(15000).fadeTo('slow', 3.0);
                        
                    } else {
                        $('#bodyAlertMessage').append('<div class=\"alert alert-danger\"><strong>Failed!</strong>Record failed to save. Please try again.</div>');
                        $('#bodyAlertMessage').delay(3000).fadeTo('slow', 3.0);
                    }
                }
            });
            });
       
        $('#editModal').on('show.bs.modal', function(e) {
           
            var id = e.relatedTarget.id;
            $('#idUpdate').val(id);
            
            $.ajax({
                url:'getLink.php',
                data:{id:id},
                type:'post',
                success:function(data){
                    if(data){
                        
                    var responseNew = JSON.parse(data);
                    
                    $('#etitle').val(responseNew.title);
                    $('#edesc').val(responseNew.description);
                    $('#eurl').val(responseNew.url);
                   
                    } else {
                        alert('failed');
                    }
                }
                 });
             
        });
         
    $('#addLink').click( function() {
        var urls = [];
        var titles = [];
        var desc = [];
        var decision = [];

        $('input[name=title]').each(function () {
            var value = $(this).val();  
            if ( value != '' ) {
                titles.push(value);
                decision.push('true');
            } 
            else {
                decision.push('false');
            }  
            });

        $('input[name=desc]').each(function () {
            var value = $(this).val();  
            if ( value != '' ) {
                desc.push(value);
                decision.push('true');
            }   else {
                decision.push('false');
            }      
            });

        $('input[name=url]').each(function () {
            var value = $(this).val();  
            if ( value != '' ) {
                urls.push(value);
                decision.push('true');
            }  else {
                decision.push('false');
            }     
            });
        
        var id = $('#cmid').val();
        var res = $.inArray('true', decision);
        
       if (res != -1){
        
            $.ajax({
            url:'addLink.php',
            data:{title:titles,
              desc: desc,
              urls: urls,
              id: id
              },
            type:'post',
            success:function(data){
                if(data){
                   
                    $('input[name=title]').val('');
                    $('input[name=desc]').val('');
                    $('input[name=url]').val('');
                    $('#myModal').modal('hide');
                    $('#box2').load(location.href + ' #box2');
                    $('#bodyAlertMessage').append('<div class=\"alert alert-success fade in \"><strong>Success!</strong> Record successfully added.</div>');
                    $('#bodyAlertMessage').delay(3000).fadeTo('slow', 3.0);

                }
            }
            });
        } else {
            $('#box2').load(location.href + ' #box2');
             $('#bodyAlertMessage').append('<div class=\"alert alert-danger\"><strong>Failed!</strong> All fields are required. Please try again.</div>');
             $('#bodyAlertMessage').delay(3000).fadeTo('slow', 3.0);
        }
        
    });

    $('#updateLink').click( function() {
        var url = $('#eurl').val();
        var title = $('#etitle').val();
        var desc = $('#edesc').val();
        
        var id = $('#idUpdate').val();

        if (url != '' && title !='' && desc != '') {
            $.ajax({
            url:'updateLink.php',
            data:{etitle:title,
              edesc: desc,
              eurl: url,
              eid: id
              },
            type:'post',
            success:function(data){
                if(data){
                    $('input[name=etitle]').val('');
                    $('input[name=edesc]').val('');
                    $('input[name=eurl]').val('');
                    $('#editModal').modal('hide');
                    $('#box2').load(location.href + ' #box2');
                    $('#bodyAlertMessage').append('<div class=\"alert alert-success fade in \"><strong>Success!</strong> Record successfully added.</div>');
                    $('#bodyAlertMessage').delay(3000).fadeTo('slow', 3.0);

                } else {
                  $('#box2').load(location.href + ' #box2');
                    $('#bodyAlertMessage').append('<div class=\"alert alert-danger\"><strong>Failed!</strong> Record not saved. Please try again.</div>');
                    $('#bodyAlertMessage').delay(3000).fadeTo('slow', 3.0);
                }
            }
            });
        }
        else {
            $('#box2').load(location.href + ' #box2');
            $('#bodyAlertMessage').append('<div class=\"alert alert-danger\"><strong>Failed!</strong>All fields are required. Please try again.</div>');
            $('#bodyAlertMessage').delay(3000).fadeTo('slow', 3.0);
        }
    });

});
</script>";

echo '  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.8/jquery.form-validator.min.js">
        <script src="js/vendor/jquery.ui.widget.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        ';

echo $OUTPUT->footer(); 
?>
