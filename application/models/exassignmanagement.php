<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ExAssignManagement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
    public function get_teacher_exercises_by_grade_class($studentGrade, $studentClass){
		$this->_db->select('EAID, KID, KnowledgeName, ExerciseName, Subject, PublishTime, ExpectTime, StudentSID, StudentGrade, StudentClass, GroupName, Description, ExerciseNumber, ExerciseContent');
		
		$this->_db->from('TeacherExerciseAssignment');
        if(is_array($studentGrade)){
             $this->_db->where_in('StudentGrade',$studentGrade);
        }else{
             $this->_db->where('StudentGrade',$studentGrade);
        }
        if(is_array($studentClass)){
            $this->_db->where_in('StudentClass',$studentClass);
        }else{
            $this->_db->where('StudentClass',$studentClass);
        }
        $this->_db->order_by('PublishTime', 'desc');
		$query = $this->_db->get();
        if($query->num_rows < 0) return null;
		$result = $query->result();
		
		return $result;
    }

	//read exercise assignment infomation
	public function get_exercise_assignment_by_teacher_id($tid){
		//get teacher's all exercise assignment
		//limited 50 most recently exercise assignment
		
		$this->_db->select('EAID, KID, KnowledgeName, ExerciseName, Subject, PublishTime, ExpectTime, StudentSID, StudentGrade, StudentClass, GroupName, Description, ExerciseNumber, ExerciseContent');
		$this->_db->from('TeacherExerciseAssignment');
		$this->_db->where('TID',$tid);
		$this->_db->order_by('PublishTime','desc');
		$this->_db->limit(50);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_exercise_assignment_by_teacher_knowledge($tid, $kid){
		//get teacher's all exercise assignment in one knowledge
		//limited 50 most recently exercise assignment order by publishtime
		
		$this->_db->select('EAID, KID, KnowledgeName, ExerciseName, Subject, PublishTime, ExpectTime, StudentSID, StudentGrade, StudentClass, GroupName, Description, ExerciseNumber');
		$this->_db->from('TeacherExerciseAssignment');
		$this->_db->where('TID',$tid);
		$this->_db->where('KID',$kid);
		$this->_db->order_by('PublishTime','desc');
		$this->_db->limit(50);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_exercise_assignment_by_student_id($sid){
		//get student's all exercise assignment
		
		$this->_db->select('EAID, SID, KID, ExerciseName, KnowledgeName, TID, Subject, PublishTime, ExpectTime, HasReviewed, ExerciseStatus, ExerciseNumber');
		$this->_db->from('StudentExerciseAssignment');
		$this->_db->where('SID',$sid);
		$this->_db->order_by('PublishTime','desc');
		$this->_db->limit(50);
		$query = $this->_db->get();
        if($query->num_rows() <=0 )
             return null;
		$result = $query->result();
		
		return $result;
	}
	
	public function get_exercise_assignment_by_student_knowledge($sid, $kid){
		//get student's all exercise assignment in one knowledge
		
		$this->_db->select('EAID, KID, ExerciseName, KnowledgeName, TID, Subject, PublishTime, ExpectTime, ExerciseStatus, ExerciseNumber');
		$this->_db->from('StudentExerciseAssignment');
		$this->_db->where('TID',$tid);
		$this->_db->where('KID',$kid);
		$this->_db->order_by('PublishTime','desc');
		$this->_db->limit(50);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_exercise_assignment_by_exercise_id($eaid){
		//get one exercise assignment infomation
	
		$this->_db->select('KnowledgeName, ExerciseName, Subject, PublishTime, ExpectTime, StudentGrade, StudentClass, GroupName, ExerciseNumber,ExerciseContent');
		$this->_db->from('TeacherExerciseAssignment');
		$this->_db->where('EAID',$eaid);
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	public function get_teacher_exercise($eaid){
	
		$this->_db->select('KnowledgeName, ExerciseName, Subject, PublishTime, ExpectTime, StudentGrade, StudentClass, GroupName, ExerciseNumber,ExerciseContent');
		$this->_db->from('TeacherExerciseAssignment');
		$this->_db->where('EAID',$eaid);
		$query = $this->_db->get();
		$result = $query->row();
	
		return $result;
	}

	public function get_exercise_assignment_complete_by_exercise_id($eaid){
		//get complete infomation about the exercise assignment
		
		$this->_db->select('SID, FinishTime, HasReviewed, ExerciseStatus, ExerciseNumber');
		$this->_db->from('StudentExerciseAssignment');
		$this->_db->where('EAID',$eaid);
		$query = $this->_db->get();
        
		$result = $query->result();
		
		return $result;
	}
	
	
	public function get_student_exercise($sid, $eaid){
		$this->_db->select('EAID, SID, KID, ExerciseName, KnowledgeName, TID, Subject, PublishTime, ExpectTime, HasReviewed, ExerciseStatus, ExerciseNumber');
		$this->_db->from('StudentExerciseAssignment');
		$this->_db->where('EAID',$eaid);
		$this->_db->where('SID',$sid);
		$query = $this->_db->get();
        if($query->num_rows()<=0)
            return null;
		$result = $query->row();
		return $result;
	}

	//create exercise assignment
	public function create_exercise_assignment($kid, $tid, $ename, $kname, $esub, $ptime, $etime, $spid, $scid, $sdid, $ssid, $slevel, $sgrade, $sclass, $sgname, $edes, $enum, $econtent, $s_array){
		//create exercise assignment for both teacher and students
		//1. create a record for teacher; 2. create a record for all students in the group
		$TEAinfo = array(
				'KID' => $tid,
				'TID' => $kid,
				'ExerciseName' => $ename,
				'KnowledgeName' => $kname,
				'Subject' => $esub,
				'PublishTime' => $ptime,
				'ExpectTime' => $etime,
				'StudentPID' => $spid,
				'StudentCID' => $scid,
				'StudentDID' => $sdid,
				'StudentSID' => $ssid,
				'StudentLevel' => $slevel,
				'StudentGrade' => $sgrade,
				'StudentClass' => $sclass,
				'GroupName' => $sgname,
				'Description' => $edes,
				'ExerciseNumber' => $enum,
				'ExerciseContent' => $econtent
		);
		
		$this->_db->insert('TeacherExerciseAssignment', $TEAinfo);
		$eaid = $this->_db->insert_id();
		
		$SEAinfolist = array();
		foreach($s_array as $sid){
			$SEAinfolist[] = array(
					'SID' => $sid,
					'EAID' => $eaid,
					'ExerciseName' => $ename,
					'KID' => $kid,
					'TID' => $tid,
					'KnowledgeName' => $kname,
					'Subject' => $esub,
					'PublishTime' => $ptime,
					'ExpectTime' => $etime,
					'FinishTime' => 0,
					'HasReviewed' => 0,
					'ExerciseStatus' => 0,
					'ExerciseNumber' => 0
			);
		}
		
		$this->_db->insert_batch('StudentExerciseAssignment', $SEAinfolist);
		$eaxid = $this->_db->insert_id();
		
		return $eaxid;
	}
	
    //create exercise assignment
	public function add_exercise_assignment($kid, $kname, $tid, $ename, $gid, $sgname, $edes, $enum, $econtent, $etime=0){
        $this->load->model('TeacherManagement','teachermanagement');
        
        $teacher=$this->teachermanagement->get_teacher_by_teacher_id($tid);
        $esub=$teacher[0]->TeacherSubject;

        $ptime=date("Y-m-d H:i:s",time());
        if ($etime==0){
            $etime=date("Y-m-d H:i:s",time()+12*3600);    
        }

        $this->load->model('TSRelationshipMangement','tsrelation');
        $students=$this->tsrelation->get_students_by_group_id($gid);

        $this->load->model('StudentManagement','studentmanagement');
        $student=$this->studentmanagement->get_student_all_info_by_student_id($students[0]->SID);

        $spid=$student[0]->StudentPID;
        $scid=$student[0]->StudentCID;
        $sdid=$student[0]->StudentDID;
        $ssid=$student[0]->StudentSID;
        $slevel=$student[0]->StudentLevel;
        $sgrade=$student[0]->StudentGrade;
        $sclass=$student[0]->StudentClass;

		//create exercise assignment for both teacher and students
		//1. create a record for teacher; 2. create a record for all students in the group
		$TEAinfo = array(
				'KID' => $tid,
				'TID' => $kid,
				'ExerciseName' => $ename,
				'KnowledgeName' => $kname,
				'Subject' => $esub,
				'PublishTime' => $ptime,
				'ExpectTime' => $etime,
				'StudentPID' => $spid,
				'StudentCID' => $scid,
				'StudentDID' => $sdid,
				'StudentSID' => $ssid,
				'StudentLevel' => $slevel,
				'StudentGrade' => $sgrade,
				'StudentClass' => $sclass,
				'GroupName' => $sgname,
				'Description' => $edes,
				'ExerciseNumber' => $enum,
				'ExerciseContent' => $econtent
		);
		
		$this->_db->insert('TeacherExerciseAssignment', $TEAinfo);
		$eaid = $this->_db->insert_id();
		
		$SEAinfolist = array();
		foreach($students as $stud){
			$SEAinfolist[] = array(
					'SID' => $stud->SID,
					'EAID' => $eaid,
					'ExerciseName' => $ename,
					'KID' => $kid,
					'TID' => $tid,
					'KnowledgeName' => $kname,
					'Subject' => $esub,
					'PublishTime' => $ptime,
					'ExpectTime' => $etime,
					'FinishTime' => 0,
					'HasReviewed' => 0,
					'ExerciseStatus' => 0,
					'ExerciseNumber' => 0
			);
		}
		
		$this->_db->insert_batch('StudentExerciseAssignment', $SEAinfolist);
		$eaxid = $this->_db->insert_id();
		
		return $eaxid;
	}

	//update student exercise record
	public function update_student_exercise_doing_status($eaid, $sid, $enum, $nownum, $nowtime){
		//update student exercise doing status
		
		$seinfo = array();
		$seinfo['ExerciseStatus'] = $nownum;
		
		if($enum == $nownum){
			$seinfo['FinishTime'] = $nowtime;
		}
		
		$this->_db->where('EAID',$eaid);
		$this->_db->where('SID',$sid);
		$this->_db->update('StudentExerciseAssignment', $seinfo);
		
		return 0;
	}
	
	public function update_student_exercise_review_status($eaid, $sid, $hasreviewed){
		//update student exercise reviewed status
		
		$seinfo = array();
		$seinfo['HasReviewed'] = $hasreviewed;
		
		$this->_db->where('EAID',$eaid);
		$this->_db->where('SID',$sid);
		$this->_db->update('StudentExerciseAssignment', $seinfo);
		
		return 0;
	}
	
	//delete exercise assignment
	public function delete_exercise_assignment_by_teacher_id($tid){
		//delete all the exercise assignment info for a teacher and related students
		$this->_db->select('EAID');
		$this->_db->from('TeacherExerciseAssignment');
		$this->_db->where('TID',$tid);
		$query = $this->_db->get();
		$result = $query->result();
		
		$eaidlist = array();
		foreach($result as $row){
			$eaidvalue = $row['EAID'];
			$eaidlist[] = $eaidvalue;
		}
		
		$this->_db->where('TID',$tid);
		$this->_db->delete('TeacherExerciseAssignment');
		
		$this->_db->where_in('EAID',$eaidlist);
		$this->_db->delete('StudentExerciseAssignment');
		
		return 0;
	}
	
	public function delete_exercise_assignment_by_student_id($sid){
		//delete all the exercise assignment info for a student
		$this->_db->where('SID',$sid);
		$this->_db->delete('StudentExerciseAssignment');
		
		return 0;
	}
	
	public function delete_exercise_assignment_by_exercise_id($eaid){
		//delete all the exercise assignment info for a student
		$this->_db->where('EAID',$eaid);
		$this->_db->delete('TeacherExerciseAssignment');
		
		$this->_db->where('EAID',$eaid);
		$this->_db->delete('StudentExerciseAssignment');
		
		return 0;
	}
	
}

?>
