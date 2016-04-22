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

$context = context_module::instance($id);
$contextid = $context->id;

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
$PAGE->set_url('/mod/videogallery/gallery/index.php', array('id' => $cm->id));
$PAGE->set_title(format_string($videogallery->name));
$PAGE->set_heading(format_string($course->fullname));
$context = context_module::instance($id);
$contextid = $context->id;

echo $OUTPUT->header();
echo $OUTPUT->heading();
file_get_contents($CFG->wwwroot.'/mod/videogallery/gallery/file.php?id='.$id);

echo '<input type="hidden" id="cmid" value="'.$id.'"/>';
echo'
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
		<link class="ui-theme" rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/vader/jquery-ui.css"/>
		<link rel="stylesheet" href="jMediaelement/css/styles.css" />
		<link rel="stylesheet" href="jMediaelement/css/player-controls.css" />
		<script src="jquery.min.js"></script>
		<!-- ui-components -->
		<script src="jquery-ui.min.js"></script>
		<!-- END:  ui-components -->
		<!-- optional for a11y-slider -->
		<script src="jMediaelement/utils/a11y-slider.ext.js"></script>
		<!-- END: for a11y-slider -->
		<!-- jMediaelement -->
		<script src="jMediaelement/packages/mm.full.min.js"></script>
		<!-- jMediaelement -->
		<!-- fullwindow plugin -->
		<script src="jMediaelement/plugins/fullwindow.js"></script>
		<!-- fullwindow plugin -->
		<!-- useractivity -->
		<script src="jMediaelement/utils/useractivity.js"></script>
		<!-- jmeEmbedControls is a very simple plugin, that you should use as a starting point for your own theme -->
		<script src="jMediaelement/utils/jmeEmbedControls.js"></script>
        <style>
			h1.title{
				position:absolute;
				top:5px;
				left:-1px;
				background-color:#000;
				border:1px solid #111;
				color:#666;
				font-size:15px;
				text-shadow:1px 1px 1px #000;
				padding:10px;
				background-color:#121212;
				border:1px solid #292929;
				-moz-border-radius:0px 5px 5px 0px;
				-webkit-border-bottom-right-radius:5px;
				-webkit-border-top-right-radius:5px;
				border-bottom-right-radius:5px;
				border-top-right-radius:5px;
				-moz-box-shadow:0px 0px 3px #000 inset;
				-webkit-box-shadow:0px 0px 3px #000 inset;
				box-shadow:0px 0px 3px #000 inset;
			}
            span.reference{
                position:absolute;
                right:5px;
                top:5px;
                font-size:12px;
            }
            span.reference a{
                color:#aaa;
                text-decoration:none;
				margin-left:10px;
            }
            span.reference a:hover{
                color:#ddd;
            }
			
			/* position the fullscreen-button */
			._video .media-controls-wrapper {
				position: absolute;
				bottom:0px;
				width:100%
			}
			div.volume-slider {
				right: 46px;
				width: 60px;
			}
			.fullscreen {
				right: 8px;
			}
        </style>';
		echo '<div id="mmg_media_wrapper" class="media_wrapper">
            <ul></ul>
			<div class="more" style="display:none">
				<a id="mmg_more" href="#">Load More...</a>
			</div>
        </div>
		<div id="mmg_overlay" class="overlay"></div>
		<div id="mmg_preview" class="preview">
			<div id="mmg_preview_loading" class="preview_loading"></div>
            <div class="preview_wrap"></div>
			<div id="mmg_nav" class="nav">
				<a href="#" class="prev"></a>
				<a href="#" class="next"></a>
			</div>
        </div>
        <div id="mmg_description" class="description"></div>
        <script type="text/javascript" src="json2.js"></script>
		<script src="jquery.viewport.js" type="text/javascript"></script>
		<script src="jquery.multimediagallery.js" type="text/javascript"></script>';
echo $OUTPUT->footer(); 