<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// HELPER General
/**
 * Método que preformatea una cadena
 * @autor 		: Jesús Díaz P.
 * @param 		: mixed $mix Cadena, objeto, arreglo a mostrar
 * @return  	: Cadena preformateada
 */
 class Seguridad {
	 
	public function __construct() {
    	$this->CI =& get_instance();
        $this->CI->load->library('session');
    }
 
	public function crear_token(){
        return md5(uniqid(rand(),TRUE));
    }
  
    public function token(){
        $this->CI->session->set_userdata('token', $this->crear_token());
        return;
    }

    public function crear_token_url(){
        return hash('sha512', uniqid(rand(),TRUE));
    }

    /**
     * Método que codifica una cadena a base 64
     * @autor       : Jesús Díaz P.
     * @modified    : 
     * @param       : string $string Cadena a codificar
     * @return      : string Cadena codificada
     */
    public function encrypt_base64($string){
        //return base64_encode($string); //convert_uuencode($string);
        //return strtr(base64_encode($string), '+/=', '-_*');
        return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
    }

    /**
     * Método que decodifica una cadena en base 64
     * @autor       : Jesús Díaz P.
     * @modified    : 
     * @param       : string $string Cadena a decodificar
     * @return      : string Cadena decodificada
     */
    public function decrypt_base64($string){
        //return base64_decode($string); //convert_uudecode($string);
        //return base64_decode(strtr($string, '-_*', '+/='));
        return base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
    }
	
    /**
     * Método que encripta una cadena con el algoritmo sha256
     * @autor       : Jesús Díaz P.
     * @modified    : 
     * @param       : string $string Cadena a decodificar
     * @return      : string Cadena decodificada
     */
    public function encrypt_sha256($string){
        return hash('sha256', $string);
    }

    /**
     * Método que encripta una cadena con el algoritmo sha512
     * @autor       : Jesús Díaz P.
     * @modified    : 
     * @param       : string $string Cadena a decodificar
     * @return      : string Cadena decodificada
     */
    public function encrypt_sha512($string){
        return hash('sha512', $string);
    }


    public function folio_random($limit = 6, $anadirEspecial=false)
    {
        $cadena_base =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; //Alfa
        $cadena_base .= '0123456789'; //Números
        if($anadirEspecial)
        {
            $cadena_base .= '%&*_,.!'; //Caracteres especiales
        }

        $password = '';
        $limite = strlen($cadena_base) - 1;

        for($i=0; $i < $limit; $i++)
        {
            $password .= $cadena_base[rand(0, $limite)];
        }

        return $password;
    }
}