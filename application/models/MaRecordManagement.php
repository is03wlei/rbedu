<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MaRecordManagement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db->= $this->load->database('rbedu', TRUE);
		$this->is_load = false;
	}
	
	//get material record
	public function read_material_result_by_student_ma($sid, $maid){
		//read material info for one student's material assignment
		$this->_db->select('MRID, MAID, SID, MID, MType, MaterialLevel, MaterialType,TimeConsume,StartTime,FinishTime,StudentQuestion,TeacherAnswer');
		$this->_db->from('MaterialRecord');
		$this->_db->where('SID',$sid);
		$this->_db->where('MAID',$maid);
		$this->_db->order_by('FinishTime','asc');
		
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;	
	}
	
	public function read_materialr_question_by_student_ma($sid, $maid){
		//read all question about one student in one material
		$this->_db->select('MRID, MID, MAID, SID, StudentQuestion');
		$this->_db->from('MaterialRecord');
		$this->_db->where('SID',$sid);
		$this->_db->where('MAID',$maid);
		
		$query = $this->_db->get();
		$result = $query->result();
		
		$questions = array();
		foreach($query as $row){
			
			$qstr = $row['StudentQuestion'];
			if(strlen($qstr) > 0){
				$questions[] = $row;
			}
		}
		
		return $questions;
	}
	
	public function read_materialr_review_by_student_ea($sid, $maid){
		//read all review about one student in one material
		$this->_db->select('MRID, MID, MAID, SID, StudentQuestion, TeacherAnswer');
		$this->_db->from('MaterialRecord');
		$this->_db->where('SID',$sid);
		$this->_db->where('MAID',$maid);
	
		$query = $this->_db->get();
		$result = $query->result();
	
		$quesAns = array();
		foreach($query as $row){
				
			$qstr = $row['StudentQuestion'];
			$rstr = $row['TeacherAnswer'];
			if(strlen($rstr) > 0){
				$quesAns[] = $row;
			}
		}
	
		return $quesAns;
	}
	
	//create material record
	public function create_material_record($maid, $sid, $mid, $stime){
		//create a new material record
		$mrinfo = array(
				'MAID' => $maid,
				'SID' => $sid,
				'MID' => $mid,
                'MType' => $mtype,
                'MaterialLevel' => $materialLevel,
                'MaterialType' => $materialType,
				'TimeConsume' => 0,
				'StartTime' => $stime,
				'FinishTime' => 0,
				'StudentQuestion' => "",
				'TeacherAnswer' => ""
		);
		
		$this->_db->insert('MaterialRecord', $mrinfo);
		$mrid = $this->_db->insert_id();
		return $mrid;
	}
	
	//update material record
	public function update_material_record_by_student_commit($maid, $sid, $mid,  $sq, $ftime, $tcom){
		//update when student commit material answer
		$mrinfo = array(
				//'Status' => 1,
				'FinishTime' => $ftime,
				'TimeConsume' => $tcom,
				'StudentQuestion' => $sq
		);
		
		$this->_db->where('MAID',$maid);
		$this->_db->where('SID',$sid);
		$this->_db->where('MID',$mid);
		$this->_db->update('MaterialRecord', $mrinfo);
		
		return 0;
	}
	
	public function update_material_record_by_auto_view($maid, $sid, $mid, $sq, $ftime, $tcom){
		//update when student commit material answer and automatic checked
		$mrinfo = array(
				//'Status' => 2,
				'FinishTime' => $ftime,
				'TimeConsume' => $tcom,
				'StudentQuestion' => $sq
		);
		
		$this->_db->where('MAID',$maid);
		$this->_db->where('SID',$sid);
		$this->_db->where('MID',$mid);
		$this->_db->update('MaterialRecord', $mrinfo);
		
		return 0;
	}
	
	public function update_material_record_by_student_review($maid, $sid, $mid, $sq, $ftime, $tcom){
		//update when student commit material answer and self checked
		$mrinfo = array(
				'FinishTime' => $ftime,
				'TimeConsume' => $tcom,
				//'Status' => 3,
				'StudentQuestion' => $sq
		);
		
		$this->_db->where('MAID',$maid);
		$this->_db->where('SID',$sid);
		$this->_db->where('MID',$Mid);
		$this->_db->update('MaterialRecord', $mrinfo);
		
		return 0;
	}
	
	public function update_material_record_by_teacher_answer($maid, $sid, $mid, $teacherAnswer){
		//update when teacher reviewed
		$mrinfo = array(
				//'Status' => 4,
				'TeacherAnswer' => $teacherAnswer
		);
		
		$this->_db->where('MAID',$maid);
		$this->_db->where('SID',$sid);
		$this->_db->where('MID',$mid);
		$this->_db->update('MaterialRecord', $mrinfo);
		
		return 0;
	}
	
	//delete material record
	public function delete_material_record_by_student_ma($sid, $maid){
		//delete all record for a student in one material
		$this->_db->where('MAID',$maid);
		$this->_db->where('SID',$sid);
		$this->_db->delete('MaterialRecord');
		
		return 0;
	}
	
	public function delete_material_record_by_mr($mrid){
		//delete all record for one material
		$this->_db->where('MRID',$mrid);
		$this->_db->delete('MaterialRecord');
	
		return 0;
	}
   
	public function delete_material_record_by_ma($maid){
		//delete all record for one material
		$this->_db->where('MAID',$maid);
		$this->_db->delete('MaterialRecord');
	
		return 0;
	}

	public function delete_material_record_by_student($sid){
		//delete all record for one student
		$this->_db->where('SID',$sid);
		$this->_db->delete('MaterialRecord');
	
		return 0;
	}
	
	public function delete_material_record_by_material($mid){
		//delete all record for one material
		$this->_db->where('MID',$mid);
		$this->_db->delete('MaterialRecord');
	
		return 0;
	}
	
}

?>
