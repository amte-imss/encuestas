<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que gestiona el login
 * @version     : 1.0.0
 * @autor       : Pablo José
 */
class Control_filtros extends CI_Controller {

    /**
     * * Carga de clases para el acceso a base de datos y para la creación de elementos del formulario
     * * @access        : public
     * * @modified  : 
     */
    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->library('form_complete');
        $this->load->library('form_validation');
        //$this->load->model('Reporte_model', 'rep_mod'); // modelo de reporte
        $this->load->model('Reporte_model', 'rep_mod'); // modelo de cursos
    }

    public function index() {
        
    }

    public function get_data_ajax() {
        if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
            if (!is_null($this->input->post())) {//Verifica que lleguen datos por post 
                $data_post = $this->input->post(); //Obtiene datos del formulatrio
                switch ($data_post){
                    case '':
                        break;
                    case '':
                        break;
                    case '':
                        break;
                    case '':
                        break;
                }
            }
        } else {
            redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        }
    }

}
