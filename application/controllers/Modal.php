<?php   defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase de prueba para las ventanas modales
 * @version 	: 1.0.0
 * @autor 		: Pablo José J.
 */
class Modal extends CI_Controller {

    /**
     * Carga de clases para el acceso a base de datos y obtencion de las variables de session
     * @access 		: public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->helper(array('form','captcha','general'));
        $this->load->library('form_complete');
        $this->load->library('form_validation');
        $this->load->library('Ventana_modal');
        $this->load->model('Encuestas_model', 'enc_mod');


    }

    /**
     * Método que prueba la ventana modal.
     * @autor 		: Pablo José J.
     * @modified 	:
     * @access 		: public
     */
    public function index()
    {
        //$abrir_modal = "<a data-toggle='modal' data-target='#modal_censo' onclick='data_ajax(site_url+\"/modal/ajax_ejemplo_modal\", \"null\", \"#modal_content\")'>Modal uno</a>";// linea de codigo en HTML necesaria para cargar una ventana modal
        //echo "";// linea de codigo en HTML necesaria para cargar una ventana modal
            $datos['cuerpo_modal']="<h1>Hola mundo</h1>";

            $parametros = array(); // se crea una variable parametros de ejemplo para llenar algun metodo
            $datos['titulo_modal'] = "Prueba ventana modal"; // las variables importantes son titulo_modal y cuerpo_modal
            $datos['cuerpo_modal'] = $this->algun_metodo($parametros);// uso de algún metodo para llenar el cuerpo de la ventana modal

            //$cuerpo_modal = $this->ventana_modal->carga_modal($datos);
            $this->ventana_modal->carga_modal($datos); 
            $this->template->setCuerpoModal($this->ventana_modal->carga_modal($datos));   
            
        $this->template->setMainContent("<a data-toggle='modal' data-target='#modal_censo'>Modal uno</a>");
        $this->template->getTemplate();
             

    }

    public function mod_encuestas($id_instrumento=null)
    {
        if ($this->input->is_ajax_request()) {    
            if (isset($id_instrumento) && !empty($id_instrumento)) {
                    # code...
                    $datos1['instrumento']=$this->enc_mod->get_instrumento_detalle($id_instrumento);
                    $datos1['preguntas'] = $this->enc_mod->preguntas_instrumento($id_instrumento);
                    //$datos['listado_preguntas'] = $this->enc_mod->get_preguntas_encuesta(array('encuesta_cve'=>1));
                    //$datos['listado_cursos'] = $this->cur_mod->get_cursos();
                    //pr($datos);
                    $parametros = array(); // se crea una variable parametros de ejemplo para llenar algun metodo
                    //$datos['titulo_modal'] = "Instrumento"; // las variables importantes son titulo_modal y cuerpo_modal
                    $datos['cuerpo_modal'] = $this->load->view('encuesta/prev_encur', $datos1, true);// uso de algún metodo para llenar el cuerpo de la ventana modal

                    //$cuerpo_modal = $this->ventana_modal->carga_modal($datos);
                    echo $this->ventana_modal->carga_modal($datos);
                    //$main_contet = $this->load->view('encuesta/prev', $datos, true);
                    //$this->template->setMainContent($main_contet);
                    //$this->template->getTemplate();
            }
       }
        else {
            redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        }
    }

    /**
     * Método que llena una ventana modal a partir de la libreria ventana_modal y los parametros arrojados.
     * @autor 		: Pablo José J.
     * @modified 	:
     * @access 		: public
     */
    public function ajax_ejemplo_modal($id_registro = null)
    {
        if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
            /*  # variables para la ventana modal
            - ver_titulo         => true
            - ver_mensaje        => false
            - ver_footer         => false
            - pie_modal
            - titulo_modal
            - cuerpo_modal
            - msg_modal
            */

            $parametros = array(); // se crea una variable parametros de ejemplo para llenar algun metodo
            $datos['titulo_modal'] = "Prueba ventana modal"; // las variables importantes son titulo_modal y cuerpo_modal
            $datos['cuerpo_modal'] = $this->algun_metodo($parametros);// uso de algún metodo para llenar el cuerpo de la ventana modal

            //$cuerpo_modal = $this->ventana_modal->carga_modal($datos);
            echo $this->ventana_modal->carga_modal($datos); // se arroja toda la informacion que va a ir en la ventana modal
            //$this->ventana_modal->carga_modal($datos); 
            //$this->template->setCuerpoModal($this->ventana_modal->carga_modal($datos)); 
            //$this->template->getTemplate();

        } else {
            redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        }
    }

    public function algun_metodo($parametros = array())
    {
        $this->config->load('general');
        $tipo_msg = $this->config->item('alert_msg');
        /*
          aqui va la accion de algun metodo que retorne una vista tratada, modificada, alterada, etc., etc., etc.
        */
        $texto_modal = "<h1>Hola mundo</h1>";
        $texto_modal .= html_message('Mensaje SUCCESS!', $tipo_msg['SUCCESS']['class']);
        $texto_modal .= html_message('Mensaje DANGER!', $tipo_msg['DANGER']['class']);
        $texto_modal .= html_message('Mensaje WARNING!', $tipo_msg['WARNING']['class']);
        $texto_modal .= html_message('Mensaje INFO!', $tipo_msg['INFO']['class']);
        return $texto_modal;
    }

    public function guardar_pdf() {

        $listaRes =   $this->load->view('plantilla_pdf.tpl.php', $data, TRUE);
        $datos['html'] = $listaRes;

        $this->load->helper(array('dompdf'));

        $html = $datos['html'];
        $nombre_archivo = "Pdf_test_" . date("d-m-Y_H-i-s");

        generarPdf($html, $nombre_archivo);

    }

}
