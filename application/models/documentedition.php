<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DocumentEdition extends CI_Model{
	
	public $_db;
	public $is_load;
	
	public function __construct(){
		parent::__construct();
		$this->_db = $this->load->database('default', TRUE);
		$this->is_load = false;
	}
	
	//get document
	public function get_document_by_document_id($did){
		//get document info by document id
		
		$this->_db->select('DMID, KID, KnowledgeName, Subject, CreateTID, DocumentLevel, DocumentType, Title, Description, SourceURI');
		$this->_db->from('DocumentMaterialInfo');
		$this->_db->where('DMID',$did);
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}
	
	public function get_document_count_by_knowledge_id($kid){
		//get document count for one knowledge
		
		$this->_db->from('DocumentMaterialInfo');
		$this->_db->where('KID',$kid);
		$resnum = $this->_db->count_all_results();
		
		return $resnum;
	}
	
	public function get_document_by_knowledge_id_and_document_type($kid, $dtype){
		//get document for one knowledge and type
		
		$this->_db->select('DMID, KID, KnowledgeName, Subject, CreateTID, DocumentLevel, DocumentType, Title, Description, SourceURI');
		$this->_db->from('DocumentMaterialInfo');
		$this->_db->where('KID',$kid);
        if(is_Array($dtype)){
		    $this->_db->where_in('DocumentType',$dtype);
        }else{
            $this->_db->where('DocumentType',$dtype);
        }
		$query = $this->_db->get();
		$result = $query->result();
		
		return $result;
	}

	public function get_document_by_limit_batch($kid, $dtype, $dlevel, $rn, $pn){
		//get document pages for given kid and limitation
		
		$this->_db->select('DMID, KID, KnowledgeName, Subject, CreateTID, DocumentLevel, DocumentType, Title, Description, SourceURI');
		$this->_db->from('DocumentMaterialInfo');
		$this->_db->where('KID',$kid);
        if(is_Array($dtype)){
		    $this->_db->where_in('DocumentType',$dtype);
        }else{
            $this->_db->where('DocumentType',$dtype);
        }

		if($dlevel > 0){
			$this->_db->where('DocumentLevel',$dlevel);
		}

		$this->_db->limit($rn,$pn);
		$query = $this->_db->get();
		$result = $query->result();

        return $result;
	}
	
	//create document
	public function create_document($kid, $kname, $dsubject, $tid, $dlevel, $dtype, $dtitle, $ddescription, $durl){
		//create an document 
		$documentinfo = array(
				'KID' => $kid,
				'KnowledgeName' => $kname,
                'Subject' => $dsubject,
				'CreateTID' => $tid,
				'DocumentLevel' => $dlevel,
				'DocumentType' => $dtype,
				'Title' => $dtitle,
				'Description' => $ddescription,
				'SourceURI' => $durl
		);
		
		$this->_db->insert('DocumentMaterialInfo', $documentinfo);
		$did = $this->_db->insert_id();
		return $did;
	}
	
	//delete document 
	public function delete_document_by_document_id($did){
		//delete a document 
		$this->_db->where('DMID',$did);
		$this->_db->delete('DocumentMaterialInfo');
		
		return 0;
	}
	
	public function delete_document_by_knowledge_id($kid){
		//delete all document of a knowledge
		$this->_db->where('KID',$kid);
		$this->_db->delete('DocumentMaterialInfo');
		
		return 0;
	}
	

}

?>
