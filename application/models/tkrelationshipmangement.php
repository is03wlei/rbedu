<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TKRelationshipMangement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('rbedu', TRUE);
		$this->is_load = false;
	}
	
	//read relationship info
	public function get_teacher_knowledge_relationship_by_teacher_id($tid){
		//get teacher knowledge info by tid and kid
		
		$this->_db.select('KID');
		$this->_db.from('KnowledgeTeacherRelationship');
		$this->_db.where('TID',$tid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	//create relationship info
	public function create_teacher_knowledge_relationship($tid, $kid){
		//create teacher private knowledge info
		$relationinfo = array(
				'TID' => $tid,
				'KID' => $kid
		);

		$this->_db.insert('KnowledgeTeacherRelationship', $relationinfo);
		$ktid = $this->_db.insert_id();
		return $ktid;
	}
	
	public function create_teacher_knowledge_relationship_batch($tid, $kidlist){
		//create teacher private knowledge info list
	
		$relationinfolist = array();
		foreach($kidlist as $kid){
			$relationinfolist[] = array(
				'TID' => $tid,
				'KID' => $kid
			);
		}
	
		$this->_db.insert_batch('KnowledgeTeacherRelationship', $relationinfolist);
		$ktid = $this->_db.insert_id();
		return $ktid;
	}
	
	//delete relationship info
	public function delete_teacher_knowledge_relationship_by_teacher_id($tid){
		//delete a teachre's all relationship
		$this->_db.where('TID',$tid);
		$this->_db.delete('KnowledgeTeacherRelationship');
		
		return 0;
	}
	
	public function delete_teacher_knowledge_relationship_by_relation_id($rid){
		//delete a teachre's all relationship
		$this->_db.where('KTID',$rid);
		$this->_db.delete('KnowledgeTeacherRelationship');
	
		return 0;
	}
	
}

?>