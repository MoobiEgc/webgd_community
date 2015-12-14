<?php
require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/coursecatlib.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

class GlossaryForm extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;

		$glossary = null;

		$mform = $this->_form; // Don't forget the underscore!

		$namePage = 'Cadastrar Glossario';
		if($this->_customdata['glossario']){
			$namePage = 'Editar glossario';

			$webgdCommunityDao = new WebgdCommunityDao();
			$glossary = $webgdCommunityDao->searchGlossaryByCommunityById($this->_customdata['community'], $this->_customdata['glossario']);
		}

		$mform->addElement('hidden', 'community', $this->_customdata['community']);

		$mform->addElement('text', 'termo', 'Termo');
		$mform->addRule('termo', 'Campo em branco', 'required', null, 'client');

		$mform->addElement('textarea', 'conceito', 'Conceito ', ' rows="7" cols="90"');
		$mform->addRule('conceito', 'Campo em branco', 'required', null, 'client');

		$mform->addElement('textarea', 'exemplo', 'Exemplo', ' rows="7" cols="90"');
		$mform->addRule('exemplo', 'Campo em branco', 'required', null, 'client');

		if($this->_customdata['glossario']){
			$mform->addElement('hidden', 'glossario', $glossary->id);
			$mform->setDefault('termo', $glossary->termo);
			$mform->setDefault('conceito', $glossary->conceito);
			$mform->setDefault('exemplo', $glossary->exemplo);
		}else{
			$mform->addElement('filepicker', 'attachmentTermo', "Video Termo", null, array('accepted_types' => '*'));
   			$mform->addRule('attachmentTermo', get_string('labelValidacaoArquivo', 'block_webgd_community'), 'required', null, 'client');

   			$mform->addElement('filepicker', 'attachmentConceito', "Video Conceito", null, array('accepted_types' => '*'));
   			$mform->addRule('attachmentConceito', get_string('labelValidacaoArquivo', 'block_webgd_community'), 'required', null, 'client');

			$mform->addElement('filepicker', 'attachmentExemplo', "Video exemplo", null, array('accepted_types' => '*'));
			$mform->addRule('attachmentExemplo', get_string('labelValidacaoArquivo', 'block_webgd_community'), 'required', null, 'client');
                        
                        $mform->addElement('filepicker', 'attachmentImage', "Imagem", null, array('accepted_types' => '*'));
			$mform->addRule('attachmentImage', get_string('labelValidacaoArquivo', 'block_webgd_community'), 'required', null, 'client');

		}

		$nameButton = get_string('savechanges');

		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', $nameButton);
		//$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('revert'));
		$buttonarray[] = &$mform->createElement('button', 'cancelar', get_string('cancelar','block_webgd_community'), 'onclick=location.href="'.$CFG->wwwroot.'/blocks/webgd_community/view.php?community='.$this->_customdata['community'].'"');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$mform->closeHeaderBefore('buttonar');
	}
}
