<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*@author: LI. Miguel Guagnelli
*@version: 1.0
*@desc: Clase padre de los controladores del sistema
**/

class MY_Controller extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
    }
	
	public function check_captcha_form($str){
		$datos['capt_sess']='txt_captcha';
		return $this->captcha_becas->check_captcha($str,$datos);                        
	}  
	
	/*
    Explicación $\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$
    $ = Inicio de cadena
    \S* = Cualquier set de caracteres
    (?=\S{8,}) = longitud de al menos 8 caracteres
    (?=\S*[a-z]) = asegurar que al menos existe una letra minúscula
    (?=\S*[A-Z]) = asegurar que al menos existe una letra mayúscula
    (?=\S*[\d]) = asegurar que al menos exista un número
    (?=\S*[\W]) = y asegurar que al menos tenga un caracter especial (+%#.,);
    $ = fin de la cadena*/
	function valid_pass($candidate) {
		if (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[-+%#.,;:\d])\S*$', $candidate, $condiciones)){
			return FALSE;
		}
		return TRUE;
	}
	
	/** Explicación
	* ^                               - A partir de la linea/cadena
		(?=.{8})                       - busqueda incremental para asegurar que se tienen 8 caracteres
		(?=.*[A-Z])                    - ...para asegurar que tenemos al menos un caracter en mayuscula
		(?=.*[a-z])                    - ...para asegurar que tenemos al menos un caracter en minuscula
		(?=.*\d.*\d.*\d                - ...para asegurar que tenemos al menos tres digitos
		(?=.*[^a-zA-Z\d].*[^a-zA-Z\d].*[^a-zA-Z\d]) 
		                             - ...para asegurar que tiene al menos 3 caracteres especiales (caracteres diferentes a letras y numeros)
		[-+%#a-zA-Z\d]+                - combinacion de caracteres permitidos
		$                              - fin de la linea/cadena
	*/
	public function password_strong($str){
		//$exp = '/^(?=.{8})(?=.*[A-Z])(?=.*[a-z])(?=.*\d.*\d.*\d)(?=.*[^a-zA-Z\d].*[^a-zA-Z\d].*[^a-zA-Z\d])[-+%#a-zA-Z\d]+$/u';
		$exp = '/^(?=.{8})(?=.*[A-Z])(?=.*[a-z])(?=.*\d.*\d.*\d)(?=.*[^a-zA-Z\d].*[^a-zA-Z\d].*[^a-zA-Z\d])[-+%#a-zA-Z.,;:\d]+$/u';
		return ( ! preg_match($exp, $str)) ? FALSE : TRUE;
	}
}