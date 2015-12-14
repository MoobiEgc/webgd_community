<?php
require_once($CFG->dirroot.'/blocks/webgd_community/commons/TableResouces.php');

class WebgdCommunityDao{
	private $DB;
	private $CFG;

	function __construct() {
		global $DB, $CFG;
		$this->DB = $DB;
		$this->CFG = $CFG;
	}

	public function postByTypeById($idPost, $type){
		switch ($type) {
			case 'text':
			  return self::searchTextById($idPost);
				break;
			case 'file':
				return self::searchFileById($idPost);
				break;
			case 'photo':
				return self::searchPhotoById($idPost);
				break;
			case 'movie':
				return self::searchMovieById($idPost);
				break;
			case 'icon':
				return self::searchIconeByCommunityById($idPost);
				break;
			case 'map':
				return self::searchMentalMapByCommunityById($idPost);
				break;
			case 'question':
				return self::searchQuestionByCommunityById($idPost);
				break;
		}
	}

	public function mediaByCommunity($idCommunity, $userid, $type){
		$sql = "select
			pt.id, md.name, u.firstname, pt.time, u.id as userid
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA." md
		inner join
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_POST." pt on
			md.post = pt.id
		inner join
		{$this->CFG->prefix}user u on
			u.id = pt.userid
		where
		   	 pt.community = $idCommunity  and pt.type = '$type'
		order by
			pt.time desc";

		return $this->DB->get_records_sql($sql);

	}

	public function photosByCommunity($idCommunity, $userid){
		$sql = "select
			wcf.id, wcf.name, u.firstname, wcf.timecreated, u.id as userid
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_PHOTO_COMMUNITY." wcf
		inner join
		{$this->CFG->prefix}user u on
			u.id = wcf.userid
		where
		   	 wcf.community = $idCommunity  and u.id = $userid
		order by
			wcf.timecreated desc";

		return $this->DB->get_records_sql($sql);

	}

	public function moviesByCommunity($idCommunity, $userid){
		$sql = "select
			wcf.id, wcf.name, u.firstname, wcf.timecreated, u.id as userid
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_MOVIE_COMMUNITY." wcf
		inner join
		{$this->CFG->prefix}user u on
			u.id = wcf.userid
		where
		   	 wcf.community = $idCommunity  and u.id = $userid
		order by
			wcf.timecreated desc";

		return $this->DB->get_records_sql($sql);

	}

	public function questionsByCommunity($idCommunity){
                    $sql = "select
			pt.id, qt.name, qt.startdate, qt.enddate, qt.enabled, u.firstname, pt.time, u.id as userid
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_QUESTION." qt
		inner join
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_POST." pt on
			qt.post = pt.id
		inner join
		{$this->CFG->prefix}user u on
			u.id = pt.userid
		where
				pt.community = $idCommunity
		order by
			pt.time desc";

		return $this->DB->get_records_sql($sql);
	}

	public function myQuestionsByCommunity($idCommunity,$userid){
		$sql = "select
			pt.id, qt.name, u.firstname, pt.time, u.id as userid
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_QUESTION." qt
		inner join
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_POST." pt on
			qt.post = pt.id
		inner join
		{$this->CFG->prefix}user u on
			u.id = pt.userid
		where
		   	 pt.community = $idCommunity  and u.id = $userid
		order by
			pt.time desc";

		return $this->DB->get_records_sql($sql);
	}

	public function mentalMapsByCommunity($idCommunity){
		$sql = " SELECT
			wcq.id,
			wcq.name as name,
			wcq.time,
			u.firstname,
			wcq.userid,
			wcq.url
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_MENTAL_MAP."  wcq
		inner join
		{$this->CFG->prefix}user u on
			wcq.userid = u.id
		where
			wcq.community = $idCommunity
		order by
			wcq.time desc";
		return $this->DB->get_records_sql($sql);
	}

	public function iconsByCommunity($idCommunity){
		return $this->DB->get_records(TableResoucer::$TABLE_PAGE_COMMUNITY_ICONE, array('community' => $idCommunity));
	}

	public function allLinksByCommunity($idCommunity, $type){
		$sql = "select
			pt.id, lk.name, lk.url, pt.time, u.firstname, u.id as userid
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_LINKS." lk
		inner join
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_POST." pt on
			lk.post = pt.id
		inner join
		{$this->CFG->prefix}user u on
			u.id = pt.userid
		where
		   	 pt.community = $idCommunity and pt.type = '$type'
		order by
			pt.time desc";

		return $this->DB->get_records_sql($sql);
	}

	public function linksByCommunity($idCommunity, $userid, $type){
		$sql = "select
			pt.id, lk.name, u.firstname, pt.time, u.id as userid
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_LINKS." lk
		inner join
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_POST." pt on
			lk.post = pt.id
		inner join
		{$this->CFG->prefix}user u on
			u.id = pt.userid
		where
		   	 pt.community = $idCommunity  and u.id = $userid and pt.type = '$type'
		order by
			pt.time desc";

		return $this->DB->get_records_sql($sql);
	}

