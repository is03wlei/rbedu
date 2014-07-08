<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VideoEdition extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
	//get video
	public function get_video_by_video_id($vid){
		//get video info by video id
		
		$this->_db->select('VMID, KID, KnowledgeName, Subject, CreateTID, VideoLevel, VideoType, VideoLength, Title, Description, SourceURI');
		$this->_db->from('VideoMaterialInfo');
		$this->_db->where('VMID',$vid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_video_count_by_knowledge_id($kid){
		//get video count for one knowledge
		
		$this->_db->from('VideoMaterialInfo');
		$this->_db->where('KID',$kid);
		$resnum = $this->_db->count_all_results();
		
		return $resnum;
	}
	
	public function get_video_by_knowledge_id_and_video_type($kid, $vtype){
		//get video for one knowledge and type
		
		$this->_db->select('VMID, KID, KnowledgeName, Subject, CreateTID, VideoLevel, VideoType, VideoLength, Title, Description, SourceURI');
		$this->_db->from('VideoMaterialInfo');
		$this->_db->where('KID',$kid);
        if(is_Array($vtype)){
		    $this->_db->where_in('VideoType',$vtype);
        }else{
            $this->_db->where('VideoType',$vtype);
        }
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}

	public function get_video_by_limit_batch($kid, $vtype, $vlevel, $rn, $pn){
		//get video pages for given kid and limitation
		
		$this->_db->select('VMID, KID, KnowledgeName, Subject, CreateTID, VideoLevel, VideoType, VideoLength, Title, Description, SourceURI');
		$this->_db->from('VideoMaterialInfo');
		$this->_db->where('KID',$kid);
        if(is_Array($vtype)){
		    $this->_db->where_in('VideoType',$vtype);
        }else{
            $this->_db->where('VideoType',$vtype);
        }

		if($vlevel > 0){
			$this->_db->where('VideoLevel',$vlevel);
		}

		$this->_db->limit($rn,$pn);
		$query = $this->_db->get();
		$result = $query->result();

        return $result;
	}
	
	//create video
	public function create_video($kid, $kname, $vsubject, $tid, $vlevel, $vtype, $vlength, $vtitle, $vdescription, $vurl){
		//create an video 
		$videoinfo = array(
				'KID' => $kid,
				'KnowledgeName' => $kname,
                'Subject' => $vsubject,
				'CreateTID' => $tid,
				'VideoLevel' => $vlevel,
				'VideoType' => $vtype,
                'VideoLength' => $vlength,
				'Title' => $vtitle,
				'Description' => $vdescription,
				'SourceURI' => $vurl
		);
        //var_dump($videoinfo);
		$this->_db->insert('VideoMaterialInfo', $videoinfo);
		$vid = $this->_db->insert_id();
        //var_dump($vid);
		return $vid;
	}
	
	//delete video 
	public function delete_video_by_video_id($vid){
		//delete a video 
		$this->_db->where('VMID',$vid);
		$this->_db->delete('VideoMaterialInfo');
		
		return 0;
	}
	
	public function delete_video_by_knowledge_id($kid){
		//delete all video of a knowledge
		$this->_db->where('KID',$kid);
		$this->_db->delete('VideoMaterialInfo');
		
		return 0;
	}
	

}

?>
