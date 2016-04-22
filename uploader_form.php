<?php
defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir . '/filelib.php');

class mod_videogallery_uploader_form extends moodleform {
	 public function definition() {
        global $CFG, $DB;
		
        $mform = $this->_form; // Don't forget the underscore! 

        $options = array('subdirs' => false,
                         'maxbytes' => 0,
                         'maxfiles' => -1,
                         'accepted_types' => array('.*'));
    		$mform->addElement(
                'filemanager',
                'vidfiles',
                'Upload Video Files',
                null,
                $options);

    		$mform->addElement(
                'filemanager',
                'vidpix',
                'Upload Cover Picture',
                null,
                $options);

        //$mform->addElement('text', 'url', 'URL');

        $vidurl = $DB->get_record('videogallery',array('cmid'=>$this->_customdata['id']));

        $url = explode(',', $vidurl->url);
        $c = 0;
        foreach($url as $urls) {
          $c++;
          echo "<br>";
          echo "<div id='wrapper'>";
          $mform->addElement('text', 'url'.$c, 'URL');
          echo "</div>";
           //$mform->setDefault('url'.$c, $urls);
          
        }

       /* if(isset($_POST['addnewLink'])) {
            $c++;
            $mform->addElement('text', 'url'.$c, 'URL');
            $_POST['ctr'] = $c;
        }
        */
    $mform->addElement('hidden', 'ctr', $c);
		$mform->addElement('hidden', 'cmid', $this->_customdata['id']);
		$mform->addElement('submit','addnewLink','Add more');
    $mform->registerNoSubmitButton('addnewLink');

		$this->add_action_buttons(true,'Save files');

      
           
    }
	
    
}

