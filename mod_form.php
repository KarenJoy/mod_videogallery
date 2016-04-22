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
 * The main videogallery configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_videogallery
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->libdir . '/filelib.php');
//require_once($CFG->dirroot.'/mod/videogallery/locallib.php');
/**
 * Module instance settings form
 *
 * @package    mod_videogallery
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_videogallery_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('videogalleryname', 'videogallery'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'videogalleryname', 'videogallery');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of videogallery settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        //$mform->addElement('static', 'label1', 'videogallerysetting1', 'Your videogallery fields go here. Replace me!');

        //$mform->addElement('header', 'videogalleryfieldset', 'Media Files');
        //$mform->addElement('filepicker', 'userfile', get_string('file'), null, array('maxbytes' => $maxbytes, 'accepted_types' => '*'));
		// $options = array('subdirs' => false,
                         // 'maxbytes' => 0,
                         // 'maxfiles' => -1,
                         // 'accepted_types' => array('.*'));
		// $mform->addElement(
            // 'filemanager',
            // 'vidfiles',
            // 'Upload Video Files',
            // null,
            // $options);
		// $mform->addRule('vidfiles', null, 'required', null, 'client');
		
		// $mform->addElement(
            // 'filemanager',
            // 'vidpix',
            // 'Upload Cover Picture',
            // null,
            // $options);
		// $mform->addRule('vidpix', null, 'required', null, 'client');
		
        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
	
	public function data_preprocessing(&$defaultvalues) {
        if ($this->current->instance) {
            $options = array('subdirs' => false,
                             'maxbytes' => 0,
                             'maxfiles' => -1);
            $draftitemid = file_get_submitted_draft_itemid('vidfiles');
            file_prepare_draft_area($draftitemid,
                                    $this->context->id,
                                    'mod_videogallery',
                                    'vidfiles',
                                    0,
                                    $options);
            $defaultvalues['vidfiles'] = $draftitemid;
			
			$draftpix = file_get_submitted_draft_itemid('vidpix');
			file_prepare_draft_area($draftpix,
                                    $this->context->id,
                                    'mod_videogallery',
                                    'vidpix',
                                    0,
                                    $options);
            $defaultvalues['vidpix'] = $draftpix;
        }
    }
	
}
