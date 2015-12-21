<?php
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/ImageResources.php');

class block_webgd_community extends block_list {

    function init() {
        $this->title = get_string('pluginname', 'block_webgd_community');
    }

    public function get_content() {
        global $USER, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }
        ?>

        <link href="../blocks/webgd/css/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet" />


        <style>
            .block_webgd_community a{
                color: #40617F !important;
                font-size: 16px !important;
                margin-left: 10px !important;
                font-weight: bold;
            }

            .block_webgd_community li{
                list-style-type: none !important;
            }
            .block_webgd_community ul{
                list-style-type: none !important;
            }

            .block_webgd_community li{
                padding-top: 15px !important;
                padding-bottom: 15px !important;
            }

            .block_webgd_community li{
                border-bottom: 2px solid #CCCBCB !important;
            }
            
            .column.c1{
                padding-left: 0px;
                width: 100%;
                position: relative;
            }

        </style>

        <?php
        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();

        if ($USER->id > 0) {
            $this->content->items[] = $this->gerateLink('/blocks/webgd_community/index.php', get_string('comunidades', 'block_webgd_community'), $CFG->wwwroot . ImageResources::COMUNIDADES, 'Comunidades');
            $this->content->items[] = $this->gerateLink('/blocks/webgd_community/my_community.php', get_string('minhasComunidades', 'block_webgd_community'), $CFG->wwwroot . ImageResources::MINHAS_COMUNIDADES, 'Minhas');
            $this->content->items[] = $this->gerateLink('/blocks/webgd_community/module.php', get_string('cadastrarComunidade', 'block_webgd_community'), $CFG->wwwroot . ImageResources::CRIAR_COMUNIDADES, 'Cadastrar');
        }


        $this->content->footer = '<!--VIDEO-->
		<div id="videodiv" class="dissmissable mobile">

				<video id="videotag" style="display:none" autoplay>
						<source src="./blocks/webgd/videos/equipe.mp4" type=\'video/mp4; codecs="avc1.42E01E"\' />
				</video>
				<canvas width="512" height="576" id="buffer"></canvas>
				<canvas width="512" height="288" id="output" class="move"></canvas>


				<div class="controls">
						<div class="myRow">
								<div class="col-xs-12">
										<input id="playBackSlider" min="0.25" max="1.75" value="1" step="0.25" type="range">
								</div>
						</div>
						<div class="playBar">

												<div class="controlBtn link" id="replay">
														<span class="fa fa-fast-backward"></span>
												</div>

												<div class="controlBtn link" id="playPause">
														<span class="fa fa-pause"></span>
												</div>

												<div class="controlBtn link" id="faster">
														<span class="fa fa-forward"></span>
												</div>


						</div>

						<div class="link dismiss">&times;</div>
				</div>

		</div>
		<div id="imagediv" class="mobile dissmissable">
				<div class="move">
					<img src="#" />
					<div class="link dismiss">&times;</div>
				</div>
		</div>

		<script type="text/javascript" src="' . $CFG->wwwroot . '/blocks/webgd/js/videolibras.js"></script>
		<!--FIM DO VIDEO e imagem LIBRAS-->';

        return $this->content;
    }

    public function get_aria_role() {
        return 'navigation';
    }

    function applicable_formats() {
        return array(
            'all' => true,
            'mod' => false,
            'my' => false,
            'admin' => false,
            'tag' => false
        );
    }

    public function gerateLink($url, $nome, $image, $video) {
        global $CFG;
        $imgLibras = $CFG->wwwroot . '/theme/moobi/pix/icons/mao-libras.png';
        $imgSignwrigth = $CFG->wwwroot . '/theme/moobi/pix/icons/mao-signwrigth.png';

        $redhand = $CFG->wwwroot . "/blocks/webgd/redhand/" . $video . ".png";

        $videoLibras = '#';

        if ($video == 'Comunidades') {
            $videoLibras = $CFG->wwwroot . '/blocks/webgd/videos/comunidade.mp4';
        }
        if ($video == 'Minhas') {
            $videoLibras = $CFG->wwwroot . '/blocks/webgd/videos/minha_comu.mp4';
        }
        if ($video == 'Cadastrar') {
            $videoLibras = $CFG->wwwroot . '/blocks/webgd/videos/cadastro.mp4';
        }

        return "<div class='linha_webgd'>

					<div style='float:left; position:relative;'>
						<img style='width:auto;height:auto;vertical-align: middle;' src='" . $image . "'>
						<span class='titulo_menu_webgd'>
					 		<a title='" . $nome . "' href='" . $CFG->wwwroot . $url . "'>$nome</a>
						</span>
					</div>

					<div class='row' style='float:right; position: relative; margin-left:0;'>
							<div class='col-lg-12'>
									<a class='hand' href='" . $videoLibras . "'><img src='" . $imgLibras . "'></img></a>
							</div>

							<div class='col-lg-12'>
									<a href='#' class='tooltip_redhand' rel='" . $redhand . "'><img src='" . $imgSignwrigth . "'></img></a>
							</div>
					</div>

				</div>";
    }

}
