<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file application/controllers/MaterialAssignController.php
 * @author yangfan(yangfan@sichuancjc.com)
 * @date 2014/07/01 14:57:07
 * @version : 1.0.0
 * @brief
 *
 **/

class AppMaterialTask extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}
	
	//show task list
	public function show_task_list(){
		
		$sid = intval($this->input->post('studentid'));
        $sid = 1;
		$this->load->model('MaAssignManagement','MaAssignManagement');
		$stasklist = $this->MaAssignManagement->get_student_assignment_by_sid($sid);
		$this->load->model('TeacherManagement','TeacherManagement');
		
		$retlist = array();
		
		foreach($stasklist as $row){
			
			$maid = $row->MAID;
			$subject = $row->Subject;
			$tid = $row->TID;
			$taskName = $row->TaskName;
			$materalNumber = intval($row->MaterialNumber);
			$finishTime = $row->ExpectTime;
			$tstatus = intval($row->MaterialStatus);
			
			$tinfo = $this->TeacherManagement->get_teacher_by_teacher_id($tid);
			$teacherName = $tinfo[0]->TeacherName;
			
			$retlist[] = array(
				"subject" => $subject,
				"teacherName" => $teacherName,
				"taskName" => $taskName,
				"materalNumber" => $materalNumber,
				"finishTime" => $finishTime,
				"taskStatus" => $tstatus,
				"taskid" => $maid
			);
		}
        
        $retarray = array(
                        'error' => 0,
                        'content' => $retlist,
                        );

		$retstr = json_encode($retarray);
        $this->output->set_content_type('application/json')->set_output($retstr);
		
		return 0;
	}
	
	
	//show detail list
	public function show_task_detail(){
	
		$sid = intval($this->input->post('studentid'));
		$maid = intval($this->input->post('taskid'));
        $sid = 1;
        $maid = 1;

		$this->load->model('MaAssignManagement','MaAssignManagement');
		$taskinfo = $this->MaAssignManagement->get_student_assignment_by_maid($sid, $maid);
		$knowledgeName = $taskinfo[0]->KnowledgeName;
		
		$this->load->model('VideoEdition','VideoEdition');
		$this->load->model('DocumentEdition','DocumentEdition');
		
		//id,type,0,0,0,0,0), id, type, status, firsttime, finishtime, acttime£¬ ask_id
		$retlist = array();
		$sourcelist = json_decode($taskinfo[0]->MaterialResults);
		foreach($sourcelist as $row){
			
			$resourceid = $row[0];
			$resourcetype = $row[1];
			$learnstatus = $row[2];
			
			if($resourcetype == "video"){
				$videoinfo = $this->VideoEdition->get_video_by_video_id($resourceid);
				$itemname = $videoinfo[0]->Title;
				$itemtype = $videoinfo[0]->VideoType;
				$itemurl = $videoinfo[0]->SourceURI;
			}else if($resourcetype == "doc"){
				$docinfo = $this->DocumentEdition->get_document_by_document_id($resourceid);
				$itemname = $docinfo[0]->Title;
				$itemtype = $docinfo[0]->DocumentType;
				$itemurl = $docinfo[0]->SourceURI;
			}
			
			$retlist[] = array(
				"materialname" => $itemname,
				"materialtype" => $resourcetype,
				"materialstatus" => $learnstatus,
				"materialid" => $resourceid,
				"materialurl" => 'http://101.69.182.26:8080/'.$itemurl,
				"materialplaytype" => $itemtype
			);
			
		}
		
		$retarray = array(
            'error' => 0,
            'content' => array(
			    'knowledgename' => $knowledgeName,
			    'materiallist' => $retlist
               )
		);
		
		$retstr = json_encode($retarray);
		$this->output->set_content_type('application/json')->set_output($retstr);
        return 0;
	}
	
	//make record
	public function record_material_learning_status(){
		
		//studentId, Taskid , materialType, materialId, materialStatus
		$sid = intval($this->input->post('studentid'));
		$maid = intval($this->input->post('taskid'));
		$matype = $this->input->post('materialtype');
		$mid = intval($this->input->post('materialid'));
		$mstatus = intval($this->input->post('materialstatus'));
        
        $sid = 1;
        $maid = 1;
        $matype = 'video';
        $mid = 57;
        $mstatus = 2;

        date_default_timezone_set('Asia/Shanghai');
		$starttime = date("Y-m-d H:i:s",time()-420);
		$finishtime = date("Y-m-d H:i:s",time());

		$this->load->model('MaAssignManagement','MaAssignManagement');
        $taskinfo = $this->MaAssignManagement->get_student_assignment_by_maid($sid, $maid);
        $sourcelist = json_decode($taskinfo[0]->MaterialResults);

        $alltaskdone = TRUE;
        $hastaskstart = FALSE;
        foreach($sourcelist as &$row){
        
            $resourceid = $row[0];
			$resourcetype = $row[1];
            if($mid == $resourceid && $matype == $resourcetype){
                $row[2] = $mstatus; 
                if($row[2] != 2){
                    $alltaskdone = FALSE;
                }
                if($row[2] != 0){
                    $hastaskstart = TRUE;
                }
                

            }else{
                if($row[2]!=2){
                    $alltaskdone = FALSE;
                }

                if($row[2] != 0){
                    $hastaskstart = TRUE;
                }

            }

        }

        $alldoneval = 0;
        if($alltaskdone == TRUE){
            $alldoneval = 2;
        }else if($hastaskstart == TRUE){
            $alldoneval = 1;
        }
       
        //update student material info data
        $this->MaAssignManagement->update_student_material_status($sid, $maid, $alldoneval, json_encode($sourcelist));
        
        $retarray = array(
               'error' => 0,
               'content' => array()
        );
		
		$retstr = json_encode($retarray);
		$this->output->set_content_type('application/json')->set_output($retstr);

		return 0;
	}
}


?>
