<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que gestiona el login
 * @version     : 1.0.0
 * @autor       : Pablo José
 */
class Inicio extends CI_Controller {

    /**
     * * Carga de clases para el acceso a base de datos y para la creación de elementos del formulario
     * * @access        : public
     * * @modified  : 
     */
    public function __construct() {
        parent::__construct();

    }

    public function index() {
		$user_id=$this->session->userdata();
		$modulos_acceso = $this->session->userdata("modulos_acceso");               
		$datos['modulos_acceso'] = $modulos_acceso;
 
		$main_contet = $this->load->view('home_pagina', $datos, true);
		$this->template->setMainContent($main_contet);
		$this->template->setMainTitle("");
		$this->template->getTemplate();            
    }

}
