<?php

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

class CadastrarArquivoForm extends moodleform {

    //Add elements to form
    public function definition() {
        global $CFG;
        
        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement('hidden', 'community', $this->_customdata['community']);
        $mform->setType('community', PARAM_NOTAGS);

        $mform->addElement('text', 'nome', get_string('labelNome', 'block_webgd_community'));
        $mform->setType('nome', PARAM_TEXT);
        $mform->addRule('nome', get_string('labelValidacaoNome', 'block_webgd_community'), 'required', null, 'client');

        //não usei o get_string pois não há esse termo no 
        $nameButton = get_string('save', 'block_webgd_community');

        if ($this->_customdata['file']) {
            $nameButton = 'Editar';
            $mform->addElement('hidden', 'file', $this->_customdata['file']);


            $webgdCommunityDao = new WebgdCommunityDao();
            $file = $webgdCommunityDao->searchFileById($this->_customdata['file']);

            $mform->setDefault('nome', $file->name);
        } else {
            $mform->addElement('filepicker', 'attachment', get_string('attachment', 'forum'), null, array('accepted_types' => '*'));
            $mform->addRule('attachment', get_string('labelValidacaoArquivo', 'block_webgd_community'), 'required', null, 'client');
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
