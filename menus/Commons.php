<?php


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/ImageResources.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
global $CFG;

class Commons {

    public static function isAdmin(){
      global $USER;
      $admins = get_admins();
      $isadmin = false;
      foreach($admins as $admin) {
          if ($USER->id == $admin->id) {
              $isadmin = true;
              break;
          }
      }
      return $isadmin;
    }

    public static function printListGlossary($list, $idCommunity, $manager, $filter) {
        global $CFG,$USER,$OUTPUT;

        $webgdCommunity = new WebgdCommunityDao();

        echo "<span class='mainBt' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::CAD_TERMO . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/glossary/glossary.php?community=$idCommunity'></span><br/><br/>";

        $url = "{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=8&filter=";
        $filterBar = "<div>";
        foreach (range('A', 'Z') as $char) {
            $filterBar.= "<a href='" . $url . $char . "'>$char</a>";
        }
        $filterBar.="</div>";


        if ($list) {
            echo $filterBar;
            foreach ($list as $object) {
                $votoUser = $webgdCommunity->searchGlossaryUserVotation($object->id,$object->userid);
                $opcao = "";
                if ($USER->id == $object->userid || self::isAdmin()) {
                    $opcao = "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/glossary/delete.php?glossario={$object->id}&community=$idCommunity' ></span>
					<span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/glossary/glossary.php?glossario={$object->id}&community=$idCommunity'></span>";
                }
            echo "<div id='mapas'>
					<div class='conteudo_mapa'>
						<div class='titulo_mapa'>
							<span class='icone_mapa'>";
              $images = self::imageById($object->userid);
              foreach ($images as $img){
                echo $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
              }
              echo "</span>
							<span class='dados_mapa'>
								<span class='nome_mapa'>" . html_writer::tag('a', $object->termo, array('href' => $CFG->wwwroot . "/blocks/webgd_community/menus/glossary/view.php?glossario={$object->id}&community=$idCommunity")) . "</span>
								$opcao
								</br>
                <div class='criado'> criado por <span class='nome_criador'>" . html_writer::tag('a', $object->firstname, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $object->userid)) . " </span></div>
								<span class='data_edicao'>" . strftime('Criado em em %d de %B de %Y às %H %M %S por teste', $object->time) . "</span>
							</span>
						</div>
					 </div>";
           $votoUsuario = 0;
           $votos = ($object->votos/$object->totalvotos)/10;
           if($votoUser){
             $votoUsuario = $votoUser->voto/10;
           }
           echo "<div class='votacao' id='vot_".$object->id."'>";
            if($votos >= 1){
                echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_ON . "' class='estrelaGlossario' rel='10_".$object->id."'>&nbsp;";
            }else{
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_OFF . "' class='estrelaGlossario' rel='10_".$object->id."'>&nbsp;";
            }
            if($votos >= 2){
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_ON . "' class='estrelaGlossario' rel='20_".$object->id."'>&nbsp;";
            }else{
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_OFF . "' class='estrelaGlossario' rel='20_".$object->id."'>&nbsp;";
            }
            if($votos >= 3){
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_ON . "' class='estrelaGlossario' rel='30_".$object->id."'>&nbsp;";
            }else{
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_OFF . "' class='estrelaGlossario' rel='30_".$object->id."'>&nbsp;";
            }
            if($votos >= 4){
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_ON . "' class='estrelaGlossario' rel='40_".$object->id."'>&nbsp;";
            }else{
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_OFF . "' class='estrelaGlossario' rel='40_".$object->id."'>&nbsp;";
            }
            if($votos == 5){
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_ON . "' class='estrelaGlossario' rel='50_".$object->id."'>";
            }else{
              echo "<img src='" . $CFG->wwwroot . ImageResources::STAR_OFF . "' class='estrelaGlossario' rel='50_".$object->id."'>";
            }
            if($votoUsuario != 0){
              echo "<div class='estrela_votada'><span id='span_votacao_".$object->id."''>".$votoUsuario."</span> <img src='" . $CFG->wwwroot . ImageResources::STAR_ON . "'>";
              foreach ($images as $img){
                echo $OUTPUT->user_picture($img, array('size' => 18, 'alttext' => false, 'link' => false));
              }
              echo "</div>";
            }
           echo "</div>
					 <div class='separador_mapa'></div>
				</div>";
            }
        } else {
            if ($filter) {
                echo $filterBar;
                echo '<br>Nenhum termo registrado.';
            } else {
                echo '<br>Nenhum termo registrado.';
            }
        }
    }

