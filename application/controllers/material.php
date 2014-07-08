<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/***************************************************************************
 * 
 * Copyright (c) 2013 renrentalk.com, Inc. All Rights Reserved
 * $Id$ 
 * 
 **************************************************************************/
 
 
 
/**
 * @file application/controllers/payment.php
 * @author chenggang(chenggang@renrentalk.com)
 * @date 2013/05/20 14:57:07
 * @version $Revision$ 
 * @brief 
 *  
 **/

class Material extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
    }

    public function publish(){
        #log_message(2,"helloMaterial");
        $materialType = $this->input->post('materialType');
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $knowledgeName = $this->input->post('KnowledgeName');
        $kid = $this->input->post('KID');
        $level = $this->input->post('level');
        $tid = 1;//$this->input->post('tid');
        $url = $this->input->post('url');
        
        #var_dump($materialType);
        #log_message("2","$materialType $title $description $knowledgeName $kid $level\n");
        #return 0;

        $this->load->model('TeacherManagement','teacherManagement');

        $teacher=$this->teacherManagement->get_teacher_by_teacher_id($tid);

        #var_dump($teacher);
        #return 0;
        $errorno=0;
       
        $subject=$teacher[0]->TeacherSubject;
        $videoLength = 100;

        if ($materialType==0){
            $this->load->model('VideoEdition','videoEdition');
            $videoLength=100;
            $this->videoEdition->create_video($kid,$knowledgeName,$subject,$tid,$level,$materialType,$videoLength,$title,$description,$url);

        }else if ($materialType==1){
            $this->load->model('DocumentEdition','documentEdition');
            $this->documentEdition->create_document($kid,$knowledgeName,$subject,$tid,$level,$materialType,$title,$description,$url);
        }

        $ret['errorno']=$errorno;
        echo json_encode($ret);
        return 0; 
    }

    private function _post_select(){
        $cardtype = $this->input->post('cardtype');
        $valid_cardtype_arr = array(1, 10, 20, 40, 100);
        if(ENVIRONMENT != 'production'){
            $valid_cardtype_arr[] = 'test';
        }
        $data = array('user' => $this->user);
        if(!in_array($cardtype, $valid_cardtype_arr)){
            $error = 'P_ERR_CARDTYPE_NOT_VALID';
            $data['error'] = $this->errmsg->get_msg($error);
            $this->smarty->view('payment_select.tpl', $data);
            return;
        }
        redirect('/payment/verify', 'location', 302);
    }

    public function index(){

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

        $data['knowledgelist'] = json_encode($retklist);

        $data['sitebar'] = 'material';
        $this->cismarty->assign($data);
        $this->cismarty->display('material-manage.tpl');
    }

    public function notify($paytype){
        //支付宝
        if($paytype==0){
            $arr = array();
            if(!empty($_POST)){
                $arr = $_POST;
                //$partner = "2088502477018064";
                $ret  = $this->_check_alipay_sign($arr);
                if($ret){
                    $this->load->model('Notifylog_model', 'notifylog');
                    $this->load->model('Payment_model', 'payment');
                    $paymentid = substr($arr['out_trade_no'], 0, strpos($arr['out_trade_no'], '-'));
                    $payment = $this->payment->get_payment_by_id($paymentid);
                    if(is_object($payment)){
                        $userid = $payment->userid;
                    }
                    else{
                        $userid = 0;
                    }
                    $this->notifylog->paymentid = $paymentid;
                    $this->notifylog->userid = $userid;
                    $this->notifylog->tradeid = $arr['trade_no'];
                    $this->notifylog->status = 0;
                    $this->notifylog->result = 0;
                    $this->notifylog->error = '';
                    $this->notifylog->content = json_encode($arr);
                    $this->notifylog->add();
                    echo "success";
                }
                else{
                    echo "fail";
                }
            }
            else{
                echo "fail";
            }
        }
        // 财付通
        else if($paytype == 2){
            $arr = array();
            $arr['bank_billno'] = $this->input->get('bank_billno');
            $arr['bank_type'] = $this->input->get('bank_type');
            $arr['discount'] = $this->input->get('discount');
            $arr['fee_type'] = $this->input->get('fee_type');
            $arr['input_charset'] = $this->input->get('input_charset');
            $arr['notify_id'] = $this->input->get('notify_id');
            $arr['out_trade_no'] = $this->input->get('out_trade_no');
            $arr['partner'] = $this->input->get('partner');
            $arr['product_fee'] = $this->input->get('product_fee');
            $arr['sign_type'] = $this->input->get('sign_type');
            $arr['time_end'] = $this->input->get('time_end');
            $arr['total_fee'] = $this->input->get('total_fee');
            $arr['trade_mode'] = $this->input->get('trade_mode');
            $arr['trade_state'] = $this->input->get('trade_state');
            $arr['transaction_id'] = $this->input->get('transaction_id');
            $arr['transport_fee'] = $this->input->get('transport_fee');
            $arr['sign'] = $this->input->get('sign');
            $ret = $this->_check_tenpay_sign($arr);
            if($ret){
                $this->load->model('Notifylog_model', 'notifylog');
                $this->load->model('Payment_model', 'payment');
                $paymentid = substr($arr['out_trade_no'], 0, strpos($arr['out_trade_no'], '-'));
                $payment = $this->payment->get_payment_by_id($paymentid);
                if(is_object($payment)){
                    $userid = $payment->userid;
                }
                else{
                    $userid = 0;
                }
                $this->notifylog->paymentid = $paymentid;
                $this->notifylog->userid = $userid;
                $this->notifylog->tradeid = $arr['transaction_id'];
                $this->notifylog->status = 0;
                $this->notifylog->result = 0;
                $this->notifylog->error = '';
                $this->notifylog->content = json_encode($arr);
                $this->notifylog->add();
            }
            echo "success";
        }
    }

    private function _check_alipay_sign($arr){
        ksort($arr);
        $signPars = '';
        foreach($arr as $key => $val){
            if("sign" != $key && 'sign_type' != $key && "" != $val) {
                $signPars .= $key . "=" . $val . "&";
            }
        }
        $signPars = substr($signPars,0,count($signPars)-2);
        $signPars .= 'r7a5kyhfax3w69idlh47as8dhyy3kox8';
        $sign = strtolower(md5($signPars));
        $alipaySign = strtolower($arr['sign']);
        return $sign == $alipaySign;
    }

    private function _check_tenpay_sign($arr){
        ksort($arr);
        $signPars = '';
        foreach($arr as $key => $val){
            if("sign" != $key && "" != $val) {
                $signPars .= $key . "=" . $val . "&";
            }
        }
        $signPars .= "key=d7ca1b8609a53cfa860e58958348013c"; 
        $sign = strtolower(md5($signPars));
        $tenpaySign = strtolower($arr['sign']);
        return $sign == $tenpaySign;
    }

    public function verify(){
        if(!$this->user->is_load){
            redirect('/user/login?next='.urlencode(current_url()), 'location', 302);
            return;
        }
        if($this->input->post('btn_buy') == 'submit'){
            return $this->_post_verify();
        }
        $paymentid = $this->session->userdata('paymentid');
        $cardtype = $this->session->userdata('cardtype');
        if($paymentid === FALSE || $cardtype === FALSE){
            redirect('/payment/select', 'location', 302);
            return;
        }
        $arr['cardtype'] = $cardtype;
        $arr['paymentid'] = $paymentid;
        $valid_cardtype_arr = array(1, 10, 20, 40, 100);
        if(ENVIRONMENT != 'production'){
            $valid_cardtype_arr[] = 'test';
        }
        /*
        if(!in_array($arr['cardtype'], $valid_cardtype_arr)){
            $this->session->set_flashdata('error', 'P_ERR_CARDTYPE_NOT_VALID');
            redirect('/payment/select', 'location', 302);
            return;
        }
        */
        switch($cardtype){
            case '1':
                $data['value'] = 240;
                break;
            case '10':
                $data['value'] = 2000;
                break;
            case '10-S':
                $data['value'] = 2500;
                break;
            case '20':
                $data['value'] = 3800;
                break;
            case '20-S':
                $data['value'] = 4750;
                break;
            case '40':
                $data['value'] = 7400;
                break;
            case '40-S':
                $data['value'] = 9250;
                break;
            case '100':
                $data['value'] = 17500;
                break;
            case '100-S':
                $data['value'] = 21875;
                break;
            case 'test':
                $data['value'] = 0.01;
                break;
        }
        $data['user'] = $this->user;
        $data['is_star'] = 0;
        if(strstr($arr['cardtype'], 'S')!==false){
            $arr['cardtype'] = substr($arr['cardtype'], 0, -2);
            $data['is_star'] = 1;
        }
        $data['cardtype'] = $arr['cardtype'];
        $data['paymentid'] = $arr['paymentid'];
        $data['has_mobile'] = false;
        if(isset($this->user->mobile) && $this->user->mobile != 0){
            $data['has_mobile'] = true;
        }
        $this->smarty->view('payment_verify.tpl', $data);
    }

    public function remit($paymentid){
        if(!$this->user->is_load){
            redirect('/user/login?next='.urlencode(current_url()), 'location', 302);
            return;
        }
        if(empty($paymentid) || !is_numeric($paymentid)){
            redirect('/payment/select', 'location', 302);
        }
        $this->load->model('Payment_model', 'payment');
        $payment = $this->payment->get_payment_by_id($paymentid);
        $data = array();
        $data['user'] = $this->user;
        $data['cardtype'] = $payment->cardtype;
        $data['value'] = $payment->value;
        $this->smarty->view('remit.tpl', $data);
    }

    private function _post_verify(){
        if(!$this->user->is_load){
            redirect('/user/login?next='.urlencode(current_url()), 'location', 302);
            return;
        }
        $this->load->library('validation');
        $paymentid = $this->input->post('paymentid');
        $cardtype = $this->input->post('cardtype');
        $banktype = $this->input->post('banktype');
        $paytype = $this->input->post('paytype');
        $is_star = $this->input->post('is_star');
        $mobile = $this->input->post('mobile');
        if(!is_numeric($paymentid)){
            $this->session->set_flashdata('error', 'P_ERR_PAYMENTID_NOT_NUMERIC');
            redirect('/payment/select', 'location', 302);
            return;
        }
        if($this->user->mobile == 0 && $this->validation->_check_mobile_validation($mobile)!==false){
            $this->session->set_flashdata('error', 'U_ERR_MOBILE_NOT_VALID');
            redirect('/payment/select', 'location', 302);
            return;
        }
        $ret = $this->_check_cardtype($cardtype);
        if($ret){
            $this->session->set_flashdata('error', $ret);
            redirect('/payment/select', 'location', 302);
            return;
        }
        $value = $this->_calcu_value($cardtype, $is_star);
        if(isset($paytype) && is_numeric($paytype) && $paytype == 0){
            $banktype = 'DEFAULT';
        }
        if(!empty($banktype)){
            $paytype = 0;// 银行都走支付宝
        }
        if($paytype === FALSE){
            $this->session->set_flashdata('error', 'P_ERR_PAYTYPE_NOT_VALID');
            redirect('/payment/select', 'location', 302);
        }
        if($cardtype == 'test'){
            $cardtype = 0;
        }
        // 检查手机号是否已经存在
        $new_user = clone $this->user;
        $new_user->is_load = false;
        $new_user->get_info_by_mobile($mobile);
        if($new_user->is_load){
            $this->session->set_flashdata('error', 'U_ERR_MOBILE_ALREADY_EXIST');
            redirect('/payment/select', 'location', 302);
            return;
        }
        // 更新user->mobile
        $this->user->set_mobile($mobile);
        // 增加payment记录
        $this->load->model('Payment_model', 'payment');
        $payment = $this->payment->get_payment_by_id($paymentid);
        if(is_object($payment)){
            foreach($payment as $key => $val){
                $this->payment->$key = $val;
            }
            $this->payment->paytype = $paytype;
            $this->payment->banktype = $banktype;
            $this->payment->value = $value;
            $this->payment->update($paymentid);
        }
        else{
            $this->payment->id = $paymentid;
            $this->payment->userid = $this->user->id;
            $this->payment->cardtype = $cardtype;
            $this->payment->value = $value;
            $this->payment->paytype = $paytype;
            $this->payment->banktype = $banktype;
            $this->payment->count = $cardtype;
            $this->payment->add();
            $this->session->unset_userdata('paymentid');
            $this->session->unset_userdata('cardtype');
        }
        if($paytype == 4){
            $url = '/payment/remit/'.$paymentid;
        }
        else{
            // TODO redirect to payment url
            $url = $this->_prepare_payment_url($this->payment);
        }
        redirect($url, 'Location', 302);
        return;
    }

    private function _prepare_payment_url($payment){
        if($payment->paytype == 0){
            $url = $this->_generate_alipay_url($payment);
        }
        elseif($payment->paytype == 2){
            $url = $this->_generate_tenpay_url($payment);
        }
        return $url;
    }

    private function _convert_bank($banktype){
        switch ($banktype){
            case '1002':
                $bankstr = 'ICBCB2C';
                break;
            case '1001':
                $bankstr = 'CMB';
                break;
            case '1003':
                $bankstr = 'CCB';
                break;
            case '1005':
                $bankstr = 'ABC';
                break;
            case '1004':
                $bankstr = 'SPDB';
                break;
            case '1009':
                $bankstr = 'CIB';
                break;
            case '1032':
                $bankstr = 'BJBANK';
                break;
            case '1022':
                $bankstr = 'CEBBANK';
                break;
            case '1006':
                $bankstr = 'CMBC';
                break;
            case '1021':
                $bankstr = 'CITIC';
                break;
            case '1027':
                $bankstr = 'GDB';
                break;
            case '1010':
                $bankstr = 'SPABANK';
                break;
            case '1052':
                $bankstr = 'BOCB2C';
                break;
            case '1020':
                $bankstr = 'COMM-DEBIT';
                break;
        }
        return $bankstr;
    }

    private function _generate_alipay_url($payment){
        $url = "https://mapi.alipay.com/gateway.do";
        $arr = array();
        if(isset($payment->banktype) && $payment->banktype != 'DEFAULT'){
            $arr['paymethod'] = 'bankPay';
            $arr['defaultbank'] = $this->_convert_bank($payment->banktype);
        }
        $arr['service'] = "create_direct_pay_by_user";
        $arr['partner'] = "2088011737815171";
        $arr['_input_charset'] = 'utf-8';
        $arr['sign_type'] = "MD5";
        $arr['return_url'] = 'http://www.renrenshuo.com/payment/success/0';
        $arr['notify_url'] = 'http://www.renrenshuo.com/payment/notify/0';
        $arr['out_trade_no'] = $payment->id. '-'. time();
        $arr['subject'] = '人人说课程卡'. $payment->count. '次';
        $arr['payment_type'] = "1";
        $arr['logistics_fee'] = "0.00";
        $arr['logistics_type'] = "EXPRESS";
        $arr['logistics_payment'] = "SELLER_PAY";
        $arr['seller_id'] = "2088011737815171";
        $arr['quantity'] = 1;
        $arr['body'] = '人人说课程卡'. $payment->count. '次';
        $arr['total_fee'] = $payment->value;
        ksort($arr);
        $arr_temp = $arr;
        unset($arr['sign_type']);
        $str = '';
        $str2 = '';
        foreach($arr as $key => $val){
            $str .= $key. '='. $val. '&';
        }
        $str = substr($str,0,count($str)-2);
        $str .= 'r7a5kyhfax3w69idlh47as8dhyy3kox8';
        $md5 = strtolower(md5($str));
        //$arr_temp['sign'] = $md5;
        ksort($arr);
        foreach($arr_temp as $key => $val){
            $str2 .= $key. '='. urlencode($val). '&';
        }
        //$str2 = substr($str2,0,count($str2)-2);
        //$str2 .= '&sign='.$md5;
        $str2 .= 'sign='.$md5;
        $url .= '?'.$str2;
        return $url;
    }

    private function _generate_tenpay_url($payment){
        $url = "https://gw.tenpay.com/gateway/pay.htm";
        $arr = array();
        $arr['bank_type'] = $payment->banktype;
        $arr['input_charset'] = 'UTF-8';
        $arr['body'] = '人人说课程卡'. $payment->count. '次';
        $arr['subject'] = '人人说课程卡'. $payment->count. '次';
        $arr['return_url'] = 'http://www.renrenshuo.com/payment/success/2';
        $arr['notify_url'] = 'http://www.renrenshuo.com/payment/notify/2';
        $arr['partner'] = 1216719501;
        $arr['out_trade_no'] = $payment->id. '-'. time();
        $arr['total_fee'] = ($payment->value * 100);
        $arr['fee_type'] = 1;
        $arr['spbill_create_ip'] = $this->input->ip_address();
        ksort($arr);
        $str = '';
        $str2 = '';
        foreach($arr as $key => $val){
            $str .= $key. '='. $val. '&';
            $str2 .= $key. '='. urlencode($val). '&';
        }
        $str .= 'key=d7ca1b8609a53cfa860e58958348013c';
        $md5 = strtolower(md5($str));
        $str2 .= '&sign='.$md5;
        $url .= '?'.$str2;
        return $url;
    }

    private function _check_cardtype($cardtype){
        $valid_cardtype_arr = array(1, 10, 20, 40, 100);
        if(ENVIRONMENT == 'development'){
            $valid_cardtype_arr[] = 'test';
        }
        if(!in_array($cardtype, $valid_cardtype_arr)){
            return 'P_ERR_CARDTYPE_NOT_VALID';
        }
        return false;
    }

    private function _calcu_value($cardtype, $is_star){
        switch($cardtype){
            case '1':
                $value = 240;
                break;
            case '10':
                if($is_star == 1){
                    $value = 2500;
                }
                else{
                    $value = 2000;
                }
                break;
            case '20':
                if($is_star == 1){
                    $value = 4750;
                }
                else{
                    $value = 3800;
                }
                break;
            case '40':
                if($is_star == 1){
                    $value = 9250;
                }
                else{
                    $value = 7400;
                }
                break;
            case '100':
                if($is_star == 1){
                    $value = 21875;
                }
                else{
                    $value = 17500;
                }
                break;
            case 'test':
                $value = 0.01;
                break;
            default:
                $value = 0;
                break;
        }
        return $value;
    }

    private function _generate_paymentid(){
        $sql = "replace `paymentid` set val='a'";
        $this->db->query($sql);
        $id = $this->db->insert_id();
        return $id;
    }

    public function send_goods_confirm($trade_no){
        $url = "https://mapi.alipay.com/gateway.do";
        $arr = array();
        $arr['service'] = "send_goods_confirm_by_platform";
        $arr['partner'] = "2088502477018064";
        $arr['_input_charset'] = 'utf-8';
        $arr['sign_type'] = 'MD5';
        $arr['trade_no'] = $trade_no;
        $arr['logistics_name'] = "宅急送ZJS";
        $arr['transport_type'] = "EXPRESS";
        $arr['seller_ip'] = $this->input->ip_address();
        ksort($arr);
        $str = '';
        $str2 = '';
        foreach($arr as $key => $val){
            if( $key!='sign' && $key!='sign_type' && ''!=$key){
                $str .= $key ."=". $val .'&';
            }
        }
        $str = substr($str,0,count($str)-2);
        $str .= '3d7i5df14i3v3ypp2tzeqtlf072t9czy';
        $sign = strtolower(md5($str));
        $arr['sign'] = $sign;
        ksort($arr);
        foreach($arr as $key => $val){
                $str2 .= $key. '='. urlencode($val). '&';
        }
        $str2 = substr($str2,0,count($str2)-2);
        $url .= '?'.$str2;
        redirect($url, 'Location', 302);
        return;
    }
}





/* vim: set ts=4 sw=4 sts=4 tw=2000 et: */
