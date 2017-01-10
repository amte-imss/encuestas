<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
  	public function __construct() {
          // Call the CI_Model constructor
          parent::__construct();
          $this->config->load('general');
         // $this->load->database();
    }
    
    public function total_docentes_participantes($params=array()){
        $resultado = array('result'=>false, 'data'=>null);
        $this->db->select("count(distinct(empleado.empleado_cve)) as total, cve_delegacion, nom_delegacion");
        
        $this->db->join('cdelegacion', 'empleado.delegacion_cve=cdelegacion.cve_delegacion', 'inner');
        $this->db->join('emp_can_bono', 'empleado.empleado_cve=emp_can_bono.empleado_cve', 'inner');
        $this->db->join('can_bono_reg', 'emp_can_bono.emp_can_cve=can_bono_reg.emp_can_cve', 'inner');

        $this->db->group_by('cve_delegacion');

        $query = $this->db->get('empleado'); //Obtener conjunto de registros
        //pr($query);
        if ($query->num_rows()>0){
            $resultado['result'] = true;
            $resultado['data'] = $query->result_array();
        }

        //pr($this->db->last_query());
        return $resultado;
    }

    public function get_docentes($params=array()){
        $resultado = array();
        if(array_key_exists('fields', $params)){
            $this->db->select($params['fields']);
        }
        if(array_key_exists('conditions', $params)){
            $this->db->where($params['conditions']);
        }
        if(array_key_exists('order', $params)){
            $this->db->order_by($params['order']['field'], $params['order']['type']);
        }

        $this->db->join('cdelegacion', 'empleado.delegacion_cve=cdelegacion.cve_delegacion', 'inner');
        
        $query = $this->db->get('empleado'); //Obtener conjunto de registros
        $resultado = $query->result_array();
        
        $query->free_result(); //Libera la memoria

        return $resultado;
    }

    public function get_delegacion($params=array()){
        $resultado = array();
        if(array_key_exists('fields', $params)){
            $this->db->select($params['fields']);
        }
        if(array_key_exists('conditions', $params)){
            $this->db->where($params['conditions']);
        }
        if(array_key_exists('order', $params)){
            $this->db->order_by($params['order']['field'], $params['order']['type']);
        }
        
        $query = $this->db->get('cdelegacion'); //Obtener conjunto de registros
        $resultado = $query->result_array();
        
        $query->free_result(); //Libera la memoria

        return $resultado;
    }
}