    public static function printListIcon($listIcons, $idCommunity, $myGlossary) {
        global $CFG;
				echo "<br/>";
        echo html_writer::tag('a', 'Cadastrar Icone', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/menus/icone/icon.php?community=$idCommunity"));
        echo html_writer::tag('a', 'Meus Icones', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/view.php?community=$idCommunity&option=4&suboption=1"));

        if ($listIcons) {
            foreach ($listIcons as $glossary) {
                $opcao = "";
                if ($myGlossary || self::isAdmin()) {
                    $opcao = "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/icone/delete.php?glossario={$glossary->id}&community=$idCommunity' ></span>
					<span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/icone/icon.php?glossario={$glossary->id}&community=$idCommunity'></span>";
                }

                echo "<div id='mapas'>
					<div class='conteudo_mapa'>
						<div class='titulo_mapa'>
							<span class='icone_mapa'><img src='" . $CFG->wwwroot . ImageResources::ARQUIVO . "'></span>
							<span class='dados_mapa'>
								<span class='nome_mapa'>" . html_writer::tag('a', $glossary->name, array('href' => $glossary->url)) . "</span>
								$opcao
								</br>
								<span class='data_edicao'>" . strftime('Criado em em %d de %B de %Y às %H %M %S por teste', $glossary->time) . "</span>
							</span>
						</div>
					 </div>
					 <div class='separador_mapa'></div>
				</div>";
            }
        } else {
            echo '<br>Nenhum icone registrado';
        }
    }

    public static function printListMap($listMaps, $idCommunity, $myMentalMap) {
        global $CFG,$OUTPUT;

        $Ajuda1 = '#';
      	$Ajuda2 = '#';
      	$Ajuda1 = $CFG->wwwroot . '/blocks/webgd/videos/Cacoo1.mp4';
      	$Ajuda2 = $CFG->wwwroot . '/blocks/webgd/videos/Cacoo2.mp4';

        echo "<a href='http://cacoo.com' target='_blank'><img class='cacooImg' style='width:107px' src='" . $CFG->wwwroot . ImageResources::CACOO."' </img></a>
             <a class='cacooLink' href='" . $Ajuda1 . "' ><img class='cacooImg' style='width:107px' src='" . $CFG->wwwroot . ImageResources::AJUDA1."' </img></a>
             <a class ='cacooLink' href='" . $Ajuda2 . "' ><img class='cacooImg' style='width:107px' src='" . $CFG->wwwroot . ImageResources::AJUDA2."' </img></a><br/>";
        echo '
        <!--VIDEO-->
      	<div id="cacoovideo" class="videodiv dissmissable mobile" onload="myFunction()">

      			<video id="videotag" style="height:600px; width:800px" class="move" autoplay>
      					<source src="./blocks/webgd/videos/equipe.mp4" type=\'video/mp4; codecs="avc1.42E01E"\' />
      			</video>

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
        echo "<span class='mainBt' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::CAD_ATIVIDADE . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/map/map.php?community={$idCommunity}'></span><br/><br/>";

        if ($listMaps) {
            foreach ($listMaps as $map) {
                $opcao = "";
                if ($myMentalMap || self::isAdmin()) {
                    $opcao = "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/map/delete.php?map={$map->id}&community=$idCommunity' ></span>
					<span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/map/map.php?map={$map->id}&community=$idCommunity'></span>";
                }
                echo "<div id='mapas'>
					<div class='conteudo_mapa'>
						<div class='titulo_mapa'>
							<span class='icone_mapa'>";
              $images = self::imageById($map->userid);
              foreach ($images as $img){
                echo $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
              }
              echo "</span><span class='dados_mapa'>
								<span class='nome_mapa'>" . html_writer::tag('a', $map->name, array('href' => $map->url, 'target' => '_blank')) . "</span>
								$opcao
								<div class='criado'> criado por <span class='nome_criador'>" . html_writer::tag('a', $map->firstname, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $map->userid)) . " </span></div>
								<span class='data_edicao'>" . strftime('Criado em em %d de %B de %Y às %H %M %S por teste', $map->time) . "</span>
							</span>
						</div>
					 </div>
					 <div class='separador_mapa'></div>
				</div>";
            }
        } else {
            echo '<br>Nenhuma atividade registrada.';
        }
    }

    public static function printListFile($idCommunity, $listFile) {
        global $CFG,$USER,$OUTPUT;

        echo "<span class='mainBt' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::CAD_ARQUIVO . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/file/file.php?community={$idCommunity}'></span><br/><br/>";

        if ($listFile) {
            foreach ($listFile as $file) {
                echo "<div id='mapas'>
					<div class='conteudo_mapa'>
						<div class='titulo_mapa'>
							<span class='icone_mapa'>";

              $images = self::imageById($file->userid);
              foreach ($images as $img){
                echo $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
              }

            echo "</span><span class='dados_mapa'>
								<span class='nome_mapa'>" . html_writer::tag('a', $file->name, array('href' => $CFG->wwwroot . '/blocks/webgd_community/menus/file/downloadFile.php?file=' . $file->id)) . "</span>";
                if($USER->id == $file->userid || self::isAdmin()){
								echo "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/file/delete.php?file={$file->id}&community=$idCommunity' ></span>
									<span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/file/file.php?file={$file->id}&community=$idCommunity'></span>";
								}

                echo "<div class='criado'> criado por <span class='nome_criador'>" . html_writer::tag('a', $file->firstname, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $file->userid)) . " </span></div>
								<span class='data_edicao'>" . strftime('Última edição em %d de %B de %Y às %H %M %S por teste', $file->timecreated) . "</span>
							</span>
						</div>
					 </div>
					 <div class='separador_mapa'></div>
				</div>";
            }
        } else {
            echo '<br>Nenhum Arquivo Registrado';
        }
    }

    public static function printListPhotos($idCommunity, $listFile) {
        global $CFG,$USER,$OUTPUT;

        echo "<span class='mainBt' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::CAD_FOTO . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/photo/photo.php?community=$idCommunity'></span><br/><br/>";

        if ($listFile) {
            foreach ($listFile as $file) {
                echo "<div id='mapas'>
					<div class='conteudo_mapa'>
						<div class='titulo_mapa'>
							<span class='icone_mapa'>";

              $images = self::imageById($file->userid);
              foreach ($images as $img){
                echo $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
              }

              echo "</span><span class='dados_mapa'>
								<span class='nome_mapa'>" . html_writer::tag('a', $file->name, array('href' => '#')) . "</span>";

                if($USER->id == $file->userid || self::isAdmin()){
								echo "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/photo/delete.php?file={$file->id}&community=$idCommunity' ></span>
									<span class='botao_edicao' style='cursor:pointer'><img  src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/photo/photo.php?file={$file->id}&community=$idCommunity'></span>";
                }

							echo "<div class='criado'> criado por <span class='nome_criador'>" . html_writer::tag('a', $file->firstname, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $file->userid)) . " </span></div>
                <div><img src='".$CFG->wwwroot."/blocks/webgd_community/menus/photo/showPhoto.php?file=".$file->id."></div>
								<span class='data_edicao'>" . strftime('Última edição em %d de %B de %Y às %H %M %S por teste', $file->timecreated) . "</span>
							</span>
						</div>
					 </div>
					 <div class='separador_mapa'></div>
				</div>";
            }
        } else {
            echo '<br>Nenhuma Imagem Registrada';
        }
    }

