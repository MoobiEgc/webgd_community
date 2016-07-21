<?php
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/helper/MultiSelectHelper.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

class ModuleForm extends moodleform {

    //Add elements to form
    public function definition() {
        global $CFG, $DB, $USER;
        $mform = $this->_form; // Don't forget the underscore!

        $multiSelect = new MultiSelectHelper('users[]', 'users');

        $webgdCommunityDao = new WebgdCommunityDao();


        $mform->addElement('text', 'nome', get_string('labelNome', 'block_webgd_community'));
        $mform->addRule('nome', get_string('labelValidacaoNome', 'block_webgd_community'), 'required', null, 'client');
        $mform->setType('nome', PARAM_TEXT);

        if ($this->_customdata['community']) {
            $mform->addElement('hidden', 'id', $this->_customdata['community']);
            $communityDao = $webgdCommunityDao->findCommunityById($this->_customdata['community']);
            $mform->setDefault('nome', $communityDao->name);
            //preciso ainda ler os dados da comunidade para inserir aqui quando for edição
        } else {
            $mform->addElement('hidden', 'id');
            $mform->setType('id', PARAM_NOTAGS);
        }

        $formCheckbox = $mform->createElement('advcheckbox', 'close_community', 'Comunidade fechada', array('group' => 1), array(0, 1));
        if ($this->_customdata['community']) {
            $communityDao = $webgdCommunityDao->findCommunityById($this->_customdata['community']);
            if ($communityDao->close_community == 1) {
                $formCheckbox->setChecked(true);
            } else {
                $formCheckbox->setChecked(false);
            }
        } else {
            $formCheckbox->setChecked(false);
        }

        $mform->addElement($formCheckbox);

        //$mform->addElement('advcheckbox', 'close_community', 'Comunidade fechada','' , array('group' => 1), array(0, 1));
        $mform->addElement('filepicker', 'video', "Vídeo", null, array('accepted_types' => array('*')));

        $records = $webgdCommunityDao->getListNameUser($USER->id);

        foreach ($records as $record) {
            $selected = false;
            if ($this->_customdata['community']) {
                if ($webgdCommunityDao->participanteInCommunity($record->id, $this->_customdata['community'])) {
                    $selected = true;
                }
            }
            $multiSelect->addElement($record->firstname, $record->id, $selected);
        }

        $radioarray = array();
        $radioarray[] = & $mform->createElement('html', '');
        $radioarray[] = & $mform->createElement('html', '');
        $mform->addGroup($radioarray, 'radioar', 'Participante', array(' '), false);

        $mform->addElement('html', $multiSelect->printMultiSelect());

        $mform->addElement('textarea', 'conteudo', get_string('labelDescricao', 'block_webgd_community'), null);
        $mform->addHelpButton('conteudo', 'coursesummary');
        $mform->setType('conteudo', PARAM_RAW);

        if ($this->_customdata['community']) {
            $mform->setDefault('conteudo',  $communityDao->description);
        }

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = &$mform->createElement('button', 'cancelar', get_string('cancelar', 'block_webgd_community'), 'onclick=location.href="' . $CFG->wwwroot . '/blocks/webgd_community/index.php"');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    //Custom validation should be added here
    function validation($data, $files) {
        $errors = array();
        if (!isset($_POST['users'])) {
            ?><script>alert('nenhum participante informado');</script><?php
            $errors['users'] = 'Vazio';
        }

        return $errors;
    }

}
