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
 * This page provides the Administration -> ... -> Theme selector UI.
 *
 * @package core
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/blocks/webgd_community/form/GlossaryForm.php');
require_once($CFG->dirroot.'/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/JsResources.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/CssResources.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/CssResources.php');

require_login(1);

global $USER;

$PAGE->requires->css(CssResources::HOME_COMMUNITY);

$PAGE->set_url('/course/index.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

$webgdCommunityDao = new WebgdCommunityDao();
$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idGlossario = optional_param('glossario', 0, PARAM_INTEGER);
$community = $webgdCommunityDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

echo $OUTPUT->header('themeselector');

if($idGlossario){
	$webgdCommunityDao = new WebgdCommunityDao();
	if(!$webgdCommunityDao->searchGlossaryByCommunityById($idCommunity, $idGlossario, $USER->id)){
		redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=8", 'Glossario não encontrado', 10);
		echo $OUTPUT->footer();
		die;
	}else{
		echo $OUTPUT->heading('<span class="titulo_list">' .
						'<a href="' . $url . '" >' .
		     $OUTPUT->heading($community->name, 2, 'titulo_comunidade') .
					  '</a></span><br/>');
		echo "<div class='subTitle'>Editar Termo</div><br/>";
	}
}else{
	  echo $OUTPUT->heading('<span class="titulo_list">' .
					'<a href="' . $url . '" >' .
	       $OUTPUT->heading($community->name , 2, 'titulo_comunidade') .
				  '</a></span><br/>');
		echo "<div class='subTitle'>Cadastrar Termo</div><br/>";
}

$mform = new GlossaryForm(null, array('community' => $idCommunity, 'glossario' => $idGlossario));

if ($data = $mform->get_data()) {
	$msg = "";

	if($idGlossario){
		$glossary = $webgdCommunityDao->searchGlossaryByCommunityById($idCommunity, $idGlossario);
		$glossary->termo = $data->termo;
		$glossary->conceito = $data->conceito;
		$glossary->exemplo = $data->exemplo;
                
		$msg = "Ocorreu um erro ao editar o glossario";

		if($DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARY, $glossary)){
			$msg = "Termo editado com sucesso";
		}
	}else{

		//TERMO
		$name = $mform->get_new_filename('attachmentTermo');
		$random = rand();
		$name = $random . '_termo_' . $name;
		$pathTermo = "{$CFG->dataroot}/webgd_community/$name";
		if(!($mform->save_file('attachmentTermo', $pathTermo, true))){
			$pathTermo = "";
			$msg = "Ocorreu um erro ao salvar o video";
		}

		//Conceito
		$name = $mform->get_new_filename('attachmentConceito');
		$random = rand();
		$name = $random . '_conceito_' . $name;
		$pathConceito = "{$CFG->dataroot}/webgd_community/$name";
		if(!($mform->save_file('attachmentConceito', $pathConceito, true))){
			$pathConceito = "";
			$msg = "Ocorreu um erro ao salvar o video";
		}

		//exemplo
		$name = $mform->get_new_filename('attachmentExemplo');
		$random = rand();
		$name = $random . '_exemplo_' . $name;
		$pathExemplo = "{$CFG->dataroot}/webgd_community/$name";
		if(!($mform->save_file('attachmentExemplo', $pathExemplo, true))){
				$pathExemplo = "";
				$msg = "Ocorreu um erro ao salvar o video";
		}

                //imagem
		$name = $mform->get_new_filename('attachmentImage');
		$random = rand();
		$name = $random . '_image_' . $name;
		$pathImage = "{$CFG->dataroot}/webgd_community/$name";
		if(!($mform->save_file('attachmentImage', $pathImage, true))){
				$pathImage = "";
				$msg = "Ocorreu um erro ao salvar a imagem";
		}

		$msg = get_string('msgErro', 'block_webgd_community');

		$glossary = new stdClass();
		$glossary->termo = $data->termo;
		$glossary->conceito = $data->conceito;
		$glossary->community = $data->community;
		$glossary->exemplo = $data->exemplo;
		$glossary->videoTermo = $pathTermo;
		$glossary->videoConceito = $pathConceito;
		$glossary->videoExemplo = $pathExemplo;
                $glossary->image = $pathImage;
		$glossary->time =  time();
		$glossary->userid = $USER->id;

		try{
			$transaction = $DB->start_delegated_transaction();
			$DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARY, $glossary);
			$transaction->allow_commit();
			$msg = "Termo registrado com sucesso";
		} catch(Exception $e) {
			$transaction->rollback($e);
			$msg = "Ocorreu um erro ao salvar o Termo";
		}
	}
	redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);
} else {
	$mform->display();
}
echo $OUTPUT->footer();