    public static function printListMovies($idCommunity, $listFile) {
        global $CFG,$USER,$OUTPUT;

        echo "<span class='mainBt' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::CAD_VIDEO . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/movie/movie.php?community={$idCommunity}'></span><br/><br/>";

        if ($listFile) {
            foreach ($listFile as $file) {
                echo "<div id='mapas'>
					<div class='conteudo_mapa'>
						<div class='titulo_mapa'>
							<span class='icone_mapa'>";
              $images = self::imageById($file->userid);
              foreach ($images as $img){
                echo $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
              }
            echo "</span><span class='dados_mapa'>
								<span class='nome_mapa'>" . html_writer::tag('a', $file->name, array('href' => $CFG->wwwroot . '/blocks/webgd_community/menus/file/downloadFile.php?file=' . $file->id)) . "</span>

                  <span class='botao_download' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::DOWNLOAD . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/file/downloadFile.php?file={$file->id}' ></span>";
                  if($USER->id == $file->userid || self::isAdmin()){
                echo	"<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/movie/delete.php?file={$file->id}&community=$idCommunity' ></span>
									<span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/movie/movie.php?file={$file->id}&community=$idCommunity'></span>";
                  }
								echo "<div class='criado'> criado por <span class='nome_criador'>" . html_writer::tag('a', $file->firstname, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $file->userid)) . " </span></div>
                <div><video controls preload='none'>
                  <source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/movie/showMovie.php?file=' . $file->id."' type='video/webm'>
                  <source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/movie/showMovie.php?file=' . $post->post."' type='video/mpeg'>
                  <source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/movie/showMovie.php?file=' . $post->post."' type='video/mp4'>
                  <source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/movie/showMovie.php?file=' . $post->post."' type='video/ogg'>
                </video></div>
								<span class='data_edicao'>" . strftime('Última edição em %d de %B de %Y às %H %M %S por teste', $file->timecreated) . "</span>
							</span>
						</div>
					 </div>
					 <div class='separador_mapa'></div>
				</div>";
            }
        } else {
            echo '<br>Nenhum Video Registrado';
        }
    }

    public static function printListRecord($idCommunity, $listFile) {
        global $CFG;

        echo html_writer::tag('a', 'Gravar Video', array('href' => "{$CFG->wwwroot}/blocks/webgd_community/menus/record/record.php?community=$idCommunity"));

        if ($listFile) {
            foreach ($listFile as $file) {
                echo "<div id='mapas'>
					<div class='conteudo_mapa'>
						<div class='titulo_mapa'>
							<span class='icone_mapa'><img src='" . $CFG->wwwroot . ImageResources::ARQUIVO . "'></span>
							<span class='dados_mapa'>
								<span class='nome_mapa'>" . html_writer::tag('a', $file->name, array('href' => $CFG->wwwroot . '/blocks/webgd_community/menus/file/downloadMovie.php?file=' . $file->id)) . "</span>
									<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/movie/delete.php?file={$file->id}&community=$idCommunity' ></span>
									<span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/movie/movie.php?file={$file->id}&community=$idCommunity'></span>
								<div class='criado'> criado por <span class='nome_criador'>" . html_writer::tag('a', $file->firstname, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $file->userid)) . " </span></div>
								<span class='data_edicao'>" . strftime('Última edição em %d de %B de %Y às %H %M %S por teste', $file->timecreated) . "</span>
							</span>
						</div>
					 </div>
					 <div class='separador_mapa'></div>
				</div>";
            }
        } else {
            echo '<br>Nenhum Video Registrado';
        }
    }

    public static function printListQuestions($listQuestions, $idCommunity, $myQuestion = false) {
        global $CFG, $USER,$OUTPUT;

        echo "<span class='mainBt' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::CAD_ENQUETE . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/question/question.php?community=$idCommunity'></span><br/><br/>";

        if ($listQuestions) {
            foreach ($listQuestions as $question) {
                $now = time();
                if (($question->enabled == 1 && $now >= $question->startdate) || $USER->id == $question->userid) {
                  $opcao = "";
                  if ($USER->id == $question->userid || self::isAdmin()) {
                        $opcao = "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/question/delete.php?question={$question->id}&community=$idCommunity' ></span>";
    					          if($question->startdate > $now){
                          $opcao .= "<span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/question/question.php?question={$question->id}&community=$idCommunity'></span>";
                        }
                    }

                    echo "<div id='mapas'>
    					<div class='conteudo_mapa'>
    						<div class='titulo_mapa'>
    							<span class='icone_mapa'>";
                  $images = self::imageById($question->userid);
                    foreach ($images as $img){
                    echo $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
                  }

                echo "</span><span class='dados_mapa'>
    								<span class='nome_mapa'>" . html_writer::tag('a', $question->name, array('href' => $CFG->wwwroot . "/blocks/webgd_community/menus/question/index.php?question={$question->id}&community=$idCommunity")) . "</span>
    								$opcao
    								<div class='criado'> criado por <span class='nome_criador'>" . html_writer::tag('a', $question->firstname, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $question->userid)) . " </span></div>
    								<span class='data_edicao'>" . strftime('Última edição em %d de %B de %Y às %H:%M por ', $question->time) . $question->firstname . "</span>
    							</span>
    						</div>
    					 </div>
    					 <div class='separador_mapa'></div>
    				</div>";
            }
            }
        } else {
            echo '<br>Nenhuma enquete registrada';
        }
    }

    public static function printListPost($community,$proprietario,$idCommunity,$automatico = 0) {
        //Aqui apenas estará o switch case que selecionará qual o print sera chamado.
        $webgdCommunity = new WebgdCommunityDao();
        $post = $webgdCommunity->postByTypeById($community->id, $community->type);
        switch ($community->type) {
          case 'text':
            return self::printTimelineText($community,$post,$proprietario,$idCommunity,$automatico);
            break;
          case 'file':
            return self::printTimelineFile($community,$post,$proprietario,$idCommunity,$automatico);
            break;
          case 'photo':
            return self::printTimelinePhoto($community,$post,$proprietario,$idCommunity,$automatico);
            break;
          case 'movie':
            return self::printTimelineMovie($community,$post,$proprietario,$idCommunity,$automatico);
            break;
          case 'icon':
            return self::printTimelineIcon($community,$post);
            break;
          case 'map':
            return self::printTimelineMap($community,$post,$proprietario,$idCommunity,$automatico);
            break;
          case 'question':
            return self::printTimelineQuestion($community,$post,$proprietario,$idCommunity,$automatico);
            break;

        }

    }

    public static function printTimelineText($community, $post, $proprietario, $idCommunity, $automatico) {
      global $CFG,$OUTPUT;

      $images = self::imageById($community->userid);

      $resposta = "";

      $resposta .= "<div class='conteudo_post'>
      <div class='titulo_post'>
        <span class='icone_user'>";

        foreach ($images as $img){
          $resposta .= $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
        }

      $resposta .= "</span><span class='nome_post'>";
        if($proprietario || self::isAdmin()){

          $resposta .= "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/text/delete.php?text={$post->post}&community=$idCommunity' ></span>
          <span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/text/text.php?text={$post->post}&community=$idCommunity'></span>";

        }
      $resposta .= "<span class='nome_criador'>" . html_writer::tag('a', $community->username, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $community->userid)) . "</span>
          publicou em
          <div class='data'>" . strftime('%d de %B de %Y às %H %M %S ', $community->time) . "</div>
        </span>
      </div>
      <div class='informacoes_post'>
        <span> {$post->message} </span>
      </div>";
      $webgdCommunity = new WebgdCommunityDao();
       $postLike = $webgdCommunity->searchPostByID($post->post);
       $resposta .= "<div class='div_like'>
                      <img src='" . $CFG->wwwroot . ImageResources::LIKE . "' class='like' rel='1_".$post->post."'>
                      <span id='likes_".$post->post."' class='like_dislike'>".$postLike->total_votos_sim."</span>
                      <img src='" . $CFG->wwwroot . ImageResources::DISLIKE . "' class='like' rel='0_".$post->post."'>
                      <span id='dislikes_".$post->post."' class='like_dislike'>".$postLike->total_votos_nao."</span>
                    </div>";
    echo  "
     </div>
     <div class='separador_lista'></div>";

     if($automatico){
       return $resposta;
     }else{
       echo $resposta;
     }
    }

    public static function printTimelineFile($community, $post, $proprietario, $idCommunity, $automatico) {
      global $CFG,$OUTPUT;
      $images = self::imageById($community->userid);

      $resposta = "";

      $resposta .= " <div class='conteudo_post'>
      <div class='titulo_post'>
        <span class='icone_user'>";

        foreach ($images as $img){
          $resposta .= $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
         }

      $resposta .= "</span><span class='nome_post'>";
        if($proprietario || self::isAdmin()){

          $resposta .= "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/file/delete.php?file={$post->post}&community=$idCommunity' ></span>
          <span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/file/file.php?file={$post->post}&community=$idCommunity'></span>";

        }
      $resposta .= "<span class='nome_criador'>" . html_writer::tag('a', $community->username, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $community->userid)) . "</span>
          publicou um arquivo em
          <div class='data'>" . strftime('%d de %B de %Y às %H %M %S ', $community->time) . "</div>
        </span>
      </div>
      <div class='informacoes_post'>
        <span>".html_writer::tag('a', $post->name, array('href' => $CFG->wwwroot . '/blocks/webgd_community/menus/file/downloadFile.php?file=' . $post->post))." </span>
      </div>";
      $webgdCommunity = new WebgdCommunityDao();
       $postLike = $webgdCommunity->searchPostByID($post->post);
       $resposta .= "<div class='div_like'>
                      <img src='" . $CFG->wwwroot . ImageResources::LIKE . "' class='like' rel='1_".$post->post."'>
                      <span id='likes_".$post->post."' class='like_dislike'>".$postLike->total_votos_sim."</span>
                      <img src='" . $CFG->wwwroot . ImageResources::DISLIKE . "' class='like' rel='0_".$post->post."'>
                      <span id='dislikes_".$post->post."' class='like_dislike'>".$postLike->total_votos_nao."</span>
                    </div>";
    echo  "
     </div>
     <div class='separador_lista'></div>";

     if($automatico){
       return $resposta;
     }else{
       echo $resposta;
     }

    }

    public static function imageById($userid){

      global $DB;

      $userfields = user_picture::fields('u', array('username'));
      $sql = "SELECT $userfields
              FROM {user} u
              WHERE u.id = ?" ;

      $users = $DB->get_records_sql($sql, array($userid), 0, 50);

      return $users;

    }

    public static function printTimelinePhoto($community, $post, $proprietario, $idCommunity, $automatico) {

      global $CFG,$OUTPUT;

      $resposta = "";

      $images = self::imageById($community->userid);

      $resposta .= "  	<div class='conteudo_post'>
      <div class='titulo_post'>
        <span class='icone_user'>";
          foreach ($images as $img){
        $resposta .= $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
      }

        $resposta .= "</span>
        <span class='nome_post'>";
      if($proprietario || self::isAdmin()){

        $resposta .= "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/photo/delete.php?file={$post->post}&community=$idCommunity' ></span>
        <span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/photo/photo.php?file={$post->post}&community=$idCommunity'></span>";

      }
      $resposta .=  "<span class='nome_criador'>" . html_writer::tag('a', $community->username, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $community->userid)) . "</span>
          publicou uma imagem em
          <div class='data'>" . strftime('%d de %B de %Y às %H %M %S ', $community->time) . "</div>
        </span>
      </div>
      <div class='informacoes_post'>
        <span> {$post->name} </span>
        <div><img src='".$CFG->wwwroot."/blocks/webgd_community/menus/photo/showPhoto.php?file=".$post->post."'/></div>
      </div>";
      $webgdCommunity = new WebgdCommunityDao();
       $postLike = $webgdCommunity->searchPostByID($post->post);
       $resposta .= "<div class='div_like'>
                      <img src='" . $CFG->wwwroot . ImageResources::LIKE . "' class='like' rel='1_".$post->post."'>
                      <span id='likes_".$post->post."' class='like_dislike'>".$postLike->total_votos_sim."</span>
                      <img src='" . $CFG->wwwroot . ImageResources::DISLIKE . "' class='like' rel='0_".$post->post."'>
                      <span id='dislikes_".$post->post."' class='like_dislike'>".$postLike->total_votos_nao."</span>
                    </div>";
    echo  "</div>
     <div class='separador_lista'></div>";

     if($automatico){
       return $resposta;
     }else{
       echo $resposta;
     }

    }

    public static function printTimelineMovie($community, $post, $proprietario, $idCommunity, $automatico) {
      global $CFG,$OUTPUT,$USER;
      $images = self::imageById($community->userid);

      $resposta = "";

      $resposta .= "  	<div class='conteudo_post'>
      <div class='titulo_post'>
        <span class='icone_user'>";
        foreach ($images as $img){
        $resposta .= $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
      }

        $resposta .= "</span><span class='nome_post'>";
     if($proprietario || self::isAdmin()){

       $resposta .= "
       <span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/movie/delete.php?file={$post->post}&community=$idCommunity' ></span>
       <span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/movie/movie.php?file={$post->post}&community=$idCommunity'></span>";
     }
     $resposta .= "
          <span class='botao_download' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::DOWNLOAD . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/file/downloadFile.php?file={$post->post}' ></span>
          <span class='nome_criador'>" . html_writer::tag('a', $community->username, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $community->userid)) . "</span>
          publicou um vídeo em
          <div class='data'>" . strftime('%d de %B de %Y às %H %M %S ', $community->time) . "</div>
        </span>
      </div>
      <div class='informacoes_post'>
        <span> {$post->name} </span>
        <div><video controls preload='none'>
          <source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/movie/showMovie.php?file=' . $post->post."' type='video/webm'>
          <source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/movie/showMovie.php?file=' . $post->post."' type='video/mpeg'>
          <source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/movie/showMovie.php?file=' . $post->post."' type='video/mp4'>
          <source src='".$CFG->wwwroot . '/blocks/webgd_community/menus/movie/showMovie.php?file=' . $post->post."' type='video/ogg'>
        </video></div>
      </div>";
    $webgdCommunity = new WebgdCommunityDao();
     $postLike = $webgdCommunity->searchPostByID($post->post);
     $resposta .= "<div class='div_like'>
                    <img src='" . $CFG->wwwroot . ImageResources::LIKE . "' class='like' rel='1_".$post->post."'>
                    <span id='likes_".$post->post."' class='like_dislike'>".$postLike->total_votos_sim."</span>
                    <img src='" . $CFG->wwwroot . ImageResources::DISLIKE . "' class='like' rel='0_".$post->post."'>
                    <span id='dislikes_".$post->post."' class='like_dislike'>".$postLike->total_votos_nao."</span>
                  </div>";

     $resposta .= "</div>
                  <div class='separador_lista'></div>";

     if($automatico){
       return $resposta;
     }else{
       echo $resposta;
     }

    }

    public static function printTimelineIcon($community, $post) {
      global $CFG,$OUTPUT;
      $images = self::imageById($community->userid);

      echo "<div class='conteudo_post'>
      <div class='titulo_post'>
        <span class='icone_user'>";

        foreach ($images as $img){
        echo $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
      }

      echo"</span><span class='nome_post'>
          <span class='nome_criador'>" . html_writer::tag('a', $community->username, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $community->userid)) . "</span>
          publicou um icone em
          <div class='data'>" . strftime('%d de %B de %Y às %H %M %S ', $community->time) . "</div>
        </span>
      </div>
      <div class='informacoes_post'>
        <span> {$post->name} </span>
      </div>
     </div>
     <div class='separador_lista'></div>";
    }

    public static function printTimelineMap($community, $post, $proprietario, $idCommunity, $automatico) {
      global $CFG,$OUTPUT;
      $images = self::imageById($community->userid);

      $resposta = "";

      $resposta .= "<div class='conteudo_post'>
      <div class='titulo_post'>
        <span class='icone_user'>";

        foreach ($images as $img){
        $resposta .= $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
        }

      $resposta .= "</span><span class='nome_post'>";
        if($proprietario || self::isAdmin()){

          $resposta .= "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/map/delete.php?map={$post->post}&community=$idCommunity' ></span>
          <span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/map/map.php?map={$post->post}&community=$idCommunity'></span>";

        }
      $resposta .= "<span class='nome_criador'>" . html_writer::tag('a', $community->username, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $community->userid)) . "</span>
          publicou uma atividade em
          <div class='data'>" . strftime('%d de %B de %Y às %H %M %S ', $community->time) . "</div>
        </span>
      </div>
      <div class='informacoes_post'>
        <span>" . html_writer::tag('a', $post->name, array('href' => $post->url, 'target'=>'_blank')) . " </span>
      </div>";
      $webgdCommunity = new WebgdCommunityDao();
       $postLike = $webgdCommunity->searchPostByID($post->post);
       $resposta .= "<div class='div_like'>
                      <img src='" . $CFG->wwwroot . ImageResources::LIKE . "' class='like' rel='1_".$post->post."'>
                      <span id='likes_".$post->post."' class='like_dislike'>".$postLike->total_votos_sim."</span>
                      <img src='" . $CFG->wwwroot . ImageResources::DISLIKE . "' class='like' rel='0_".$post->post."'>
                      <span id='dislikes_".$post->post."' class='like_dislike'>".$postLike->total_votos_nao."</span>
                    </div>";
    echo  "
     </div>
     <div class='separador_lista'></div>";

     if($automatico){
       return $resposta;
     }else{
       echo $resposta;
     }
    }

    public static function printTimelineQuestion($community, $post, $proprietario, $idCommunity, $automatico) {
      global $CFG,$OUTPUT;
      $images = self::imageById($community->userid);

      $now = time();
      if ($post->enabled == 1 && $now >= $post->startdate) {

      $resposta = "";

        $resposta .= "<div class='conteudo_post'>
        <div class='titulo_post'>
          <span class='icone_user'>";

          foreach ($images as $img){
          $resposta .= $OUTPUT->user_picture($img, array('size' => 30, 'alttext' => false, 'link' => false));
          }

        $resposta .= "</span><span class='nome_post'>";
        if ($proprietario || self::isAdmin()) {

            $resposta .= "<span class='botao_excluir' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/question/delete.php?question={$post->post}&community=$idCommunity' ></span>";
				    if($post->startdate > $now){
              $resposta .= "<span class='botao_edicao' style='cursor:pointer'><img src='" . $CFG->wwwroot . ImageResources::EDITAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/question/question.php?question={$post->post}&community=$idCommunity'></span>";
            }

            }

        $resposta .=  "<span class='nome_criador'>" . html_writer::tag('a', $community->username, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $community->userid)) . "</span>
              publicou uma enquete em
              <div class='data'>" . strftime('%d de %B de %Y às %H %M %S ', $community->time) . "</div>
            </span>
					</div>
          <div class='informacoes_post'>
            <span>" . $post->name . "</span>";
      if($post->attachmentquestion != '' && $post->attachmentquestion != "0"){
        $resposta .= "<div><video controls preload='none'>
              <source src='" . $CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $post->post . "&q=1' type='video/webm'>
              <source src='" . $CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $post->post . "&q=1' type='video/mpeg'>
              <source src='" . $CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $post->post . "&q=1' type='video/mp4'>
              <source src='" . $CFG->wwwroot . '/blocks/webgd_community/menus/question/showMovieQuestion.php?file=' . $post->post . "&q=1' type='video/ogg'>
            </video></div>";
        }
        $resposta .= " <span><img src='" . $CFG->wwwroot . ImageResources::VOTAR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/menus/question/index.php?question={$post->post}&community=$idCommunity'></span>
          </div>";
          $webgdCommunity = new WebgdCommunityDao();
           $postLike = $webgdCommunity->searchPostByID($post->post);
           $resposta .= "<div class='div_like'>
                          <img src='" . $CFG->wwwroot . ImageResources::LIKE . "' class='like' rel='1_".$post->post."'>
                          <span id='likes_".$post->post."' class='like_dislike'>".$postLike->total_votos_sim."</span>
                          <img src='" . $CFG->wwwroot . ImageResources::DISLIKE . "' class='like' rel='0_".$post->post."'>
                          <span id='dislikes_".$post->post."' class='like_dislike'>".$postLike->total_votos_nao."</span>
                        </div>";
        echo  "
				 </div>
				 <div class='separador_lista'></div>";
       }

         if($automatico){
           return $resposta;
         }else{
           echo $resposta;
         }

    }

    public static function printListMyCommunity($community) {
        self::printListHomeCommunity($community, '', 0);
    }

    public static function printListHomeCommunity($community, $participar, $fechada) {
        global $CFG,$USER;
        echo "<div class='conteudo'>
                    <div class='titulo_comunidade'>
                            <span class='icone'><img src='" . $CFG->wwwroot . ImageResources::ICONE_QUADRADRO . "'></span>";
        if($fechada){
          echo "<span class='nome_comunidade'>".$community->name."</span>";
        }else{
          echo "<span class='nome_comunidade'>" . html_writer::tag('a', $community->name, array('href' => 'view.php?community=' . $community->id)) . "</span>";
        }
        if($USER->id == $community->user_id || self::isAdmin()){
          echo "<span class='botao_excluir' style='cursor:pointer; margin-left:30px;'><img src='" . $CFG->wwwroot . ImageResources::EXCLUIR . "' onclick=location.href='" . $CFG->wwwroot . "/blocks/webgd_community/deleteCommunity.php?community=$community->id' ></span>";
        }
        echo "           </div>
                    <div class='informacoes_comunide'>
                            <span> criado por <span class='nome_criador'>" . html_writer::tag('a', $community->user, array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $community->user_id)) . " </span>
                            </span>
                            $participar
                    </div>
             </div>
             <div class='separador_lista'></div>";
    }

    public static function printTimelineComments($idpost, $automatico = 0) {
        global $CFG,$OUTPUT;

        $webgdCommunity = new WebgdCommunityDao();
        $postComment = $webgdCommunity->postCommentById($idpost);
        $resposta = "";
        foreach ($postComment as $comment) {
          $images = self::imageById($comment->userid);
          $resposta .= "<div class='conteudo_post'>
          <div class='titulo_post'>
            <span class='icone_user'>";

            foreach ($images as $img){
            $resposta .= $OUTPUT->user_picture($img, array('size' => 25, 'alttext' => false, 'link' => false));
            }

          $resposta .=  "</span>&nbsp;<span class='nome_criador'>" . html_writer::tag('a', /*$comment->username*/"teste", array('href' => $CFG->wwwroot . '/user/profile.php?id=' . $comment->userid)) . "</span>
                publicou em
                <div class='data'>" . strftime('%d de %B de %Y às %H %M %S ', $comment->time) . "</div>
              </span>
            <div class='informacoes_post'>
              <span>" . $comment->comentario . "</span>
            </div></div></div>";
        }
        if($automatico){
          return $resposta;
        }else{
          echo $resposta;
        }
    }
}
