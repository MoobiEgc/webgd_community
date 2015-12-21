<?php

require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');

class WebgdCommunityDao {

    private $DB;
    private $CFG;

    function __construct() {
        global $DB, $CFG;
        $this->DB = $DB;
        $this->CFG = $CFG;
    }

    public function postByTypeById($idPost, $type) {
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

    public function mediaByCommunity($idCommunity, $userid, $type) {
        $sql = "SELECT pt.id, md.name, u.firstname, pt.time, u.id AS userid
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA . "} md
          JOIN {" . TableResouces::$TABLE_PAGE_COMMUNITY_POST . "} pt ON md.post = pt.id
          JOIN {user} u ON u.id = pt.userid
         WHERE pt.community = ? 
                       AND pt.type = ?
         ORDER BY pt.time DESC";

        return $this->DB->get_records_sql($sql, array($idCommunity, $type));
    }

    public function photosByCommunity($idCommunity, $userid) {
        $sql = "SELECT wcf.id, wcf.name, u.firstname, wcf.timecreated, u.id AS userid
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_PHOTO_COMMUNITY . "} wcf
          JOIN {user} u ON u.id = wcf.userid
         WHERE wcf.community = ?  
                       AND u.id = ?
          ORDER BY wcf.timecreated DESC";

        return $this->DB->get_records_sql($sql, array($idCommunity, $userid));
    }

    public function moviesByCommunity($idCommunity, $userid) {
        $sql = "SELECT wcf.id, wcf.name, u.firstname, wcf.timecreated, u.id AS userid
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_MOVIE_COMMUNITY . "}wcf
          JOIN {user} u ON u.id = wcf.userid
         WHERE wcf.community = ?  
                       AND u.id = ?
          ORDER BY wcf.timecreated DESC";

        return $this->DB->get_records_sql($sql, array($idCommunity, $userid));
    }

    public function questionsByCommunity($idCommunity) {
        $sql = "SELECT pt.id, qt.name, qt.startdate, qt.enddate, qt.enabled, u.firstname, pt.time, u.id AS userid
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_QUESTION . "} qt
          JOIN {" . TableResouces::$TABLE_PAGE_COMMUNITY_POST . "} pt ON qt.post = pt.id
          JOIN {user} u ON u.id = pt.userid
         WHERE pt.community = ?
         ORDER BY pt.time DESC";

        return $this->DB->get_records_sql($sql, array($idCommunity));
    }

    public function myQuestionsByCommunity($idCommunity, $userid) {
        $sql = "SELECT pt.id, qt.name, u.firstname, pt.time, u.id AS userid
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_QUESTION . "} qt
          JOIN {" . TableResouces::$TABLE_PAGE_COMMUNITY_POST . "} pt ON qt.post = pt.id
          JOIN {user} u ON u.id = pt.userid
         WHERE pt.community = ?  
                       AND u.id = ?
          ORDER BY pt.time DESC";

        return $this->DB->get_records_sql($sql, array($idCommunity, $userid));
    }

    public function mentalMapsByCommunity($idCommunity) {
        $sql = " SELECT wcq.id, wcq.name AS name, wcq.time, u.firstname, wcq.userid, wcq.url
           FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_MENTAL_MAP . "}  wcq
           JOIN {user} u ON wcq.userid = u.id
          WHERE wcq.community = ?
           ORDER BY wcq.time DESC";
        return $this->DB->get_records_sql($sql, array($idCommunity));
    }

    public function iconsByCommunity($idCommunity) {
        return $this->DB->get_records(TableResouces::$TABLE_PAGE_COMMUNITY_ICONE, array('community' => $idCommunity));
    }

    public function allLinksByCommunity($idCommunity, $type) {
        $sql = "SELECT pt.id, lk.name, lk.url, pt.time, u.firstname, u.id AS userid
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_LINKS . "} lk
          JOIN {" . TableResouces::$TABLE_PAGE_COMMUNITY_POST . "} pt ON lk.post = pt.id
          JOIN {user} u ON u.id = pt.userid
         WHERE pt.community = ? 
                       AND pt.type = ?
          ORDER BY pt.time DESC";

        return $this->DB->get_records_sql($sql, array($idCommunity, $type));
    }

    public function linksByCommunity($idCommunity, $userid, $type) {
        $sql = "SELECT pt.id, lk.name, u.firstname, pt.time, u.id AS userid
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_LINKS . "} lk
          JOIN {" . TableResouces::$TABLE_PAGE_COMMUNITY_POST . "} pt ON lk.post = pt.id
          JOIN {user} u ON u.id = pt.userid
         WHERE pt.community = ?  
                       AND u.id = ? 
                       AND pt.type = ?
          ORDER BY pt.time DESC";

        return $this->DB->get_records_sql($sql, array($idCommunity, $userid, $type));
    }

    public function glossarysByCommunity($idCommunity) {

        return self::glossarysByCommunityAndLike($idCommunity, 0, 1);
    }

    public function glossarysByCommunityAndLike($idCommunity, $like, $case) {
        $sql = " SELECT gls.id, gls.termo, gls.conceito, gls.userid, u.firstname, 
                        gls.time, gls.community, gls.total_votos, gls.votos
           FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARY . "} gls
           JOIN {user} u ON gls.userid = u.id";
        if ($case == 0) {
            $sql .= " WHERE gls.termo COLLATE UTF8_GENERAL_CI LIKE '$like%' AND gls.community = $idCommunity ";
        } else {
            $sql .= " WHERE gls.community = $idCommunity ";
        }
        return $this->DB->get_records_sql($sql);
    }

    public function myIcons($idCommunity, $userid) {
        return $this->DB->get_records(TableResouces::$TABLE_PAGE_COMMUNITY_ICONE, array('community' => $idCommunity, 'userid' => $userid));
    }

    public function myMentalMapsByCommunity($idCommunity, $userid) {
        $sql = " SELECT wcq.id, wcq.name AS name, wcq.time,
            u.firstname, wcq.userid, wcq.url
           FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_MENTAL_MAP . "}  wcq
           JOIN {user} u ON wcq.userid = u.id
          WHERE wcq.community = ? 
                        AND wcq.userid = ?
           ORDER BY wcq.time DESC";

        return $this->DB->get_records_sql($sql, array($idCommunity, $userid));
    }

    public function postCommentById($idPost) {
        return $this->DB->get_records(TableResouces::$TABLE_PAGE_COMMUNITY_POST_COMMENT, array('postid' => $idPost));
    }

    public function searchPostByID($idPost) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('id' => $idPost));
    }

    public function searchTextById($idFile) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_TEXT, array('post' => $idFile));
    }

    public function searchFileById($idFile) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA, array('post' => $idFile));
    }

    public function searchPhotoById($idPhoto) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA, array('post' => $idPhoto));
    }

    public function searchMovieById($idMovie) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA, array('post' => $idMovie));
    }

    public function searchPhotoCommunityById($idCommunity, $idPhoto) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id' => $idPhoto));
    }

    public function searchMovieCommunityById($idCommunity, $idMovie) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id' => $idMovie));
    }

    public function searchFileCommunityById($idCommunity, $idFile) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id' => $idFile));
    }

    public function searchAskQuestionByCommunityById($idQuestion) {
        return $this->DB->get_records(TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION, array('question' => $idQuestion), "name_question");
    }

    public function deleteAskedQuestionByUserById($idQuestion, $userid) {
        $sql = " DELETE aqu
           FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION_USER . "} aqu
           JOIN {" . TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION . "} aq ON aqu.answer_question = aq.id
          WHERE aq.question = ? 
                        AND aqu.userid = ?";

        return $this->DB->execute($sql, array($idQuestion, $userid));
    }

    public function getTotalRespondidasEnquete($idQuestion) {
        $sql = " SELECT aqu.id
           FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION_USER . "} aqu
           JOIN {" . TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION . "} aq ON aqu.answer_question = aq.id
          WHERE aq.question = ?";

        return sizeof($this->DB->get_records_sql($sql, array($idQuestion)));
    }

    public function getTotalRespondidasEnqueteByPergunta($idPergunta) {
        $sql = " SELECT aqu.id
           FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION_USER . "} aqu
          WHERE aqu.answer_question = ?";

        return sizeof($this->DB->get_records_sql($sql, array($idPergunta)));
    }

    public function searchQuestionByCommunityById($idQuestion) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_QUESTION, array('post' => $idQuestion));
    }

    public function searchAnswerById($idQuestion) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION, array('id' => $idQuestion));
    }

    public function deleteAskQuestionByCommunity($idQuestion) {
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_ANSWER_QUESTION, array('question' => $idQuestion));
    }

    public function deleteTextById($idText, $idUser, $idPost) {
        $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_TEXT, array('id' => $idText));
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
    }

    public function deleteQuestionByCommunityById($idQuestion, $idUser, $idPost) {
        $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_QUESTION, array('id' => $idQuestion));
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
    }

    public function deleteMentalMapByCommunityByIdByuser($idLinks, $idUser, $idPost) {
        $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_LINKS, array('id' => $idLinks));
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
    }

    public function deleteIconsByCommunityByIdByuser($idLinks, $idUser, $idPost) {
        $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_LINKS, array('id' => $idLinks));
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
    }

    public function deleteGlossaryById($idGlossario) {
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARY, array('id' => $idGlossario));
    }

    public function deleteCommunityById($idCommunity) {
        $this->DB->execute('SET FOREIGN_KEY_CHECKS=0', null);
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY, array('id' => $idCommunity));
    }

    public function deletePhotoByCommunityByIdByuser($idPhoto, $idUser, $idPost) {
        $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA, array('id' => $idPhoto));
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
    }

    public function deleteFileByIdUser($idFile, $idUser, $idPost) {
        $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA, array('id' => $idFile));
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
    }

    public function deleteMovieByIdUser($idMovie, $idUser, $idPost) {
        $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA, array('id' => $idMovie));
        return $this->DB->delete_records(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('userid' => $idUser, 'id' => $idPost));
    }

    public function getListNameUser($notUser) {
        return $this->DB->get_records_sql("SELECT id,firstname FROM {user} WHERE id != ?", array($notUser));
    }

    public function searchMentalMapByCommunityById($idMap) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_LINKS, array('post' => $idMap));
    }

    public function searchIconeByCommunityById($idGlossary) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_LINKS, array('post' => $idGlossary));
    }

    public function searchGlossaryByCommunityById($idCommunity, $idGlossary) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARY, array('community' => $idCommunity, 'id' => $idGlossary));
    }

    public function searchGlossaryById($idGlossary) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARY, array('id' => $idGlossary));
    }

    public function searchGlossaryUserVotation($idGlossary, $idUser) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_GLOSSARRY_VOTACAO, array('glossarryid' => $idGlossary, 'userid' => $idUser));
    }

    public function searchLikeDislikeUserVotation($idPost, $idUser) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_LIKEDISLIKE, array('postid' => $idPost, 'userid' => $idUser));
    }

    public function searchMentalMapByCommunityByIdByUser($idCommunity, $idMap, $idUser) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id' => $idMap, 'userid' => $idUser));
    }

    public function searchIconeByCommunityByIdByUser($idCommunity, $idIcone, $idUser) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST, array('community' => $idCommunity, 'id' => $idIcone, 'userid' => $idUser));
    }

    public function searchGlossaryByCommunityByIdByUser($idCommunity, $idGlo, $idUser) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_ICONE, array('community' => $idCommunity, 'id' => $idGlo, 'userid' => $idUser));
    }

    public function findPageById($id) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGINA, array('id' => $id));
    }

    public function findCommunityById($id) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY, array('id' => $id));
    }

    public function findUserInCommunityById($idCommunity, $userid) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY_USER, array('community' => $idCommunity, 'userid' => $userid));
    }

    public function findUserCreatorCommunityById($community, $userid, $creator = 1) {
        return $this->DB->get_record(TableResouces::$TABLE_PAGE_COMMUNITY, array('id' => $community, 'userid' => $userid));
    }

    public function deleteRecordByTablePageOrder() {
        $this->deleteRecordByTable(TableResouces::$TABLE_PAGE_ORDER);
    }

    public function insertRecordInTablePageOrder($paginaOrder, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResouces::$TABLE_PAGE_ORDER, $paginaOrder, $returnId, $bulk);
    }

    public function insertRecordInTableCommunityPost($post, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST, $post, $returnId, $bulk);
    }

    public function getRecordsTableCommunityPost($idCommunity) {
        $sql = "SELECT bw.message,bw.time, u.id AS userid, u.firstname AS username
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_POST . "} bw
          JOIN {user} u ON bw.userid = u.id
            WHERE bw.community = ?
             ORDER BY bw.time DESC ";
        return $this->DB->get_records_sql($sql, array($idCommunity));
    }

    public function getRecordsTableFilesCommunity($idCommunity) {
        $sql = "SELECT bwc.*, u.id AS userid, u.firstname AS username
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_FILE . "} bwc
          JOIN {user} u ON bwc.userid = u.id
             ORDER BY bwc.timecreated DESC ";
        return $this->DB->get_records_sql($sql);
    }

    public function deleteRecordByTable($table) {
        return $this->DB->execute("DELETE FROM \{$table\}");
    }

    public function findChildrenByHabilityAndVisible($idParent, $visivel = 1, $habilitado = 1) {
        $sql = "SELECT p.id, p.nome, po.parent
          FROM {" . TableResouces::$TABLE_PAGINA . "} p
         LEFT JOIN {" . TableResouces::$TABLE_PAGINA_order . "} po ON p.id = po.page
         WHERE po.parent = ? 
                       AND p.habilitado = ? 
                       AND p.visivel = ?
          ORDER BY po.id, po.parent";
        return $this->DB->get_records_sql($sql, array($idParent, $habilitado, $visivel));
    }

    public function findChildren($idParent) {
        $sql = "SELECT p.id, p.nome, po.parent
          FROM {$this->CFG->prefix}{" . TableResouces::$TABLE_PAGINA . "} p
         LEFT JOIN {$this->CFG->prefix}{" . TableResouces::$TABLE_PAGINA_order . "} po ON p.id = po.page
         WHERE po.parent = ?
          ORDER BY po.parent";
        return $this->DB->get_records_sql($sql, array($idParent));
    }

    public function getListFatherByHabilityAndVisible($visivel = 1, $habilitado = 1) {
        $sql = "SELECT p.id, p.nome, po.parent
          FROM {" . TableResouces::$TABLE_PAGINA . "} p
         LEFT JOIN {" . TableResouces::$TABLE_PAGINA_order . "} po ON p.id = po.page
                 WHERE (po.parent = 0 OR po.parent IS NULL) 
                       AND p.habilitado = ? 
                       AND p.visivel = ?
              ORDER BY po.id, po.parent";
        return $this->DB->get_records_sql($sql, array($habilitado, $visivel));
    }

    public function getListFather() {
        $sql = "SELECT p.id, p.nome, po.parent
          FROM {$this->CFG->prefix}{" . TableResouces::$TABLE_PAGINA . "} p
         LEFT JOIN {$this->CFG->prefix}{" . TableResouces::$TABLE_PAGINA_order . "} po ON p.id = po.page
         WHERE po.parent = 0 
                       OR po.parent IS NULL
              ORDER BY po.id, po.parent";
        return $this->DB->get_records_sql($sql);
    }

    public function getListCommunity() {
        $sql = "SELECT wc.id,wc.name AS name, u.firstname AS user, u.id AS user_id
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY . "} wc
          JOIN {user} u ON wc.userid = u.id";
        return $this->DB->get_records_sql($sql);
    }

    public function getListMyCommunityByUser($idUser) {
        $sql = "SELECT wc.id,wc.name AS name, u.firstname AS user, u.id AS user_id
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_USER . "} wcu
          JOIN {user} u ON wcu.userid = u.id
          JOIN {" . TableResouces::$TABLE_PAGE_COMMUNITY . "} wc ON wcu.community = wc.id
         WHERE wcu.userid = ?";
        return $this->DB->get_records_sql($sql, array($idUser));
    }

    public function findCommunityEmailById($id) {
        $sql = "SELECT u.email AS email
                  FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_USER . "} cm
                  JOIN {user} u ON cm.userid = u.id
                 WHERE cm.community = ?
              ORDER BY cm.id ASC ";
        return $this->DB->get_records_sql($sql, array($id));
    }

    public function findCommunityParticipantsById($id) {
        $sql = "SELECT cm.id, u.id AS userid, u.firstname AS username
                  FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_USER . "} cm
                  JOIN {user} u ON cm.userid = u.id
                 WHERE cm.community = ?
              ORDER BY cm.id ASC ";
        return $this->DB->get_records_sql($sql, array($id));
    }

    public function participanteInCommunity($idParticipante, $idCommunity) {
        $sql = "SELECT cm.id, u.id AS userid, u.firstname AS username
                  FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_USER . "} cm
                  JOIN {user} u ON cm.userid = u.id
                 WHERE cm.community = ? 
                       AND cm.userid = ?
              ORDER BY cm.id ASC ";
        return $this->DB->get_records_sql($sql, array($idCommunity, $idParticipante));
    }

    public function insertRecordInTableCommunityText($postText, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_TEXT, $postText, $returnId, $bulk);
    }

    public function insertRecordInTableCommunityMedia($postMedia, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_MEDIA, $postMedia, $returnId, $bulk);
    }

    public function getAllCommunityPost($idCommunity) {
        $sql = "SELECT pt.id, pt.time, pt.type, u.id AS userid, u.firstname AS username
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_POST . "} pt
          JOIN {user} u ON pt.userid = u.id
         WHERE pt.community = ?
          ORDER BY pt.time DESC ";
        return $this->DB->get_records_sql($sql, array($idCommunity));
    }

    public function getAllCommunityPostSince($idCommunity, $ultimo_post) {
        $sql = "SELECT pt.id, pt.time, pt.type, u.id AS userid, u.firstname AS username
          FROM {" . TableResouces::$TABLE_PAGE_COMMUNITY_POST . "} pt
          JOIN {user} u ON pt.userid = u.id
             WHERE pt.community = ? 
                       AND pt.id > ?
          ORDER BY pt.time DESC ";
        return $this->DB->get_records_sql($sql, array($idCommunity, $ultimo_post));
    }

    public function insertRecordInTablePostComment($postComment, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResouces::$TABLE_PAGE_COMMUNITY_POST_COMMENT, $postComment, $returnId, $bulk);
    }

}