	public function glossarysByCommunity($idCommunity){

		return self::glossarysByCommunityAndLike($idCommunity, 0,1);
	}

	public function glossarysByCommunityAndLike($idCommunity, $like,$case){
		$sql = " SELECT gls.id,
			gls.termo,
			gls.conceito,
			gls.userid,
			u.firstname,
			gls.time,
			gls.community,
			gls.total_votos,
			gls.votos
		from
			{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_GLOSSARY." gls
		inner join
			{$this->CFG->prefix}user u on
			gls.userid = u.id";
			if($case == 0){
		$sql .= " where
			gls.termo COLLATE UTF8_GENERAL_CI LIKE '".$like."%' AND gls.community =". $idCommunity;
		}
		else {
			$sql .= " where gls.community =". $idCommunity;
		}
		return $this->DB->get_records_sql($sql);
	}

	public function myIcons($idCommunity,$userid){
		return $this->DB->get_records(TableResoucer::$TABLE_PAGE_COMMUNITY_ICONE, array('community' => $idCommunity, 'userid'=> $userid));
	}


	public function myMentalMapsByCommunity($idCommunity,$userid){
		$sql = " SELECT
			wcq.id,
			wcq.name as name,
			wcq.time,
			u.firstname,
			wcq.userid,
			wcq.url
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_MENTAL_MAP."  wcq
		inner join
		{$this->CFG->prefix}user u on
			wcq.userid = u.id
		where
			wcq.community = $idCommunity and wcq.userid = $userid
		order by
			wcq.time desc";

		return $this->DB->get_records_sql($sql);
	}

	public function postCommentById($idPost){
		return $this->DB->get_records(TableResoucer::$TABLE_PAGE_COMMUNITY_POST_COMMENT, array('postid'=> $idPost));
	}

	public function searchPostByID($idPost){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('id'=> $idPost));
	}

