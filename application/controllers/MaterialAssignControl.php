<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file application/controllers/MaterialAssignController.php
 * @author yangfan(yangfan@sichuancjc.com)
 * @date 2014/07/01 14:57:07
 * @version : 1.0.0
 * @brief
 *
 **/

class MaterialAssignControl extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}


    public function index(){
        $this->show_teacher_group();
    }


	//first in the page
	public function show_teacher_group(){
		
		//set cookie first
		$tid = 1;
		$mycookie = array(
			'tid' => $tid,
			'taskid' => 0,
			'kid' => 0,
			'groupid' => 0,
		);
			
		$cvalue =  json_encode($mycookie);
		setcookie('rbedu_cookie', $cvalue, time()+86400);
		
		//get teacher all group
		$this->load->model('TeacherManagement','TeacherManagement');
		$teacher=$this->TeacherManagement->get_teacher_by_teacher_id($tid);
		$tsub = $teacher[0]->TeacherSubject;
		$tlevel = intval($teacher[0]->TeacherLevel);
		
		$tgroup = $this->TeacherManagement->get_group_by_teacher_id_v2($tid);
		$grouptree = array();
		
		foreach($tgroup as $row){
			$tgrade = 2014 - intval($row->ClassGrade) + 1;
			$tclass = $row->TeacherClass;
			$tgname = $row->GroupName;
			$tgid = $row->GID;
			
			if(!isset($grouptree[$tgrade])){
				$grouptree[$tgrade] = array();
				$grouptree[$tgrade][$tclass] = array();
				$grouptree[$tgrade][$tclass][$tgid] = $tgname;
			}else{
				if(!isset($grouptree[$tgrade][$tclass])){
					$grouptree[$tgrade][$tclass] = array();
					$grouptree[$tgrade][$tclass][$tgid] = $tgname;
				}else{
					$grouptree[$tgrade][$tclass][$tgid] = $tgname;
				}
			}
		}

//		echo json_encode($grouptree);
		
		//get teacher all knowledge
		$this->load->model('KnowledgeMangement','KnowledgeMangement');
		$knowledgelist = $this->KnowledgeMangement->get_common_knowledge_by_subject($tsub,$tlevel);
		$retklist = array();
		$maxgradeval = 0;
		foreach($knowledgelist as $row){
			$kname =  $row->KnowledgeName;
			$kgrade =  $row->KnowledgeGrade;
			$kid =  $row->KID;
			$kpid = $row->KnowledgeParentID;
			$klevel = $row->KnowledgeTreeLevel;
			
			if($kgrade > $maxgradeval){
				$maxgradeval = $kgrade;
			}
		}
		
		$i = 0;
		while($i < $maxgradeval){
			$retklist[] = array();
			$i += 1;
		}
		
		
		foreach($knowledgelist as $row){
			$kname =  $row->KnowledgeName;
			$kgrade =  $row->KnowledgeGrade;
			$kid =  $row->KID;
			$kpid = $row->KnowledgeParentID;
			$klevel = $row->KnowledgeTreeLevel;
			
			$retklist[$kgrade-1][] = array(
                    'label' => $kname,
                    'id' => $kid,
                    'children' => array()
             );
		}
	
   //		echo json_encode($retklist[1]);
		$retval = array(
				"grouptree" =>json_encode($grouptree), 
				"knowledgelist" => json_encode($retklist)
		);
        $retval['sitebar'] = 'material-publish';
		
	    $this->cismarty->assign($retval);
		$this->cismarty->display('publish-init.tpl');
		
		return 0;
	}
	
	
	//get student list by groupid
	public function show_student_list(){
		
		$cookieval = json_decode($_COOKIE['rbedu_cookie']);
	
		$tgid = intval($this->input->get('teacherGroupId',TRUE));
//        $tgid = 1;
        $cookieval->groupid = $tgid;
		setcookie('rbedu_cookie', json_encode($cookieval), time()+86400);
		
 //       echo json_encode($cookieval);
		
		$this->load->model('TSRelationshipMangement','TSRelationshipMangement');
		$tslist = $this->TSRelationshipMangement->get_students_by_group_id($tgid);
		
		//output
		$retslist = array();
		foreach($tslist as $row){
			$retslist[] = $row->StudentName;
		}
		
		$this->cismarty->assign("studentlist", $retslist);
		$this->cismarty->display('publish-init-part.tpl');
		
		return 0;
	}
	
	//get resource list by tkid
	public function show_resource_list(){
		
		$cookieinfo = json_decode($_COOKIE['rbedu_cookie']);
		$isfirst = FALSE;
		
		$tid = $cookieinfo->tid;
	    $hasgval = FALSE;
        $haspval = FALSE;

		if($this->input->post('kid')==FALSE){
			$kid = $cookieinfo->kid;
		}else{
			$kid = intval($this->input->post('kid'));
            //test
			$cookieinfo->kid = $kid;
            $haspval = TRUE;
		}
		
		if($this->input->post('groupid')==FALSE){
			$groupid = $cookieinfo->groupid;
		}else{
			$groupid = intval($this->input->post('groupid'));
			$cookieinfo->groupId = $groupid;
            $haspval = TRUE;
		}
		
		if($this->input->get('type',TRUE)==FALSE){
			$resourcetype = "video";
		}else{
			$resourcetype = $this->input->get('type');
            $hasgval = TRUE;
		}
		
		if($this->input->get('pagenumber',TRUE)==FALSE){
			$pagenumber = 0;
		}else{
			$pagenumber = $this->input->get('pagenumber');
            $hasgval = TRUE;
		}

        if($hasgval == TRUE){
            $isfirst = FALSE;
        }else{

            if($haspval == TRUE){
                $isfirst = TRUE;
            }else{
                $isfirst = FALSE;
            }
        }
	
		$rn = 10;
	
        //echo $kid."\t".$groupid."\t".$resourcetype."\t".$pagenumber;

		$resultlist = array();

		if($resourcetype == "video"){
			$resultlist = $this->_get_video_list($kid, $rn, $pagenumber);
		}else if($resourcetype == "doc"){
			$resultlist = $this->_get_document_list($kid, $rn, $pagenumber);
		}
		
		//get task id
		if($isfirst == FALSE){
			$taskid = $cookieinfo->taskid;
			$selcontent = $this->_get_selected_content($taskid);
		   	
			foreach($resultlist['itemlist'] as &$row){
				$tkey = $row['ItemId']."_".$row['SourceType'];
				if(isset($selcontent[$tkey])){
					$row['IsSelectItem'] = 1; 
				}
			}
		}else{
			$taskid = $this->_create_material_assignment_first($tid, $kid, $groupid);
            $cookieinfo->taskid = $taskid;
		    setcookie('rbedu_cookie', json_encode($cookieinfo), time()+86400);
            redirect('http://101.69.182.26:8080/MaterialAssignControl/show_resource_list?type=video', 'refresh');
		}
	
        $resultlist['sitebar'] = 'material-publish';
        $resultlist['resourcetype'] = $resourcetype;
		//output
		$this->cismarty->assign($resultlist);
		$this->cismarty->display('publish-select.tpl');

		return 0;
	}
	
	//change material selection
	public function material_selection(){
		
		$cookieinfo = json_decode($_COOKIE['rbedu_cookie']);
		$maid = $cookieinfo->taskid;

        //echo json_encode($cookieinfo);
		
		$itemid = 0;
		$resourcetype = "";
		$isadd = 0;
		
		if($this->input->post('itemid')==FALSE){
			return 0;
		}else{
			$itemid = intval($this->input->post('itemid'));
		}
		
		if($this->input->post('resourcetype')==FALSE){
			return 0;
		}else{
			$resourcetype = $this->input->post('resourcetype');
		}
		
		if($this->input->post('isadd')==FALSE){
			return 0;
		}else{
		    if($this->input->post('isadd') == "true"){
                $isadd = 1;
            }else{
                $isadd = 0;
            }
		}


		$this->load->model('MaAssignManagement','MaAssignManagement');
		$mta = $this->MaAssignManagement->get_teacher_assignment_by_maid($maid);
        //echo json_encode($mta);
		$matcontent = json_decode($mta[0]->MaterialContent);
		
		if($isadd == 1){
            $hasadd = FALSE;
            foreach($matcontent as $row){
                $titemid = $row[0];
                $tresourcetype = $row[1];
                if($titemid == $itemid && $tresourcetype == $resourcetype){
                        $hasadd = TRUE;
                        break;
                }
            }
            if($hasadd == FALSE){
			    $matcontent[] = array($itemid, $resourcetype);
                // echo json_encode($matcontent);
			    $this->MaAssignManagement->update_teacher_assignment_macontent($maid, count($matcontent), json_encode($matcontent));
            }
		}else{
			$newmatcontent = array();
			foreach($matcontent as $row){
				$titemid = $row[0];
				$tresourcetype = $row[1];
				
				if($titemid == $itemid && $tresourcetype == $resourcetype){
					continue;
				}else{
					$newmatcontent[] = array($titemid, $tresourcetype);
				}
			}
			
          //  echo json_encode($newmatcontent);
			$this->MaAssignManagement->update_teacher_assignment_macontent($maid, count($newmatcontent), json_encode($newmatcontent));
		}

        $retarray = array(
                        'status' => 0
                        );

        echo json_encode($retarray);
		return 0;
	}
	
	//show publish info
	public function publish_info_confirm(){
		
		$cookieinfo = json_decode($_COOKIE['rbedu_cookie']);
		$maid = $cookieinfo->taskid;
		$kid = $cookieinfo->kid;
		$groupid = $cookieinfo->groupid;
		$tid = $cookieinfo->tid;
		
		$this->load->model('MaAssignManagement','MaAssignManagement');
		$mta = $this->MaAssignManagement->get_teacher_assignment_by_maid($maid);

		$kname = $mta[0]->KnowledgeName;
		$matcontent = json_decode($mta[0]->MaterialContent);
		
		$this->load->model('TeacherManagement','TeacherManagement');
		$ginfo = $this->TeacherManagement->get_group_by_group_id($groupid);
		$gname = $ginfo[0]->GroupName;
		
		$videocount = 0;
		$doccount = 0;
		foreach($matcontent as $row){
			if($row[1] == "video"){
				$videocount += 1;
			}else if($row[1] == "doc"){
				$doccount += 1;
			}
		}
		
		$retval = array(
			'KnowledgeName' => $kname,
			'GroupName' => $gname,
			'VideoCount' => $videocount,
			'DocumentCount' => $doccount,
            'sitebar' => 'material-publish'
		);
		
//        echo $retvalstr;
	   // echo $retvalstr;

		$this->cismarty->assign($retval);
		$this->cismarty->display('publish-confirm.tpl');
		
		return 0;
	}
	

	//publish material assignment
	public function publish_material_assignment(){
	
		$cookieinfo = json_decode($_COOKIE['rbedu_cookie']);
		$maid = $cookieinfo->taskid;
		$kid = $cookieinfo->kid;
		$groupid = $cookieinfo->groupid;
		$tid = $cookieinfo->tid;

		$this->load->model('MaAssignManagement','MaAssignManagement');
		$mta = $this->MaAssignManagement->get_teacher_assignment_by_maid($maid);
		$kname = $mta[0]->KnowledgeName;
        $taskname = $mta[0]->TaskName;
		$subject = $mta[0]->Subject;
		$ptime = $mta[0]->PublicTime;
		$etime = $mta[0]->ExpectTime;
		$ftime = $mta[0]->ExpectTime;
		$maNum = $mta[0]->MaterialNumber;
		$maStatus = 0;
		$matcontent = json_decode($mta[0]->MaterialContent);
		$maResult = array();
		foreach($matcontent as $row){	//id:type
			$maResult[] = array($row[0],$row[1],0,0,0,0,0);
		}
	
		$this->load->model('TSRelationshipMangement','TSRelationshipMangement');
		$studentlist = $this->TSRelationshipMangement->get_students_by_group_id($groupid);
	
		if(count($studentlist) == 0){
			return 0;
		}
	
		//create material assignment for every student
		foreach($studentlist as $row){
			$sid = $row->SID;
			$this->MaAssignManagement->create_student_assignment($sid, $taskname, $kid, $maid, $kname, $subject, $tid, $ptime, $etime, $ftime, $maStatus, $maNum, json_encode($maResult));
		}

        redirect('http://101.69.182.26:8080/MaterialAssignControl/show_teacher_group','refresh');
		return 0;
	}
	
	
	
	public function _get_selected_content($maid){
		
		$this->load->model('MaAssignManagement','MaAssignManagement');
		$mta = $this->MaAssignManagement->get_teacher_assignment_by_maid($maid);
		$matcontent = json_decode($mta[0]->MaterialContent);
		
		$retdict = array();
		foreach($matcontent as $row){
			$itemid = $row[0];
			$itemtype = $row[1];
			$keystr = $itemid."_".$itemtype;
			$retdict[$keystr] = 1;
		}
	    
		return $retdict;
	}
	
	//create material assignment first
	private function _create_material_assignment_first($tid, $tkid, $tgid){
		
		$tmlist = array();	//[[id1,type1],[id2,type2]]
		
		$materialNumber = count($tmlist);
		$materialContent = json_encode($tmlist);
		//echo $materialContent;
        $description = "";
		date_default_timezone_set('Asia/Shanghai');
        $publishTime = date("Y-m-d H:i:s",time());
	    $expectTime = date("Y-m-d H:i:s",time()+43200);	//remind : need +12 hour

		//get student id list and student info : SID, StudentName
		$this->load->model('TSRelationshipMangement','TSRelationshipMangement');
		$ssel = $this->TSRelationshipMangement->get_students_by_group_id($tgid);
		$sid = intval($ssel[0]->SID);

        $this->load->model('StudentManagement','StudentManagement');
        $sinfo = $this->StudentManagement->get_student_all_info_by_student_id($sid);

		$studentPID = intval($sinfo[0]->StudentPID);
		$studentCID = intval($sinfo[0]->StudentCID);
		$studentDID = intval($sinfo[0]->StudentDID);
		$studentSID = intval($sinfo[0]->StudentSID);
		$studentLevel = intval($sinfo[0]->StudentLevel);
		$studentGrade = intval($sinfo[0]->StudentGrade);
		$studentClass = $sinfo[0]->StudentClass;
		
		//get group info :'TID, TeacherClass, GroupName, IsDefaultClass'
		$this->load->model('TeacherManagement','TeacherManagement');
		$tginfo = $this->TeacherManagement->get_group_by_group_id($tgid);
		$groupname = $tginfo[0]->GroupName;
		
		//get knowledge info : 'KnowledgeName , KnowledgeSubject, IsCommonKnowledge, KnowledgeBookVersion, KnowledgeLevel, KnowledgeGrade'
		$this->load->model('KnowledgeMangement','KnowledgeMangement');
		$kinfo = $this->KnowledgeMangement->get_all_knowledge_by_knowledge_batch(array($tkid));
		$knowledgeName = $kinfo[0]->KnowledgeName;
		$subject = $kinfo[0]->KnowledgeSubject;
	
		//create material assignment record for this task
		$this->load->model('MaAssignManagement','MaAssignManagement');
		$histnum = $this->MaAssignManagement->get_teacher_assignment_by_kid_tid($tkid, $tid);
        $histnum += 1;
        $taskname = $knowledgeName."_".$histnum;

        $maid = $this->MaAssignManagement->create_teacher_assignment($tkid, $taskname, $knowledgeName, $subject, $tid, $publishTime, $expectTime, $studentPID, $studentCID, $studentDID, $studentSID, $studentLevel, $studentGrade, $studentClass, $groupname, "---", $materialNumber, $materialContent);
	
		return $maid;
	}
	
	
	//get document list
	private function _get_document_list($tkid, $rn, $pn){
		
		$retlist = array();
		$this->load->model('DocumentEdition','DocumentEdition');
		$allcount = $this->DocumentEdition->get_document_count_by_knowledge_id($tkid);

		if($allcount == 0){
			$retlist['sumpage'] = 0;
			$retlist['currentpage'] = $pn;
			$retlist['itemlist'] = array();
		}else{
			$dtypelist = array('doc','ppt','docx');
			
			$vlist = $this->DocumentEdition->get_document_by_limit_batch($tkid, $dtypelist, 0, $rn, $pn*10);
			$retlist['sumpage'] = intval($allcount/10)+1;
			$retlist['currentpage'] = $pn;
			$retlist['itemlist'] = array();
			foreach($vlist as $row){
				$retlist['itemlist'][] = array(
					'ItemId' => $row->DMID,
					'IsSelectItem' => 0,
					'Title' => $row->Title,
					'Description' => $row->Description,
					'ItemType' => $row->DocumentType,
					'SourceURI' => $row->SourceURI,
					'SourceType' => "doc"
				);
			}
		}
	
		return $retlist;
	}
	
	//get video list
	private function _get_video_list($tkid, $rn, $pn){
	
		$retlist = array();
		$this->load->model('VideoEdition','VideoEdition');
        $allcount = $this->VideoEdition->get_video_count_by_knowledge_id($tkid);
		if($allcount == 0){
			$retlist['sumpage'] = 0;
			$retlist['currentpage'] = $pn;
			$retlist['itemlist'] = array();
		}else{
			$vlist = $this->VideoEdition->get_video_by_limit_batch($tkid, 'mp4', 0, $rn, $pn*10);
			
			$retlist['sumpage'] = intval($allcount/10)+1;
			$retlist['currentpage'] = $pn;
			$retlist['itemlist'] = array();
			foreach($vlist as $row){
				$retlist['itemlist'][] = array(
					'ItemId' => $row->VMID,
					'IsSelectItem' => 0,
					'Title' => $row->Title,
					'Description' => $row->Description,
					'ItemType' => $row->VideoType,
					'SourceURI' => $row->SourceURI,
					'SourceType' => "video"
				);
			}
		}
        
		return $retlist;
	}	
}

?>
