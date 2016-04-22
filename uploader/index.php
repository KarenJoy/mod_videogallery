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
$PAGE->set_url('/mod/videogallery/uploader/index.php', array('id' => $cm->id));
$PAGE->set_title(format_string($videogallery->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

echo "<link rel='stylesheet' href='css/style.css'>
      <link rel='stylesheet' href='css/jquery.fileupload.css'>
      <link rel='stylesheet' href='css/jquery.fileupload-ui.css'>
      <noscript><link rel='stylesheet' href='css/jquery.fileupload-noscript.css'></noscript>
      <noscript><link rel='stylesheet' href='css/jquery.fileupload-ui-noscript.css'></noscript>";

echo "<div class='container'>
        <h2>Learnbook Media Uploader</h2>
        <ul class='nav nav-tabs'>
                <li class='active'><a href='#'>Video Files</a></li>
                <li><a href='externalvid.php?id=153'>External Video Url</a></li>
        </ul><br>
        <blockquote>
                <p>Upload and share your media files and video playlists on Learnbook</p>
        </blockquote><br>"; 

echo "<form id='fileupload' action='#' method='POST' enctype='multipart/form-data'>";

echo "<div class='row fileupload-buttonbar'>
            <div class='col-lg-7'>
                <br>
                <!-- The fileinput-button span is used to style the file input field as button -->
              
                <span class='btn btn-success fileinput-button'>
                    <i class='glyphicon glyphicon-plus'></i>
                    <span>Add files...</span>
                    <input type='file' name='files[]'' multiple>
                </span>

                <button type='submit' class='btn btn-primary start'>
                    <i class='glyphicon glyphicon-upload'></i>
                    <span>Start upload</span>
                </button>

                <button type='reset' class='btn btn-warning cancel'>
                    <i class='glyphicon glyphicon-ban-circle'></i>
                    <span>Cancel upload</span>
                </button>
                <button type='button' class='btn btn-danger delete'>
                    <i class='glyphicon glyphicon-trash'></i>
                    <span>Delete</span>
                </button>
                <input type='checkbox' class='toggle'>
                <!-- The global file processing state -->
                <span class='fileupload-process'></span>
            </div>
            <!-- The global progress state -->
            <div class='col-lg-5 fileupload-progress fade'>
                <!-- The global progress bar -->
                <div class='progress progress-striped active' role='progressbar' aria-valuemin='0' aria-valuemax='100'>
                    <div class='progress-bar progress-bar-success' style='width:0%;'></div>
                </div>
                <!-- The extended global progress state -->
                <div class='progress-extended'>&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role='presentation' class='table table-striped'><tbody class='files'></tbody></table>
    "; 


echo '<div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Demo Notes</h3>
        </div>
        <div class="panel-body">
            <ul>
                <li>The maximum file size for uploads in this demo is <strong>999 KB</strong> (default file size is unlimited).</li>
                <li>Only image files (<strong>JPG, GIF, PNG</strong>) are allowed in this demo (by default there is no file type restriction).</li>
                <li>Uploaded files will be deleted automatically after <strong>5 minutes or less</strong> (demo files are stored in memory).</li>
                <li>You can <strong>drag &amp; drop</strong> files from your desktop on this webpage.</li>
                <li>Built with the <a href="http://getbootstrap.com/">Bootstrap</a> CSS framework and Icons from <a href="http://glyphicons.com/">Glyphicons</a>.</li>
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Options</h4>
        </div>
        <div class="modal-body">
          <p>Modal content..</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
';



echo '<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">

        <td>
            <span class="preview"></span>
        </td>
        <td>
            <div class="col-md-12>"
            <div class="form-group">
			  <label for="vidtitle"> Video Title:</label>
			  <input type="text" class="form-control" name="vidtitle">
			</div>
			<div class="form-group">
			  <label for="viddesc">Description:</label>
			  <textarea class="form-control" name="viddesc" rows="5"/>
			</div>
			
            <p class="name">{%=file.name%}</p>

            <strong class="error text-danger"></strong>
            </div>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
        </td>
      
        <td>
            {% var title = document.getElementById("vidtitle");
			       
            	
            	if (!i && !o.options.autoUpload) {           	
			         %}
                <button name="start" class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {%}%}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr></form>
{% } %}

</script>';

echo '<script type="text/javascript">
			$(document).ready(function (){
			    validate();
			    $("#vidtitle, #viddesc, #vidthumbnail").change(validate);
			});

			function validate(){
			    if ($("#vidtitle").val().length   >   0   &&
			        $("#viddesc").val().length  >   0   &&
			        $("#vidthumbnail").val().length    >   0) {
			        $("button[name=start]").prop("disabled", false);
			    }
			    else {
			        $("button[name=start]").prop("disabled", true);
			    }
			}
		</script>';


echo '<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } else { %}
                     {% var filetrim =file.name.slice(0, file.name.indexOf("."));%}
                     <a href="server/php/files/thumbnail/{%=filetrim%}.jpg" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="server/php/files/thumbnail/{%=filetrim%}.jpg"></a>    
                 {% } %}
                
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" >{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields={"withCredentials":true}{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% }  %}
</script>';

echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="js/vendor/jquery.ui.widget.js"></script>
        <script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
        <script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
        <script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
        <script src="js/jquery.iframe-transport.js"></script>
        <script src="js/jquery.fileupload.js"></script>
        <script src="js/jquery.fileupload-process.js"></script>
        <script src="js/jquery.fileupload-image.js"></script>
        <script src="js/jquery.fileupload-audio.js"></script>
        <script src="js/jquery.fileupload-video.js"></script>
        <script src="js/jquery.fileupload-validate.js"></script>
        <script src="js/jquery.fileupload-ui.js"></script>
        <script src="js/main.js"></script>';

echo $OUTPUT->footer(); 
?>
