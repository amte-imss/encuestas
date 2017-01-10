<?php   defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que gestiona las pruebas del captcha
 * @version 	: 1.0.0
 * @autor 		: Pablo José J.
 */
class Captcha extends CI_Controller {

    /**
     * Carga de clases para el acceso a base de datos y obtencion de las variables de session
     * @access 		: public
     */
    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->helper(array('form','captcha'));
        $this->load->library('form_complete');
        $this->load->library('form_validation');

    }

    /**
     * Método que prueba la funcion captcha reload mediante ajax.
     * @autor 		: Pablo José J.
     * @modified 	:
     * @access 		: public
     */
    public function get_new_captcha_ajax(){
    		if($this->input->is_ajax_request()){ //Solo se accede al método a través de una petición ajax
            $error = ""; // Variable requerida para ejecutar la funcion captcha
            $word = $this->new_captcha($error); // actualiza el captcha
      		  echo $word; // se imprime la palabra word del captcha
    		}
	  }

    /**
     * Método que carga un nuevo captcha.
     * @autor 		: Pablo José J.
     * @modified 	:
     * @access 		: public
     */
    private function new_captcha($error=""){
        $data['error'] = (isset($error) && !empty($error)) ? $error : "";
        $data['captcha'] = create_captcha($this->captcha_config());
        $this->session->set_userdata('captchaWord', $data['captcha']['word']);

        $form_captcha = $this->load->view('captcha.tpl.php', $data, TRUE); // Carga de el archivo tpl de la imagen captcha
        return $form_captcha;
    }

    /**
     * Método que carga la configuracion del captcha.
     * @autor 		: Pablo José J.
     * @modified 	:
     * @access 		: public
     */
    private function captcha_config(){
        $params = array(
            'img_path'      => './captcha/',
            'img_url'       =>  base_url().'captcha/',
            'img_width'     => 220,
            'img_height'    => 50,
            'word_length'   => 5,
            'expiration'    => 7200,
            'font_size'     => array(30, true),
            'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'colors'        => array(
                'background' => array(76, 175, 80),
                'border' => array(165, 214, 167),
                'text' => array(0, 0, 0),
                'grid' => array(232, 245, 233)
            )
        );
        return $params;
    }
    /*
    public function index() {
        //pr(DIRECTORY_SEPARATOR);
        $error = "";
        $this->template->setMainContent($this->new_captcha($error));
        $this->template->getTemplate();

    }
    */


}
