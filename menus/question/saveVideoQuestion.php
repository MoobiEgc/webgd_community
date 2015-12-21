<?php
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

require_login(1);

global $USER;

//$idCommunity = optional_param('community', 0, PARAM_INTEGER);
//$idQuestion = optional_param('question', 0, PARAM_INTEGER);

//$mform = new QuestionForm(null, array('community' => $idCommunity, 'question' => $idQuestion));

if ($_FILES['fileUpload']) {

    $random = rand();
    $name = $random . '_' . optional_param('name', '', PARAM_TEXT);
    
    move_uploaded_file($_FILES['fileUpload']['tmp_name'], $CFG->dataroot.'/blocks/'.'webgd_community/'.$name);
    echo $CFG->dataroot.'/blocks/'.'webgd_community/'.$name;

} else {
	echo "erro";
}
