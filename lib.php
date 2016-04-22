<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants for module videogallery
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the videogallery specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_videogallery
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Example constant, you probably want to remove this :-)
 */
define('videogallery_ULTIMATE_ANSWER', 42);

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function videogallery_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the videogallery into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $videogallery Submitted data from the form in mod_form.php
 * @param mod_videogallery_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted videogallery record
 */
function videogallery_add_instance(stdClass $videogallery, mod_videogallery_mod_form $mform = null) {
    global $DB;

    $videogallery->timecreated = time();
	$context = context_module::instance($videogallery->coursemodule);
	// $videogallery->pixitemid = $videogallery->vidpix;
	// $videogallery->viditemid = $videogallery->vidfiles;
	$videogallery->cmid = $videogallery->coursemodule;
    // You may have to add extra stuff in here.
	
    // You may have to add extra stuff in here.

    $videogallery->id = $DB->insert_record('videogallery', $videogallery);

    videogallery_grade_item_update($videogallery);
	
	// $draftitemid = $videogallery->vidfiles;
        // if ($draftitemid) {
			// file_save_draft_area_files($draftitemid, $context->id, 'mod_videogallery', 'vidfiles', 0);
        // }
	// $draftpix = $videogallery->vidpix;
        // if ($draftpix) {
			// file_save_draft_area_files($draftpix, $context->id, 'mod_videogallery', 'vidpix', 0);
        // }
    return $videogallery->id;
}

/**
 * Updates an instance of the videogallery in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $videogallery An object from the form in mod_form.php
 * @param mod_videogallery_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function videogallery_update_instance(stdClass $videogallery, mod_videogallery_mod_form $mform = null) {
    global $DB;

    $videogallery->timemodified = time();
    $videogallery->id = $videogallery->instance;
	// $context = context_module::instance($videogallery->coursemodule);
	// $videogallery->pixitemid = $videogallery->vidpix;
	// $videogallery->viditemid = $videogallery->vidfiles;
	$videogallery->cmid = $videogallery->coursemodule;
    // You may have to add extra stuff in here.
	

	
    $result = $DB->update_record('videogallery', $videogallery);

    videogallery_grade_item_update($videogallery);
	
	// $draftitemid = $videogallery->vidfiles;
        // if ($draftitemid) {
			// $file = file_save_draft_area_files($draftitemid, $context->id, 'mod_videogallery', 'vidfiles', 0);
        // }
	// $draftpix = $videogallery->vidpix;
        // if ($draftpix) {
			// file_save_draft_area_files($draftpix, $context->id, 'mod_videogallery', 'vidpix', 0);
        // }
	return $result;
}

/**
 * Removes an instance of the videogallery from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function videogallery_delete_instance($id) {
    global $DB;

    if (! $videogallery = $DB->get_record('videogallery', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records('videogallery', array('id' => $videogallery->id));

    videogallery_grade_item_delete($videogallery);

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $videogallery The videogallery instance record
 * @return stdClass|null
 */
function videogallery_user_outline($course, $user, $mod, $videogallery) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $videogallery the module instance record
 */
