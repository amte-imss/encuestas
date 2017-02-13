<?php

if (!defined('BASEPATH'))
    exit('NO DIRECT SCRIPT ACCESS ALLOWED');

class Iniciar_sesion {

    var $CI;

    function login() {
        $CI = & get_instance();

        $CI->load->library('session');
        $CI->load->helper('url');
        $CI->config->load('general');

        $logueado = $CI->session->userdata('logueado'); ///Obtener datos de sesión
        $usuario_id = $CI->session->userdata('id');
        $sesion_id = $CI->session->userdata('token');
        $controlador = $CI->uri->segment(1);  //Controlador
        $accion = $CI->uri->segment(2);  //Accion
        $is_ajax = $CI->input->is_ajax_request();  //Accion

        $datos_['sesion_iniciada'] = $logueado;
//        pr($CI->session->userdata());
        if (!is_null($logueado)) {
            if (permiso_acceso_ruta($controlador, $accion, $is_ajax)) {//Verifica que el rol del usuario permita el accesos a por lo menos un módulo
            } else {
                echo $CI->load->view('template/sin_acceso', $datos_, true);
                exit();
            }
        } else {//Si el usuario no se encuentra con sesión iniciada
            $no_logueo = $CI->config->item('menu_no_logueado');
            $concat = $controlador . '/' . $accion;
            $valida = 0;
            foreach ($no_logueo as $value) {
                if ($value == $concat) {
                    $valida = 1;
                    break;
                }
            }
            if (!$valida) {
                echo $CI->load->view('template/sin_acceso', $datos_, true);
                exit();
            }
        }
    }

//    function login(){
//        $CI =& get_instance();
//        
//        $CI->load->library('session');
//        $CI->load->helper('url');
//        $CI->config->load('general');
//
//        $logueado = $CI->session->userdata('logueado'); ///Obtener datos de sesión
//        $usuario_id = $CI->session->userdata('id');
//        $sesion_id = $CI->session->userdata('token');
////        pr($logueado);
//        if($logueado){
//
//            $modulos_permitidos = $CI->config->item('menu_super_admin');
//
//        }else{
//
//            $modulos_permitidos = $CI->config->item('menu_no_logueado');
//
//        }
//
//        //$modulos_permitidos = ($tipo_admin==$tipo_admin_config['ADMIN']['id']) ? $CI->config->item('menu_admin') : $CI->config->item('menu_docente');
//        //pr($modulos_permitidos); pr($CI->session->userdata());
//
//        $controlador = $CI->uri->segment(1);  //Controlador
//        $accion = $CI->uri->segment(2);  //Accion
//        $accion = (empty($accion) || is_null($accion)) ? 'index' : $accion;
//        $accion_total = "*";
//
//        $excepciones = array('login'=>array('*'),'encuestausuario'=>array('*')); //Excepciones, acceso sin sesión activa
//        $no_accesos = array('login'=>array('logeo')); //No acceso, con sesión activa
//        $url_sied = $CI->config->item('url_sied');
//                
//        $bandera_excepcion = $bandera_no_acceso = FALSE;
//        if((empty($logueado) || is_null($logueado))){ //En caso de que no cuente con datos en sesión
//            foreach ($excepciones as $key_excepcion => $excepcion) { //Recorremos listado de excepciones
//                if(($controlador==$key_excepcion && in_array($accion, $excepcion)) || ($controlador==$key_excepcion && in_array($accion_total, $excepcion))) { //Verificamos si la ruta actual se encuentra dentro de las excepciones
//                    $bandera_excepcion = TRUE;
//                }
//            }
//            if($bandera_excepcion===FALSE){
//                if($CI->input->is_ajax_request()){
//                    redirect('login/cerrar_session_ajax');
//                } else {
//                    redirect($url_sied);
//                    exit();
//                }
//            }
//        } else { //En caso de que existan datos en sesión
//            $bandera_encontrado=FALSE;
//            foreach ($modulos_permitidos as $key_mod_perm => $modulo_permitido) {
//                //echo "controlador: $controlador - key_mod_perm: $key_mod_perm - accion: $accion - modulo_permitido: $modulo_permitido - accion_total: $accion_total <br>";
//                if(($controlador==$key_mod_perm && in_array($accion, $modulo_permitido)) || ($controlador==$key_mod_perm && in_array($accion_total, $modulo_permitido))){
//                    $bandera_encontrado=TRUE;
//                }
//            }
//            foreach ($no_accesos as $key_excepcion_login => $no_acceso) { //Recorremos listado de no accesos
//                if($controlador==$key_excepcion_login && in_array($accion, $no_acceso)){ //Verificamos si la ruta actual se encuentra dentro de los no acceso
//                    $bandera_no_acceso = TRUE;
//                }
//            }
//
//            if($bandera_encontrado===FALSE || $bandera_no_acceso===TRUE){
//                redirect('encuestas');
//                exit();
//            }
//        }
//    }
}
