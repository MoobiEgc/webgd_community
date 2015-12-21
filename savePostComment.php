<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot . '/blocks/webgd_community/menus/Commons.php');
require_login(1);
global $USER, $DB;

$idPost = optional_param('post_id_comment', 0, PARAM_INT);
$message = optional_param('comment', '', PARAM_TEXT);

$webgbDao = new WebgdCommunityDao();
try {
    $transaction = $DB->start_delegated_transaction();

    $postComment = new stdClass();
    $postComment->userid = $USER->id;
    $postComment->postid = $idPost;
    $postComment->comentario = $message;
    $postComment->time = time();

    $idPostComment = $webgbDao->insertRecordInTablePostComment($postComment);

    $transaction->allow_commit();

    echo $idPostComment;
} catch (Exception $e) {
    $transaction->rollback($e);
}
