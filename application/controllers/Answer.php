<?php   defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class that allows the User evaluate their teachers
 * @version 	: 1.0.0
 * @autor 		: Miguel Guagnelli
 */
class Answer extends CI_Controller{
    
    /**
     * * Load the general objects needed to the class works propertly
     * * @access 		: public
     * * @modified 	: 
     */
    public function __construct(){
        parent::__construct();
        $this->load->library('form_complete');
        $this->load->library('form_validation');
        
    }

    public function index(){
        $cuestionario = $this->input->get('encuesta');
        $cuestionario = $this->input->get('curso');
        $this->load->model('Answer_model', 'ans_mod');
        $data["cuestionario"] = $this->ans_mod->getQuestionnaire();
        $content = $this->load->view('answer/index.tpl.php', $data, TRUE);
        $this->template->setMainContent($content);
        
        $this->template->getTemplate();

    }
}
