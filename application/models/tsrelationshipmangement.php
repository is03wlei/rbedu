<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TSRelationshipMangement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
	//read relationship info
	public function get_students_by_group_id($gid){
		
//		$this->_db->select('TID');
		$this->_db->select('SID, StudentName');  
		$this->_db->from('TeacherStudentRelationship');
		$this->_db->where('GID',$gid);
		$query = $this->_db->get();
		$result = $query->result();
		return $result;
	}
	
	//create relationship info
	public function create_teacher_student_relationship($tid, $sid, $sname, $gid){
		//create teacher private knowledge info
		$relationinfo = array(
				'TID' => $tid,
				'SID' => $sid,
                'StudentName' => $sname,
                'GID' => $gid
		);

		$this->_db->insert('TeacherStudentRelationship', $relationinfo);
		$tsid = $this->_db->insert_id();
		return $tsid;
	}
	
	public function create_teacher_student_relationship_batch($tid, $slist, $gid){
	
		$relationinfolist = array();
		foreach($slist as $sid=>$sname){
			$relationinfolist[] = array(
				'TID' => $tid,
				'SID' => $sid,
                'StudentName' => $sname,
                'GID' => $gid
			);
		}
	
		$this->_db->insert_batch('TeacherStudentRelationship', $relationinfolist);
		$ktid = $this->_db->insert_id();
		return $ktid;
	}
	
	//delete relationship info
	public function delete_teacher_student_relationship($tid,$sid,$gid){
		$this->_db->where('TID',$tid);
		$this->_db->where('SID',$sid);
		$this->_db->where('GID',$gid);
		$this->_db->delete('TeacherStudentRelationship');
		return 0;
	}
	
	public function delete_teacher_student_relationship_by_relation_id($rid){
		//delete a teachre's all relationship
		$this->_db->where('RID',$rid);
		$this->_db->delete('TeacherStudentRelationship');
		return 0;
	}
	
}

?>
