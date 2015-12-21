<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

global $USER;

require_login(1);

$PAGE->set_url('/course/index.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

echo $OUTPUT->header('themeselector');

echo $OUTPUT->heading('Deletar Comunidade');

$idCommunity = optional_param('community', 0, PARAM_INTEGER);

$webgdCommunityDao = new WebgdCommunityDao();

$msg = 'Erro ao excluir Comunidade';

if ($webgdCommunityDao->deleteCommunityById($idCommunity)) {
    $msg = 'Comunidade deletada com sucesso';
}

redirect("{$CFG->wwwroot}/blocks/webgd_community/index.php", $msg, 10);

echo $OUTPUT->footer();
