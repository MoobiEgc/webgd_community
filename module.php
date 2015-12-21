<style>
    .mdl-left.filepicker-filelist{
        position: relative; 
        width: 100%;
    }
    #users {
        position: relative; 
        width: 50%;
    }
    #id_conteudo{
        position: relative; 
        width: 110%;
    }
</style>
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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This page provides the Administration -> .
 * .. -> Theme selector UI.
 *
 * @package core
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (dirname(__FILE__) . '/../../config.php');
require_once ($CFG->libdir . '/adminlib.php');
require_once ($CFG->dirroot . '/blocks/webgd_community/form/ModuleForm.php');
require_once ($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/CssResources.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

global $USER;

require_login(1);

$PAGE->set_url('/course/index.php');
$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

$PAGE->requires->css(CssResources::HOME_COMMUNITY);

$idCommunity = optional_param('community', 0, PARAM_INTEGER);

echo $OUTPUT->header('');

$mform = new ModuleForm (null, array('community' => $idCommunity));

if ($data = $mform->get_data()) {

    if($data->id){
      echo $OUTPUT->heading("Editar Comunidade");
    }else{
      echo $OUTPUT->heading(get_string('adicionarComunidade', 'block_webgd_community'));
    }

    if($data->id){
      $msg = get_string('msgErro', 'block_webgd_community');

      $webgdDao = new WebgdCommunityDao();

      if($communityDao = $webgdDao->findCommunityById($data->id)){
        $communityDao->name = $data->nome;
        $communityDao->description = $data->conteudo;
        $communityDao->close_community = $data->close_community;

        $realfilename = $mform->get_new_filename('video'); // this gets the name of the file
        $random = rand(); // generate some random number
        $new_file = $random . '_' . $realfilename; //add some random string to the file
        $dst = $CFG->dataroot . "/blocks/webgd_community/$new_file";  // directory name+ new filename

        if ($mform->save_file('video', $dst, true)){
          $communuty->video = $dst;
        }

        if($id = $DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY, $communityDao)){
  				$msg = 'Comunidade editada com sucesso';

          $communityParticipants = $webgdDao->findCommunityParticipantsById($data->id);

          foreach($communityParticipants as $participantes => $pt){
            if($pt->id != $USER->id){
              $DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY, array('id'=>$pt->id));
            }
          }
          $users = optional_param_array('users', 0, PARAM_INT);
            foreach($users as $idUser){
              $communutyUser = new stdClass();
              $communutyUser->community = $data->id;
              $communutyUser->admin = 0;
              $communutyUser->userid = $idUser;
              $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_USER, $communutyUser);
          }

  			}
      }
      redirect($CFG->wwwroot . "/blocks/webgd_community/view.php?community=$data->id", $msg, 10);
    }else{

      $realfilename = $mform->get_new_filename('video'); // this gets the name of the file

      $random = rand(); // generate some random number
      $new_file = $random . '_' . $realfilename; //add some random string to the file
      $dst = $CFG->dataroot . "/blocks/webgd_community/$new_file";  // directory name+ new filename

      if (!$mform->save_file('video', $dst, true)){
        $dst = '';
      }

      $communuty = new stdClass ();
      $communuty->name = $data->nome;
      $communuty->close_community = $data->close_community;
      $communuty->description = $data->conteudo;
      $communuty->userid = $USER->id;
      $communuty->video = $dst;
      $communuty->timecreated = time();

      $msg = get_string('msgErroComunidadeEdicao', 'block_webgd_community');

      try {
          $transaction = $DB->start_delegated_transaction();

          $msg = get_string('msgErroComunidadeRegistro', 'block_webgd_community');

          if ($idCommunity = $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY, $communuty, true)) {
              $msg = get_string('msgComunidadeCadastradaSucesso', 'block_webgd_community');
          }

          $users = optional_param_array('users', 0, PARAM_INT);
            foreach($users as $idUser){
              $communutyUser = new stdClass();
              $communutyUser->community = $idCommunity;
              $communutyUser->admin = 0;
              $communutyUser->userid = $idUser;
              $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_USER, $communutyUser);
          }

          //ADMIN
          $communutyUser->admin = 1;
          $communutyUser->userid = $USER->id;

          $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_USER, $communutyUser);

          $transaction->allow_commit();
          redirect($CFG->wwwroot . "/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);
      } catch (Exception $e) {
          $transaction->rollback($e);
          redirect($CFG->wwwroot . '/blocks/webgd_community/module.php', $msg, 10);
      }
    }
} else {
    if($idCommunity){
      echo $OUTPUT->heading("Editar Comunidade");
    }else{
      echo $OUTPUT->heading(get_string('adicionarComunidade', 'block_webgd_community'));
    }
    $mform->display();
}

echo $OUTPUT->footer();
