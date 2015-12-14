<?php

require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

class UpdateText extends moodleform {

    //Add elements to form
    public function definition() {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'community', $this->_customdata['community']);
        $mform->setType('community', PARAM_NOTAGS);

        $mform->addElement('textarea', 'message', "Post",array("rows"=> "10","cols" => "40  "));
        $mform->setType('message', PARAM_TEXT);
        $mform->addRule('message', "Você precisa inserir o texto do post", 'required', null, 'client');

        $nameButton = get_string('savechanges');

        if ($this->_customdata['idText']) {
            $nameButton = 'Editar';


            $mform->setDefault('message', $this->_customdata['message']);
            $mform->addElement('hidden', 'text', $this->_customdata['idText']);
        }

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $nameButton);
        //$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('revert'));
        $buttonarray[] = &$mform->createElement('button', 'cancelar', get_string('cancelar', 'block_webgd_community'), 'onclick=location.href="' . $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $this->_customdata['community'] . '"');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

}
