<?php
if (!defined('BASEPATH')) exit("no direct script access allowd");  
//�����Ǽ���smarty�����ļ�  
require_once(APPPATH.'libraries/smarty/Smarty.class.php');  
//����cismarty�࣬�̳�smarty��  
class cismarty extends Smarty{  
    //����һ���ܱ����ı���  
    protected $ci;  
  
    function __construct(){  
        parent::__construct();  
        //����ʵ����CI,������Ҫ�ǽ�smarty�������ļ�д��ci�У��Է���������  
        $this->ci = & get_instance();  
        //����ci���½���smarty�����ļ�  
        $this->ci->load->config('smarty');  
        $this->cache_lifetime  = $this->ci->config->item('cache_lifetime');  
        $this->caching         = $this->ci->config->item('caching');  
        $this->template_dir    = $this->ci->config->item('template_dir');  
        $this->compile_dir     = $this->ci->config->item('compile_dir');  
        $this->cache_dir       = $this->ci->config->item('cache_dir');  
        $this->use_sub_dirs    = $this->ci->config->item('use_sub_dirs');  
        $this->left_delimiter  = $this->ci->config->item('left_delimiter');  
        $this->right_delimiter = $this->ci->config->item('right_delimiter'); 
	}
}
?>