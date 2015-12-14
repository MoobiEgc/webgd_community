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
require_once($CFG->dirroot.'/blocks/webgd_community/form/IconeForm.php');
require_once($CFG->dirroot.'/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/JsResources.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/CssResources.php');


require_login(1);

global $USER;

$PAGE->set_url('/course/index.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

$webgdDao = new WebgdCommunityDao();
$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idIcone = optional_param('glossario', 0, PARAM_INTEGER);

echo $OUTPUT->header('themeselector');

if($idIcone){
	$webgdCommunityDao = new WebgdCommunityDao();
	if(!$webgdCommunityDao->searchIconeByCommunityByIdByUser($idCommunity, $idIcone, $USER->id)){
		redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=2", 'icone não encontrado', 10);
		echo $OUTPUT->footer();
		die;
	}else{
		echo $OUTPUT->heading('Editar Icone');
	}
}else{
	echo $OUTPUT->heading('Cadastrar Icone');
}

$mform = new IconeForm(null, array('community' => $idCommunity, 'glossario' => $idIcone));

if ($data = $mform->get_data()) {
	$msg = "";

	if($idIcone){
		$glossary = $webgdCommunityDao->searchIconeByCommunityById($idIcone);
		$glossary->name = $data->nome;
		$glossary->url= $data->link;

		$msg = "Ocorreu um erro ao editar o icone";

		if($DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY_LINKS, $glossary)){
			$msg = "Icone editado com sucesso";
		}
	}else{

		try{

			$transaction = $DB->start_delegated_transaction();

			$post = new stdClass();
			$post->community = $data->community;
			$post->userid = $USER->id;
			$post->time = time();
			$post->type = 'icon';

			$idPost = $webgdDao->insertRecordInTableCommunityPost($post);

			$icon = new stdClass();
			$icon->post = $idPost;
			$icon->name = $data->nome;
			$icon->url= $data->link;

			$DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_LINKS, $icon);

			$transaction->allow_commit();

			$msg = "Icone registrado com sucesso";

		}catch(Exception $e) {
			$transaction->rollback($e);
			$msg = "Ocorreu um erro ao salvar o Icone";
		}
	}
	redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);
} else {
	$mform->display();
}
echo $OUTPUT->footer();
