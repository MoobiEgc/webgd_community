<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot . '/blocks/webgd_community/menus/Commons.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/ImageResources.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/CssResources.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/JsResources.php');
global $USER, $DB, $CFG, $OUTPUT;

require_login(1);

$PAGE->set_url('/course/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

$PAGE->requires->css(CssResources::HOME_COMMUNITY);
$PAGE->requires->css(CssResources::BOOTSTRAP);
$PAGE->requires->js(JsResources::HOME_COMMUNITY);

$idMenu = optional_param('option', 0, PARAM_INTEGER);
$idSubOption = optional_param('suboption', 0, PARAM_INTEGER);
$idCommunity = optional_param('community', '', PARAM_INTEGER);
$confirm = optional_param('confirm', 0, PARAM_INTEGER);
$cam = optional_param('cam', 0, PARAM_INTEGER);
$filter = optional_param('filter', '', PARAM_ALPHAEXT);

echo $OUTPUT->header('themeselector');

$webgdDao = new WebgdCommunityDao();

$community = $webgdDao->findCommunityById($idCommunity);

$communityParticipants = $webgdDao->findCommunityParticipantsById($idCommunity);
$communityEmails = $webgdDao->findCommunityEmailById($idCommunity);

$emaillist = "";
foreach ($communityEmails as $participant => $pt) {
    $emaillist .= $pt->email . ",";
}
?>

<style>
    .iconeBt {
        margin-top: 5px;
    }
</style>


<?php

echo '<div class="lista_home">';

if ($community) {
    $PAGE->set_cacheable(false);

    $url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

    $participantes_btn = "<button type='button' class='participantes_community' onClick='open_participantes();'>...</button>";

    if ($confirm == 1) {
        $communutyUser = new stdClass();
        $communutyUser->community = $idCommunity;
        $communutyUser->userid = $USER->id;

        $msg = get_string('msgErroParticiparComunidade', 'block_webgd_community') . $community->name;
        if ($DB->insert_record(TableResoucer::$TABLE_PAGE_COMMUNITY_USER, $communutyUser, true)) {

            $msg = get_string('msgParticiparComunidade', 'block_webgd_community') . $community->name;
        }

        redirect($url, $msg, 10);
    } elseif ($confirm == 2) {
        $DB->execute("DELETE FROM {$CFG->prefix}" . TableResoucer::$TABLE_PAGE_COMMUNITY_USER . " WHERE community = {$community->id} AND userid = {$USER->id}");

        redirect($url, get_string('msgSairDaComunidade', 'block_webgd_community') . $community->name, 10);
    } else {
        $value = 1;
        $comunidadeImg = $CFG->wwwroot . ImageResources::PARTICIPAR_COMUNIDADE;
        $comunidadeSair = $CFG->wwwroot . ImageResources::SAIR_COMUNIDADE;

        if ($webgdDao->findUserInCommunityById($idCommunity, $USER->id)) {
            $value = 2;

            echo '<div class="btn-group" style="float:right">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ... </button>
            <ul class="dropdown-menu">';
            if ($USER->id == $community->userid) {
                echo '<li><a href="' . $CFG->wwwroot . '/blocks/webgd_community/module.php?community=' . $idCommunity . '" >Editar comunidade</a></li>';
            }
            echo '<li><a href="#" onClick="open_participantes()">Exibir membros</a></li>
            <li><a href="https://mail.google.com/mail/?view=cm&fs=1&to=' . $emaillist . '" target="_wblank">Enviar e-mail para os participantes</a></li>' .
            '<li>' . '<span class="titulo_list">' . html_writer::tag('a', "<img class='botao_comunidade' src='" . $comunidadeSair . "' alt='" . get_string('sair', 'block_webgd_community') . "'>", array('onClick' => 'return confirm("Tem certeza que deseja sair da comunidade?");', 'title' => get_string('sair', 'block_webgd_community'), 'class' => 'participar', 'href' => "view.php?community={$community->id}&confirm=2")) . '</span><div style="clear:both"></div>' . '</li>';
            '<li role="separator" class="divider"></li>';
            echo '<div id="modal_participantes">
                    <div class="modal_header"><span>Participantes</span></div>
                    <div class="modal_content">
                    <ul>';

            foreach ($communityParticipants as $participant => $pt) {
                echo "<span class='nome_criador'>" . html_writer::tag('a', $pt->username, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $pt->userid)) . "</span><br/>";
            }
            echo '</ul>
                    </div>
                  </div>';
            echo '</ul></div>';
            echo '<span class="titulo_list">' . '<a href="' . $url . '" >' . $OUTPUT->heading($community->name, 2, 'titulo_comunidade') . '</a>' . '</span><div style="clear:both"></div>';
        } else {
            if ($community->close_community == 1) {
                echo '<span class="titulo_list">' . '<a href="' . $url . '" >' . $OUTPUT->heading($community->name, 2, 'titulo_comunidade') . '</a>' . $participantes_btn . ' </span><div style="clear:both"></div>';
            } else {
                echo '<div class="btn-group"  style="float:right">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ... </button>
              <ul class="dropdown-menu">';
                if ($USER->id == $community->userid) {
                    echo '<li><a href="' . $CFG->wwwroot . '/blocks/webgd_community/module.php?community=' . $idCommunity . '" >Editar comunidade</a></li>';
                }
                echo '<li><a href="#" onClick="open_participantes();">Exibir membros</a></li>
              <li><a href="#">Enviar e-mail para todos</a></li>
              <li>' . '<span class="titulo_list">' . html_writer::tag('a', "<img class='botao_comunidade' src='" . $comunidadeImg . "' alt='" . get_string('participar', 'block_webgd_community') . "'>", array('title' => get_string('participar', 'block_webgd_community'), 'class' => 'participar', 'href' => "view.php?community={$community->id}&confirm=1")) . '</span><div style="clear:both"></div>' . ' </li>' .
                '<li role="separator" class="divider"></li>';
                echo '<div id="modal_participantes">
                      <div class="modal_header"><span>Participantes</span></div>
                      <div class="modal_content">
                      <ul>';
                foreach ($communityParticipants as $participant => $pt) {
                    echo "<li>" . $pt->username . "</li>";
                }
                echo '</ul>
                      </div>
                    </div>';
                echo '</ul></div>';
                echo '<span class="titulo_list">' . '<a href="' . $url . '" >' . $OUTPUT->heading($community->name, 2, 'titulo_comunidade') . '</a>' . '</span><div style="clear:both"></div>';
            }
        }

        //}

        echo "<div class='menus'>
		<div class='menu1'>

          <div class='iconeBt'>
				<span class='icone'>
					<img src='" . $CFG->wwwroot . ImageResources::ICONE_MENU_06 . "'>
				</span>
				<span class='nome_menu'>
					" . html_writer::tag('a', 'Arquivos', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/view.php?community={$community->id}&option=5")) . "
				</span>
			</div>
			<div class='iconeBt'>
				<span class='icone'>
					<img src='" . $CFG->wwwroot . ImageResources::ICONE_MENU_04 . "'>
				</span>
				<span class='nome_menu'>
					" . html_writer::tag('a', 'Fotos', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/view.php?community={$community->id}&option=6")) . "
				</span>
			</div>



		</div>
		<div class='separador_menu'></div>
		<div class='menu1'>

			<div class='iconeBt'>
				<span class='icone'>
					<img src='" . $CFG->wwwroot . ImageResources::ICONE_MENU_05 . "'>
				</span>
				<span class='nome_menu'>
					" . html_writer::tag('a', 'Vídeo', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/view.php?community={$community->id}&option=7")) . "
				</span>
			</div>
                        <div class='iconeBt'>
				<span class='icone'>
					<img src='" . $CFG->wwwroot . ImageResources::ICONE_MENU_09 . "'>
				</span>
				<span class='nome_menu'>
					" . html_writer::tag('a', 'Gravar vídeo', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/menus/record/record.php?community={$community->id}")) . "
				</span>
			</div>

		</div>
		<div class='separador_menu'></div>
		<div class='menu1'>
			<div class='iconeBt'>
				<span class='icone'>
					<img src='" . $CFG->wwwroot . ImageResources::ICONE_MENU_07 . "'>
				</span>
				<span class='nome_menu'>
					" . html_writer::tag('a', 'Enquetes', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/view.php?community={$community->id}&option=3")) . "
				</span>
			</div>

			<div class='iconeBt'>
				<span class='icone'>
					<img src='" . $CFG->wwwroot . ImageResources::ICONE_MENU_03 . "'>
				</span>
				<span class='nome_menu'>
					" . html_writer::tag('a', 'Glossário', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/view.php?community={$community->id}&option=8")) . "
				</span>
			</div>
                        <div class='iconeBt'>
				<span class='icone'>
					<img src='" . $CFG->wwwroot . ImageResources::ICONE_MENU_02 . "'>
				</span>
				<span class='nome_menu'>
                                            " . html_writer::tag('a', 'Cacoo', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/view.php?community={$community->id}&option=2&suboption=1")) . "
				</span>
			</div>
		</div>
		<div style='clear:both'></div>
	  </div>";


        echo $community->description;
        echo "</br>";
        if ($community->video != '' && $community->video != "0") {

            echo "<div><video controls preload='none'>
          <source src='" . $CFG->wwwroot . '/blocks/webgd_community/showCommunityMovie.php?file=' . $idCommunity . "' type='video/webm'>
          <source src='" . $CFG->wwwroot . '/blocks/webgd_community/showCommunityMovie.php?file=' . $idCommunity . "' type='video/mpeg'>
          <source src='" . $CFG->wwwroot . '/blocks/webgd_community/showCommunityMovie.php?file=' . $idCommunity . "' type='video/mp4'>
          <source src='" . $CFG->wwwroot . '/blocks/webgd_community/showCommunityMovie.php?file=' . $idCommunity . "' type='video/ogg'>
        </video></div>";
        }

        $webgbDao = new WebgdCommunityDao();

        setlocale(LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        switch ($idMenu) {
            case 0:
                homePage($webgbDao, $idCommunity, $cam);
                break;
            case 1:
                filesPage($webgbDao, $idCommunity);
                break;
            case 2:
                if ($idSubOption) {
                    myMentalMapPage($webgbDao, $idCommunity);
                } else {
                    mentalMapsPage($webgbDao, $idCommunity);
                }
                break;
            case 3:
                if ($idSubOption) {
                    myQuestionsPage($webgbDao, $idCommunity);
                } else {
                    questionsPage($webgbDao, $idCommunity);
                }
                break;
            case 4:
                if ($idSubOption) {
                    myIconPage($webgbDao, $idCommunity);
                } else {
                    iconPage($webgbDao, $idCommunity);
                }
                break;
            case 5:
                filesPage($webgbDao, $idCommunity);
                break;
            case 6:
                photoPage($webgbDao, $idCommunity);
                break;
            case 7:
                moviePage($webgbDao, $idCommunity);
                break;
            case 8:
                glossaryPage($webgbDao, $idCommunity, $filter);
                break;
            case 9:
                recordPage($webgbDao, $idCommunity);
                break;
            default:
                homePage($webgbDao, $idCommunity, $cam);
                break;
        }
    }

    echo '</div>';
} else {
    print_error('unspecifycourseid', 'error');
}

