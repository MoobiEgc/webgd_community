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
require_once($CFG->dirroot.'/blocks/webgd_community/form/MapForm.php');
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

$webgdDao = new WebgdCommunityDao();
$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idMap = optional_param('map', 0, PARAM_INTEGER);
$community = $webgdDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

echo $OUTPUT->header('themeselector');

if($idMap){
	$webgdCommunityDao = new WebgdCommunityDao();
	if(!$webgdCommunityDao->searchMentalMapByCommunityByIdByUser($idCommunity, $idMap, $USER->id)){
		redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=2&suboption=1", 'Mapa mental não encontrado', 10);
		echo $OUTPUT->footer();
		die;
	}else{
		echo $OUTPUT->heading('<span class="titulo_list">' .
						'<a href="' . $url . '" >' .
		     $OUTPUT->heading($community->name , 2, 'titulo_comunidade') .
					  '</a></span><br/>');
		echo "<div class='subTitle'>Editar Mapa mental</div><br/>";
	}
}else{
	echo $OUTPUT->heading('<span class="titulo_list">' .
					  '<a href="' . $url . '" >' .
			 $OUTPUT->heading($community->name , 2, 'titulo_comunidade') .
					  '</a></span><br/>');
	echo "<div class='subTitle'>Cadastrar Atividade</div><br/>";
}

$mform = new MapForm(null, array('community' => $idCommunity, 'map' => $idMap));

if ($data = $mform->get_data()) {
	$msg = "";

	if($idMap){
		$map = $webgdCommunityDao->searchMentalMapByCommunityById($idMap);
		$map->name = $data->nome;
		$map->url= $data->link;

		$msg = "Ocorreu um erro ao editar o link";

		if($DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY_LINKS, $map)){
			$msg = "Link editado com sucesso";
		}
	}else{

		try{

			$transaction = $DB->start_delegated_transaction();

			$post = new stdClass();
			$post->community = $idCommunity;
			$post->userid = $USER->id;
			$post->time = time();
			$post->type = 'map';

			$idPost = $webgdDao->insertRecordInTableCommunityPost($post);

			$map = new stdClass();
			$map->post = $idPost;
			$map->name = $data->nome;
			$map->url= $data->link;

			$DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_LINKS, $map);

			$transaction->allow_commit();

			$msg = "Link registrado com sucesso";

		}catch(Exception $e){
			$transaction->rollback($e);
			$msg = "Ocorreu um erro ao salvar o link";
		}
	}
	redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&suboption=1", $msg, 10);
} else {
	$mform->display();
}
echo $OUTPUT->footer();
