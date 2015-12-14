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
require_once($CFG->dirroot.'/blocks/webgd_community/form/QuestionForm.php');
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
$idQuestion = optional_param('question', 0, PARAM_INTEGER);
$community = $webgdDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

echo $OUTPUT->header('themeselector');

echo '<span class="titulo_list">' . '<a href="' . $url . '" >' .$OUTPUT->heading($community->name, 2, 'titulo_comunidade'). '</a>'  . '</span><div style="clear:both"></div>';

if($idQuestion){
	$webgdCommunityDao = new WebgdCommunityDao();
	if($question = $webgdCommunityDao->searchQuestionByCommunityById($idQuestion)){
		if(isset($_POST['resposta'])){
			global $DB;
			$msg = "Ocorreu um erro ao responder a enquete";

			//Se o usuário já respondeu, apaga a resposta dele para depois inserir a nova
			$webgdCommunityDao->deleteAskedQuestionByUserById($question->id,$USER->id);

			try{
				$transaction = $DB->start_delegated_transaction();

				$object = new stdClass();
				$object->userid = $USER->id;
				$object->answer_question = $_POST['resposta'];
				$object->time = time();
				$DB->insert_record(TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION_USER, $object);

				$transaction->allow_commit();
				$msg = "Enquete Respondida com sucesso";
			}catch(Exception $e) {
				$transaction->rollback($e);
			}
			redirect($CFG->wwwroot."/blocks/webgd_community/view.php?&community=$idCommunity", $msg, 10);
		}else{
			$perguntas = $webgdCommunityDao->searchAskQuestionByCommunityById($question->id);
			echo "{$question->name} <br>";
			if($question->attachmentquestion != '' && $question->attachmentquestion != '0'){
				echo "<div><video controls preload='none'>
								<source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $idQuestion ."&q=1' type='video/webm'>
								<source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $idQuestion ."&q=1' type='video/mpeg'>
								<source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $idQuestion ."&q=1' type='video/mp4'>
								<source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $idQuestion ."&q=1' type='video/ogg'>
							</video></div>";
			}
			echo '<form method="POST" action="">';
			$totalRespondidas = $webgdCommunityDao->getTotalRespondidasEnquete($question->id);
			foreach ($perguntas as $pergunta){
				$nrRespondidas = $webgdCommunityDao->getTotalRespondidasEnqueteByPergunta($pergunta->id);
				if($totalRespondidas==0) //evitando divisão por zero
                                    $porcentagem=0;
                                else
                                    $porcentagem = ($nrRespondidas*100)/$totalRespondidas;
				echo '<hr><input type="radio" name="resposta" value="'.$pergunta->id.'"> <progress max="100" value="'.$porcentagem.'"></progress> '.$pergunta->name_question.'<br>';
				if($pergunta->video != '' && $pergunta->video != '0'){
					echo "<br><div><video controls preload='none'>
									<source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $pergunta->id ."' type='video/webm'>
									<source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $pergunta->id ."' type='video/mpeg'>
									<source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $pergunta->id ."' type='video/mp4'>
									<source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $pergunta->id ."' type='video/ogg'>
								</video></div>";
				}
			}
			echo "<input type='hidden' value='".$idCommunity."' name='community'>";
			echo "<input type='hidden' value='".$idQuestion."' name='question'>";
			$now = time();
			if($now <= $question->enddate){
				echo "<br><input type='submit' value='salvar'>";
			}else{
				echo "<br>Enquete finalizada!";
			}
			echo "</form>";
		}
	}else{
		redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", 'Enquete não localizada', 10);
	}
}
echo $OUTPUT->footer();
