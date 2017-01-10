<?php   defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que gestiona el login
 * @version 	: 1.0.0
 * @autor 		: Pablo José
 */
class Evaluacion extends CI_Controller
{
    
    /**
     * * Carga de clases para el acceso a base de datos y para la creación de elementos del formulario
     * * @access 		: public
     * * @modified 	: 
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->library('form_complete');
        $this->load->library('form_validation');
        $this->load->model('Encuestas_model', 'enc_mod');
        $this->load->model('Curso_model', 'cur_mod');
    }

    public function index()
    {
        $datos['total_encuestas'] = $this->enc_mod->total_encuestas();
        $datos['listado_encuestas'] = $this->enc_mod->get_encuestas();
        $datos['listado_preguntas'] = $this->enc_mod->get_preguntas_encuesta(array('encuesta_cve'=>1));
        $datos['listado_cursos'] = $this->cur_mod->get_cursos();
        pr($datos);

    }

    public function reporte()
    {
        // metodo que muestra un reporte de las evaluaciones que a tenido un usuario



    }
}
