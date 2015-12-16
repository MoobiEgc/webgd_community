<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');
require_once($CFG->dirroot.'/blocks/webgd_community/commons/TableResouces.php');
require_once($CFG->dirroot.'/blocks/webgd_community/menus/Commons.php');
require_login(1);
global $USER,$DB;

$idCommunity = optional_param('community', 0, PARAM_INT);
$message = optional_param('message', '', PARAM_TEXT);

$webgbDao = new WebgdCommunityDao();
try{
	$transaction = $DB->start_delegated_transaction();

	$post = new stdClass();
	$post->community = $idCommunity;
	$post->userid = $USER->id;
	$post->time = time();
	$post->type = 'text';

	$idPost = $webgbDao->insertRecordInTableCommunityPost($post);

	$postText = new stdClass();
	$postText->post = $idPost;
	$postText->message = $message;

	$webgbDao->insertRecordInTableCommunityText($postText);

	$transaction->allow_commit();

	if($communities =  $webgbDao->getAllCommunityPost($idCommunity)){

		foreach ($communities as $community){
			Commons::printListPost($community);
		}
	}
} catch(Exception $e) {
	$transaction->rollback($e);
}
