<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AeraInfoManagement extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('rbedu', TRUE);
		$this->is_load = false;
	}
	
	public function get_province(){
		//get all province name and id
		$this->_db.select('AID , AreaName');
		$this->_db.from('AreaInfo');
		$this->_db.where('AreaLevel',3);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_city_by_province_id($pid){
		//get all city name and id for the given province id
		$this->_db.select('AID , AreaName');
		$this->_db.from('AreaInfo');
		$this->_db.where('AreaLevel',4);
		$this->_db.where('A3ID',$pid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_district_by_city_id($cid){
		//get all district name and id for the given city id
		$this->_db.select('AID , AreaName');
		$this->_db.from('AreaInfo');
		$this->_db.where('AreaLevel',5);
		$this->_db.where('A4ID',$pid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_school_by_province_id($pid,$stype){
		//get all school name and id for the given province id, limit in count of 100
		$this->_db.select('ASID , SchoolName');
		$this->_db.from('SchoolInfo');
		$this->_db.where('APID',$pid);
		$this->_db.where('SchoolType',$stype);
		$this->_db->limit(100);
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	public function get_school_by_city_id($cid,$stype){
		//get all school name and id for the given city id, limit in count of 100
		$this->_db.select('ASID , SchoolName');
		$this->_db.from('SchoolInfo');
		$this->_db.where('ACID',$pid);
		$this->_db.where('SchoolType',$stype);
		$this->_db->limit(100);
		$query = $this->_db->get();
		$result = $query->result();
	
		return $result;
	}
	
	public function get_school_by_district_id($did,$stype){
		//get all school name and id for the given district id
		$this->_db.select('ASID , SchoolName');
		$this->_db.from('SchoolInfo');
		$this->_db.where('ADID',$did);
		$this->_db.where('SchoolType',$stype);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_school_by_school_id($sid){
		//get school infomation by school id
		$this->_db.select('SchoolName, SchoolType');
		$this->_db.from('SchoolInfo');
		$this->_db.where('ASID',$sid);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;	
	}
	
	public function get_province_tree(){
		//get city and district, construct the area tree for all provinces
		$this->_db.select('AID , AreaName');
		$this->_db.from('AreaInfo');
		$query = $this->_db->get();
		$result = $query->result();
		
		$retarray = array();
		foreach($result as $row){
			
		}
		
	}
	
	public function get_city_tree_by_province_id($pid){
		//get city and district, construct a area tree for the province
		
	}
}

?>