<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registro_model extends CI_Model {
	public function __construct() {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->database();
    }
    
    public function getDelegacion($params=array()) {
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
        
        $query = $this->db->get('rist_delegacion'); //Obtener conjunto de registros
        $resultado = $query->result_array();
        
        $query->free_result(); //Libera la memoria

        return $resultado;
    }

    public function getSesion($params=array()){
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
        
        $query = $this->db->get('rist_agenda'); //Obtener conjunto de registros
        $resultado = $query->result_array();
        //pr($this->db->last_query());
        $query->free_result(); //Libera la memoria

        return $resultado;
    }

    public function getTaller($params=array()) {

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

        $this->db->join('rist_usuario AS U', 'U.usr_matricula=T.usr_matricula');
        $this->db->join('rist_agenda AS A', 'T.agenda_id=A.agenda_id');
        
        $query = $this->db->get('rist_taller AS T'); //Obtener conjunto de registros
        
        $resultado['data'] = $query->result_array();
        $resultado['total'] = $query->num_rows();
        //pr($this->db->last_query());
        $query->free_result(); //Libera la memoria

        return $resultado;
    }

    public function getCupo($agenda){
        //SELECT *, (SELECT count(*) FROM rist_taller T WHERE T.agenda_id=A.agenda_id AND t_estado IN (1,2)) AS total FROM rist_agenda AS A WHERE agenda_id=2 AND a_estado=1;
        $estado_agenda = $this->config->item('estado_agenda');
        $estado_taller = $this->config->item('estado_taller');

        $resultado = array();

        $this->db->select('*, (SELECT count(*) FROM rist_taller T WHERE T.agenda_id=A.agenda_id AND t_estado IN ('.$estado_taller['ACTIVO']['id'].','.$estado_taller['REAGENDADO']['id'].')) AS ocupado');

        $this->db->where('agenda_id='.$agenda.' AND a_estado='.$estado_agenda['ACTIVO']['id']);

        $query = $this->db->get('rist_agenda AS A'); //Obtener conjunto de registros
        
        $resultado = $query->result_array();
        //pr($this->db->last_query());
        $query->free_result(); //Libera la memoria

        return $resultado;
    }

    public function guardarUsuarioTaller($usuario, $taller){
        $resultado = array('result'=>null, 'msg'=>'', 'data'=>null);
        $this->db->trans_begin(); //Definir inicio de transacción

        $validarUsuario = $this->getUsuario(array('conditions'=>'usr_matricula=\''.$usuario->usr_matricula.'\''));
        
        if($validarUsuario['total']<1)
        {
            $this->db->insert('rist_usuario', $usuario); //Inserción de usuario
        } else {
            $this->db->where('usr_matricula', $usuario->usr_matricula);
            $this->db->update('rist_usuario', array('usr_correo'=>$usuario->usr_correo)); //Actualización de correo solamente
        }
        $this->db->insert('rist_taller', $taller); //Inserción de taller

        $taller_id = $this->db->insert_id(); //Obtener identificador insertado
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $resultado['result'] = FALSE;
            $resultado['msg'] = "Ocurrió un error durante el guardado, por favor intentelo de nuevo más tarde.";
        } else {
            $this->db->trans_commit();
            $resultado['data'] = $taller_id;
            $resultado['result'] = TRUE;
        }

        return $resultado;
    }

    public function guardarCancelacion($taller_id, $taller_data){
        $resultado = array('result'=>null, 'msg'=>'', 'data'=>null);
        $this->db->trans_begin(); //Definir inicio de transacción
        
        $this->db->where('taller_id', $taller_id);
        $this->db->update('rist_taller', array('t_estado'=>$taller_data['t_estado'])); //Actualización de estado
                
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $resultado['result'] = FALSE;
            $resultado['msg'] = "Ocurrió un error durante el guardado, por favor intentelo de nuevo más tarde.";
        } else {
            $this->db->trans_commit();
            $resultado['result'] = TRUE;
        }

        return $resultado;
    }

    public function getUsuario($params=array()) {

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
        
        $query = $this->db->get('rist_usuario'); //Obtener conjunto de registros
        
        $resultado['data'] = $query->result_array();
        $resultado['total'] = $query->num_rows();
        //pr($this->db->last_query());
        $query->free_result(); //Libera la memoria

        return $resultado;
    }

    public function getAsistencia($params=array()){
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
        
        $query = $this->db->get('rist_asistencia'); //Obtener conjunto de registros
        
        $resultado['data'] = $query->result_array();
        $resultado['total'] = $query->num_rows();
        //pr($this->db->last_query());
        $query->free_result(); //Libera la memoria

        return $resultado;
    }

}
