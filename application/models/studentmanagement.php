<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class StudentManagement extends CI_Model{
	
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
	
	//read student data
	public function get_student_by_student_id($sid){
		//get student's meta data
		
		$this->_db->select('StudentName , StudentLevel');
		$this->_db->from('StudentInfo');
		$this->_db->where('SID',$sid);
		$this->_db->limit(1);
		$query = $this->_db->get();

        $result = $query->result();
		return $result;
	}

    public function get_student_all_info_by_student_id($sid){
        $this->_db->select('StudentPID, StudentCID, StudentDID, StudentSID, StudentLevel, StudentGrade, StudentClass');
        $this->_db->from('StudentInfo');
        $this->_db->where('SID',$sid);
        $this->_db->limit(1);
        $query = $this->_db->get();
        $result = $query->result();
        return $result;
    }


    public function get_student_name($sid){
		$this->_db->select('StudentName , StudentLevel');
		$this->_db->from('StudentInfo');
		$this->_db->where('SID',$sid);
		$this->_db->limit(1);
		$query = $this->_db->get();
        if($query->num_rows() < 0){
            return null;
        }
        return $query->row()->StudentName;
    }
		

	
	public function get_student_by_school_id($sid){
		//get all students' meta date for one school
		
		$this->_db->select('SID, StudentName , StudentLevel, StudentPicture');
		$this->_db->from('StudentInfo');
		$this->_db->where('StudentSID',$sid);
		$query = $this->_db->get();
		$result = $query->result();

		return $result;
	}

	/*	
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
	*/
	
	//insert student data
	public function create_student($email, $sname, $passwd, $retime, $spid, $scid, $sdid, $ssid, $slevel){
		
		$studinfo = array(
			"Email" => $email,
			"StudentName" => $sname,
			"Password" => $passwd,
			"RegisterTime" => $retime,
			"StudentPID" => $spid,
			"StudentCID" => $scid,
			"StudentDID" => $sdid,
			"StudentSID" => $ssid,
			"StudentLevel" => $slevel,
			"UserLevel" => 1,
			"StudentPicture" => 'default_student.jpg'
		);
		
		$this->_db->insert('StudentInfo', $studinfo);
		$sid = $this->_db->insert_id();
		
		$gid = $this->create_student_default_group($sid);
		
		return $sid;
	}
	
	//update student data
	public function update_student_data($sid, $sname, $spic, $passwd){
		//update student data
		$studinfo = array();
		if(strlen($sname) > 0){
			$studinfo['StudentName'] = $sname;	
		}
		
		if(strlen($spic) > 0){
			$studinfo['StudentPicture'] = $spic;
		}
		
		if(strlen($passwd) > 0){
			$studinfo['Password'] = $passwd;
		}
		
		if(count($studinfo) == 0){
			return 0;
		}
		
		$this->_db->where('SID',$sid);
		$this->_db->update('StudentInfo', $studinfo);
		
		return 0;
	}
	
	//delete student
	public function delete_student_data($sid){
		//delete student data
		$this->_db->where('SID',$sid);
		$this->_db->delete('StudentInfo');
		
		return 0;
	}

	/*	
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
	
	public function get_group_by_teacher_class($tid,$classname){
		//read all group info of a teacher limited in one class
		$this->_db->select('GID, TeacherClass, GroupName, IsDefaultClass');
		$this->_db->from('TeacherStudentGroup');
		$this->_db->where('TID',$tid);
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
	*/
}

?>
