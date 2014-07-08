<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class KnowledgeMangement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
	//read knowledge info
	public function get_common_knowledge_by_knowledge_id($kid){
		//get knowledge info by id
		
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('KID',$kid);
		$this->_db->where('IsCommonKnowledge',1);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_common_knowledge_by_knowledge_batch($kidlist){
		//get knowledge info by id list
		if(count($kidlist) == 0){
			$result = array();
			return $result;
		}
		
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where_in('KID',$kidlist);
		$this->_db->where('IsCommonKnowledge',1);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_common_knowledge_by_subject($sub,$level){
		//get knowledge info by level and subject
		
		$this->_db->select('KID, KnowledgeName, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeGrade, KnowledgeParentID, KnowledgeTreeLevel');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('KnowledgeSubject',$sub);
		$this->_db->where('KnowledgeLevel',$level);
		$this->_db->where('IsCommonKnowledge',1);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_common_knowledge_by_subject_grade($sub,$level,$grade){
		//get knowledge info by level and subject and grade
		
		$this->_db->select('KID, KnowledgeName, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeParentID, KnowledgeTreeLevel');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('KnowledgeSubject',$sub);
		$this->_db->where('KnowledgeLevel',$level);
		$this->_db->where('KnowledgeGrade',$grade);
		$this->_db->where('IsCommonKnowledge',1);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_teacher_knowledge_by_teacher_id($tid){
		//get all teacher private knowledge info
		
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('CreateTeacherID',$tid);
		$this->_db->where('IsCommonKnowledge',0);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_teacher_knowledge_by_knowledge_id($kid){
		//get one teacher private knowledge by kid
	
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('KID',$kid);
		$this->_db->where('IsCommonKnowledge',0);
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	public function get_knowledgeinfo_kid($kid){
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('KID',$kid);
		//$this->_db->where('IsCommonKnowledge',0);
		$query = $this->_db->get();
		$result = $query->row();
	
		return $result;
	}

	public function get_teacher_knowledge_by_knowledge_batch($kidlist){
		//get some teacher private knowledge by kid list
		if(count($kidlist) == 0){
			$result = array();
			return $result;
		}
		
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where_in('KID',$kidlist);
		$this->_db->where('IsCommonKnowledge',0);
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	
	
	public function get_teacher_knowledge_by_teacher_knowledge_id($tid,$kid){
		//get teacher private knowledge info by tid and kid
		
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('KID',$kid);
		$this->_db->where('CreateTeacherID',$tid);
		$this->_db->where('IsCommonKnowledge',0);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_teacher_knowledge_by_subject($tid, $sub, $level){
		//get teacher knowledge info by level and subject
		
		$this->_db->select('KID, KnowledgeName, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeGrade, KnowledgeParentID, KnowledgeTreeLevel');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('KnowledgeSubject',$sub);
		$this->_db->where('KnowledgeLevel',$level);
		$this->_db->where('CreateTeacherID',$tid);
		$this->_db->where('IsCommonKnowledge',0);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_teacher_knowledge_by_subject_grade($tid, $sub, $level,$grade){
		//get teacher knowledge info by level and subject and grade
		
		$this->_db->select('KID, KnowledgeName, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeParentID, KnowledgeTreeLevel');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where('KnowledgeSubject',$sub);
		$this->_db->where('KnowledgeLevel',$level);
		$this->_db->where('KnowledgeGrade',$grade);
		$this->_db->where('CreateTeacherID',$tid);
		$this->_db->where('IsCommonKnowledge',0);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_all_knowledge_by_subject_grade($tid, $sub,$level,$grade){
		//get teacher knowledge info , combine with teacher private knowledge and common knowledge
	
		$common_knowledge = $this->read_common_knowledge_by_subject_grade($sub, $level, $grade);
		$private_knowledge = $this->read_teacher_knowledge_by_subject_grade($tid, $sub, $level, $grade);
		
		$result = array();
		foreach($common_knowledge as $row){
			$result[] = $row;
		}
		
		foreach($private_knowledge as $row){
			$result[] = $row;
		}
		
		return $result;
	}
	
	public function get_all_knowledge_by_knowledge_batch($kidlist){
		//get all teacher's knowledge by kid list
		if(count($kidlist) == 0){
			$result = array();
			return $result;
		}
	
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where_in('KID',$kidlist);
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	public function get_all_knowledge_by_knowledge_grade_batch($kidlist, $grade){
		//get all teacher's knowledge by kid list and grade
		if(count($kidlist) == 0){
			$result = array();
			return $result;
		}
	
		$this->_db->select('KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade');
		$this->_db->from('KnowledgeInfo');
		$this->_db->where_in('KID',$kidlist);
		$this->_db->where('KnowledgeGrade',$grade);
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	//create knowledge info
	public function create_teacher_private_knowledge($tid, $kname, $ksub, $ctime, $kbook, $klevel, $kgrade, $kdes){
		//create teacher private knowledge info
		
		$knowledgeinfo = array(
				'KnowledgeName' => $kname,
				'KnowledgeSubject' => $ksub,
				'CreateTime' => $ctime,
				'CreateTeacherID' => $tid,
				'IsCommonKnowledge' => 0,
				'KnowledgeBookVersion' => "ÈË½Ì°æ",
				'KnowledgeParentID' => 0,
				'KnowledgeTreeLevel' => 0,
				'KnowledgeLevel' => $klevel,
				'KnowledgeGrade' => $kgrade,
				'KnowledgeDescription' => $kdes
		);
		
		if(strlen($kbook) > 0){
			$knowledgeinfo['KnowledgeBookVersion'] = $kbook;
		}
		
		$this->_db->insert('KnowledgeInfo', $knowledgeinfo);
		$kid = $this->_db->insert_id();
		return $kid;
	}
	
	//update knowledge info
	public function update_teacher_private_knowledge($kid, $tid, $kname, $ksub, $ctime, $kbook, $klevel, $kgrade, $kdes){
		//update teacher private knowledge info
		$knowledgeinfo = array(
				'KnowledgeName' => $kname,
				'KnowledgeSubject' => $ksub,
				'CreateTime' => $ctime,
				'CreateTeacherID' => $tid,
				'KnowledgeLevel' => $klevel,
				'KnowledgeGrade' => $kgrade,
				'KnowledgeDescription' => $kdes
		);
		
		if(strlen($kbook)){
			$knowledgeinfo['KnowledgeBookVersion'] = $kbook;
		}
		
		$this->_db->where('KID',$kid);
		$this->_db->update('KnowledgeInfo', $knowledgeinfo);
	
		return 0;
	}
	
	//delete knowledge info
	public function delete_teacher_knowledge_by_teacher($tid){
		//delete a teacher's all private knowledge
		$this->_db->where('CreateTeacherID',$tid);
		$this->_db->where('IsCommonKnowledge',0);
		$this->_db->delete('KnowledgeInfo');
		
		return 0;
	}
	
	public function delete_teacher_knowledge_by_id($kid){
		//delete a knowledge
		$this->_db->where('KID',$kid);
		$this->_db->delete('KnowledgeInfo');
		
		return 0;
	}
	
}

?>
