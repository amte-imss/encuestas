<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Clase que contiene métodos para codigo de seguridad captcha
 * @version 	: 1.0.0
 * @author      : Pablo José
 **/
class Captcha_becas {
    
    /***********Costructor
    *Función inicial que atrae los atributos de captcha y session
    *
    */
    public function __construct() {
    	$this->CI =& get_instance();
        $this->CI->load->helper('captcha');
        $this->CI->load->library('session');
    }
    
    /***********Captcha
    *Función que genera una imagen captcha
    * recibe dos parametros (nombre_formulario, nombre_captcha_en_session)
    * Nota: por ahora, solo es para formularios creados en vistas
    *@method: void captcha()
    */
    
    function captcha($nom_form, $datos=array()){
		$data = $datos;
      
        $vals = array(
           'img_path'      => './captcha/',
           'img_url'       =>  base_url().'captcha/',
           'img_width'     => '220',
           'img_height'    => 50,
           'word_length'   => 6,
           'font_size'     => 24,
           'pool'          => '012345678abcdefhijklmnopstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
           'colors'        => array(
               'background' => array(255, 255, 255),
               'border' => array(255, 255, 255),
               'text' => array(10, 10, 10),
               'grid' => array(82, 246, 164)
			)
		);
       
        //$data['delegaciones'] = $datos['delegaciones'];
        //$data['delegaciones'] = $datos['delegaciones'];
        $data['captcha']=  create_captcha($vals);
        $this->CI->session->set_userdata($datos['capt_sess'],$data['captcha']['word']);
        
        $captcha = $this->CI->load->view($nom_form, $data, TRUE);
        return $captcha;
    }
        
	/***********Validar captcha
	* Función que valida el captcha con el texto introducido en el formulario
	* recibe dos parametros (texto_introducido, nombre_captcha_en_session)
	* @method: void check_captcha()
	* */        
	public function check_captcha($str, $datos=array()){
		$this->CI->load->library('session');
		$word = $this->CI->session->userdata('captchaWord');
        if(strcmp(strtoupper($str), strtoupper($word))==0){
            return true;
        } else {
            $this->CI->form_validation->set_message('txt_captcha','Por favor introduce correctamente los caracteres');
            return false;
        }
	}
    
	function captcha_config(){
        $params = array(
           'img_path'      => './captcha/',
           'img_url'       =>  base_url().'captcha/',
           'img_width'     => '220',
           'img_height'    => 50,
           'word_length'   => 6,
           'font_size'     => 24,
           'pool'          => '012345678abcdefhijklmnopstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
           'colors'        => array(
               'background' => array(255, 255, 255),
               'border' => array(255, 255, 255),
               'text' => array(10, 10, 10),
               'grid' => array(82, 246, 164)
           )
        );
        return $params;
    }
}