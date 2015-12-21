
<?php

require_once($CFG->dirroot . '/blocks/webgd_community/commons/TableResouces.php');
/*
class WebgdDao{
    private $DB;
    private $CFG;

    function __construct() {
        global $DB, $CFG;
        $this->DB = $DB;
        $this->CFG = $CFG;
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
            {$this->CFG->prefix}$TABLE_PAGE_COMMUNITY_POST bw
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
            {$this->CFG->prefix}$TABLE_PAGE_COMMUNITY_FILE bwc
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
                    {$this->CFG->prefix}$TABLE_PAGINA p
                LEFT join
                    {$this->CFG->prefix}$TABLE_PAGINA_order po on
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
                    {$this->CFG->prefix}$TABLE_PAGINA p
                LEFT join
                    {$this->CFG->prefix}$TABLE_PAGINA_order po on
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
                    {$this->CFG->prefix}$TABLE_PAGINA p
                LEFT join
                    {$this->CFG->prefix}$TABLE_PAGINA_order po on
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
                    {$this->CFG->prefix}$TABLE_PAGINA p
                LEFT join
                    {$this->CFG->prefix}$TABLE_PAGINA_order po on
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
                    {$this->CFG->prefix}block_webgd_community wc
                inner join
                    {$this->CFG->prefix}user u on
                    wc.userid = u.id";
        return $this->DB->get_records_sql($sql);
    }

    public function getListMyCommunityByUser($idUser){
        $sql = "select
                    wc.id,wc.name as name, u.firstname as user, u.id as user_id
                from
                    {$this->CFG->prefix}$TABLE_PAGE_COMMUNITY_USER wcu
                inner join
                    {$this->CFG->prefix}user u on
                    wcu.userid = u.id
                inner join
                    {$this->CFG->prefix}block_webgd_community wc on
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
        {$this->CFG->prefix}$TABLE_PAGE_COMMUNITY_USER cm
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
        {$this->CFG->prefix}$TABLE_PAGE_COMMUNITY_USER cm
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
        {$this->CFG->prefix}$TABLE_PAGE_COMMUNITY_USER cm
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
            {$this->CFG->prefix}$TABLE_PAGE_COMMUNITY_POST pt
        INNER JOIN
                {$this->CFG->prefix}user u on
                pt.userid = u.id
            WHERE
                    pt.community = $idCommunity
            ORDER BY
                    pt.time desc ";
        return $this->DB->get_records_sql($sql);
    }

    public function getAllCommunityPostSince($idCommunity,$ultimo_post) {
    //Função que gera os records para TIMELINE a partir de um post x
    $sql = "SELECT
            pt.id, pt.time, pt.type, u.id as userid, u.firstname as username
        FROM
            {$this->CFG->prefix}$TABLE_PAGE_COMMUNITY_POST pt
        INNER JOIN
                {$this->CFG->prefix}user u on
                pt.userid = u.id
            WHERE
                    pt.community = $idCommunity and pt.id > $ultimo_post
            ORDER BY
                    pt.time desc ";
        return $this->DB->get_records_sql($sql);
    }

    

    public function insertRecordInTablePostComment($postComment, $returnId = true, $bulk = false) {
        return $this->DB->insert_record(TableResoucer::$TABLE_PAGE_COMMUNITY_POST_COMMENT, $postComment, $returnId, $bulk);
    }
    

    

    

}
*/