echo $OUTPUT->footer();

function homePage($webgbDao, $idCommunity, $cam) {

    global $CFG, $USER, $OUTPUT;

    if ($cam) {
        include_once($CFG->dirroot . '/blocks/webgd_community/lib/camera/index.php');
    }

    $linkCam = $CFG->wwwroot . "/blocks/webgd_community/view.php?community=$idCommunity&cam=1";

    echo '<div id="camera"></div>
    <form id="form" method="post" role="form" enctype="multipart/form-data" class="facebook-share-box">
        <input type="hidden" name="community" value="' . $idCommunity . '">
        <div class="arrow"></div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-file"></i> Atualizar Status
            </div>
            <div class="panel-body">
                <div class="">
                    <textarea name="message" cols="30" rows="10" id="status_message"
                              class="form-control message"
                              style="height: 62px; width: 95%; overflow: hidden;"
                              placeholder="Escreva aqui o seu post"></textarea>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-5">
                        <input type="button" name="submit" value="Enviar"
                              style="margin-left: 50px"
                               class="btn btn-primary" onclick="salvar()">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div id="posts">';


    $ultimo_post = 0;
    if ($communities = $webgbDao->getAllCommunityPost($idCommunity)) {
        $images = Commons::imageById($USER->id);
        foreach ($communities as $community) {
            if ($USER->id == $community->userid) {
                Commons::printListPost($community, 1, $idCommunity);
            } else {
                Commons::printListPost($community, 0, $idCommunity);
            }
            echo '&nbsp;<span style="color:#41627F;">Responder</span><div class="comentarios">';
            Commons::printTimelineComments($community->id);
            echo '<form method="post" enctype="multipart/form-data" id="form-comment-' . $community->id . '" class="form-comment" onsubmit="enviaComentario(' . $community->id . '); return false;">
                    <input type="hidden" name="post_id_comment" value="' . $community->id . '">
                    <div style="line-height:25px;">';
            foreach ($images as $img) {
                echo $OUTPUT->user_picture($img, array('size' => 25, 'alttext' => false, 'link' => false));
            }
            echo '<input style="margin:4px 0 0 5px; width:75%;" type="text" name="comment" class="comment" placeholder="Escreva um comentário...">
                    </div>
                    </form></div>';
            if ($ultimo_post == 0) {
                $ultimo_post = $community->id;
            }
        }
    } else {
        echo get_string('nenhumComentario', 'block_webgd_community');
    }

    echo "<input type='hidden' id='ultimo_post' value='" . $ultimo_post . "'>";
    echo "<input type='hidden' id='comunidade_id' value='" . $idCommunity . "'>";

    echo '</div>';
}

