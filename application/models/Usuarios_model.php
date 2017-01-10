<?php   defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends CI_Model {
  	public function __construct() {
          // Call the CI_Model constructor
          parent::__construct();
          $this->config->load('general');
         // $this->load->database();
    }
    
    public function total_usuarios(){
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

    public function get_curso_usuario(){
        $resultado = array();
        
        $this->db->limit(10);
        $query = $this->db->get('mdl_course'); //Obtener conjunto de encuestas

        $resultado = $query->result_array();
        
        $query->free_result(); //Libera la memoria

        return $resultado;
    }
/*
    public function get_usuarios_curso($params=array()){
        $resultado = array('result'=>false, 'data'=>null);
        $this->db->where($params);
        $query = $this->db->get('mdl_course'); //Obtener conjunto de registros
        //pr($query);
        if ($query->num_rows()>0){
            $resultado['result'] = true;
            $resultado['data'] = $query->result_array();
        }

        //pr($this->db->last_query());
        return $resultado;
    }*/
}
