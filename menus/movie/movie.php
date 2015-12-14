<?php

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/blocks/webgd_community/form/CadastrarArquivoForm.php');
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
$idFile = optional_param('file', 0, PARAM_INTEGER);

echo $OUTPUT->header('themeselector');

$webgdDao = new WebgdCommunityDao();
$community = $webgdDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

if ($idFile) {
    $webgdCommunityDao = new WebgdCommunityDao();
    if (!$webgdCommunityDao->searchMovieCommunityById($idCommunity, $idFile)) {
        redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=7", 'Video não encontrado', 10);
        echo $OUTPUT->footer();
        die;
    } else {
        echo $OUTPUT->heading('<span class="titulo_list">' .
                '<a href="' . $url . '" >' .
                $OUTPUT->heading($community->name , 2, 'titulo_comunidade') .
                '</a></span><br/>');
        echo "<div class='subTitle'>Editar Vídeo</div><br/>";
    }
} else {
    echo $OUTPUT->heading('<span class="titulo_list">' .
            '<a href="' . $url . '" >' .
            $OUTPUT->heading($community->name , 2, 'titulo_comunidade') .
            '</a></span><br/>');
    echo "<div class='subTitle'>Cadastar Vídeo</div><br/>";
}

$mform = new CadastrarArquivoForm(null, array('community' => $idCommunity, 'file' => $idFile));

if ($data = $mform->get_data()) {

    if ($idFile) {
        $msg = get_string('msgErro', 'block_webgd_community');

        if ($arquivo = $webgdCommunityDao->searchMovieById($idFile)) {
            $arquivo->name = $data->nome;

            if ($id = $DB->update_record(TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA, $arquivo)) {
                $msg = 'Video editado com sucesso';
            }
        }
    } else {
        $name = $mform->get_new_filename('attachment');

        $random = rand();
        $name = $random . '_' . $name;

        $path = "{$CFG->dataroot}/webgd_community/$name";

        $msg = get_string('msgErro', 'block_webgd_community');

        if ($mform->save_file('attachment', $path, true)) {

          try{

            $transaction = $DB->start_delegated_transaction();


            $post = new stdClass();
            $post->community = $idCommunity;
            $post->userid = $USER->id;
            $post->time = time();
            $post->type = 'movie';

            $idPost = $webgdDao->insertRecordInTableCommunityPost($post);


            $arquivo = new stdClass();
            $arquivo->post = $idPost;
            $arquivo->name = $data->nome;
            $arquivo->path = $path;

            $webgdDao->insertRecordInTableCommunityMedia($arquivo);

            $transaction->allow_commit();

            $msg = 'Video Cadastrado com sucesso';

          }catch(Exception $e){
            $transaction->rollback($e);
          }
        }
    }
    redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity", $msg, 10);
} else {
    $mform->display();
}
echo $OUTPUT->footer();
