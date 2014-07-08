<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Savepic extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		if ($_FILES["file"]["error"] > 0){
			header('HTTP/1.1 501 Not Implemented');
		}else{
			if (file_exists("./upload" . $_FILES["file"]["name"])){
			  header('HTTP/1.1 501 Not Implemented');
     	    }
			else{
			    if(move_uploaded_file($_FILES["file"]["tmp_name"],"./upload/" . $_FILES["file"]["name"])){
					 echo $_FILES["file"]["name"];
				}else{
					 header('HTTP/1.1 501 Not Implemented');
				}
			 
			}
		}
	}
	
	public function save(){
		if ($_FILES["file"]["error"] > 0){
			header('HTTP/1.1 501 Not Implemented');
		}else{
			if (file_exists("./upload" . $_FILES["file"]["name"])){
			     header('HTTP/1.1 501 Not Implemented');
     	    }
			else{
				$timeStamp = time();
				
			    if(move_uploaded_file($_FILES["file"]["tmp_name"],"./upload/" . $timeStamp.".jpg")){
					 echo $timeStamp.".jpg";
				}else{
					 header('HTTP/1.1 501 Not Implemented');
				}
			 
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */