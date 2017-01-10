<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_general_model extends CI_Model {

    public function __construct() {
        // Call the CI_Model constructor
        parent::__construct();
        $this->config->load('general');
        // $this->load->database();
    }

    public function get_reporte_gral_datos($parametros) {
        $result_reporte = array();
        $parametros;
        if ($parametros['tipo_curso'] == 1) {//Is tutorizado
            $result_reporte = array(
                array(
                    
                )
                
            );
        } else {//No tutorizado
            $result_reporte = array(
                
            );
        }

        return $result_reporte;
    }

}