function filesPage($webgdCommunity, $idCommunity) {

    global $USER;

    $listFiles = $webgdCommunity->mediaByCommunity($idCommunity, $USER->id, 'file');

      
    Commons::printListFile($idCommunity, $listFiles);
}

function photoPage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();
    $listFiles = $webgdCommunity->mediaByCommunity($idCommunity, $USER->id, 'photo');
    Commons::printListPhotos($idCommunity, $listFiles);
}

function moviePage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();
    $listFiles = $webgdCommunity->mediaByCommunity($idCommunity, $USER->id, 'movie');
    Commons::printListMovies($idCommunity, $listFiles);
}

function recordPage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();

    $listFiles = $webgdCommunity->moviesByCommunity($idCommunity, $USER->id);

    Commons::printListRecord($idCommunity, $listFiles);
}

function myQuestionsPage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();

    $listQuestions = $webgdCommunity->myQuestionsByCommunity($idCommunity, $USER->id);

    Commons::printListQuestions($listQuestions, $idCommunity, true);
}

function questionsPage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();

    $listQuestions = $webgdCommunity->questionsByCommunity($idCommunity);

    Commons::printListQuestions($listQuestions, $idCommunity);
}

function myIconPage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();

    $list = $webgdCommunity->linksByCommunity($idCommunity, $USER->id, 'icon');

    Commons::printListIcon($list, $idCommunity, true);
}

function glossaryPage($webgbDao, $idCommunity, $filter) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();

    if (!empty($filter)) {
        $list = $webgdCommunity->glossarysByCommunityAndLike($idCommunity, $filter);
        Commons::printListGlossary($list, $idCommunity, true, true);
    } else {
        $list = $webgdCommunity->glossarysByCommunity($idCommunity);
        Commons::printListGlossary($list, $idCommunity, true, false);
    }
}

function iconPage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();

    $list = $webgdCommunity->allLinksByCommunity($idCommunity, 'icon');

    Commons::printListIcon($list, $idCommunity, false);
}

function myMentalMapPage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();

    $listMaps = $webgdCommunity->linksByCommunity($idCommunity, $USER->id, 'map');

    Commons::printListMap($listMaps, $idCommunity, true);
}

function mentalMapsPage($webgbDao, $idCommunity) {
    global $USER;
    $webgdCommunity = new WebgdCommunityDao();

    $listMaps = $webgdCommunity->allLinksByCommunity($idCommunity, 'map');

    Commons::printListMap($listMaps, $idCommunity, false);
}
