<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/***************************************************************************
 * 
 * Copyright (c) 2014 
 * $Id$ 
 * 
 **************************************************************************/
 
 
 
/**
 * @file application/controllers/exerciseRecordJudge.php
 * @author Ruiqiang(wangrq2008@gmail.com) 
 * @date 2014/06/30 23:39:37
 * @version $Revision$ 
 * @brief 
 *  
 **/

class ExerciseRecordJudge extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
    }

    public function index(){
        $data = array();
        $data['sitebar'] = "marking";
        $this->cismarty->assign($data);
        $this->cismarty->display('marking-step1.tpl');
    }

    //第一个页面通过ajax异步传输数据
    public function ajax_exercises($studentGrade){
        //$studentGrade = $this->input->post('grade');
        //$studentClass = $this->input->post('class');
        $studentClass = "一班";
        $this->load->model("exassignManagement", "exerciseAssignment");
        $exercises = $this->exerciseAssignment->get_teacher_exercises_by_grade_class($studentGrade, $studentClass);
        $data = array();
        $data['sitebar'] = "marking";
        //$data['user'] = clone $this->user;
        if($exercises == null){
            $data['status'] = 1;
            echo json_encode($data);
            return ;
        }else{
            $data['status'] = 0;
        }
        $excis = array();
        foreach($exercises as $exercise){
            $HasReview = 0;
            $students = $this->exerciseAssignment->get_exercise_assignment_complete_by_exercise_id($exercise->EAID);
            //if($students == null) continue;
            $allCount = 0 ; $finishCount=0;
            foreach($students as $student){
                //HasReview=1这显示继续批阅
                if($HasReview==0 && $student->HasReviewed != 0){
                    $HasReview == 1;
                }
                if($student->FinishTime != null){
                    ++$finishCount;
                }
                ++$allCount;
            }
            $exercise->finishCount = $finishCount;
            $exercise->allCount = $allCount;
            $exercise->HasReviewed = $HasReview; 
            $excis[] = $exercise;
        }
        $data['excis'] = $excis;
        echo json_encode($data);
        return;
    }

    public function judge($eaid, $sid = "xxxx"){
        $this->load->model("exassignManagement", "exerciseAssignment");
        $stu = $this->exerciseAssignment->get_exercise_assignment_complete_by_exercise_id($eaid);
        $this->load->model('studentmanagement', 'studentm');
        $students = array();
        foreach($stu as $student){
            $ar=array();
            $name = $this->studentm->get_student_name($student->SID);
            $ar['SID'] = $student->SID;
            $ar['StudentName'] = $name;
            //$ar['PublishTime'] = $student->FinishTime;
            //$ar['HasReviewed'] = $student->HasReviewed;
            //$ar['ExerciseStatus'] = $student->ExerciseStatus;
            //$ar['ExerciseNUmber'] = $student->ExerciseNumber;
            $students[] = $ar;
            unset($ar);
        }
        $data['students'] = $students;
        //var_dump($students);
        $flag = 1;
        //未指定学生，则默认返回第一个学生的答题情况
        if($sid == "xxxx"){
            $sid = $students[0]['SID'];
        }else{
            $flag =0;
        }

        $this->load->model('exerciseedition', 'exerciseInfo');
        $this->load->model('exrecordmanagement', 'exerciseRecord');
        $exercises = $this->exerciseRecord->read_exercise_result_by_student_ea($sid, $eaid);
        $arr = array();
        foreach($exercises as $exc){
            $a = array();
            //status>=2，说明此题已经判过
            if($exc->Status >= 2 ){
                continue;
            }
            $exer = $this->exerciseInfo->get_exercise_by_exercise_id($exc->EID);
            //sc为主观题，为选择题,跳过选择题
            if($exer[0]->ExerciseType != "sc"){
                continue;
            }
            $a['EID'] = $exer[0]->EID;
            $a['Etype'] = $exer[0]->ExerciseType;
            $a['ExerciseTitle'] = $exer[0]->ExerciseTitle;
            $a['ExerciseContent'] = $exer[0]->ExerciseContent;
            $a['AnswerContentPicture'] = $exer[0]->AnswerContentPicture;
            $a['AnswerContentCharacter'] = $exer[0]->AnswerContentCharacter;
            $a['StudentAnswerPicture'] = $exc->StudentAnswerPicture;
            $a['StudentAnswerCharacter'] = $exc->StudentAnswerCharacter;
            $a['erid'] = $exc->ERID;
            $a['studentQestion'] = $exc->StudentQuestion;
            $a['sid'] = $sid;
            $arr[] = $a;
        }
        $data['eaid'] = $eaid;
        $data['exercises'] = $arr;
        $data['sitebar'] = "marking";
        if( $flag == 1){
            $this->cismarty->assign($data);
            $this->cismarty->display('marking-step2.tpl');
        }else{
            echo json_encode($data);
        }
    }

    public function ajax_teacher_judge(){
        $erid = $this->input->post('erid');
        $isCorrect = $this->input->post('isCorrect');
        $score = $this->input->post('Score');
        $review = $this->input->post('review');
        $review = json_encode($review);
        $this->load->model('exrecordmanagement', 'exerciseRecord');
        $this->exerciseRecord->update_teacher_review($erid, $score, $isCorrect, $review);
        $data = array();
        $data['status'] = 0;
        $data['sitebar'] = "marking";
        echo json_encode($data);
        return;
    }

    public function show_results($eaid){
        //$eaid = $this->input->get('eaid');
        $this->load->model("exassignManagement", "exerciseAssignment");
        $exercise = $this->exerciseAssignment->get_exercise_assignment_by_exercise_id($eaid); 
        //var_dump($exercise[0]->ExerciseContent);
        //$json_data = '{"ex": [{"ID":"1","type":"1"},{"ID":"2","type":"1"},{"ID":"3","type":"1"},{"ID":"4","type":"1"},{"ID":"5","type":"1"},{"ID":"6","type":"1"} ]}';

        $exerciseIDs = @json_decode($exercise[0]->ExerciseContent);
        $this->load->model('exrecordmanagement', 'exerciseRecord');
        $this->load->model('studentmanagement', 'studentinfo');
        $this->load->model('exerciseedition', 'exerciseinfo');
        foreach($exerciseIDs->ex as $exerciseID){
            if($exerciseID->ExerciseType == "sc"){
                $exerciseID->ExerciseType = "判断题";
            }else{
                $exerciseID->ExerciseType = "选择题";
            }
            $exerciseResults = $this->exerciseRecord->read_exercise_result_by_eaid_eid($exerciseID->EID, $eaid);
            
            $count = 0; $correctCount = 0;$scores = 0;
            foreach($exerciseResults as $r){
                ++$count;
                if($r->IsCorrect == 1){
                    ++$correctCount;
                }
                $scores = $scores + $r->Score;
            }
            $exerciseID->RatioCorrect = round($correctCount/$count*100, 2);
            $exerciseID->average = round($scores/$count*100, 2);

            $exinfo = $this->exerciseinfo->get_exercise_by_id($exerciseID->EID);
            $exerciseID->exerciseLevel = $exinfo->ExerciseLevel;
        }
        $data = array();
        $data['sitebar'] = "marking";
        $data['exercises'] = $exerciseIDs->ex;

        $students = $this->exerciseAssignment->get_exercise_assignment_complete_by_exercise_id($eaid);
        foreach($students as $student){
            $student->name = $this->studentinfo->get_student_name($student->SID);
            $es = $this->exerciseRecord->read_exercise_result_by_student_ea($student->SID, $eaid); 
            $count = 0; $correctCount = 0;$scores = 0;
            $Acount=0; $AcorrectCount=0;
            $Bcount=0; $BcorrectCount=0;
            $Ccount=0; $CcorrectCount=0;
            foreach($es as $e){
                ++$count;
                if($e->IsCorrect == 1){
                    ++$correctCount;
                }
                $ee = $this->exerciseinfo->get_exercise_by_exercise_id($e->EID);
                if($ee[0]->ExerciseType== "dc"){
                    ++$Acount;
                    if($e->IsCorrect == 1){
                        ++$AcorrectCount;
                    }
                    $e->ExerciseType = "选择题";
                }
                else if($ee[0]->ExerciseType== "sc"){
                    ++$Bcount;
                    if($e->IsCorrect == 1){
                        ++$BcorrectCount;
                    }
                    $e->ExerciseType = "填空题";
                }
                else if($ee[0]->ExerciseType== "bc"){
                    ++$Ccount;
                    if($e->IsCorrect == 1){
                        ++$CcorrectCount;
                    }
                    $e->ExerciseType = "简答题";
                }
            }
            if($Acount == 0){
                $student->Aratio = 0;
            }else{
                $student->Aratio = round($AcorrectCount/$Acount*100, 2);
            }
            if($Bcount == 0){
                $student->Bratio = 0;
            }else{
                $student->Bratio = round($BcorrectCount/$Bcount*100, 2);
            }
            if($Ccount == 0){
                $student->Cratio = 0;
            }else{
                $student->Cratio = round($CcorrectCount/$Ccount*100, 2);
            }
            $student->exercises = $es;
        }
        $data['students'] = $students;
     // var_dump($data);
       // exit;
        $this->cismarty->assign($data);
        $this->cismarty->display('marking-step3.tpl');
    }

    //一下函数为平板返回数据
    public function find_all_unfinished_unexercise($sid){
        //$sid = $this->input->post("sid");
        $this->load->model('exassignmanagement', 'exerciseAssign');
        $this->load->model('teachermanagement', 'teacher');
        $all_exs = $this->exerciseAssign->get_exercise_assignment_by_student_id($sid);
        $data = array();
        if($all_exs == null){
            $data['status'] = 1;
            echo json_encode($data);
            return;
        }
        $data['status'] = 0;
        $exercises = array();
        foreach($all_exs as $exs){
            //HasReviewed=-1表示批阅完成
            if($exs->ExerciseStatus == -1){
                continue;
            } 
            else if($exs->ExerciseStatus == 0){
                $tmp['exerciseStatus'] = 0;
            }else{
                $tmp['exerciseStatus'] = 1;
            }
            $tmp = array();
            $tmp['subject'] = $exs->Subject;
            $tmp['exerciseName'] = $exs->ExerciseName;
            $tmp['exerciseNumber'] = $exs->ExerciseNumber;
            $tmp['expectTime'] = $exs->ExpectTime;
            $teacher = $this->teacher->get_teacherinfo_tid($exs->TID);
            $tmp['teacherName'] = $teacher->TeacherName;
            $tmp['eaid'] = $exs->EAID;
            $exercises[] = $tmp;
        }
        $data['exercises'] = $exercises;
        echo json_encode($data);
        //var_dump(json_decode(json_encode($data)));
    }

    public function doHomework($sid, $eaid){
        $this->load->model('exassignmanagement', 'exerciseAssign');
        $this->load->model('teachermanagement', 'teacher');
        $this->load->model('exerciseedition', 'exerciseinfo');
        $this->load->model('knowledgemangement', 'knowledge');
        $exs = $this->exerciseAssign->get_student_exercise($sid, $eaid);
        $data = array();
        $index = $exs->ExerciseStatus+1;
        if($index == $exs->ExerciseNumber){
            $data['status'] = 1;
            echo json_encode($data);
            return;
        }
        $data['status'] = 0;
        $index = $index-1;
        $ext = $this->exerciseAssign->get_teacher_exercise($eaid);
        $exids = json_decode($ext->ExerciseContent);
        $eid = $exids->ex[$index]->EID;
        $exinfo = $this->exerciseinfo->get_exercise_by_id($eid);
        $data['eaid'] = $eaid;
        $data['eid'] = $eid;
        $knowledge = $this->knowledge->get_knowledgeinfo_kid($exinfo->KID);
        $data['knowledgeName'] = $knowledge->KnowledgeName;
        $data['finishCount'] = $index+1;
        $data['count'] = $exs->ExerciseNumber;
        $data['ExerciseType'] = $exinfo->ExerciseType;
        $data['ExerciseLevel'] = $exinfo->ExerciseLevel;
        $data['ExerciseContent'] = $exinfo->ExerciseContent;
        $data['ExerciseTitle'] = $exinfo->ExerciseTitle;
        echo json_encode($data);
        //var_dump(json_decode(json_encode($data)));
    }

    public function update_answer(){
        $eid = $this->input->post('eid');
        $sid = $this->input->post('sid');
        $eaid = $this->input->post('eaid');
        $studentAnswer_json = $this->input->post('studentAnswer');
        $StudentQuestion = $this->input->post('studentQuesion');
        $TimeConsume = $this->input->post('timeConsume');
    
        // '[["ars","xxx"],[ "score",3],["ars","iii"],["score",4]]';
        $eid = 1;
        $sid = 1;
        $eaid = 10;

        $data = array();

        $this->load->model('exassignmanagement', 'exAssignment');
        $this->load->model('exerciseedition', 'exerciseinfo');
        $exinfo = $this->exerciseinfo->get_exercise_by_id($eid);

        $Score = 0;
        $Status = 0;
        $isCorrect = 2; $StudentAnswer='';$StudentAnswerPicture='';
        //选择填空题自动评阅         
        if($exinfo->ExerciseType == 'danx' || $exinfo->ExerciseType == 'pd' || $exinfo->ExerciseType == 'duox'){
            $studentAnswer = json_decode($studentAnswer_json);
            $answer = json_decode($exinfo->AnswerContentCharacter);
            $Status = 2;
            if($studentAnswer[0] == $answer[0]){
                $Score = $answer[1];
                $isCorrect = 2;
            }else{
                $isCorrect = 1;
            }
            $StudentAnswer = $studentAnswer;
        }
        else if($exinfo->ExerciseType == 'tk'){
            $studentAnswer = json_decode($studentAnswer_json);
            $answer = json_decode($exinfo->AnswerContentCharacter);
            $Status = 2;
            for($i=0; $i<count($studentAnswer); ++$i){
                if($studentAnswer[$i][0] == $answer[$i][0]){
                    $Score += $answer[$i][1];
                }else{
                    $isCorrect = 1;
                }
            }
            $StudentAnswer = $studentAnswer;
        }else{
            $Status = 1;
            $isCorrect = 0;
            $StudentAnswerPicture = $studentAnswer_json;
        }
        date_default_timezone_set('GMT');
        $now =  time();
        $FinishTime = date('Y-m-d H:i:s', $now);
        $StartTime = date('Y-m-d H:i:s', $now-$TimeConsume);
        $this->load->model('exrecordmanagement','exerciseRecord');
		$erinfo = array(
				'EAID' => $eaid,
				'SID' => $sid,
				'EID' => $eid,
				'Status' => $Status,
				'Score' => $Score,
				'IsCorrect' => $isCorrect,
				'StudentAnswerPicture' => $StudentAnswer,
				'StudentAnswerCharacter' => $StudentAnswerPicture,
				'TimeConsume' => $TimeConsume,
				'StartTime' => $StartTime,
				'FinishTime' => $FinishTime,
				'StudentQuestion' => $StudentQuestion,
				'TeacherReview' => ""
		);
        $erid = $this->exerciseRecord->create_record($erinfo);
        echo $erid;
    }


}





/* vim: set ts=4 sw=4 sts=4 tw=2000 et: */
