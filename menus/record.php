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

echo $OUTPUT->heading('<span class="titulo_list">' .
				'<a href="' . $url . '" >' .
				$OUTPUT->heading($community->name, 2, 'titulo_comunidade') .
				'</a><br/></span>');
echo "<div class='subTitle'>Gravar Vídeo</div>";
?>
<script src= "<?php echo $CFG->wwwroot; ?>/blocks/webgd_community/menus/record/js/whammy.js"></script>
<script src= "<?php echo $CFG->wwwroot; ?>/blocks/webgd_community/menus/record/js/recorder.js"></script>
<script src= "<?php echo $CFG->wwwroot; ?>/blocks/webgd_community/menus/record/js/VIRecorder.js"></script>

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
						<video id="recordedvideo" controls></video>
						<p><a id="downloadurl">[ Download Vídeo ]</a></p>
		    </div>
		</section>

	  	<script type="text/javascript">



        var startRecord = document.getElementById("record-me");
        var stopRecord  = document.getElementById("stop-me");
				var ligarCamera = document.getElementById("camera-me");
				var videoPreview  = document.getElementById("recordedvideo");
				var downloadPreview = document.getElementById("downloadurl");
				var virec;

		 startRecord.addEventListener("click" , function(){
		        virec.startCapture(); // this will start recording video and the audio
		 });

		 stopRecord.addEventListener("click" , function(){
		 	/*
		 	stops the recording and after recording is finalized oncaptureFinish call back
		 	will occur
		 	*/
			    virec.stopCapture(oncaptureFinish);
	     });

	    ligarCamera.addEventListener("click" , function(){
				 virec = new VIRecorder.initVIRecorder(
						 {
							 recorvideodsize : 0.4, // recorded video dimentions are 0.4 times smaller than the original
									 webpquality 	: 0.7, // chrome and opera support webp imags, this is about the aulity of a frame
									 framerate 		: 15,  // recording frame rate
									 videotagid 		: "video_inicial",
									 videoWidth 		: "640",
									 videoHeight 	: "480",
						 } ,
						 function(){
							 //success callback. this will fire if browsers supports
						 },
							 function(err){
								 //onerror callback, this will fire if browser does not support
								 console.log(err.code +" , "+err.name);
						 }
					);

         });


	//------------------------------- few functions that demo, how to play with the api --------------------------

	var functioncalltime = 0;

 	function oncaptureFinish(audioblob, videoblob){

						var videobase64 = window.URL.createObjectURL(videoblob);

            videoPreview.src = videobase64;
						downloadPreview.download = 'meu_video.webm';
            downloadPreview.href = videobase64;
	}



		</script>

<?php
//} //else
echo $OUTPUT->footer();
