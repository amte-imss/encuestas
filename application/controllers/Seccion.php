<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Seccion extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
                $this->db->schema = 'encuestas';
		$this->load->helper('url');

		//$this->load->library('grocery_CRUD');
		$this->load->library('form_validation');
	}

	public function new_crud(){
        $db_driver = $this->db->platform();
        //pr($db_driver);

        $model_name = 'grocery_crud_model_'.$db_driver;
        $model_alias = 'm'.substr(md5(rand()), 0, rand(4,15) );

        unset($this->{$model_name});
        $this->load->library('grocery_CRUD');
        $crud = new Grocery_CRUD();
        if (file_exists(APPPATH.'/models/'.$model_name.'.php')){
            $this->load->model('grocery_crud_model');
            $this->load->model('grocery_crud_generic_model');
            $this->load->model($model_name, $model_alias);
            $crud->basic_model = $this->{$model_alias};
        }
        return $crud;
    }

	function secciones($encuesta_cve=null)
	{
		try{
			$crud = $this->new_crud();

		    //$crud = new grocery_CRUD();

		    $crud->set_table('sse_seccion')
		        ->set_subject('Sección')
		        ->columns('seccion_cve','descripcion')
		        ->display_as('seccion_cve','#')
		        ->display_as('descripcion','Nombre sección');
	     
		    $crud->fields('descripcion');
		    $crud->required_fields('descripcion');
		    //$crud->set_rules('descripcion','Nombre sección','required|alpha_numeric_accent_space');
	        $crud->order_by('descripcion','ASC');
	        $crud->unset_edit();
	        $crud->unset_delete();
	        $crud->unset_read();
	        
		    $output = $crud->render();
		    /*pr($output);
		    exit();
		    */

		    $output->encuesta_cve = $encuesta_cve;
		    
		 	$this->template->setMainContent($this->load->view('gc_output',$output, TRUE));
		 	$this->template->getTemplate();

	 	}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}

	}

}