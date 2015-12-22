<?php

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

global $USER;

require_login(1);

$PAGE->set_url('/course/index.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

echo $OUTPUT->header('themeselector');

echo $OUTPUT->heading(get_string('deletarArq','block_webgd_community'));

$idFile = optional_param('file', 0, PARAM_INTEGER);
$idCommunity = optional_param('community', 0, PARAM_INTEGER);

$webgdCommunityDao = new WebgdCommunityDao();

$msg = get_string('nenhumArqEnc','block_webgd_community');

if ($webgdCommunityDao->searchFileCommunityById($idCommunity, $idFile)) {

    $file = $webgdCommunityDao->searchFileById($idFile);

    if ($webgdCommunityDao->deleteFileByIdUser($file->id, $USER->id, $file->post)) {

        $msg = get_string('deletarArqSuc','block_webgd_community');

        unlink($file->path);
    }
}

redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);

echo $OUTPUT->footer();
