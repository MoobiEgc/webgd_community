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
require_once($CFG->dirroot . '/blocks/webgd_community/form/CadastrarArquivoForm.php');
require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/ImageResources.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/CssResources.php');

require_login(1);

global $USER, $CFG;

$PAGE->requires->css(CssResources::HOME_COMMUNITY);

$PAGE->set_url('/course/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

$idCommunity = optional_param('community', 0, PARAM_INTEGER);
$idFile = optional_param('file', 0, PARAM_INTEGER);

echo $OUTPUT->header('themeselector');

$webgdCommunityDao = new WebgdCommunityDao();
$community = $webgdCommunityDao->findCommunityById($idCommunity);
$url = $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $idCommunity;

if ($idFile) {
    $webgdCommunityDao = new WebgdCommunityDao();
    if (!$webgdCommunityDao->searchFileMovieById($idCommunity, $idFile)) {
        redirect("{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=7", 'Video não encontrado', 10);
        echo $OUTPUT->footer();
        die;
    } else {
        echo $OUTPUT->heading('<span class="titulo_list">' .
                '<a href="' . $url . '" >' .
                $OUTPUT->heading($community->name . ' - Editar Vídeo', 2, 'titulo_comunidade') .
                '</a>');
    }
} else {
    echo $OUTPUT->heading('<span class="titulo_list">' .
            '<a href="' . $url . '" >' .
            $OUTPUT->heading($community->name, 2, 'titulo_comunidade') .
            '</a><br/></span>');
    echo "<div class='subTitle'>Gravar Vídeo</div>";
}
?>
<script src= "<?php echo $CFG->wwwroot; ?>/blocks/webgd_community/menus/record/js/whammy.min.js"></script>
<br/>
<section style="width:100%;">
    <div style="float:left; width:45%;">
        <?php
        echo "<img src='" . $CFG->wwwroot . ImageResources::CAMERA . "' id='camera-me' style='cursor:pointer; width:90%;'>";
        ?>
        <h4>Ao vivo</h4>
        <video autoplay muted style="width:95%; height:auto;" id="video_inicial"></video>
    </div>
    <div id="video-preview" style="width:45%; float:right;">
        <?php
        echo "<img src='" . $CFG->wwwroot . ImageResources::GRAVAR . "' id='record-me' style='cursor:pointer; width:45%;'>&nbsp;
              <img src='" . $CFG->wwwroot . ImageResources::PARAR . "' id='stop-me' style='cursor:pointer; width:45%;'>&nbsp;";
        ?>
        <span id="elasped-time"></span>
        <h4>Gravado em .webm</h4>
    </div>
</section>



<script>
    (function (exports) {

        exports.URL = exports.URL || exports.webkitURL;

        exports.requestAnimationFrame = exports.requestAnimationFrame ||
                exports.webkitRequestAnimationFrame || exports.mozRequestAnimationFrame ||
                exports.msRequestAnimationFrame || exports.oRequestAnimationFrame;

        exports.cancelAnimationFrame = exports.cancelAnimationFrame ||
                exports.webkitCancelAnimationFrame || exports.mozCancelAnimationFrame ||
                exports.msCancelAnimationFrame || exports.oCancelAnimationFrame;

        navigator.getUserMedia = navigator.getUserMedia ||
                navigator.webkitGetUserMedia || navigator.mozGetUserMedia ||
                navigator.msGetUserMedia;

        var ORIGINAL_DOC_TITLE = document.title;
        var video = document.querySelector('#video_inicial');
        var canvas = document.createElement('canvas'); // offscreen canvas.
        var rafId = null;
        var startTime = null;
        var endTime = null;
        var frames = [];

        /*function $(selector) {
            return document.querySelector(selector) || null;
        }*/

        function toggleActivateRecordButton() {

            document.querySelector('#record-me').src = "../../lib/icones/menus/gravar.png";
        }

        function turnOnCamera(e) {
            e.target.disabled = true;
            document.querySelector('#record-me').src = "../../lib/icones/menus/gravar.png";
            document.querySelector('#camera-me').src = "../../lib/icones/menus/ligar_camera_inativo.png";
            document.querySelector('#stop-me').src = "../../lib/icones/menus/parar_desabilitado.png";

            video.controls = false;

            var finishVideoSetup_ = function () {
                setTimeout(function () {
                    video.width = 320;//video.clientWidth;
                    video.height = 240;// video.clientHeight;
                    canvas.width = video.width;
                    canvas.height = video.height;
                }, 1000);
            };

            navigator.getUserMedia({video: true, audio: true}, function (stream) {
                video.src = window.URL.createObjectURL(stream);
                finishVideoSetup_();
            }, function (e) {
                alert("Não é possível utilizar a câmera ou o seu navegador não possui suporte para este recurso.");
                finishVideoSetup_();
            });
        }
        ;

        function record() {
            var elapsedTime = document.querySelector('#elasped-time');
            var ctx = canvas.getContext('2d');
            var CANVAS_HEIGHT = canvas.height;
            var CANVAS_WIDTH = canvas.width;

            frames = []; // clear existing frames;
            startTime = Date.now();

            toggleActivateRecordButton();
            document.querySelector('#stop-me').src = "../../lib/icones/menus/parar.png";
            document.querySelector('#record-me').src = "../../lib/icones/menus/gravar_desabilitado.png";

            function drawVideoFrame_(time) {
                rafId = requestAnimationFrame(drawVideoFrame_);

                ctx.drawImage(video, 0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

                document.title = 'Gravando...' + Math.round((Date.now() - startTime) / 1000) + 's';

                var url = canvas.toDataURL('image/webp', 1);
                
                frames.push(url);

            }
            ;

            rafId = requestAnimationFrame(drawVideoFrame_);
        }
        ;

        function stop() {
            cancelAnimationFrame(rafId);
            endTime = Date.now();
            document.querySelector('#stop-me').src = "../../lib/icones/menus/parar_desabilitado.png";
            document.querySelector('#record-me').src = "../../lib/icones/menus/gravar.png";
            document.title = ORIGINAL_DOC_TITLE;

            toggleActivateRecordButton();

            console.log('frames captured: ' + frames.length + ' => ' +
                    ((endTime - startTime) / 1000) + 's video');

            embedVideoPreview();
        }
        ;

        function embedVideoPreview(opt_url) {
            var url = opt_url || null;
            var video = document.querySelector('#video-preview video') || null;
            var downloadLink = document.querySelector('#video-preview a[download]') || null;

            if (!video) {
                video = document.createElement('video');
                video.autoplay = true;
                video.controls = true;
                video.loop = true;
                video.style.width = '95%';
                video.style.height = canvas.height + 'px';
                document.querySelector('#video-preview').appendChild(video);

                downloadLink = document.createElement('a');
                downloadLink.download = 'meu_video.webm';
                downloadLink.textContent = '[ Download Vídeo ]';
                downloadLink.title = 'Download your .webm video';
                var p = document.createElement('p');
                p.appendChild(downloadLink);

                document.querySelector('#video-preview').appendChild(p);

            } else {
                window.URL.revokeObjectURL(video.src);
            }

            if (!url) {
                //Apenas aqui estou pegando o array de frames de dentro do canvas e compactando via biblioteca Whammy para um vídeo
                var webmBlob = Whammy.fromImageArray(frames, 1000 / 60);
                //Pego o vídeo convertido pela biblioteca e crio uma URL para ele
                url = window.URL.createObjectURL(webmBlob);
            }

            video.src = url;
            downloadLink.href = url;
        }

        function initEvents() {
            document.querySelector('#camera-me').addEventListener('click', turnOnCamera);
            document.querySelector('#record-me').addEventListener('click', record);
            document.querySelector('#stop-me').addEventListener('click', stop);
        }

        initEvents();

        exports.$ = $;

    })(window);

</script>

<?php
//} //else
echo $OUTPUT->footer();
