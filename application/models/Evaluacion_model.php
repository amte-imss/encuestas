<?php   defined('BASEPATH') OR exit('No direct script access allowed');

class Evaluacion_model extends CI_Model {
  	public function __construct() {
          // Call the CI_Model constructor
          parent::__construct();
          $this->config->load('general');
         // $this->load->database();
    }
    
    public function total_evaluaciones(){
        $resultado = array('result'=>false, 'data'=>null);
        
        $query = $this->db->count_all('mdl_course'); //Obtener total de encuestas
        //pr($query);
        if ($query > 0){
            $resultado['result'] = true;
            $resultado['data'] = $query;
        }

        //pr($this->db->last_query());
        return $resultado;
    }

    public function get_evaluacion(){
        $resultado = array();
        
        $this->db->limit(10);
        $query = $this->db->get('mdl_course'); //Obtener conjunto de encuestas

        $resultado = $query->result_array();
        
        $query->free_result(); //Libera la memoria

        return $resultado;
    }
    
}
