<?php

require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

class IconeForm extends moodleform {

    //Add elements to form
    public function definition() {
        global $CFG;

        $question = null;

        $mform = $this->_form; // Don't forget the underscore!

        $namePage = 'Cadastrar Icone';
        $nameButton = 'Salvar';
        if ($this->_customdata['glossario']) {
            $namePage = 'Editar Icone';
            $nameButton = 'Editar';
        }

        $mform->addElement('header', 'nameforyourheaderelement', $namePage);

        $mform->addElement('hidden', 'community', $this->_customdata['community']);

        $mform->addElement('text', 'nome', get_string('labelNome', 'block_webgd_community'));
        $mform->addRule('nome', get_string('labelValidacaoNome', 'block_webgd_community'), 'required', null, 'client');

        $mform->addElement('text', 'link', 'Url do icone');
        $mform->addRule('link', 'Campo em branco', 'required', null, 'client');

        if ($this->_customdata['glossario']) {
            $webgdCommunityDao = new WebgdCommunityDao();
            $glossary = $webgdCommunityDao->searchGlossaryByCommunityById($this->_customdata['community'], $this->_customdata['glossario']);
            $mform->setDefault('nome', $glossary->name);
            $mform->setDefault('link', $glossary->url);

            $mform->addElement('hidden', 'glossario', $this->_customdata['glossario']);
        }

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $nameButton);
        //$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('revert'));
        $buttonarray[] = &$mform->createElement('button', 'cancelar', 'Cancelar', 'onclick=location.href="' . $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $this->_customdata['community'] . '"');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

}