function videogallery_user_complete($course, $user, $mod, $videogallery) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in videogallery activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function videogallery_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link videogallery_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function videogallery_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@link videogallery_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function videogallery_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function videogallery_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function videogallery_get_extra_capabilities() {
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of videogallery?
 *
 * This function returns if a scale is being used by one videogallery
 * if it has support for grading and scales.
 *
 * @param int $videogalleryid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given videogallery instance
 */
function videogallery_scale_used($videogalleryid, $scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('videogallery', array('id' => $videogalleryid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of videogallery.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any videogallery instance
 */
function videogallery_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('videogallery', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given videogallery instance
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $videogallery instance object with extra cmidnumber and modname property
 * @param bool $reset reset grades in the gradebook
 * @return void
 */
function videogallery_grade_item_update(stdClass $videogallery, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($videogallery->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($videogallery->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $videogallery->grade;
        $item['grademin']  = 0;
    } else if ($videogallery->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$videogallery->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('mod/videogallery', $videogallery->course, 'mod', 'videogallery',
            $videogallery->id, 0, null, $item);
}

/**
 * Delete grade item for given videogallery instance
 *
 * @param stdClass $videogallery instance object
 * @return grade_item
 */
function videogallery_grade_item_delete($videogallery) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/videogallery', $videogallery->course, 'mod', 'videogallery',
            $videogallery->id, 0, null, array('deleted' => 1));
}

/**
 * Update videogallery grades in the gradebook
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $videogallery instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 */
function videogallery_update_grades(stdClass $videogallery, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = array();

    grade_update('mod/videogallery', $videogallery->course, 'mod', 'videogallery', $videogallery->id, 0, $grades);
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */


/**
 * Extends the global navigation tree by adding videogallery nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the videogallery module instance
 * @param stdClass $course current course record
 * @param stdClass $module current videogallery instance record
 * @param cm_info $cm course module information
 */
function videogallery_extend_navigation(navigation_node $navref, stdClass $course, stdClass $module, cm_info $cm) {
    // TODO Delete this function and its docblock, or implement it.
}

/**
 * Extends the settings navigation with the videogallery settings
 *
 * This function is called when the context for the page is a videogallery module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav complete settings navigation tree
 * @param navigation_node $videogallerynode videogallery administration node
 */
function videogallery_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $videogallerynode=null) {
    // TODO Delete this function and its docblock, or implement it.
}

function videogallery_get_file_areas($course, $cm, $context) {
    return array(
        'vidfiles' => 'vidfiles',
        'vidpix' => 'vidpix',
    );
}

/**
 * File browsing support for videogallery file areas.
 *
 * @param file_browser $browser File browser object
 * @param array $areas File areas
 * @param stdClass $course Course object
 * @param stdClass $cm Course module
 * @param stdClass $context Context module
 * @param string $filearea File area
 * @param int $itemid Item ID
 * @param string $filepath File path
 * @param string $filename File name
 * @return file_info Instance or null if not found
 */
function videogallery_get_file_info($browser,
                                 $areas,
                                 $course,
                                 $cm,
                                 $context,
                                 $filearea,
                                 $itemid,
                                 $filepath,
                                 $filename) {
    global $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return null;
    }

    // Filearea must contain a real area.
    if (!isset($areas[$filearea])) {
        return null;
    }

    if (!has_capability('moodle/course:managefiles', $context)) {
        // Students can not peek here!
        return null;
    }

    $fs = get_file_storage();
    if ($filearea === 'vidfiles' || $filearea === 'vidpix') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($context->id,
                                         'mod_videogallery',
                                         $filearea,
                                         0,
                                         $filepath,
                                         $filename)) {
            // Not found.
            return null;
        }

        $urlbase = $CFG->wwwroot . '/pluginfile.php';

        return new file_info_stored($browser,
                                    $context,
                                    $storedfile,
                                    $urlbase,
                                    $areas[$filearea],
                                    false,
                                    true,
                                    true,
                                    false);
    }

    // Not found.
    return null;
}

function videogallery_pluginfile($course,
                              $cm,
                              $context,
                              $filearea,
                              array $args,
                              $forcedownload,
                              array $options=array()) {
    global $CFG, $DB, $USER;

    require_once(dirname(__FILE__) . '/locallib.php');

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    if (!has_capability('mod/videogallery:view', $context)) {
        return false;
    }

    if ($filearea !== 'vidfiles' && $filearea !== 'vidpix') {
        // Intro is handled automatically in pluginfile.php.
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim('/' . $context->id . '/mod_videogallery/' . $filearea . '/' .
                      $relativepath, '/');
    $file = $fs->get_file_by_hash(sha1($fullpath));

    if (!$file || $file->is_directory()) {
        return false;
    }

    // Default cache lifetime is 86400s.
    send_stored_file($file);
}
