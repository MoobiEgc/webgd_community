<?php

  require_once(dirname(__FILE__) . '/../../config.php');
  require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
  require_once($CFG->dirroot.'/blocks/webgd_community/commons/TableResouces.php');

  global $USER, $DB, $CFG;
  $idPost= $_POST['idPost'];
  $voto = $_POST['votacao'];

  if($voto == 0){
    $voto = -1;
  }

  $webgdCommunityDao = new WebgdCommunityDao();

  $votoAnterior = 0;

  if($likedislike = $webgdCommunityDao->searchLikeDislikeUserVotation($idPost,$USER->id)){
    $votoAnterior = $likedislike->voto;
    $DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_LIKEDISLIKE, array('id'=>$likedislike->id));
  }

  $likedislike_user_votation = new stdClass();
  $likedislike_user_votation->userid = $USER->id;
  $likedislike_user_votation->postid = $idPost;
  $likedislike_user_votation->voto = $voto;

  $DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_LIKEDISLIKE, $likedislike_user_votation);

  $post = $webgdCommunityDao->searchPostByID($idPost);
  if($votoAnterior != 0){
    if($votoAnterior > 0){
      $post->total_votos_sim = $post->total_votos_sim-1;
    }else{
      $post->total_votos_nao = $post->total_votos_nao-1;
    }
  }

  if($voto > 0){
    $post->total_votos_sim = $post->total_votos_sim+1;
  }else{
    $post->total_votos_nao = $post->total_votos_nao+1;
  }

  $DB->update_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST, $post);

  $nivel = $post->total_votos_sim."_".$post->total_votos_nao;

  echo $nivel;

?>
