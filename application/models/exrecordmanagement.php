<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ExRecordManagement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
	//get exercise record
	//public function read_exercise_record_by_
	public function read_exercise_result_by_student_ea($sid, $eaid){
		//read exercise info for one student's exercise assignment
		$this->_db->select('ERID, EAID, SID, EID, Status, Score, IsCorrect,StudentAnswerPicture, StudentAnswerCharacter,TimeConsume,StartTime,FinishTime,StudentQuestion,TeacherReview');
		$this->_db->from('ExerciseRecord');
		$this->_db->where('SID',$sid);
		$this->_db->where('EAID',$eaid);
		$this->_db->order_by('FinishTime','asc');
		
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;	
	}
	
	public function read_exercise_result_by_eaid_eid($eid, $eaid){
		//read exercise info for one student's exercise assignment
		$this->_db->select('ERID, EAID,SID, EID, Status, Score, IsCorrect,StudentAnswerPicture, StudentAnswerCharacter,TimeConsume,StartTime,FinishTime,StudentQuestion,TeacherReview');
		$this->_db->from('ExerciseRecord');
		$this->_db->where('EID',$eid);
		$this->_db->where('EAID',$eaid);
		$this->_db->order_by('FinishTime','asc');
		
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;	
	}

	public function read_exercise_result_by_student_ea_for_teacher_review($sid, $eaid, $eid_list){
		//read exercise info orderly for teacher review it one by one
		$status_array = array(1,3);
		$this->_db->select('ERID, EID, Score, IsCorrect,StudentAnswerPicture, StudentAnswerCharacter,TimeConsume,StartTime,FinishTime,StudentQuestion');
		$this->_db->from('ExerciseRecord');
		$this->_db->where('SID',$sid);
		$this->_db->where('EAID',$eaid);
		$this->_db->where_in('EID',$eid_list);
		$this->_db->where_in('Status',$status_array);
		$this->_db->order_by('FinishTime','asc');
		$this->_db->limit(1);
		
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function read_exerciser_question_by_student_ea($sid, $eaid){
		//read all question about one student in one exercise
		$this->_db->select('ERID, EID, EAID, SID, StudentQuestion');
		$this->_db->from('ExerciseRecord');
		$this->_db->where('SID',$sid);
		$this->_db->where('EAID',$eaid);
		
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
	
	public function read_exerciser_review_by_student_ea($sid, $eaid){
		//read all review about one student in one exercise
		$this->_db->select('ERID, EID, EAID, SID, StudentQuestion, TeacherReview');
		$this->_db->from('ExerciseRecord');
		$this->_db->where('SID',$sid);
		$this->_db->where('EAID',$eaid);
	
		$query = $this->_db->get();
		$result = $query->result();
	
		$treview = array();
		foreach($query as $row){
				
			$qstr = $row['StudentQuestion'];
			$rstr = $row['TeacherReview'];
			if(strlen($rstr) > 0){
				$questions[] = $row;
			}
		}
	
		return $treview;
	}
	
	public function read_exerciser_score_by_student_ea($sid, $eaid){
		//read all score and iscorrect about one student in one exercise
		$this->_db->select('ERID, EID, EAID, SID, Status, Score, IsCorrect, StudentQuestion, TeacherReview');
		$this->_db->from('ExerciseRecord');
		$this->_db->where('SID',$sid);
		$this->_db->where('EAID',$eaid);
	
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	//create exercise record
	public function create_exercise_record($eaid, $sid, $eid, $stime){
		//create a new exercise record
		$erinfo = array(
				'EAID' => $eaid,
				'SID' => $sid,
				'EID' => $eid,
				'Status' => 0,
				'Score' => 0,
				'IsCorrect' => 0,
				'StudentAnswerPicture' => "",
				'StudentAnswerCharacter' => "",
				'TimeConsume' => 0,
				'StartTime' => $stime,
				'FinishTime' => 0,
				'StudentQuestion' => "",
				'TeacherReview' => ""
		);
		
		$this->_db->insert('ExerciseRecord', $erinfo);
		$erid = $this->_db->insert_id();
		return $erid;
	}
	
    public function create_record($data){
        $this->_db->insert('ExerciseRecord', $data);

        $erid =  $this->_db->insert_id();
        return $erid;
    }
    
	//update exercise record
	public function update_exercise_record_by_student_commit($eaid, $sid, $eid, $sapic, $sachar, $sq, $ftime, $tcom){
		//update when student commit exercise answer
		$erinfo = array(
				'StudentAnswerPicture' => $sapic,
				'StudentAnswerCharacter' => $sachar,
				'FinishTime' => $ftime,
				'TimeConsume' => $tcom,
				'Status' => 1,
				'StudentQuestion' => $sq
		);
		
		$this->_db->where('EAID',$eaid);
		$this->_db->where('SID',$sid);
		$this->_db->where('EID',$eid);
		$this->_db->update('ExerciseRecord', $erinfo);
		
		return 0;
	}
	
	public function update_exercise_record_by_auto_review($eaid, $sid, $eid, $score, $iscorr, $sapic, $sachar, $sq, $ftime, $tcom){
		//update when student commit exercise answer and automatic checked
		$erinfo = array(
				'Score' => $score,
				'IsCorrect' => $iscorr,
				'StudentAnswerPicture' => $sapic,
				'StudentAnswerCharacter' => $sachar,
				'FinishTime' => $ftime,
				'TimeConsume' => $tcom,
				'Status' => 2,
				'StudentQuestion' => $sq
		);
		
		$this->_db->where('EAID',$eaid);
		$this->_db->where('SID',$sid);
		$this->_db->where('EID',$eid);
		$this->_db->update('ExerciseRecord', $erinfo);
		
		return 0;
	}
	
	public function update_exercise_record_by_student_review($eaid, $sid, $eid, $score, $iscorr, $sapic, $sachar, $sq, $ftime, $tcom){
		//update when student commit exercise answer and self checked
		$erinfo = array(
				'Score' => $score,
				'IsCorrect' => $iscorr,
				'StudentAnswerPicture' => $sapic,
				'StudentAnswerCharacter' => $sachar,
				'FinishTime' => $ftime,
				'TimeConsume' => $tcom,
				'Status' => 3,
				'StudentQuestion' => $sq
		);
		
		$this->_db->where('EAID',$eaid);
		$this->_db->where('SID',$sid);
		$this->_db->where('EID',$eid);
		$this->_db->update('ExerciseRecord', $erinfo);
		
		return 0;
	}
	
	public function update_exercise_record_by_teacher_review($eaid, $sid, $eid, $score, $iscorr, $treview){
		//update when teacher reviewed
		$erinfo = array(
				'Score' => $score,
				'IsCorrect' => $iscorr,
				'Status' => 4,
				'TeacherReview' => $treview
		);
		
		$this->_db->where('EAID',$eaid);
		$this->_db->where('SID',$sid);
		$this->_db->where('EID',$eid);
		$this->_db->update('ExerciseRecord', $erinfo);
		
		return 0;
	}
	
	public function update_teacher_review($erid, $score, $iscorr, $treview){
		//update when teacher reviewed
		$erinfo = array(
				'Score' => $score,
				'IsCorrect' => $iscorr,
				'Status' => 4,
				'TeacherReview' => $treview
		);
		
		$this->_db->where('ERID',$erid);
		$this->_db->update('ExerciseRecord', $erinfo);
		
		return 0;
	}
	//delete exercise record
	public function delete_exercise_record_by_student_ea($sid, $eaid){
		//delete all record for a student in one exercise
		$this->_db->where('EAID',$eaid);
		$this->_db->where('SID',$sid);
		$this->_db->delete('ExerciseRecord');
		
		return 0;
	}
	
	public function delete_exercise_record_by_ea($eaid){
		//delete all record for one exercise
		$this->_db->where('EAID',$eaid);
		$this->_db->delete('ExerciseRecord');
	
		return 0;
	}

	public function delete_exercise_record_by_student($sid){
		//delete all record for one student
		$this->_db->where('SID',$sid);
		$this->_db->delete('ExerciseRecord');
	
		return 0;
	}
	
	public function delete_exercise_record_by_exercise($eid){
		//delete all record for one exercise
		$this->_db->where('EID',$eid);
		$this->_db->delete('ExerciseRecord');
	
		return 0;
	}
	
	public function delete_exercise_record_by_record_id($erid){
		//delete all record for one exercise
		$this->_db->where('ERID',$erid);
		$this->_db->delete('ExerciseRecord');
	
		return 0;
	}
	
}

?>
