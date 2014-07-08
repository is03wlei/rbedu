<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ExerciseEdition extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
	//get exercise
	public function get_exercise_by_exercise_id($eid){
		//get exercise info by exercise id
		
		$this->_db->select('EID, KID, CreateTime, CreateTID, ExerciseLevel, ExerciseScore, ExerciseType, ExerciseTitle, ExerciseContent, AnswerNumber, AnswerContentPicture, AnswerContentCharacter');
		$this->_db->from('ExerciseInfo');
		$this->_db->where('EID',$eid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_exercise_by_id($eid){
		$this->_db->select('EID, KID, CreateTime, CreateTID, ExerciseLevel, ExerciseScore, ExerciseType, ExerciseTitle, ExerciseContent, AnswerNumber, AnswerContentPicture, AnswerContentCharacter');
		$this->_db->from('ExerciseInfo');
		$this->_db->where('EID',$eid);
		$query = $this->_db->get();
		$result = $query->row();
		return $result;
	}
    
	public function get_exercise_count_by_knowledge_id($kid){
		//get exercise count for one knowledge
		
		$this->_db->from('ExerciseInfo');
		$this->_db->where('KID',$kid);
		$resnum = $this->_db->count_all_results();
		
		return $resnum;
	}
	
    public function get_exercise_count($kid,$etype,$elevel){
		//get exercise count for one knowledge
	
		$this->_db->from('ExerciseInfo');
		$this->_db->where('KID',$kid);
        $this->_db->where('ExerciseType',$etype);
        $this->_db->where('ExerciseLevel',$elevel);
		$resnum = $this->_db->count_all_results();
		return $resnum;
	}

	public function get_exercise_by_limit_batch($kid, $etype, $elevel, $etarget, $rn, $pn){
		//get exercise pages for given kid and limitation
		
		$this->_db->select('EID, ExerciseLevel, ExerciseScore, ExerciseType, ExerciseTitle, ExerciseContent, AnswerNumber, AnswerContentPicture, AnswerContentCharacter');
		$this->_db->from('ExerciseInfo');
		$this->_db->where('KID',$kid);
		if($elevel > 0){
			$this->_db->where('ExerciseLevel',$elevel);
		}
		if(strlen($etype) > 0){
			$this->_db->where('ExerciseType',$etype);
		}
		if(strlen($etarget) > 0){
			$this->_db->where('ExerciseTarget',$etarget);
		}
		$this->_db->limit($rn,$pn);
		$query = $this->_db->get();
		$result = $query->result();
        return $result;
	}
	
	//create exercise
	public function create_exercise($kid, $ctid, $ctime, $elevel, $etype, $etarget, $escore, $etitle, $econtent, $eansnum, $eanspic, $eanschar){
		//create an exercise
		$exerciseinfo = array(
				'KID' => $kid,
				'CreateTime' => $ctime,
				'CreateTID' => $ctid,
				'ExerciseLevel' => $elevel,
				'ExerciseScore' => $escore,
				'ExerciseType' => $etype,
				'ExerciseTarget' => $etarget,
				'ExerciseTitle' => $etitle,
				'ExerciseContent' => $econtent,
				'AnswerNumber' => $eansnum,
				'AnswerContentPicture' => $eanspic,
				'AnswerContentCharacter' => $eanschar
		);
		
		$this->_db->insert('ExerciseInfo', $exerciseinfo);
		$eid = $this->_db->insert_id();
		return $eid;
	}

    public function update_exercise_picture_by_exercise_id($eid, $titlepicpath, $contentpicpath, $answerpicpath){

            $picdata = array(
                            'ExerciseTitle' => $titlepicpath,
                            'ExerciseContent' => $contentpicpath,
                            'AnswerContentPicture' => $answerpicpath
                            );
            $this->_db->where('EID',$eid);
            $this->_db->update('ExerciseInfo', $picdata);
            return 0;
    }
	
	//delete exercise
	public function delete_exercise_by_exercise_id($eid){
		//delete a exercise
		$this->_db->where('EID',$eid);
		$this->_db->delete('ExerciseInfo');
		
		return 0;
	}
	
	public function delete_exercise_by_knowledge_id($kid){
		//delete all exercise of a knowledge
		$this->_db->where('KID',$kid);
		$this->_db->delete('ExerciseInfo');
		
		return 0;
	}
	

}

?>
