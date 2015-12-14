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
require_once($CFG->dirroot.'/blocks/webgd_community/form/CadastrarArquivoForm.php');
require_once($CFG->dirroot.'/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/CssResources.php');

require_login(1);

global $USER;

$PAGE->requires->css(CssResources::HOME_COMMUNITY);

$PAGE->set_url('/course/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idFile = optional_param('file', 0, PARAM_INTEGER);

echo $OUTPUT->header('themeselector');

$webgdCommunityDao = new WebgdCommunityDao();
$community = $webgdCommunityDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot.'/blocks/webgd_community/view.php?community='.$idCommunity;

if($idFile){
	$webgdCommunityDao = new WebgdCommunityDao();
	if(!$webgdCommunityDao->searchPhotoCommunityById($idCommunity, $idFile)){
		redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=6", 'Foto não encontrado', 10);
		echo $OUTPUT->footer();
		die;
	}else{
			echo $OUTPUT->heading('<span class="titulo_list">'.
                                '<a href="'.$url.'" >'.
                                    $OUTPUT->heading($community->name, 2, 'titulo_comunidade').
                                '</a></span><br/>');
			echo "<div class='subTitle'>Editar Foto</div><br/>";
	}
}else{
		echo $OUTPUT->heading('<span class="titulo_list">'.
                                '<a href="'.$url.'" >'.
                                    $OUTPUT->heading($community->name , 2, 'titulo_comunidade').
                                '</a></span><br/>');
		echo "<div class='subTitle'>Cadastrar Foto</div><br/>";
}

$mform = new CadastrarArquivoForm(null, array('community' => $idCommunity, 'file' => $idFile));

if ($data = $mform->get_data()) {

	if($idFile){
		$msg = get_string('msgErro', 'block_webgd_community');

		if($arquivo = $webgdCommunityDao->searchPhotoById($idFile)){
			$arquivo->name = $data->nome;

			if($id = $DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA, $arquivo)){
				$msg = 'Foto editado com sucesso';
			}
		}
	}else{
		$name = $mform->get_new_filename('attachment');

		$random = rand();
		$name = $random . '_' . $name;

		$path = "{$CFG->dataroot}/webgd_community/$name";

		$msg = get_string('msgErro', 'block_webgd_community');

		if($mform->save_file('attachment', $path, true)){

			try{

				$transaction = $DB->start_delegated_transaction();

				$post = new stdClass();
				$post->community = $idCommunity;
				$post->userid = $USER->id;
				$post->time = time();
				$post->type = 'photo';

				$idPost = $webgdCommunityDao->insertRecordInTableCommunityPost($post);

				$arquivo = new stdClass();
				$arquivo->post = $idPost;
				$arquivo->path = $path;
				$arquivo->name = $data->nome;

				$webgdCommunityDao->insertRecordInTableCommunityMedia($arquivo);

				$transaction->allow_commit();


				$msg = 'Foto Cadastrada com sucesso';
			}catch(Exception $e){
				$transaction->rollback($e);
			}
		}
	}
	redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);
} else {
	$mform->display();
}
echo $OUTPUT->footer();
