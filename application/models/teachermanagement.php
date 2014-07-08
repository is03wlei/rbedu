<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TeacherManagement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
	//teacher authentication
	public function teacher_authenticate($email, $passwd){
		//make authenticate operation
			
	}
	
	//read teacher data
	public function get_teacher_by_teacher_id($tid){
		//get teacher's meta data
		
		$this->_db->select('TeacherName , TeacherSubject, TeacherLevel, TeacherPicture');
		$this->_db->from('TeacherInfo');
		$this->_db->where('TID',$tid);
		$this->_db->limit(1);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}

	public function get_teacherinfo_tid($tid){
		//get teacher's meta data
		
		$this->_db->select('TeacherName , TeacherSubject, TeacherLevel, TeacherPicture');
		$this->_db->from('TeacherInfo');
		$this->_db->where('TID',$tid);
		$this->_db->limit(1);
		$query = $this->_db->get();
	    return $query->row();	
	}
	
	public function get_teacher_by_school_id($sid){
		//get all teachers' meta date for one school
		
		$this->_db->select('TID, TeacherName , TeacherSubject, TeacherLevel, TeacherPicture');
		$this->_db->from('TeacherInfo');
		$this->_db->where('TeacherSID',$sid);
		$query = $this->_db->get();
		$result = $query->result();

		return $result;
	}
	
	public function get_teacher_by_school_subject($sid,$subname){
		//get all teachers' meta date for one school with same subject
		
		$this->_db->select('TID, TeacherName , TeacherSubject, TeacherLevel, TeacherPicture');
		$this->_db->from('TeacherInfo');
		$this->_db->where('TeacherSID',$sid);
		$this->_db->where('TeacherSubject',$subname);
		$query = $this->_db->get();
		$result = $query->result();

		return $result;
	}
	
	//insert teacher data
	public function create_teacher($email, $tname, $passwd, $tsub, $retime, $tpid, $tcid, $tdid, $tsid, $tlevel){
		
		$teainfo = array(
			"Email" => $email,
			"TeacherName" => $tname,
			"Password" => $passwd,
			"TeacherSubject" => $tsub,
			"RegisterTime" => $retime,
			"TeacherPID" => $tpid,
			"TeacherCID" => $tcid,
			"TeacherDID" => $tdid,
			"TeacherSID" => $tsid,
			"TeacherLevel" => $tlevel,
			"UserLevel" => 1,
			"TeacherPicture" => 'default_teacher.jpg'
		);
		
		$this->_db->insert('TeacherInfo', $teainfo);
		$tid = $this->_db->insert_id();
		
		$gid = $this->create_teacher_default_group($tid);
		
		return $tid;
	}
	
	//update teacher data
	public function update_teacher_data($tid, $tname, $tpic, $passwd){
		//update teacher data
		$teainfo = array();
		if(strlen($tname) > 0){
			$teainfo['TeacherName'] = $tname;	
		}
		
		if(strlen($tpic) > 0){
			$teainfo['TeacherPicture'] = $tpic;
		}
		
		if(strlen($passwd) > 0){
			$teainfo['Password'] = $passwd;
		}
		
		if(count($teainfo) == 0){
			return 0;
		}
		
		$this->_db->where('TID',$tid);
		$this->_db->update('TeacherInfo', $teainfo);
		
		return 0;
	}
	
	//delete teacher
	public function delete_teacher_data($tid){
		//delete teacher data
		$this->_db->where('TID',$tid);
		$this->_db->delete('TeacherInfo');
		
		return 0;
	}
	
	
	//read group info by group id
	public function get_group_by_group_id($gid){
		//read all group info of a teacher limited in one class
		$this->_db->select('TID, TeacherClass, GroupName, IsDefaultClass');
		$this->_db->from('TeacherStudentGroup');
		$this->_db->where('GID',$gid);
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	
	//read teacher group data
	public function get_group_by_teacher_id($tid){
		//read all group info of a teacher
		
		$this->_db->select('GID, TeacherClass, GroupName, IsDefaultClass');
		$this->_db->from('TeacherStudentGroup');
		$this->_db->where('TID',$tid);
		$query = $this->_db->get();
		$result = $query->result();
		
		$classtree = array();
		if($query->num_rows() > 0){
			foreach($result as $row){
				$classname = $row->TeacherClass;
				$groupname = $row->GroupName;
				$isdclass = $row->IsDefaultClass;
				$gid = $row->GID;
				
				if(isset($classtree[$classname])){
					$classtree[$classname][] = array($gid,$groupname);
				}else{
					$classtree[$classname] = array($isdclass, array($gid,$groupname));					
				}
			}
		}
		
		$result = $classtree;
		return $result;
	}
	
	
	public function get_group_by_teacher_id_v2($tid){
		//read all group info of a teacher
	
		$this->_db->select('GID, TeacherClass, GroupName, ClassGrade, IsDefaultClass');
		$this->_db->from('TeacherStudentGroup');
		$this->_db->where('TID',$tid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	
	public function get_group_by_teacher_class($tid,$classGrade,$classname){
		//read all group info of a teacher limited in one class
		$this->_db->select('GID, TeacherClass, GroupName, IsDefaultClass');
		$this->_db->from('TeacherStudentGroup');
		$this->_db->where('TID',$tid);
        $this->_db->where('ClassGrade',$classGrade);
		$this->_db->where('TeacherClass',$classname);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	//create teacher group
	public function create_teacher_default_group($tid){
		//create a deafault teacher group for a teacher
		$grpinfo = array(
			'TID' => $tid,
			'GroupName' => 'È«°à',
			'TeacherClass' => 'Ä¬ÈÏ°à¼¶',
			'IsDefaultClass' => 1
		);
		
		$this->_db->insert('TeacherStudentGroup', $grpinfo);
		$gid = $this->_db->insert_id();
		return $gid;
	}	
	
	public function create_teacher_new_group($tid, $classname, $groupname){
		//create a new teacher group for a teacher
		$grpinfo = array(
				'TID' => $tid,
				'GroupName' => $classname,
				'TeacherClass' => $groupname,
				'IsDefaultClass' => 0
		);
		
		$this->_db->insert('TeacherStudentGroup', $grpinfo);
		$gid = $this->_db->insert_id();
		return $gid;
	}
	
	//update teacher group
	public function update_teacher_class($tid,$oldname,$newname){
		//update teacher class name for new name
		$grpinfo = array(
			'TeacherClass' => $newname
		);
		
		$this->_db->where('TeacherClass',$oldname);
		$this->_db->where('TID',$tid);
		$this->_db->update('TeacherStudentGroup', $grpinfo);
		
		return 0;
	}
	
	public function update_teacher_group($tid,$oldname,$newname){
		//update teacher group name for new name
		$grpinfo = array(
				'GroupName' => $newname
		);
		
		$this->_db->where('GroupName',$oldname);
		$this->_db->where('TID',$tid);
		$this->_db->update('TeacherStudentGroup', $grpinfo);
		
		return 0;
	}
	
	//delete teacher group
	public function delete_teacher_class($tid, $classname){
		//delete a teacher class and all its group
		$this->_db->where('TID',$tid);
		$this->_db->where('TeacherClass',$classname);
		$this->_db->delete('TeacherStudentGroup');
		
		return 0;
	}
	
	public function delete_teacher_group($gid){
		//delete a teacher group
		$this->_db->where('GID',$gid);
		$this->_db->delete('TeacherStudentGroup');
		
		return 0;
	}
}

?>
