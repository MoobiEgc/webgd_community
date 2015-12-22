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
require_once($CFG->dirroot . '/blocks/webgd_community/form/QuestionForm.php');
require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/JsResources.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/CssResources.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/CssResources.php');

require_login(1);

global $USER;

$PAGE->requires->css(CssResources::HOME_COMMUNITY);

$PAGE->set_url('/course/index.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

$PAGE->requires->css(CssResources::UI_JQUERY);
$PAGE->requires->css(CssResources::UI_THEME);
$PAGE->requires->js(JsResources::JQUERY);
$PAGE->requires->js(JsResources::JQUERY_UI);

$webgdDao = new WebgdCommunityDao();
$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idQuestion = optional_param('question', 0, PARAM_INTEGER);
$community = $webgdDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

echo $OUTPUT->header('themeselector');

if ($idQuestion) {
    $webgdCommunityDao = new WebgdCommunityDao();
    if (!$webgdCommunityDao->searchQuestionByCommunityById($idQuestion)) {
        redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=3", get_string('arqNaoEnc','block_webgd_community') . $idQuestion, 10);
        echo $OUTPUT->footer();
        die;
    } else {
        echo $OUTPUT->heading('<span class="titulo_list">' .
                '<a href="' . $url . '" >' .
                $OUTPUT->heading($community->name, 2, 'titulo_comunidade') .
                '</a></span><br/>');
        echo "<div class='subTitle'>Editar Enquete</div><br/>";
    }
} else {
    echo $OUTPUT->heading('<span class="titulo_list">' .
            '<a href="' . $url . '" >' .
            $OUTPUT->heading($community->name, 2, 'titulo_comunidade') .
            '</a></span><br/>');
    echo "<div class='subTitle'>Cadastrar Enquete</div><br/>";
}

$mform = new QuestionForm(null, array('community' => $idCommunity, 'question' => $idQuestion));


if ($data = $mform->get_data()) {
    $msg = "";

    if ($idQuestion) {
        $question = $webgdCommunityDao->searchQuestionByCommunityById($idQuestion);
        $question->name = $data->nome;
        $question->enabled = $data->enable;
        $question->startdate = DateTime::createFromFormat('d-m-Y', str_replace("/", "-", $data->from))->getTimestamp();
        $question->enddate = DateTime::createFromFormat('d-m-Y', str_replace("/", "-", $data->to))->getTimestamp();

        $attQuestion = $mform->get_new_filename('attachmentQuestion');

        $random = rand();
        $name = $random . '_' . $attQuestion;

        $pathAttQuestion = "{$CFG->dataroot}/webgd_community/$name";

        if ($mform->save_file('attachmentQuestion', $pathAttQuestion, true)) {
            $question->attachmentQuestion = $pathAttQuestion;
        }

        //Deleta todos os videos atuais e insere os novos que sobraram

        $webgdCommunityDao->deleteAskQuestionByCommunity($question->id);

        $correrVideos = 0;
        foreach ($data->video as $video) {
            $perguntaQuestao = new stdClass();
            $perguntaQuestao->name_question = $data->pergunta[$correrVideos];
            $perguntaQuestao->video = $video;
            $perguntaQuestao->question = $question->id;
            $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION, $perguntaQuestao);
            $correrVideos++;
        }

        $msg = "Ocorreu um erro ao editar a enquete";

        if ($DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY_QUESTION, $question)) {
            $msg = "Enquete editada com sucesso";
        }
    } else {

        $attQuestion = $mform->get_new_filename('attachmentQuestion');

        $random = rand();
        $name = $random . '_' . $attQuestion;

        $pathAttQuestion = "{$CFG->dataroot}/webgd_community/$name";

        $msg = get_string('msgErro', 'block_webgd_community');

        if (!($mform->save_file('attachmentQuestion', $pathAttQuestion, true))) {
            $pathAttQuestion = "";
        }
        try {

            $transaction = $DB->start_delegated_transaction();

            $post = new stdClass();
            $post->community = $data->community;
            $post->userid = $USER->id;
            $post->time = time();
            $post->type = 'question';

            $idPost = $webgdDao->insertRecordInTableCommunityPost($post);

            $question = new stdClass();
            $question->name = $data->nome;
            $question->enabled = $data->enable;
            $question->startdate = DateTime::createFromFormat('d-m-Y', str_replace("/", "-", $data->from))->getTimestamp();
            $question->enddate = DateTime::createFromFormat('d-m-Y', str_replace("/", "-", $data->to))->getTimestamp();
            $question->post = $idPost;
            $question->attachmentQuestion = $pathAttQuestion;

            $idQuestion = $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_QUESTION, $question);

            $correrVideos = 0;
            foreach ($data->video as $video) {
                $perguntaQuestao = new stdClass();
                $perguntaQuestao->name_question = $data->pergunta[$correrVideos];
                $perguntaQuestao->video = $video;
                $perguntaQuestao->question = $idQuestion;
                $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION, $perguntaQuestao);
                $correrVideos++;
            }

            $transaction->allow_commit();
            $msg = "Enquete Registrada com sucesso";
        } catch (Exception $e) {
            $transaction->rollback($e);
            $msg = "Ocorreu um erro ao salvar a enquete";
        }
    }
    redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);
} else {
    $mform->display();
}
echo $OUTPUT->footer();
