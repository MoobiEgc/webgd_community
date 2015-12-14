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

global $USER;

require_login(1);

$PAGE->set_url('/course/index.php');
$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

echo $OUTPUT->header('');

echo $OUTPUT->heading(get_string('adicionarComunidade', 'block_webgd_community'));

$mform = new ModuleForm ();

if ($data = $mform->get_data()) {

    $realfilename = $mform->get_new_filename('file'); // this gets the name of the file

    $random = rand(); // generate some random number
    $new_file = $random . '_' . $realfilename; //add some random string to the file
    $dst = $CFG->dataroot . "/blocks/webgd_community/$new_file";  // directory name+ new filename

    $communuty = new stdClass ();
    $communuty->name = $data->nome;
    $communuty->close_community = $data->close_community;
    $communuty->description = $data->conteudo ['text'];
    $communuty->userid = $USER->id;
    $communuty->timecreated = time();

    $msg = get_string('msgErroComunidadeEdicao', 'block_webgd_community');

    try {
        $transaction = $DB->start_delegated_transaction();

        $msg = get_string('msgErroComunidadeRegistro', 'block_webgd_community');

        if ($idCommunity = $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY, $communuty, true)) {
            $msg = get_string('msgComunidadeCadastradaSucesso', 'block_webgd_community');
        }


        foreach ($_POST['users'] as $idUser) {
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
} else {
    $mform->display();
}
echo $OUTPUT->footer();
