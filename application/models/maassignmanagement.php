<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MaAssignManagement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
	//get teacher material assignment 
	public function get_teacher_assignment_by_maid($maid){
		
		$this->_db->select('MAID, KID, TaskName, KnowledgeName, Subject, TID, PublicTime, ExpectTime, StudentPID, StudentCID, StudentDID, StudentSID, StudentLevel, StudentGrade, StudentClass, GroupName, Description, MaterialNumber, MaterialContent');
		
		$this->_db->from('TeacherMaterialAssignment');
		$this->_db->where('MAID',$maid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}

    public function get_teacher_assignment_by_kid_tid($kid, $tid){
    
        $this->_db->select('MAID');
		$this->_db->from('TeacherMaterialAssignment');
		$this->_db->where('KID',$kid);
		$this->_db->where('TID',$tid);
		$query = $this->_db->get();
		$result = $query->result();
    
        $retval = count($result);
        
        return $retval;
    }
	
	//get student material assignment 
	public function get_student_assignment_by_maid($sid, $maid){
		
		$this->_db->select('SID, MAID, KID, TaskName, KnowledgeName, Subject, TID, PublicTime, ExpectTime, FinishTime, MaterialStatus, MaterialNumber, MaterialResults');
		
		$this->_db->from('StudentMaterialAssignment');
        $this->_db->where('SID', $sid);
		$this->_db->where('MAID',$maid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
    
    public function get_student_assignment_by_sid($sid){
		
		$this->_db->select('SID, MAID, KID, TaskName, KnowledgeName, Subject, TID, PublicTime, ExpectTime, FinishTime, MaterialStatus, MaterialNumber, MaterialResults');
		
		$this->_db->from('StudentMaterialAssignment');
        $this->_db->where('SID', $sid);
        $statuslist = array(0,1);
		$this->_db->where_in('MaterialStatus',$statuslist);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}

    //update teacher material assignment
	public function update_teacher_assignment(){
	    $obj = $this;	
        unset($obj->is_load, $obj->maid);

		$this->_db->where('MAID',$this->maid);
		$this->_db->update('TeacherMaterialAssignment', $obj);

	}

    public function update_teacher_assignment_macontent($maid, $manum, $macontent){
    
        $upinfo = array();
        $upinfo['MaterialNumber'] = $manum;
        $upinfo['MaterialContent'] = $macontent;

        $this->_db->where('MAID',$maid);
        $this->_db->update('TeacherMaterialAssignment',$upinfo);

        return 0;
    }
	
    //update student material assignment
	public function update_student_assignment(){
	    $obj = $this;	
        unset($obj->is_load, $obj->maid, $obj->sid);

		$this->_db->where('MAID', $this->maid);
        $this->_db->where('SID', $this->sid);
		$this->_db->update('StudentMaterialAssignment', $obj);

	}


    public function update_student_material_status($sid, $maid, $taskstatus, $detailstatus){

            $statusdata = array(
                            'MaterialStatus' => $taskstatus,
                            'MaterialResults' => $detailstatus
                            );
            $this->_db->where('SID',$sid);
            $this->_db->where('MAID',$maid);
            $this->_db->update('StudentMaterialAssignment', $statusdata);

            return 0;
    }

	
	//create teacherMaterialAssignment 
	public function create_teacher_assignment($kid, $taskname, $kname, $subject, $tid, $ptime, $etime, $spid, $scid, $sdid, $ssid, $slevel, $sgrade, $sclass, $gname, $description, $maNum, $maContent){
		// 这个以后得改，这些构造都在controller里面构造，然后直接传进来一个array
		$TeacherAssignInfo = array(
				'KID' => $kid,
                'TaskName' => $taskname,
				'KnowledgeName' => $kname,
                'Subject' => $subject,
				'TID' => $tid,
                'PublicTime' => $ptime,
                'ExpectTime' => $etime,
                'StudentPID' => $spid,
                'StudentCID' => $scid,
                'StudentDID' => $sdid,
                'StudentSID' => $ssid,
				'StudentLevel' => $slevel,
                'StudentGrade' => $sgrade,
                'StudentClass' => $sclass,
                'GroupName' => $gname,
				'Description' => $description,
                'MaterialNumber' => $maNum,
                'MaterialContent' => $maContent
		);
		
		$this->_db->insert('TeacherMaterialAssignment', $TeacherAssignInfo);
		$maid = $this->_db->insert_id();
		return $maid;
	}
	
    //create student material assignment
	public function create_student_assignment($sid, $taskname, $kid, $maid, $kname, $subject, $tid, $ptime, $etime, $ftime, $maStatus, $maNum, $maResult){
		// 这个以后得改，这些构造都在controller里面构造，然后直接传进来一个array
		$StudentAssignInfo = array(
                'SID' => $sid,
                'TaskName' => $taskname,
				'KID' => $kid,
                'MAID' => $maid,
				'KnowledgeName' => $kname,
                'Subject' => $subject,
				'TID' => $tid,
                'PublicTime' => $ptime,
                'ExpectTime' => $etime,
                'FinishTime' => $ftime,
				'MaterialStatus' => $maStatus,
                'MaterialNumber' => $maNum,
                'MaterialResults' => $maResult
		);
		
		$this->_db->insert('StudentMaterialAssignment', $StudentAssignInfo);
		//$sid = $this->_db->insert_id();
		///return $sid;
	}

	//delete Teacher material assignment 
	public function delete_teacher_assign_by_maid($maid){
		$this->_db->where('MAID',$maid);
		$this->_db->delete('TeacherMaterialAssignment');
		
		return 0;
	}

	//delete student material assignment 
	public function delete_student_assign_by_sid_and_maid($sid, $maid){
		$this->_db->where('SID',$sid);
		$this->_db->where('MAID',$maid);
		$this->_db->delete('StudentMaterialAssignment');
		
		return 0;
	}
	
	

}

?>
