<?php

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/blocks/webgd_community/form/UpdateText.php');
require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/CssResources.php');

require_login(1);

global $USER;

$PAGE->requires->css(CssResources::HOME_COMMUNITY);

$PAGE->set_url('/course/index.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('standard');

$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idText = optional_param('text', 0, PARAM_INTEGER);

echo $OUTPUT->header('themeselector');

$webgdDao = new WebgdCommunityDao();
$community = $webgdDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

if ($idText) {
    $webgdCommunityDao = new WebgdCommunityDao();
    if (!$textData = $webgdCommunityDao->searchTextById($idText)) {
        redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=1", get_string('arqNaoEnc','block_webgd_community'), 10);
        echo $OUTPUT->footer();
        die;
    } else {
        echo $OUTPUT->heading('<span class="titulo_list">' .
                '<a href="' . $url . '" >' .
                $OUTPUT->heading($community->name . ' - Editar Texto', 2, 'titulo_comunidade') .
                '</a>');
    }
} else {
    echo $OUTPUT->heading('<span class="titulo_list">' .
            '<a href="' . $url . '" >' .
            $OUTPUT->heading($community->name . ' - Cadastrar Texto', 2, 'titulo_comunidade') .
            '</a>');
}

$mform = new UpdateText(null, array('community' => $idCommunity, 'idText' => $idText, 'message' => $textData->message));

if ($data = $mform->get_data()) {

    if ($idText) {
        $msg = get_string('msgErro', 'block_webgd_community');

        if ($arquivo = $webgdCommunityDao->searchTextById($idText)) {
            $arquivo->message = $data->message;

            if ($id = $DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY_TEXT, $arquivo)) {
                $msg = 'Texto editado com sucesso';
            }
        }
    }
    redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);
} else {
    $mform->display();
}
echo $OUTPUT->footer();