	public function searchTextById($idFile){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_TEXT, array('post'=> $idFile));
	}

	public function searchFileById($idFile){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA, array('post'=> $idFile));
	}

	public function searchPhotoById($idPhoto){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA, array('post'=> $idPhoto));
	}

	public function searchMovieById($idMovie){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA, array('post'=> $idMovie));
	}

	public function searchPhotoCommunityById($idCommunity, $idPhoto){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id'=> $idPhoto));
	}

	public function searchMovieCommunityById($idCommunity, $idMovie){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id'=> $idMovie));
	}

	public function searchFileCommunityById($idCommunity, $idFile){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id'=> $idFile));
	}

	public function searchAskQuestionByCommunityById($idQuestion){
		return $this->DB->get_records(TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION, array('question' => $idQuestion), "name_question");
	}

	public function deleteAskedQuestionByUserById($idQuestion, $userid){
		$sql = " DELETE aqu
		from {$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION_USER." aqu
		inner join
			{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION." aq on
			aqu.answer_question = aq.id
		where
			aq.question = $idQuestion and aqu.userid = $userid";

		return $this->DB->execute($sql);
	}

	public function getTotalRespondidasEnquete($idQuestion){
		$sql = " SELECT aqu.id
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION_USER." aqu
		inner join
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION." aq on
			aqu.answer_question = aq.id
		where
			aq.question = $idQuestion";

		return sizeof($this->DB->get_records_sql($sql));
	}

	public function getTotalRespondidasEnqueteByPergunta($idPergunta){
		$sql = " SELECT aqu.id
		from
		{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION_USER." aqu
		where
			aqu.answer_question = $idPergunta";

		return sizeof($this->DB->get_records_sql($sql));
	}

	public function searchQuestionByCommunityById($idQuestion){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_QUESTION, array('post'=> $idQuestion));
	}

	public function searchAnswerById($idQuestion){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION, array('id' => $idQuestion));
	}

	public function deleteAskQuestionByCommunity($idQuestion){
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION, array('question' => $idQuestion));
	}

	public function deleteTextById($idText, $idUser, $idPost){
		$this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_TEXT, array('id' => $idText));
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
	}

	public function deleteQuestionByCommunityById($idQuestion, $idUser, $idPost){
		$this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_QUESTION, array('id' => $idQuestion));
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
	}

	public function deleteMentalMapByCommunityByIdByuser($idLinks, $idUser, $idPost){
		$this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_LINKS, array('id' => $idLinks));
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
	}

	public function deleteIconsByCommunityByIdByuser($idLinks, $idUser, $idPost){
		$this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_LINKS, array('id' => $idLinks));
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
	}

	public function deleteGlossaryById($idGlossario){
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_GLOSSARY, array('id' => $idGlossario));
	}

	public function deleteCommunityById($idCommunity){
		$this->DB->execute('SET FOREIGN_KEY_CHECKS=0', null);
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY, array('id' => $idCommunity));
	}

	public function deletePhotoByCommunityByIdByuser($idPhoto, $idUser, $idPost){
		$this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA, array('id' => $idPhoto));
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
	}

	public function deleteFileByIdUser($idFile, $idUser, $idPost){
		$this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA, array('id' => $idFile));
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
	}

	public function deleteMovieByIdUser($idMovie, $idUser, $idPost){
		$this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA, array('id' => $idMovie));
		return $this->DB->delete_records(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
	}

	public function getListNameUser($notUser){
		return $this->DB->get_records_sql("SELECT id,firstname FROM {$this->CFG->prefix}user WHERE id != '$notUser'");
	}

	public function searchMentalMapByCommunityById($idMap){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_LINKS, array('post'=> $idMap));
	}

	public function searchIconeByCommunityById($idGlossary){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_LINKS, array('post'=> $idGlossary));
	}

	public function searchGlossaryByCommunityById($idCommunity, $idGlossary){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_GLOSSARY, array('community' => $idCommunity, 'id'=> $idGlossary));
	}

	public function searchGlossaryById($idGlossary){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_GLOSSARY, array('id'=> $idGlossary));
	}

	public function searchGlossaryUserVotation($idGlossary,$idUser){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_GLOSSARRY_VOTACAO, array('glossarryid'=> $idGlossary, 'userid' => $idUser));
	}

	public function searchLikeDislikeUserVotation($idPost,$idUser){                
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_LIKEDISLIKE, array('postid'=> $idPost, 'userid' => $idUser));
	}

	public function searchMentalMapByCommunityByIdByUser($idCommunity, $idMap, $idUser){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id'=> $idMap, 'userid' => $idUser));
	}

	public function searchIconeByCommunityByIdByUser($idCommunity, $idIcone, $idUser){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id'=> $idIcone, 'userid' => $idUser));
	}

	public function searchGlossaryByCommunityByIdByUser($idCommunity, $idGlo, $idUser){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_ICONE, array('community' => $idCommunity, 'id'=> $idGlo, 'userid' => $idUser));
	}
        public function findPageById($id){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGINA, array('id'=>$id));
	}

	public function findCommunityById($id){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY, array('id'=>$id));
	}
        

	public function findUserInCommunityById($idCommunity, $userid){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY_USER, array('community' => $idCommunity, 'userid'=> $userid));
	}

	public function findUserCreatorCommunityById($community, $userid, $creator = 1){
		return $this->DB->get_record(TableResoucer::$TABLE_PAGE_COMMUNITY, array('id' => $community, 'userid' => $userid));
	}
        

	public function deleteRecordByTablePageOrder(){
		$this->deleteRecordByTable(TableResoucer::$TABLE_PAGE_ORDER);
	}

	public function insertRecordInTablePageOrder($paginaOrder, $returnId = true, $bulk = false){
		return $this->DB->insert_record(TableResoucer::$TABLE_PAGE_ORDER, $paginaOrder, $returnId, $bulk);
	}

	public function insertRecordInTableCommunityPost($post, $returnId = true, $bulk = false){
		return $this->DB->insert_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST, $post, $returnId, $bulk);
	}

	public function getRecordsTableCommunityPost($idCommunity){
		$sql = "SELECT
			bw.message,bw.time, u.id as userid, u.firstname as username
		FROM
			{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_POST." bw
		INNER JOIN
  			{$this->CFG->prefix}user u on
			bw.userid = u.id
   		WHERE
   		   bw.community = $idCommunity
   		ORDER BY
   		   bw.time desc ";
   		return $this->DB->get_records_sql($sql);
	}


	public function getRecordsTableFilesCommunity($idCommunity){
		$sql = "SELECT
			bwc.*, u.id as userid, u.firstname as username
		FROM
			{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_FILE." bwc
		INNER JOIN
  			{$this->CFG->prefix}user u on
			bwc.userid = u.id
   		ORDER BY
   		   bwc.timecreated desc ";
   		return $this->DB->get_records_sql($sql);
	}

	public function deleteRecordByTable($table){
		return $this->DB->execute("DELETE FROM {$this->CFG->prefix}$table");
	}

	public function findChildrenByHabilityAndVisible($idParent, $visivel = 1, $habilitado = 1){
		$sql = "select
					p.id, p.nome, po.parent
				from
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGINA." p
				LEFT join
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGINA_order." po on
					p.id = po.page
				where
				   po.parent = $idParent and p.habilitado = $habilitado and p.visivel = $visivel
				order by
 					 po.id, po.parent";
		return $this->DB->get_records_sql($sql);

	}
	public function findChildren($idParent){
		$sql = "select
					p.id, p.nome, po.parent
				from
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGINA." p
				LEFT join
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGINA_order." po on
					p.id = po.page
				where
				   po.parent = $idParent
				order by
				  po.parent";
		return $this->DB->get_records_sql($sql);
	}

	public function getListFatherByHabilityAndVisible($visivel = 1, $habilitado = 1){
		$sql = "select
					p.id, p.nome, po.parent
				from
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGINA." p
				LEFT join
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGINA_order." po on
					p.id = po.page
				where
   					(po.parent = 0 or po.parent is null) and p.habilitado = $habilitado and p.visivel = $visivel
				order by
 					 po.id, po.parent";
		return $this->DB->get_records_sql($sql);
	}

	public function getListFather(){
		$sql = "select
					p.id, p.nome, po.parent
				from
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGINA." p
				LEFT join
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGINA_order." po on
					p.id = po.page
				where
   					po.parent = 0 or po.parent is null
				order by
 					 po.id, po.parent";
		return $this->DB->get_records_sql($sql);
	}


	public function getListCommunity(){
		$sql = "select
					wc.id,wc.name as name, u.firstname as user, u.id as user_id
				from
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY." wc
				inner join
					{$this->CFG->prefix}user u on
					wc.userid = u.id";
		return $this->DB->get_records_sql($sql);
	}

	public function getListMyCommunityByUser($idUser){
		$sql = "select
					wc.id,wc.name as name, u.firstname as user, u.id as user_id
				from
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_USER." wcu
				inner join
					{$this->CFG->prefix}user u on
					wcu.userid = u.id
				inner join
					{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY." wc on
					wcu.community = wc.id
				where
					wcu.userid = $idUser";
		return $this->DB->get_records_sql($sql);
	}
        
    

    public function findCommunityEmailById($id) {
      //Função que gera os records para TIMELINE
      $sql = "SELECT
        u.email as email
      FROM
        {$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_USER." cm
      INNER JOIN
          {$this->CFG->prefix}user u on
          cm.userid = u.id
        WHERE
            cm.community = $id
        ORDER BY
            cm.id asc ";
          return $this->DB->get_records_sql($sql);
    }

    public function findCommunityParticipantsById($id) {
      //Função que gera os records para TIMELINE
      $sql = "SELECT
        cm.id, u.id as userid, u.firstname as username
      FROM
        {$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_USER." cm
      INNER JOIN
          {$this->CFG->prefix}user u on
          cm.userid = u.id
        WHERE
            cm.community = $id
        ORDER BY
            cm.id asc ";
          return $this->DB->get_records_sql($sql);
    }

    public function participanteInCommunity($idParticipante, $idCommunity) {
      $sql = "SELECT
        cm.id, u.id as userid, u.firstname as username
      FROM
        {$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_USER." cm
      INNER JOIN
          {$this->CFG->prefix}user u on
          cm.userid = u.id
        WHERE
            cm.community = $idCommunity and cm.userid = $idParticipante
        ORDER BY
            cm.id asc ";
      return $this->DB->get_records_sql($sql);
    }

    
    public function insertRecordInTableCommunityText($postText, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResoucer::$TABLE_PAGE_COMMUNITY_TEXT, $postText, $returnId, $bulk);
    }

    public function insertRecordInTableCommunityMedia($postMedia, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResoucer::$TABLE_PAGE_COMMUNITY_MEDIA, $postMedia, $returnId, $bulk);
    }

    public function getAllCommunityPost($idCommunity) {
        //Função que gera os records para TIMELINE
        $sql = "SELECT
			pt.id, pt.time, pt.type, u.id as userid, u.firstname as username
		FROM
			{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_POST." pt
		INNER JOIN
				{$this->CFG->prefix}user u on
				pt.userid = u.id
			WHERE
					pt.community = ".$idCommunity."
			ORDER BY
					pt.time desc ";
        return $this->DB->get_records_sql($sql);
    }

    public function getAllCommunityPostSince($idCommunity,$ultimo_post) {
        
        
    //Função que gera os records para TIMELINE a partir de um post x
    $sql = "SELECT
			pt.id, pt.time, pt.type, u.id as userid, u.firstname as username
		FROM
			{$this->CFG->prefix}".TableResoucer::$TABLE_PAGE_COMMUNITY_POST." pt
		INNER JOIN
				{$this->CFG->prefix}user u on
				pt.userid = u.id
			WHERE
					pt.community = ".$idCommunity." and pt.id > ".$ultimo_post."
			ORDER BY
					pt.time desc ";
        return $this->DB->get_records_sql($sql);
    }

    

    public function insertRecordInTablePostComment($postComment, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST_COMMENT, $postComment, $returnId, $bulk);
    }
}
