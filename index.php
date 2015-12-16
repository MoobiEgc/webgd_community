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
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/ImageResources.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/CssResources.php');
require_once($CFG->dirroot.'/blocks/webgd_community/menus/Commons.php');
require_login(1);
global $USER, $DB, $CFG;

$PAGE->set_url('/course/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

$PAGE->requires->css(CssResources::LIST_HOME_COMMUNITY);

echo $OUTPUT->header('themeselector');

$webgdCommunityDao = new WebgdCommunityDao();

if($listCommunity = $webgdCommunityDao->getListCommunity()){
	echo '<span class="titulo_list">'.$OUTPUT->heading('Comunidades').'</span>';
	echo '<div class="lista_home">';
		echo '<span class="titulo_list"></span>';
		foreach ($listCommunity as $community){
			$participar = "";
			$fechada = 0;
			if(!$webgdCommunityDao->findUserInCommunityById($community->id, $USER->id)){
                                if($community->close_community==1){
                                    $participar = "<img class='botao_comunidade' src='".$CFG->wwwroot.ImageResources::COMUNIDADE_FECHADA."'>";
																		$fechada = 1;
                                }else{
                                    $participar = "<div>".html_writer::tag('a', "<img class='botao_comunidade' src='".$CFG->wwwroot.ImageResources::PARTICIPAR_COMUNIDADE."'>", array('href' => "view.php?community={$community->id}&confirm=1"))."</div>";
                                }
			}else{
                            if($community->close_community==1){
                                    $participar = "<img class='botao_comunidade' src='".$CFG->wwwroot.ImageResources::COMUNIDADE_FECHADA."'>";
                                }
                        }

			Commons::printListHomeCommunity($community, $participar,$fechada);
	}
	echo '<div>';
}else{
	echo get_string('nenhumaComunidadeRegistrada','block_webgd_community');
}
echo $OUTPUT->footer();
