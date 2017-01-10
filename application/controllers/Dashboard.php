<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//require_once ("Login_controller.php");

class Dashboard extends Controller {

    /**
     * Carga de clases para el acceso a base de datos y obtencion de las variables de session
     * @access      : public
     */
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->config->load('general'); 
        $this->load->model('Encuestas_model', 'enc_mod');       
        
    }

    public function logeo($id=NULL) {

         
         if(isset($id))
         {
       
         $usuario = $this->enc_mod->usuario_existe($id);
         if (isset($usuario)) 
         {
            $token=md5(uniqid(rand(),TRUE));
            $usuario_data = array(
               'id' => $usuario->id,
               'nombre' => $usuario->nombre.' '.$usuario->apellidos,
               'logueado' => TRUE,
               'token' => $token
               
            );
            //pr($usuario_data);
            $this->session->set_userdata($usuario_data);
            redirect('encuestas');
         } else {
            redirect('http://innovaedu.imss.gob.mx/educacionadistancia/login/index.php');
         }
        } 
        else {
            echo 'entra';
              redirect('http://innovaedu.imss.gob.mx/educacionadistancia/login/index.php');
        }

    }



    private function get_array_valor($array_busqueda, $key) {
        if (array_key_exists($key, $array_busqueda)) {
            $array_result = $array_busqueda[$key];
            return $array_result;
        }
        return array();
    }


}
