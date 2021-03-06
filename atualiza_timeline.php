<?php


require_once(dirname(__FILE__) . '/../../config.php');
global $USER, $CFG;
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot . '/blocks/webgd_community/menus/Commons.php');
$ultimo_post = optional_param('ultimo_post', 0, PARAM_INT);
$id_comunidade = optional_param('id_comunidade', 0, PARAM_INT);
require_login(1);
$retorno = array('ultimo_post' => $ultimo_post, 'mensagem' => '', 'atualizar' => '0');

$novo_ultimo = $ultimo_post;

$webgbDao = new WebgdCommunityDao();



if ($communities = $webgbDao->getAllCommunityPostSince($id_comunidade, $ultimo_post)) {
    foreach ($communities as $community) {
        if ($USER->id == $community->userid) {
            $retorno['mensagem'] .= Commons::printListPost($community, 1, $id_comunidade, 1);
            //$retorno['mensagem'] .= "userid: ".$community->userid." id:".$community->id." ";
        } else {
            $retorno['mensagem'] .= Commons::printListPost($community, 0, $id_comunidade, 1);
            //$retorno['mensagem'] .= "userid: ".$community->userid." id:".$community->id." ";
        }
        if ($novo_ultimo == $ultimo_post) {
            $novo_ultimo = $community->id;
        }
    }
}

if ($novo_ultimo != $ultimo_post) {
    $retorno['atualizar'] = '1';
}
$retorno['ultimo_post'] = $novo_ultimo;
$retorno = json_encode($retorno);
echo $retorno;
?>
