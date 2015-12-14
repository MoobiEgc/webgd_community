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


require_login(1);

global $USER, $CFG;

$PAGE->requires->css(CssResources::HOME_COMMUNITY);
$PAGE->requires->js(JsResources::JQUERY);
$PAGE->requires->js(JsResources::HOME_COMMUNITY);

$PAGE->set_url('/course/index.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');
$webgdCommunityDao = new WebgdCommunityDao();
$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idGlossario = optional_param('glossario', 0, PARAM_INTEGER);
$community = $webgdCommunityDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

echo $OUTPUT->header('themeselector');

$webgdCommunityDao = new WebgdCommunityDao();
if(!$webgdCommunityDao->searchGlossaryByCommunityById($idCommunity, $idGlossario, $USER->id)){
	redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=8", 'Glossario não encontrado', 10);
	echo $OUTPUT->footer();
	die;
}

$glossary = $webgdCommunityDao->searchGlossaryByCommunityById($idCommunity, $idGlossario);

echo $OUTPUT->heading('<span class="titulo_list">' .
						'<a href="' . $url . '" >' .
		     $OUTPUT->heading($community->name, 2, 'titulo_comunidade') .
					  '</a></span>');

echo "<div style='clear:both;'>".$OUTPUT->heading("Glossário")."</div>";
echo "<p style='font-weight:bold;'>TERMO: ".$glossary->termo."</p>";

if($glossary->image != '' && $glossary->image != '0'){
	echo "<div style='margin-bottom: 10px; font-weight:bold;'>SIGNWRITING: <img src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showPhoto.php?glossary=".$idGlossario."&community=".$idCommunity."'></div>";
}

echo "<div id='div-tab'>";
echo "<ul id='menu-tab'>
				<li class='active'>Sinal</li>
				<li>Conceito</li>
				<li>Exemplo</li>
			</ul>";
echo "<div class='tab-glossary' id='Sinal'>";
if($glossary->videotermo != '' && $glossary->videotermo != '0'){
	echo "<div class='tab-glossary-video'><video controls preload='none'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=termo' type='video/webm'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=termo' type='video/mpeg'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=termo' type='video/mp4'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=termo' type='video/ogg'>
				</video></div>";
}

echo "</div><div class='tab-glossary tab-hide' id='Conceito'>";
echo "<div class='div-tab-texto'><span style='font-weight:bold;'>CONCEITO:</span><br/><br/>".$glossary->conceito."</div>";
if($glossary->videoconceito != '' && $glossary->videoconceito != '0'){
	echo "<div class='tab-glossary-video'><video controls preload='none'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=conceito' type='video/webm'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=conceito' type='video/mpeg'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=conceito' type='video/mp4'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=conceito' type='video/ogg'>
				</video></div>";
}

echo "</div><div class='tab-glossary tab-hide' id='Exemplo'>";
echo "<div class='div-tab-texto'><span style='font-weight:bold;'>EXEMPLO:</span><br/><br/>" . $glossary->exemplo."</div>";

if($glossary->videoexemplo != '' && $glossary->videoexemplo!= '0'){
	echo "<div class='tab-glossary-video'><video controls preload='none'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=exemplo' type='video/webm'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=exemplo' type='video/mpeg'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=exemplo' type='video/mp4'>
					<source src='".$CFG->wwwroot . "/blocks/webgd_community/menus/glossary/showMovieGlossary.php?glossary=". $idGlossario . "&community=" . $idCommunity ."&q=exemplo' type='video/ogg'>
				</video></div>";
}

echo "</div></div>";

echo $OUTPUT->box_start();
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script>
$(function() {
    $("#tabs").tabs();
});
</script>



<?php

$OUTPUT->single_button($CFG->wwwroot."/blocks/webgd_community/view.php?community=$idCommunity&option=8", "Voltar");

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
