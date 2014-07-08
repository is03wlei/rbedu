<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file application/controllers/ExercisEdictContorl.php
 * @author yangfan(yangfan@sichuancjc.com)
 * @date 2014/06/29 14:57:07
 * @version : 1.0.0
 * @brief
 *
 **/


class ExerciseEditControl extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}


    public function index(){
       $this->show_knowledge_list(); 
    }

	
	//output knowledge list
	public function show_knowledge_list(){
		
        $tid = 1;
        
        $this->load->model('TeacherManagement','TeacherManagement');
		
		$teacher=$this->TeacherManagement->get_teacher_by_teacher_id($tid);
        $tsub = $teacher[0]->TeacherSubject;
		$tlevel = intval($teacher[0]->TeacherLevel);
		
		$this->load->model('KnowledgeMangement','KnowledgeMangement');
		$knowledgelist = $this->KnowledgeMangement->get_common_knowledge_by_subject($tsub,$tlevel);
		
		$retlist = array();
		foreach($knowledgelist as $row){
		    $kname =  $row->KnowledgeName;
            $kid =  $row->KID;
            $retklist[] = array(
                    'label' => $kname,
                    'id' => $kid,
                    'children' => array()
            );
        }
		
		$data['tree'] = json_encode($retklist);
        $data['sitebar'] = 'exercise';
		$this->cismarty->assign($data);
        $this->cismarty->display('exercise-manage.tpl');
	}
	
    
    //get exercise commit and insert to database
	public function exercise_commit(){
        
        $tid = 1;
		$kid = intval($this->input->post('kid'));
		
        $exerciseType = $this->input->post('exercisetype');
        $exerciseLevel = intval($this->input->post('exerciselevel'));
		if($this->input->post('answernumber')!=FALSE){
			$answerNumber = intval($this->input->post('answernumber'));
		}else{
			$answerNumber = 0;
		}
		
        if($this->input->post('answercontentcharacter')!=FALSE){
			$answerContentCharacter = $this->input->post('answercontentcharacter');
		}else{
			$answerContentCharacter = "";
		}
	

		//check data
		$fret = $this->_exercise_parameter_validataion($exerciseType, $answerNumber, $answerContentCharacter);
		if(0>$fret){
            $retarray = array(
                        "errorno" => 1
                        );
            $retval = json_encode($retarray);
              echo $retval;        
			return -1;
		}
		
		//debug variable
		//echo $kid."\n".$tid."\n".$exerciseLevel."\n".$exerciseType."\n".$answerNumber."\n".$answerContentCharacter;
		//end debug
		$this->load->model('ExerciseEdition','ExerciseEdition');
		
		//insert database first
        date_default_timezone_set('Asia/Shanghai');
		$exerciseCreateTime = date("Y-m-d H:i:s",time());
		$eid = $this->ExerciseEdition->create_exercise($kid, $tid, $exerciseCreateTime, $exerciseLevel, $exerciseType, "0", 5, "default.jpg", "default.jpg", $answerNumber, "default.jpg", $answerContentCharacter);
		
		//get picture path
		$pichomepath = '/home/work/YfWebSource/upload/excercise_pic';
		$picfilepath = $this->_get_picture_path($pichomepath, $eid);
		
		$titlefilename = "E".$eid."_title.jpg";
		$contentfilename = "E".$eid."_content.jpg";
		$answerfilename = "E".$eid."_answer.jpg";

		//do upload picture
		$exerciseTitlePath = $pichomepath."/default_title.jpg";
		$exerciseContentPath = $pichomepath."/default_content.jpg";;
		$answerContentPicturePath = $pichomepath."/default_answer.jpg";;
	
        if($this->input->post('exercisetitlepic') != FALSE){
                $etpicpath = '/home/work/YfWebSource/upload/'.$this->input->post('exercisetitlepic');
                rename($etpicpath, $picfilepath."/".$titlefilename);
                $exerciseTitlePath = $picfilepath."/".$titlefilename;
        }

        if($this->input->post('exercisecontentpic') != FALSE){
                $ecpicpath = '/home/work/YfWebSource/upload/'.$this->input->post('exercisecontentpic');
                rename($ecpicpath, $picfilepath."/".$contentfilename);
                $exerciseContentPath = $picfilepath."/".$contentfilename;
        }

        if($this->input->post('exerciseanswerpic') != FALSE){
                $eapicpath = '/home/work/YfWebSource/upload/'.$this->input->post('exerciseanswerpic');
                rename($eapicpath, $picfilepath."/".$answerfilename);
                $answerContentPicturePath = $picfilepath."/".$answerfilename;
        }

		//update picture info
		$this->ExerciseEdition->update_exercise_picture_by_exercise_id($eid, $exerciseTitlePath, $exerciseContentPath, $answerContentPicturePath);
		
        //
        $retarray = array(
                        "errorno" => 0
                        );
        $retval = json_encode($retarray);
        echo $retval;        

		return 0;
	}
	
    
    //parameters validation
	private function _exercise_parameter_validataion($exerciseType, $answerNumber, $answerContentCharacter){
		
		$ret = 0;
		if($exerciseType == "sc"){
			if($answerNumber == 0){
				$ret = -1;
			}
			if(strlen($answerContentCharacter) == 0){
				$ret = -1;
			}
		}else if($exerciseType == "jq"){
			if($answerNumber == 0){
				$ret = -1;
			}
			if(strlen($answerContentCharacter) == 0){
				$ret = -1;
			}
		}else if($exerciseType == "mc"){
			if($answerNumber == 0){
				$ret = -1;
			}
			if(strlen($answerContentCharacter) == 0){
				$ret = -1;
			}
		}else if($exerciseType == "bq"){
			if($answerNumber == 0){
				$ret = -1;
			}
		}else{
			if($answerNumber > 0){
				$ret = -1;
			}
			if(strlen($answerContentCharacter) > 0){
				$ret = -1;
			}
		}
		
		return $ret;
	}
	
	
	
	
	//upload picture, get $key from $_FILES
	private function _upload_picture($frontname, $picfilepath, $picfilename){
		
		$ret = 0;
		
		$upconfig['upload_path'] = $picfilepath;
		$upconfig['allowed_types'] = 'gif|jpg|png';
		$upconfig['file_name'] = $picfilename;
		$upconfig['overwrite'] = TRUE;
		$upconfig['max_size'] = '1000';
		$upconfig['max_width']  = '1024';
		$upconfig['max_height']  = '768';
		
		$this->load->library('upload');
		$this->upload->initialize($upconfig);
		
		$fret = $this->upload->do_upload($frontname);
		if($fret == FALSE){
			$ret = -1;
		}
		return $ret;
	}
	
	//give picture path
	private function _get_picture_path($pichomepath, $picid){
		
		$picfilepath = "";
		$idval = intval($picid);
		
		$l1_id = intval($idval/(1024*1024));
		$l2_id = intval($idval/1024);
		
		$picfilepath = $pichomepath.'/'.$l1_id.'/'.$l2_id;
		if(!file_exists($picfilepath)){	
            mkdir($picfilepath,0777,TRUE);
		}
		
		return $picfilepath;
	}
	
}

?>
