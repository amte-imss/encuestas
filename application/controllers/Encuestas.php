<?php   defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que gestiona el login
 * @version 	: 1.0.0
 * @autor 		: Pablo José
 */
class Encuestas extends CI_Controller
{
    
    /**
     * * Carga de clases para el acceso a base de datos y para la creación de elementos del formulario
     * * @access 		: public
     * * @modified 	: 
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->library('form_validation'); //implemantación de la libreria form validation
        $this->load->library('form_complete'); // form complete
        $this->config->load('form_validation'); // abrir el archivo general de validaciones
        $this->config->load('general'); // instanciamos el archivo de constantes generales
        $this->load->model('Encuestas_model', 'enc_mod');
        $this->load->model('Curso_model', 'cur_mod');
        $this->load->library('csvimport');
    }


    /*public function index()
    {
        $datos['encuestas'] = $this->enc_mod->listado_instrumentos();
        //$datos['listado_preguntas'] = $this->enc_mod->get_preguntas_encuesta(array('encuesta_cve'=>1));
        //$datos['listado_cursos'] = $this->cur_mod->get_cursos();
        //pr($datos);

        $main_contet = $this->load->view('encuesta/encuestas', $datos, true);
        $this->template->setMainTitle("Listado de todas las encuestas registradas actualmente");
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();
    */
    public function index()
    {
        
        $user_id=$this->session->userdata();
        //var_dump($this->session->has_userdata('id')); //true

        //var_dump($this->session->userdata('token'));

        if($this->session->has_userdata('id'))
        {
               
            $datos['encuestas'] = $this->enc_mod->listado_instrumentos();
            //$datos['listado_preguntas'] = $this->enc_mod->get_preguntas_encuesta(array('encuesta_cve'=>1));
            //$datos['listado_cursos'] = $this->cur_mod->get_cursos();
            //pr($datos);
            
            $main_contet = $this->load->view('encuesta/encuestas', $datos, true);
            $this->template->setMainContent($main_contet);
            $this->template->getTemplate();            
        }
        else
        {
            $url_sied = $this->config->item('url_sied');
            redirect($url_sied);
            //redirect('http://11.32.41.13/kio/sied');        # code...
        }    
 
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
            redirect('encuestas/index');
         } else {
            redirect('http://11.32.41.92/kio/sied');
            //redirect('http://11.32.41.13/kio/sied');
         }
        } 
        else {
            //echo 'entra';
              redirect('http://11.32.41.92/kio/sied');
              //redirect('http://11.32.41.13/kio/sied');
        }

    }


    public function cargar_instrumento()
    {    
        $datos=array();

        $main_contet = $this->load->view('encuesta/carga_encuestas', $datos, true);
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();

    }

    public function ordenar_preguntas($id_instrumento=null, $seccion=null)
    {            
            if (isset($id_instrumento) && !empty($id_instrumento)) {
                    $datos=array();
                    $busqueda = array('encuesta_cve'=>$id_instrumento,'seccion_cve'=>$seccion);
                    $datos['instrumento']=$id_instrumento;
                    $datos['preguntas'] = $this->enc_mod->get_preguntas_encuesta($busqueda);

                    $main_contet = $this->load->view('encuesta/lista_preguntas', $datos, true);
                    $this->template->setMainContent($main_contet);
                    $this->template->getTemplate();
            }

    }

    public function ajax_orden()
    {            
        if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
            if (!is_null($this->input->post())) { //Se verifica que se haya recibido información por método post
                $preguntas_ordenadas = $_POST['pregunta'];

                $pos=1;
                $orden_preguntas = array();
                foreach ($preguntas_ordenadas as $key) {
                    $orden_preguntas[]=array('preguntas_cve'=>$key, 'orden'=>$pos);

                    $pos++;
                }

                    $guardado_orden = $this->enc_mod->guarda_orden_preguntas($orden_preguntas);

                    $this->config->load('general');
                    $tipo_msg = $this->config->item('alert_msg');

                    if ($guardado_orden == TRUE) {
                        echo html_message("El orden de las preguntas se ha actualizado", $tipo_msg['SUCCESS']['class']); 

                    }else{
                        echo html_message("No se ha podido actualizar el orden de las preguntas", $tipo_msg['WARNING']['class']); 

                    }

                }
        }

    }


    public function edita_instrumento($id_instrumento=null)
    {            
            if (isset($id_instrumento) && !empty($id_instrumento)) {
                    
                $tiene_evaluaciones = $this->enc_mod->tiene_evaluaciones($id_instrumento);

                $arre=$this->config->item('EVA_TIPO');
                foreach ($arre as $key => $value) {
                        $arrol[$value['valor']]=$value['text'];
     
                 }

               
                
                $datos=array();
                if (isset($tiene_evaluaciones[0]['tiene_evaluacion']) && $tiene_evaluaciones[0]['tiene_evaluacion']==0) {                    
                    $reglas = $this->enc_mod->get_reglas_evaluacion();
                    $datos['reglas_evaluacion']=dropdown_options($reglas, 'reglas_evaluacion_cve', 'nom_regla_desc');
                    $datos['instrumento']=$this->enc_mod->get_instrumento_detalle($id_instrumento);
                    $datos['tipo_instrumento']=$this->config->item('TIPO_INSTRUMENTOV');
                    $datos['eva_tipo']=$arrol;

                    
                    if ($this->input->post()) {
                        /*
                            [is_bono] => 1
                            [status] => 1
                            [descripcion_encuestas] => Encuesta ccttna prueba 2016
                            [cve_corta_encuesta] => CCTTNA2016
                            [regla_evaluacion_cve] => 1
                            [btn_submit] => Guardar instrumento

                        */
                        //pr($_POST);
                        $this->load->library('form_validation');                        
                        
                        //
                        //$this->form_validation->set_data($campos_pregunta);
                        
                        $validations = $this->config->item('edita_instrumento');
                    
                        //pr($validations);
                        //echo"estamos aqui";
                        $this->form_validation->set_rules($validations);

                        if($this->form_validation->run() == TRUE) //Se ejecuta la validación de datos
                        {
                            $campos_edita = $this->input->post(null, true);
                            $resultEdit = $this->enc_mod->guarda_edita_instrumento($id_instrumento, $campos_edita);

                            if ($resultEdit == true) {
                                $this->session->set_flashdata('success', 'El instrumento ha sido modificado correctamente'); // devuelve mensaje flash
                           
                            }else{
                                /* falta mensaje de que no fue actualizado*/
                            }

                        }
                        
                    }
                    //edit
                    //pr($datos);
                    $main_contet = $this->load->view('encuesta/edita_instrumento', $datos, true);
                    
                    $this->template->setMainContent($main_contet);
                    $this->template->getTemplate();
                    
                }else{
                    $this->session->set_flashdata('warning', 'No puede editar el instrumento ya que tiene historial'); // devuelve mensaje flash
                    redirect(site_url('encuestas'));
                }                


            }


    }

    public function drop_instrumento($id_instrumento=null)
    {   
        if ($this->input->is_ajax_request()) {

            if (isset($id_instrumento) && !empty($id_instrumento)) {
                    //$datos=array();
                $tiene_evaluaciones = $this->enc_mod->tiene_evaluaciones($id_instrumento);

                //pr($tiene_evaluaciones);

                //exit();
                if (isset($tiene_evaluaciones[0]['tiene_evaluacion']) && $tiene_evaluaciones[0]['tiene_evaluacion']==0) {
                                    
                    if($this->enc_mod->drop_instrumento($id_instrumento)){
                        $this->session->set_flashdata('success', 'El instrumento ha sido eliminado correctamente'); // devuelve mensaje flash
                            
                    }
                    
                }else{
                    $this->session->set_flashdata('warning', 'No puede eliminar el instrumento ya que tiene historial'); // devuelve mensaje flash
                    
                }  
                
                echo '
                <script type="text/javascript">
                    data_ajax(site_url + "/encuestas/get_encuestas_ajax", "#form_listado_encuestas", "#listado_resultado");
                </script>
                ';
                //$datos['encuestas'] = $this->enc_mod->listado_instrumentos();

                //redirect(site_url('encuestas'));

            }
        }else{
            redirect(site_url());
        }

    }

    public function delete_data_ajax_pregunta($pregunta_cve=null, $encuesta_cve = null)
    {
        if ($this->input->is_ajax_request()) {
            if (isset($pregunta_cve) && !empty($pregunta_cve)) {
                $drop_pregunta = $this->enc_mod->drop_pregunta($pregunta_cve);

                if($drop_pregunta == TRUE){
                    $this->session->set_flashdata('success', 'La pregunta ha sido eliminada'); // devuelve mensaje flash
                            
                }

                $datos['instrumento']=$this->enc_mod->get_instrumento_detalle($encuesta_cve);
                $datos['preguntas'] = $this->enc_mod->preguntas_instrumento($encuesta_cve);

                echo $this->load->view('encuesta/encuesta_preguntas', $datos, true);
            }
        }

    }

    public function block_instrumento($id_instrumento=null)
    {   
        if ($this->input->is_ajax_request()) {

            if (isset($id_instrumento) && !empty($id_instrumento)) {
                    //$datos=array();
                    $result_block = $this->enc_mod->block_instrumento($id_instrumento);
                    if($result_block == true){
                        //echo "Ok";
                        $this->session->set_flashdata('success', 'El instrumento ha sido desactivado correctamente'); // devuelve mensaje flash
                            
                    }else{
                        // error desconocido, no se pudo desactivar la encuesta
                    }
                    echo '
                        <script type="text/javascript">
                            data_ajax(site_url + "/encuestas/get_encuestas_ajax", "#form_listado_encuestas", "#listado_resultado");
                        </script>
                    ';
            }

        }else{
            redirect(site_url('encuestas'));
        }

    }

    public function unlock_instrumento($id_instrumento=null)
    {   
        if ($this->input->is_ajax_request()) {

            if (isset($id_instrumento) && !empty($id_instrumento)) {
                    //$datos=array();
                    $result_block = $this->enc_mod->unlock_instrumento($id_instrumento);
                    if($result_block == true){
                        //echo "Ok";
                        $this->session->set_flashdata('success', 'El instrumento ha sido activado correctamente'); // devuelve mensaje flash
                            
                    }else{
                        // error desconocido, no se pudo desactivar la encuesta
                    }
                    echo '
                        <script type="text/javascript">
                            data_ajax(site_url + "/encuestas/get_encuestas_ajax", "#form_listado_encuestas", "#listado_resultado");
                        </script>
                    ';
            }

        }else{
            redirect(site_url('encuestas'));
        }

    }

    public function edita_pregunta($id_pregunta=null, $id_instrumento=null)
    {            
        if (isset($id_pregunta) && !empty($id_pregunta)) {
                
                $tiene_evaluaciones = $this->enc_mod->tiene_evaluaciones($id_instrumento);

                $datos=array();

                if (isset($tiene_evaluaciones[0]['tiene_evaluacion']) && $tiene_evaluaciones[0]['tiene_evaluacion']==0) {                    

                    $datos=array('preguntas_cve'=>$id_pregunta, 'encuesta_cve'=>$id_instrumento);

                    $datos['pregunta']=$this->enc_mod->get_pregunta_detalle($id_pregunta, $id_instrumento);
                    if (!isset($datos['pregunta'][0])) {
                        $this->session->set_flashdata('warning', 'No se han encontrado los datos solicitados'); // devuelve mensaje flash
                        redirect(site_url('encuestas'));
                    }
                    $datos['tipo_pregunta']=$this->enc_mod->get_tipo_pregunta();

                    $secciones = $this->enc_mod->get_secciones();
                    $datos['secciones']=dropdown_options($secciones, 'seccion_cve', 'descripcion');
                    
                    $indicadores = $this->enc_mod->get_indicadores();
                    $datos['indicadores']=dropdown_options($indicadores, 'indicador_cve', 'descripcion');
                    
                    $preguntas_padre = $this->enc_mod->listado_preguntas_seccion($id_instrumento,$datos['pregunta'][0]['seccion_cve'],$id_pregunta);
                    $datos['preguntas_padre'] = dropdown_options($preguntas_padre, 'preguntas_cve', 'pregunta');
                    
                    if($this->input->post()) {
                        
                        /*
                        $datos=array(
                                'seccion_cve'=>$params['seccion_cve'],
                                'tipo_pregunta_cve'=>$params['tipo_pregunta_cve'],
                                'pregunta'=>$params['pregunta'],
                                'obligada'=>$params['obligada'],
                                'is_bono'=>$params['is_bono'],
                                'pregunta_padre'=>$params['pregunta_padre'],
                                'val_ref'=>$params['val_ref'],
                            );
                        */
                        
                        /*
                         *  [seccion_cve] => 56
                            [pregunta] => En caso de problema de acceso se comunicó por correo electrónico o vía telefónica a mesa de ayuda
                            [is_bono] => 1
                            [obligada] => 1
                            [no_obligatoria] => 1
                            [tipo_pregunta_radio] => 2
                         * 
                        */                        
                        
                        
                        $this->load->library('form_validation');                        
                        
                        //
                        //$this->form_validation->set_data($campos_pregunta);
                        
                        $validations = $this->config->item('edit_pregunta');
                    
                        //pr($validations);
                        //echo"estamos aqui";
                        $this->form_validation->set_rules($validations);

                        if($this->form_validation->run() == TRUE) //Se ejecuta la validación de datos
                        {
                            $this->config->load('general');
                            $respuestas = $this->config->item('ENCUESTAS_RESPUESTAS_PREGUNTA');

                            $campos_pregunta = $this->input->post(null, true);
                            //pr($campos_pregunta);
                            $tipos_pregunta = array(
                                1=>array('id'=>1, 'descripcion'=>'si|no'),
                                2=>array('id'=>3, 'descripcion'=>'siempre|nunca'),
                                3=>array('id'=>5, 'descripcion'=>'respuesta abierta'),
                            );
                            
                            $pregunta_radio = $tipos_pregunta[$campos_pregunta['tipo_pregunta_radio']]['id'];
                            $no_obligatoria = isset($campos_pregunta['no_obligatoria']) ? $campos_pregunta['no_obligatoria'] : 0;
                            $tipo_pregunta = ($pregunta_radio + $no_obligatoria);
                            $campos_pregunta['tipo_pregunta_cve'] = $tipo_pregunta;
                            $campos_pregunta['respuestas'] = $respuestas[$tipo_pregunta];
                            $campos_pregunta['pregunta_anterior']= $datos['pregunta'];
                            //pr($campos_pregunta);
                            $guardar_cambios = $this->enc_mod->update_pregunta($id_pregunta,$campos_pregunta);

                            if ($guardar_cambios == true) {
                                $this->session->set_flashdata('success', 'La pregunta ha sido modificada correctamente'); // devuelve mensaje flash                                
                            }

                        }
                        
                    }

                    $main_contet = $this->load->view('encuesta/edita_pregunta', $datos, true);
                    $this->template->setMainContent($main_contet);
                    $this->template->getTemplate();
                }else{
                    $this->session->set_flashdata('warning', 'No puede editar el instrumento ya que tiene historial'); // devuelve mensaje flash
                    redirect(site_url('encuestas'));
                }
        }

    }

    public function nueva_pregunta($id_instrumento=null)
    {            
        if (isset($id_instrumento) && !empty($id_instrumento)) {
                
                $tiene_evaluaciones = $this->enc_mod->tiene_evaluaciones($id_instrumento);

                $datos=array();

                if (isset($tiene_evaluaciones[0]['tiene_evaluacion']) && $tiene_evaluaciones[0]['tiene_evaluacion']==0) {                    

                    $datos=array('encuesta_cve'=>$id_instrumento);
                    
                    $datos['tipo_pregunta']=$this->enc_mod->get_tipo_pregunta();
                    $secciones = $this->enc_mod->get_secciones();
                    $datos['secciones']=dropdown_options($secciones, 'seccion_cve', 'descripcion');


                    $indicadores = $this->enc_mod->get_indicadores();
                    $datos['indicadores']=dropdown_options($indicadores, 'indicador_cve', 'descripcion');
                        
                    if ($this->input->post()) {

                        /*
                            validaciones
                        */
                        /*
                            *[seccion_cve] => 164
                            *[pregunta] => zckcvbxcn
                            [is_bono] => 1
                            [obligada] => 1
                            [no_obligatoria] => 1
                            [tipo_pregunta_radio] => 1
                            [btn_submit] => Guardar pregunta
                        */
                        /**/

                        $this->load->library('form_validation');                        
                        
                        //
                        //$this->form_validation->set_data($campos_pregunta);
                        
                        $validations = $this->config->item('nueva_pregunta');
                    
                        //pr($validations);
                        //echo"estamos aqui";
                        $this->form_validation->set_rules($validations);

                        if($this->form_validation->run() == TRUE) //Se ejecuta la validación de datos
                        {
                           
                            $campos_pregunta = $this->input->post(null, true);
                            //pr($campos_pregunta);
                            $this->config->load('general');
                            $respuestas = $this->config->item('ENCUESTAS_RESPUESTAS_PREGUNTA');

                            $seccion_id = $campos_pregunta['seccion_cve'];


                            $tipos_pregunta = array(
                                    1=>array('id'=>1, 'descripcion'=>'si|no'),
                                    2=>array('id'=>3, 'descripcion'=>'siempre|nunca'),
                                    3=>array('id'=>5, 'descripcion'=>'respuesta abierta'),
                                );
                            /*
                            iinvestigaa -> valores por default para un arreglo (renviar datos de que no existe la variable solicitada dinamicamente)
                            */
                            $pregunta_radio = $tipos_pregunta[$campos_pregunta['tipo_pregunta_radio']]['id'];
                            $no_obligatoria = isset($campos_pregunta['no_obligatoria']) ? $campos_pregunta['no_obligatoria'] : 0;
                            $tipo_pregunta = ($pregunta_radio + $no_obligatoria);
                            $campos_pregunta['tipo_pregunta_cve'] = $tipo_pregunta;
                            $pregunta['respuestas'] = $respuestas[$tipo_pregunta];

                            $pregunta['tipo_indicador_cve']= $campos_pregunta['tipo_indicador_cve'];
                            $pregunta['tipo_pregunta']['tipo_pregunta_cve']= $campos_pregunta['tipo_pregunta_cve'];
                            $pregunta['pregunta']= $campos_pregunta['pregunta'];
                            $pregunta['pregunta_obligada']= isset($campos_pregunta['obligada']) ? $campos_pregunta['obligada'] : 0;
                            $pregunta['pregunta_bono']= isset($campos_pregunta['is_bono']) ? $campos_pregunta['is_bono'] : 0;
                            //pr($pregunta);
                            /**/
                            $nuevaPreguntaRes = $this->enc_mod->guarda_nueva_pregunta($pregunta,$id_instrumento,$seccion_id);

                            if(isset($nuevaPreguntaRes['success']) && $nuevaPreguntaRes['success'] == TRUE){
                                $this->session->set_flashdata('success', 'La pregunta ha sido guardada correctamente'); // devuelve mensaje flash                                
                            }else{
                                // mensaje de error
                            }
                            

                        }
                        //pr($_POST);

                    }

                    $main_contet = $this->load->view('encuesta/nueva_pregunta', $datos, true);
                    $this->template->setMainContent($main_contet);
                    $this->template->getTemplate();
                }else{
                    $this->session->set_flashdata('warning', 'No puede editar el instrumento ya que tiene historial'); // devuelve mensaje flash
                    redirect(site_url('encuestas'));
                }
        }

    }

    /*public function respuestas_pregunta_padre($pregunta_cve=null)
    {
        ///
    }*/

    public function prev($id_instrumento=null)
    {
            if (isset($id_instrumento) && !empty($id_instrumento))
            {
                    # code...
                    $datos['instrumento']=$this->enc_mod->get_instrumento_detalle($id_instrumento);
                    $datos['preguntas'] = $this->enc_mod->preguntas_instrumento($id_instrumento);
                    //$datos['listado_preguntas'] = $this->enc_mod->get_preguntas_encuesta(array('encuesta_cve'=>1));
                    //$datos['listado_cursos'] = $this->cur_mod->get_cursos();
                    //pr($datos);

                    $main_contet = $this->load->view('encuesta/prev', $datos, true);
                    $this->template->setMainContent($main_contet);
                    $this->template->getTemplate();
            }

    }

    public function copy($id_instrumento=null)
    {
        if ($this->input->is_ajax_request()) {
            if (isset($id_instrumento) && !empty($id_instrumento)) {
                
                $tiene_evaluaciones = $this->enc_mod->tiene_evaluaciones($id_instrumento);
                //pr($tiene_evaluaciones);

                $datos=array();

                $copiado = $this->enc_mod->duplica_instrumento($id_instrumento);

                if(isset($copiado['success']) && $copiado['success']==TRUE){
                        //$this->session->set_flashdata('success', 'El instrumento ha sido duplicado correctamente'); // devuelve mensaje flash
                        //redirect(site_url('encuestas/edit/'.$instrumento_id,'refresh')); 
                        echo html_message("El instrumento ha sido duplicado correctamente", 'success');
                        echo "<script type='text/javascript'>
                        window.location.assign('".site_url('encuestas/edit/'.$copiado['instrumento_id'])."');
                        </script>

                        ";
                        exit();

                }else{
                    $this->session->set_flashdata('warning', 'Error desconocido, no es posible duplicar el instrumento, por favor notifiquelo al área correspondiente'); // devuelve mensaje flash
                    redirect(site_url('encuestas'));

                }
            }                   
        }else{
            redirect(site_url('encuestas'));
        }

    }

    public function edit($id_instrumento=null)
    {
            if (isset($id_instrumento) && !empty($id_instrumento)) {
                    # code...
                    $tiene_evaluaciones = $this->enc_mod->tiene_evaluaciones($id_instrumento);

                    $datos=array();

                    if (isset($tiene_evaluaciones[0]['tiene_evaluacion']) && $tiene_evaluaciones[0]['tiene_evaluacion']==0) {                    

                        $datos['instrumento']=$this->enc_mod->get_instrumento_detalle($id_instrumento);
                        $datos['preguntas'] = $this->enc_mod->preguntas_instrumento($id_instrumento);
                        //$datos['listado_preguntas'] = $this->enc_mod->get_preguntas_encuesta(array('encuesta_cve'=>1));
                        //$datos['listado_cursos'] = $this->cur_mod->get_cursos();
                        //pr($datos);

                        $main_contet = $this->load->view('encuesta/prev_instrumento_edit', $datos, true);
                        $this->template->setMainContent($main_contet);
                        $this->template->getTemplate();

                    }else{
                        $this->session->set_flashdata('warning', 'No puede editar el instrumento ya que tiene historial'); // devuelve mensaje flash
                        redirect(site_url('encuestas'));
                    }
            }

    }    
    
    public function get_encuestas_ajax($current_row = null) {
            if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
                if (!is_null($this->input->post())) { //Se verifica que se haya recibido información por método post
                    //aqui va la nueva conexion a la base de datos del buscador
                    //Se guarda lo que se busco asi como la matricula de quien realizo la busqueda
                    $filtros = $this->input->post();
                    $filtros['current_row'] = (isset($current_row) && !empty($current_row)) ? $current_row : 0;

                    //pr($filtros);
                    $resultado = $this->enc_mod->listado_instrumentos($filtros); //Datos del formulario se envían para generar la consulta segun los filtros
                    $data=$filtros;
                    $data['total_encuestas'] = $resultado['total'];
                    $data['encuestas'] = $resultado['data'];
                    $data['current_row'] = $filtros['current_row'];
                    $data['per_page'] = $this->input->post('per_page');
                    //pr($data);
                    $this->listado_resultado($data, array('form_recurso' => '#form_listado_encuestas', 'elemento_resultado' => '#listado_resultado')); //Generar listado en caso de obtener datos
                
                }

            } else {
            
                redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
            
            }
    }

    private function listado_resultado($data, $form)
    {
            $pagination = $this->template->pagination_data_encuestas($data); //Crear mensaje y links de paginación
            $links = "<div class='col-sm-5 dataTables_info' style='line-height: 50px;'>".$pagination['total']."</div>
                    <div class='col-sm-7 text-right'>".$pagination['links']."</div>";
            echo $links.$this->load->view('encuesta/listado_encuestas', $data, TRUE).$links.'
                <script>
                $("ul.pagination li a").click(function(event){
                    data_ajax(this, "'.$form['form_recurso'].'", "'.$form['elemento_resultado'].'");
                    event.preventDefault();
                });
                </script>';
    }

    /*
        # CARGA DE ARCHIVO CSV
    */
    function carga_csv()
    {
        if ($this->input->post()) {     // SI EXISTE UN ARCHIVO EN POST
            $data['error'] = '';        // DECLARAMOS LA VARIABLE DONDE SE ALMACENARAN LOS ERRORES
     
            $config['upload_path'] = './uploads/';      // CONFIGURAMOS LA RUTA DE LA CARGA PARA LA LIBRERIA UPLOAD
            $config['allowed_types'] = 'csv';           // CONFIGURAMOS EL TIPO DE ARCHIVO A CARGAR
            $config['max_size'] = '1000';               // CONFIGURAMOS EL PESO DEL ARCHIVO
            
            $this->load->library('upload', $config);    // CARGAMOS LA LIBRERIA UPLOAD

            if (!$this->upload->do_upload()) {  
                   // SI EL PROCESO DE CARGA ENCONTRO UN ERROR
                $data['error']['carga_csv'] = $this->upload->display_errors();      // CARGAR EN LA VARIABLE ERROR LOS ERRORES ENCONTRADOS

            }else{                      // SI NO SE ENCONTRARON ERRORES EN EL PROCESO DE CARGA
                $file_data = $this->upload->data();     //BUSCAMOS LA INFORMACIÓN DEL ARCHIVO CARGADO
                $file_path =  './uploads/'.$file_data['file_name'];         // CARGAMOS LA URL DEL ARCHIVO
     
                if ($this->csvimport->get_array($file_path)) {              // EJECUTAMOS EL METODO get_array() DE LA LIBRERIA csvimport PARA BUSCAR SI EXISTEN DATOS EN EL ARCHIVO Y VERIFICAR SI ES UN CSV VALIDO
                    $csv_array = $this->csvimport->get_array($file_path);   //SI EXISTEN DATOS, LOS CARGAMOS EN LA VARIABLE $csv_array                    
                                   
                    $insert_data = array(); // definimos un arreglo de desglose de informacion
                    
                    $count_errors = 0;// contador de errores
                    $array_error = array(); // arreglo de errores

                    $instrumento_id; // id_instrumento_temporal
                    //exit();
                    $numero_pregunta=1; // no pregunta temporal

                    foreach ($csv_array as $row) {  // RECORREMOS CADA FILA DE INFORMACION DEL ARCHIVO CARGADO EN LA VARIABLE TEMPORAL $row
                        //pr($row);
                        if (($this->validar_registros(json_encode($row)) === TRUE)){
                        // 1 clave
                        // 2 regls repetida
                        //    -> si
                        //    ejecuta carga csv

                         // si no existen errores de datos en las filas del csv
                            //pr($row);
                            
                            if (isset($instrumento_id) && !empty($instrumento_id)) {
                                



                                if ($instrumento_id !== (md5($row['NOMBRE_INSTRUMENTO']))) {
                                        $instrumento_id=md5($row['NOMBRE_INSTRUMENTO']);
                                        $numero_pregunta=1;
                                
                                }

                                if ($numero_pregunta != $row['NO_PREGUNTA'] ) {
                                        # el numero de pregunta no coincide con el seguimiento de las preguntas
                                        $count_errors=$count_errors+1; // generamos una bandera
                                        $row['error'] = "El conteo del número de preguntas del instrumento no coincide con el número de pregunta introducido en el campo NO_PREGUNTA"; // cargamos el arreglo de errores
                                        $row['numero_pregunta']=$numero_pregunta;
                                        $array_error['numero_pregunta'][]=$row;

                                }else{
                                    if (isset($row['PREGUNTA_PADRE']) && !empty($row['PREGUNTA_PADRE'])) {
                                        
                                        if ($row['PREGUNTA_PADRE'] < $numero_pregunta) {

                                            //$insert_data[] = $this->create_array_csv_encuestas($row);
                                            if ($this->verificar_respuesta_padre($csv_array, $instrumento_id, $row['PREGUNTA_PADRE'], $row['RESPUESTA_ESPERADA'])) {
                                                    $count_errors=$count_errors+1; // generamos una bandera
                                                    $row['error'] = "La respuesta esperada no existe en la pregunta padre"; // cargamos el arreglo de errores
                                                    $array_error['respuesta_pregunta_padre'][]=$row;
                                            }else{
                                                    $insert_data[] = $this->create_array_csv_encuestas($row);
                                            }

                                        }else{
                                            $count_errors=$count_errors+1; // generamos una bandera
                                            $row['error'] = "El número introducido en el campo PREGUNTA_PADRE debe ser menor a la pregunta hija"; // cargamos el arreglo de errores
                                            $array_error['pregunta_padre'][]=$row;

                                        }

                                    }else{
                                        
                                        $insert_data[] = $this->create_array_csv_encuestas($row);

                                    }                               

                                }

                            }else{

                                $instrumento_id=md5($row['NOMBRE_INSTRUMENTO']);
                                $numero_pregunta=1;

                                if ($numero_pregunta != $row['NO_PREGUNTA'] ) {
                                    # el numero de pregunta no coincide con el seguimiento de las preguntas
                                    $count_errors=$count_errors+1; // generamos una bandera
                                    $row['error'] = "El conteo del número de preguntas del instrumento no coincide con el número de pregunta introducido en el campo NO_PREGUNTA"; // cargamos el arreglo de errores
                                    $row['numero_pregunta']=$numero_pregunta;
                                    $array_error['numero_pregunta'][]=$row;

                                }else{
                                    if (isset($row['PREGUNTA_PADRE']) && !empty($row['PREGUNTA_PADRE'])) {
                                        
                                        if ($row['PREGUNTA_PADRE'] < $numero_pregunta) {
                                            //$insert_data[] = $this->create_array_csv_encuestas($row);
                                            if ($this->verificar_respuesta_padre($csv_array, $instrumento_id, $row['PREGUNTA_PADRE'], $row['RESPUESTA_ESPERADA'])) {
                                                    $count_errors=$count_errors+1; // generamos una bandera
                                                    $row['error'] = "La respuesta esperada no existe en la pregunta padre"; // cargamos el arreglo de errores
                                                    $array_error['respuesta_pregunta_padre'][]=$row;
                                            }else{
                                                    $insert_data[] = $this->create_array_csv_encuestas($row);
                                            }

                                        }else{
                                            $count_errors=$count_errors+1; // generamos una bandera
                                            $row['error'] = "El número introducido en el campo PREGUNTA_PADRE debe ser menor a la pregunta hija"; // cargamos el arreglo de errores
                                            $array_error['pregunta_padre'][]=$row;
                                        }

                                    }else{                                        
                                        $insert_data[] = $this->create_array_csv_encuestas($row);                                        

                                    }                                    

                                }
                                
                            }

                            //$instrumento_id=md5($row['NOMBRE_INSTRUMENTO']);
                            $numero_pregunta++;

                        }else{ // si la variable is_error esta activa
                            //echo "fasdfsd";
                            $count_errors=$count_errors+1; // generamos una bandera
                            $row['error'] = $this->validar_registros(json_encode($row)); // cargamos el arreglo de errores
                            $array_error['form_validation'][]=$row; // y lo asignamos al arreglo de datos
                        
                        }
                    
                    }

                    if($count_errors != 0){                     
                        $data['error']=$array_error;


                    
                    }else{
                        // si nuestra bandera no se activo
                        //echo "No hubo error F1";

                        $err_tipo_pregunta = $this->error_tipo_pregunta($insert_data);
                        
                        if ($err_tipo_pregunta['is_error']===FALSE) {
                            //if ($registro_en_regla['result'] == false) {
                            
                            $insertado=$this->enc_mod->carga_csv_instrumentos($insert_data); // ejecutamos la carga de datos a la base de datos
                            
                            if($insertado['success']==TRUE) // si la carga de datos fue exitsa
                            {
                                $this->session->set_flashdata('success', 'El archivo se guardo correctamente'); // devuelve mensaje flash
                            
                            }
                            /*}else{ // si no
                                //cho "Error F3";
                                $data['error'] = 'Uno de los instrumentos que intento cargar, requiere de una regla de evaluacion que ya esta ocupada'; //mandar error
                            
                            }*/
                        
                        }else{
                            //echo "<br>Error F2";
                            $data['error']['tipo_pregunta']['tipo_pregunta'] = $err_tipo_pregunta['error'];
                        
                        }
                    }

                    unlink($file_path); // eliminamos el archivo
                
                } else{ // si el carchivo cargado no es CSV
                    $data['error']['desconocido'] = "Ocurrió un error inesperado al cargar el arcivo";

                }

            }
            //pr($data);
            //$main_contet = $this->load->view('encuesta/carga_csv', $data, true);
            $this->template->setMainContent($main_contet);
            $this->template->getTemplate();
        
        }else{
            redirect(site_url('encuestas'));
        }
 
    }
/**/
    function carga_csv_datos()
    {
        if ($this->input->post()) {     // SI EXISTE UN ARCHIVO EN POST
           $data['error'] = '';        // DECLARAMOS LA VARIABLE DONDE SE ALMACENARAN LOS ERRORES
     
            $config['upload_path'] = './uploads/';      // CONFIGURAMOS LA RUTA DE LA CARGA PARA LA LIBRERIA UPLOAD
            $config['allowed_types'] = 'csv';           // CONFIGURAMOS EL TIPO DE ARCHIVO A CARGAR
            $config['max_size'] = '1000';               // CONFIGURAMOS EL PESO DEL ARCHIVO
            
            $this->load->library('upload', $config);    // CARGAMOS LA LIBRERIA UPLOAD
            $main_contet;
            if (!$this->upload->do_upload()) {  
                   // SI EL PROCESO DE CARGA ENCONTRO UN ERROR
                $data['error']['carga_csv'] = $this->upload->display_errors();      // CARGAR EN LA VARIABLE ERROR LOS ERRORES ENCONTRADOS

            }else{                      // SI NO SE ENCONTRARON ERRORES EN EL PROCESO DE CARGA
                $file_data = $this->upload->data();     //BUSCAMOS LA INFORMACIÓN DEL ARCHIVO CARGADO
                $file_path =  './uploads/'.$file_data['file_name'];         // CARGAMOS LA URL DEL ARCHIVO
                //pr($file_path);
                //echo "fasfa";
     
                if ($this->csvimport->get_array($file_path)) { 
                //echo "emtra";             // EJECUTAMOS EL METODO get_array() DE LA LIBRERIA csvimport PARA BUSCAR SI EXISTEN DATOS EN EL ARCHIVO Y VERIFICAR SI ES UN CSV VALIDO
                    $csv_array = $this->csvimport->get_array($file_path);   //SI EXISTEN DATOS, LOS CARGAMOS EN LA VARIABLE $csv_array                    
                                   
                    $insert_data = array(); // definimos un arreglo de desglose de informacion
                    
                    $count_errors = 0;// contador de errores
                    $array_error = array(); // arreglo de errores

                    $instrumento_id; // id_instrumento_temporal
                    //exit();
                    $numero_pregunta=1; // no pregunta temporal

                    //$exis_rule = 0;
                    $contador=0;

                    foreach ($csv_array as $row) {  // RECORREMOS CADA FILA DE INFORMACION DEL ARCHIVO CARGADO EN LA VARIABLE TEMPORAL $row
                        //pr($row);
                        if (($this->validar_registros(json_encode($row)) === TRUE)){
                        // 1 clave
                        // 2 regls repetida
                        //    -> si
                        //    ejecuta carga csv

                         // si no existen errores de datos en las filas del csv
                            //pr($row);

                        //Validad existencia de clave
                        if (isset($instrumento_id) && !empty($instrumento_id)) {

                            if ($instrumento_id !== (md5($row['NOMBRE_INSTRUMENTO']))) {

                                $instrumento_id=md5($row['NOMBRE_INSTRUMENTO']);
                                $numero_pregunta=1;
                            }
                            //echo $row['FOLIO_INSTRUMENTO'];
                            $existe_clave=$this->enc_mod->existe_clave($row['FOLIO_INSTRUMENTO']);
                            if(isset($existe_clave) && ($existe_clave == TRUE))
                            {
                                //No insertar y mandar mensaje
                                //echo "entra";
                                //die();
                                $array_error['carga_csv']= "No puedo insertar un instrumento con el mismo FOLIO_INSTRUMENTO."; // cargamos el arreglo de errores
                                $count_errors=$count_errors+1;
                           
                           }else{
                                //echo "entra2";

                                $this->config->load('general');
                                $rol_evalua = $this->config->item('ENCUESTAS_ROL_EVALUA');
                                $rol_evaluador = $this->config->item('ENCUESTAS_ROL_EVALUADOR');
                                $tipo_instrumento = $this->config->item('TIPO_INSTRUMENTO');

                                $existe_regla=$this->enc_mod->regla_disponible($rol_evaluador[$row['ROL_EVALUADOR']],$rol_evalua[$row['ROL_A_EVALUAR']]);
                                $params = array(
                                    'rol_evaluado_cve'=>$rol_evalua[$row['ROL_A_EVALUAR']],
                                    'rol_evaluador_cve'=>$rol_evaluador[$row['ROL_EVALUADOR']]
                                );
                                //$existe_regla=$this->enc_mod->regla_disponible($params);
                                
                                //pr($existe_regla);
                                /*if($existe_regla['result'] ==1)
                                {
                                    
                                    //echo "entra23";
                                    $data['mensaje_regla']= "Existen encuestas con la misma regla de evaluación.¿ Desea insertarla?"; // cargamos el arreglo de errores
                                    //$count_errors=$count_errors+1;
                                    $data['csv_array']=$csv_array;
                                    $data['exist_rule'] = TRUE;
                                    //$data['ruta_archivo']= $file_data;
                                    //$main_contet = $this->load->view('encuesta/confirma_carga_encuesta', $data, true);
                                    //$this->template->setMainContent($main_contet);
                                    //$this->template->getTemplate();
                                    //exit();
                                }else{*/
                                    //echo "entra22";
                                    //$this->carga_matriz_csv($csv_array);

                                    $data['guarda_matriz'] = TRUE;
                                    $data['vista_guarda_matriz']=$this->carga_matriz_csv($csv_array);
                                    
                                //}
                                    
                            }    


                        }else{
                            $instrumento_id=md5($row['NOMBRE_INSTRUMENTO']);
                            

                        } 

                        }else{ // si la variable is_error esta activa
                            //echo "fasdfsd";
                            $count_errors=$count_errors+1; // generamos una bandera
                            $row['error'] = $this->validar_registros(json_encode($row)); // cargamos el arreglo de errores
                            $array_error['form_validation'][]=$row; // y lo asignamos al arreglo de datos
                        
                        }
                    
                    }

                    if($count_errors != 0){                     
                        $data['error']=$array_error;

                    }else{
                       
                       //seguir proceso
                    }
                    //pr($data); 

                    unlink($file_path);
                    unset($array_error); // eliminamos el archivo
                
                } else{ // si el carchivo cargado no es CSV
                    $data['error']['desconocido'] = "Ocurrió un error inesperado al cargar el arcivo";

                }

            }
            //pr($data);
            if (isset($data['exist_rule']) && $data['exist_rule']== TRUE) {
                $main_contet = $this->load->view('encuesta/confirma_carga_encuesta', $data, true);
                
            }elseif (isset($data['guarda_matriz']) && $data['guarda_matriz']== TRUE) {
                //$data['vista_guarda_matriz']=$this->carga_matriz_csv($csv_array);
                $main_contet= $data['vista_guarda_matriz'];
            }else{
               $main_contet = $this->load->view('encuesta/carga_csv', $data, true); 
            }
            
            //
            $this->template->setMainContent($main_contet);
            $this->template->getTemplate();
        
        }else{
            redirect(site_url('encuestas'));
        }
 
    }
    function carga_matriz_form()
    {  
        
          if ($this->input->post()) {
              $informacion=$this->input->post();

           //pr($archivo);
           /*if(isset($archivo))
           {

               
            $config['upload_path'] = './uploads/';      // CONFIGURAMOS LA RUTA DE LA CARGA PARA LA LIBRERIA UPLOAD
            $config['allowed_types'] = 'csv';           // CONFIGURAMOS EL TIPO DE ARCHIVO A CARGAR
            $config['max_size'] = '1000';               // CONFIGURAMOS EL PESO DEL ARCHIVO
            
            $this->load->library('upload', $config);    // CARGAMOS LA LIBRERIA UPLOAD
            
               $finfo=$this->upload->data();
               pr($finfo);

               $data = file_get_contents($this->folder.$archivo); 
               //$n=force_download($name,$data); 

               //pr($data);
        
               /*$file_data['file_name']=$archivo;*/
                //$file_path=fopen ($file_path, "r");
                /*$config['upload_path'] = './uploads/';
                $config['allowed_types'] = 'csv';
                $this->load->library('upload', $config);
                $file_data = $this->upload->data();
                pr($file_data['file_name']);
                $file_path = './uploads/'.$file_data['file_name'];*/


               /*
               if ($this->csvimport->get_array($file_path)) {              // EJECUTAMOS EL METODO get_array() DE LA LIBRERIA csvimport PARA BUSCAR SI EXISTEN DATOS EN EL ARCHIVO Y VERIFICAR SI ES UN CSV VALIDO
                  echo "entra";                
                  $csv_array = $this->csvimport->get_array($file_path); 
                  $data['vista_guarda_matriz']=$this->carga_matriz_csv($csv_array);
                  $main_contet= $data['vista_guarda_matriz']; 
                  $this->template->setMainContent($main_contet);
                  $this->template->getTemplate();

               }
               else {
                echo "no entra";
                        # code...
                    }     
            }*/
          } 
          else
          {
            echo "fdsafs";
          } 
          

    }

    function carga_matriz_csv($csv_array)
    {
        //$csv_array = json_decode($csv_array_json);
        if (count($csv_array) > 0) {     // SI EXISTE UN ARCHIVO EN POST
            $data['error'] = '';        // DECLARAMOS LA VARIABLE DONDE SE ALMACENARAN LOS ERRORES
     
                    $count_errors = 0;// contador de errores
                    $array_error = array(); // arreglo de errores

                    $instrumento_id; // id_instrumento_temporal
                    //exit();
                    $numero_pregunta=1; // no pregunta temporal

                    foreach ($csv_array as $row) {  // RECORREMOS CADA FILA DE INFORMACION DEL ARCHIVO CARGADO EN LA VARIABLE TEMPORAL $row
                        //pr($row);
                        if (($this->validar_registros(json_encode($row)) === TRUE)){
                        // 1 clave
                        // 2 regls repetida
                        //    -> si
                        //    ejecuta carga csv

                         // si no existen errores de datos en las filas del csv
                            //pr($row);
                            
                            if (isset($instrumento_id) && !empty($instrumento_id)) {
                                



                                if ($instrumento_id !== (md5($row['NOMBRE_INSTRUMENTO']))) {
                                        $instrumento_id=md5($row['NOMBRE_INSTRUMENTO']);
                                        $numero_pregunta=1;
                                
                                }

                                if ($numero_pregunta != $row['NO_PREGUNTA'] ) {
                                        # el numero de pregunta no coincide con el seguimiento de las preguntas
                                        $count_errors=$count_errors+1; // generamos una bandera
                                        $row['error'] = "El conteo del número de preguntas del instrumento no coincide con el número de pregunta introducido en el campo NO_PREGUNTA"; // cargamos el arreglo de errores
                                        $row['numero_pregunta']=$numero_pregunta;
                                        $array_error['numero_pregunta'][]=$row;

                                }else{
                                    if (isset($row['PREGUNTA_PADRE']) && !empty($row['PREGUNTA_PADRE'])) {
                                        
                                        if ($row['PREGUNTA_PADRE'] < $numero_pregunta) {

                                            //$insert_data[] = $this->create_array_csv_encuestas($row);
                                            if ($this->verificar_respuesta_padre($csv_array, $instrumento_id, $row['PREGUNTA_PADRE'], $row['RESPUESTA_ESPERADA'])) {
                                                    $count_errors=$count_errors+1; // generamos una bandera
                                                    $row['error'] = "La respuesta esperada no existe en la pregunta padre"; // cargamos el arreglo de errores
                                                    $array_error['respuesta_pregunta_padre'][]=$row;
                                            }else{
                                                    $insert_data[] = $this->create_array_csv_encuestas($row);
                                            }

                                        }else{
                                            $count_errors=$count_errors+1; // generamos una bandera
                                            $row['error'] = "El número introducido en el campo PREGUNTA_PADRE debe ser menor a la pregunta hija"; // cargamos el arreglo de errores
                                            $array_error['pregunta_padre'][]=$row;

                                        }

                                    }else{
                                        
                                        $insert_data[] = $this->create_array_csv_encuestas($row);

                                    }                               

                                }

                            }else{

                                $instrumento_id=md5($row['NOMBRE_INSTRUMENTO']);
                                $numero_pregunta=1;

                                if ($numero_pregunta != $row['NO_PREGUNTA'] ) {
                                    # el numero de pregunta no coincide con el seguimiento de las preguntas
                                    $count_errors=$count_errors+1; // generamos una bandera
                                    $row['error'] = "El conteo del número de preguntas del instrumento no coincide con el número de pregunta introducido en el campo NO_PREGUNTA"; // cargamos el arreglo de errores
                                    $row['numero_pregunta']=$numero_pregunta;
                                    $array_error['numero_pregunta'][]=$row;

                                }else{
                                    if (isset($row['PREGUNTA_PADRE']) && !empty($row['PREGUNTA_PADRE'])) {
                                        
                                        if ($row['PREGUNTA_PADRE'] < $numero_pregunta) {
                                            //$insert_data[] = $this->create_array_csv_encuestas($row);
                                            if ($this->verificar_respuesta_padre($csv_array, $instrumento_id, $row['PREGUNTA_PADRE'], $row['RESPUESTA_ESPERADA'])) {
                                                    $count_errors=$count_errors+1; // generamos una bandera
                                                    $row['error'] = "La respuesta esperada no existe en la pregunta padre"; // cargamos el arreglo de errores
                                                    $array_error['respuesta_pregunta_padre'][]=$row;
                                            }else{
                                                    $insert_data[] = $this->create_array_csv_encuestas($row);
                                            }

                                        }else{
                                            $count_errors=$count_errors+1; // generamos una bandera
                                            $row['error'] = "El número introducido en el campo PREGUNTA_PADRE debe ser menor a la pregunta hija"; // cargamos el arreglo de errores
                                            $array_error['pregunta_padre'][]=$row;
                                        }

                                    }else{                                        
                                        $insert_data[] = $this->create_array_csv_encuestas($row);    
                                        //pr($insert_data);                                    

                                    }                                    

                                }
                                
                            }

                            //$instrumento_id=md5($row['NOMBRE_INSTRUMENTO']);
                            $numero_pregunta++;

                        }else{ // si la variable is_error esta activa
                            //echo "fasdfsd";
                            $count_errors=$count_errors+1; // generamos una bandera
                            $row['error'] = $this->validar_registros(json_encode($row)); // cargamos el arreglo de errores
                            $array_error['form_validation'][]=$row; // y lo asignamos al arreglo de datos
                        
                        }
                    
                    }

                    if($count_errors != 0){                     
                        $data['error']=$array_error;


                    
                    }else{
                        // si nuestra bandera no se activo
                        //echo "No hubo error F1";

                        $err_tipo_pregunta = $this->error_tipo_pregunta($insert_data);
                        
                        if ($err_tipo_pregunta['is_error']===FALSE) {
                            //if ($registro_en_regla['result'] == false) {
                            
                            $insertado=$this->enc_mod->carga_csv_instrumentos($insert_data); // ejecutamos la carga de datos a la base de datos
                            
                            if($insertado['success']==TRUE) // si la carga de datos fue exitsa
                            {
                                $this->session->set_flashdata('success', 'El archivo se guardo correctamente'); // devuelve mensaje flash
                            
                            }
                            /*}else{ // si no
                                //cho "Error F3";
                                $data['error'] = 'Uno de los instrumentos que intento cargar, requiere de una regla de evaluacion que ya esta ocupada'; //mandar error
                            
                            }*/
                        
                        }else{
                            //echo "<br>Error F2";
                            $data['error']['tipo_pregunta']['tipo_pregunta'] = $err_tipo_pregunta['error'];
                        
                        }
                    }

                    //unlink($file_path); // eliminamos el archivo
                

            //pr($data);
            $main_contet = $this->load->view('encuesta/carga_csv', $data, true);
            return $main_contet;
        
        }else{
            redirect(site_url('encuestas'));
        }
 
    }

/**/
    public function create_array_csv_encuestas($row=array())
    {

        if (isset($row) && !empty($row)) {
            
            $this->config->load('general'); // si ocupamos informacion genreal, creamos una instancia
            $respuesta = $this->config->item('ENCUESTAS_RESPUESTA'); //CARGAMOS ARREGLO DE RESPUESTAS DEFINIDAS

            $rol_evalua = $this->config->item('ENCUESTAS_ROL_EVALUA'); // obtenemos los valores de la constante ENCUESTAS_RESPUESTA
            $rol_evaluador = $this->config->item('ENCUESTAS_ROL_EVALUADOR'); // obtenemos los valores de la constante ENCUESTAS_RESPUESTA
            $obligada = ((!empty($row['OBLIGADA'])) ? strtoupper($row['OBLIGADA']) : 'NO' ); // SI EL CAMPO [OBLIGADA] no esta vacio transformar el texto en mayusculas, si el campo esta vacio arrojar el texto 'NO'
            $roles_evaluar = array('COORDINADOR_CURSO','COORDINADOR_TUTORES','TUTOR_TITULAR','TUTOR_ADJUNTO'); //arreglo de roles a evaluar
            $roles_evaluador = array('COORDINADOR_CURSO','COORDINADOR_TUTORES','TUTOR_TITULAR','TUTOR_ADJUNTO','ALUMNO','COORDINADOR_NORMATIVO'); //arreglo de roles a evaluar
            $rol_asignado = ((!empty($row['ROL_A_EVALUAR']) && in_array($row['ROL_A_EVALUAR'], $roles_evaluar)) ? $rol_evalua[$row['ROL_A_EVALUAR']] : 0 );  // si el campo [INSTRUMENTO_ROL_ASIGNADO] no esta vacio y si existe en uno de los
            $rol_asignado_evaluador = ((!empty($row['ROL_EVALUADOR']) && in_array($row['ROL_EVALUADOR'], $roles_evaluador)) ? $rol_evaluador[$row['ROL_EVALUADOR']] : 0 );  // si el campo [INSTRUMENTO_ROL_ASIGNADO] no esta vacio y si existe en uno de los
            $pregunta_bono = ((!empty($row['PREGUNTA_BONO'])) ? strtoupper($row['PREGUNTA_BONO']) : 'NO' );
            $tutorizado = ((!empty($row['TUTORIZADO'])) ? strtoupper($row['TUTORIZADO']) : 'NO' );
            $tipo_instrumento = $this->config->item('TIPO_INSTRUMENTO');
            $eva_tipo = $this->config->item('EVA_TIPO');


            $valido_no_aplica = ((!empty($row['VALIDO_NO_APLICA'])) ? strtoupper($row['VALIDO_NO_APLICA']) : 'NO' ); // SI EL CAMPO [VALIDO_NO_APLICA] no esta vacio transformar el texto en mayusculas, si el campo esta vacio arrojar el texto 'NO'

            $insert_data = array( // generamos el arreglo con los campos tratados
                                'id_instrumento_enc'=> md5($row['NOMBRE_INSTRUMENTO']), // generamos un id temporal para identificar al curso
                                'descripcion_encuestas'=>$row['NOMBRE_INSTRUMENTO'], // definimos el nombre del instrumento
                                'cve_corta_encuesta'=>$row['FOLIO_INSTRUMENTO'], // definimos el nombre del instrumento
                                'rol_a_evaluar'=>$rol_asignado, // definimos el rol asignado de la variable obtenida
                                'rol_evaluador'=>$rol_asignado_evaluador, // definimos el rol asignado de la variable obtenida
                                'instrumento_tutorizado'=>$respuesta['CERRADA'][$tutorizado], // definimos si el instrumento es para un curso tutorizado
                                'id_seccion_enc' => md5($row['NOMBRE_SECCION']),// definimos un id temporal de sección de preguntas
                                'descripcion'=>$row['NOMBRE_SECCION'], // definimos el nombre de la sección de la pregunta
                                'orden_pregunta'=>$row['NO_PREGUNTA'], // definimos el numero de la pregunta
                                //'orden_pregunta_padre'=>$row['PREGUNTA_PADRE'], // definimos el numero de la pregunta padre
                                //'respuesta_esperada'=>$row['RESPUESTA_ESPERADA'], // definimos el nombre de la respuesta esperada
                                'pregunta'=>$row['PREGUNTA'], // definimos el texto de la pregunta
                                'pregunta_bono'=>$respuesta['CERRADA'][$pregunta_bono], // definimos si la pregunta aplica para bono
                                'pregunta_obligada'=>$respuesta['CERRADA'][$obligada], // definimos si la pregunta es obligada
                                'tipo_pregunta'=> $this->tipo_pregunta($row), //se define el tipo de pregunta con sus respectivas respuestas
                                'tipo_encuesta'=> $tipo_instrumento[$row['TIPO_INSTRUMENTO']],
                                'eva_tipo'=> $eva_tipo[$row['EVA_TIPO']],
                                //'tipo_indicador_cve'=> $row['TIPO_INDICADOR_CVE'],
                                'valido_no_aplica'=> $respuesta['CERRADA'][$valido_no_aplica],
                                'id_indicador_enc' => md5($row['NOMBRE_INDICADOR']),// definimos un id temporal de INDICADOR de preguntas
                                'descripcion_indicador'=>$row['NOMBRE_INDICADOR'],

                            );
            
            return $insert_data;

        }else{
            redirect(site_url('encuestas'));
        }

    }


    public function tipo_pregunta($row=array())
    {            
            $respuesta = $this->config->item('ENCUESTAS_RESPUESTA'); // obtenemos los valores de la constante ENCUESTAS_RESPUESTA
            $tipo_pregunta = $this->config->item('ENCUESTAS_TIPO_PREGUNTA'); // obtenemos los valores de la constante ENCUESTAS_TIPO_PREGUNTA
            $respuesta_esperada = $this->config->item('ENCUESTAS_RESPUESTA_ESPERADA'); //obtenemos los valores de la constante ENCUESTAS_RESPUESTA_ESPERADA

            $es_nulo = ((!empty($row['NO_APLICA'])) ? strtoupper($row['NO_APLICA']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $si= ((!empty($row['SI'])) ? strtoupper($row['SI']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $no= ((!empty($row['NO'])) ? strtoupper($row['NO']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $siempre= ((!empty($row['SIEMPRE'])) ? strtoupper($row['SIEMPRE']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $casi_siempre= ((!empty($row['CASI_SIEMPRE'])) ? strtoupper($row['CASI_SIEMPRE']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $algunas_veces= ((!empty($row['ALGUNAS_VECES'])) ? strtoupper($row['ALGUNAS_VECES']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $casi_nunca= ((!empty($row['CASI_NUNCA'])) ? strtoupper($row['CASI_NUNCA']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $nunca= ((!empty($row['NUNCA'])) ? strtoupper($row['NUNCA']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $respuesta_abierta = ((!empty($row['RESPUESTA_ABIERTA'])) ? strtoupper($row['RESPUESTA_ABIERTA']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            //$valido_no_aplica = ((!empty($row['VALIDO_NO_APLICA'])) ? strtoupper($row['VALIDO_NO_APLICA']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO
            $no_envio_mensaje = ((!empty($row['NO_ENVIO_MENSAJE'])) ? strtoupper($row['NO_ENVIO_MENSAJE']) : 'NO' ); //convertimos el valor en UPPER si viene vacio lo definimos como valor NO


            $reactivos = ( // GENERAMOS UNA SUMA DE REACTIVOS PARA TRANSFORMARLO EN TIPO DE PREGUNTA
            $respuesta['CERRADA'][$es_nulo] + 
            $respuesta['CERRADA'][$si] + 
            $respuesta['CERRADA'][$no] + 
            $respuesta['CERRADA'][$siempre] + 
            $respuesta['CERRADA'][$casi_siempre] + 
            $respuesta['CERRADA'][$algunas_veces] + 
            $respuesta['CERRADA'][$nunca] + 
            $respuesta['CERRADA'][$casi_nunca] + 
            $respuesta['NO_ENVIO_MENSAJE'][$no_envio_mensaje] + 
            $respuesta['ABIERTA'][$respuesta_abierta]

            );
            //pr($reactivos);

            $error_tipo_pregunta;
            if ($reactivos<=9 && !in_array($reactivos, array(2,3,5,6,7,8,9))) { // SI EL VALOR DEL REACTIVO NO SE ENCUENTRA EN EL ARREGLO Y ES MENOR A 8
                    $reactivos=1; // ERROR EN EL LLENADO DE LAS RESPUESTAS // FALTAN RESPUESTAS
                    $error_tipo_pregunta = "El grupo de opciones de respuestas seleccionado no es correcto"; // ASIGNAMOS EL ERROR
                    
            }elseif($reactivos>9){ // SI EL VALOR DE LOS REACTIVOS SON MAYORES A 8
                    $reactivos = 1; //DEFINIMOS COMO RESPUESTA INDEFINIDA
                    // no puede seleccionar todas las opciones respuestas
                    $error_tipo_pregunta = "No puede seleccionar todas las opciones respuestas"; // ASIGNAMOS EL ERROR
            }elseif(in_array($reactivos, array(7,8))  && $respuesta_abierta !== 'SI') { // Si el valor de los reactivos se encuentra en el arreglo pero la respuesta_abierta no esta activa
                    $reactivos =1; // ERROR EN EL LLENADO DE LAS RESPUESTAS // A SELECCIONADO MAS DE LAS RESPUESTAS VALIDAS

                    $error_tipo_pregunta = "El grupo de opciones de respuesta seleccionado no esta clasificado"; // asignamos el error

            }elseif(in_array($reactivos, array(5,6)) && ($si ==='SI' OR $no ==='SI')) { // Si el valor de los reactivos se encuentran en el arrreglo pero las respuestas 'SI' Y 'NO' no estan vacias
                    $reactivos =1; // su seleccion de respuestas no es valida defina si es el grupo de respuestas, se esperaban respuestas diferentes de SI o NO

                    $error_tipo_pregunta = "La seleccion de opciones de respuestas no es valida, se esperaban respuestas diferentes de SI o NO"; // asignamos el error

            }elseif(in_array($reactivos, array(2,3,8)) && ($si !=='SI' OR $no !=='SI')) { // Si el valor de los reactivos se encuentran en el arreglo pero las respuestas SI y NO no estan activadas
                    $reactivos =1; // su seleccion de respuestas no es valida, se esperan respuestas SI, NO, NULO

                    $error_tipo_pregunta = "La seleccion de opciones de respuestas no es valida, se esperaban las opciones de respuesta: SI, NO, NULO";                    

            }

            elseif(in_array($reactivos, array(2,3,9)) && ($si !=='SI' OR $no !=='SI')) { // Si el valor de los reactivos se encuentran en el arreglo pero las respuestas SI y NO no estan activadas
                    $reactivos =1; // su seleccion de respuestas no es valida, se esperan respuestas SI, NO, NULO

                    $error_tipo_pregunta = "La seleccion de opciones de respuestas no es valida, se esperaban las opciones de respuesta: SI, NO, NO ENVIO MENSAJE";                    

            }

            elseif($reactivos==9 && ($si !=='SI' OR $no !=='SI')) { // Si el valor de los reactivos se encuentran en el arreglo pero las respuestas SI y NO no estan activadas
                    $reactivos =1; // su seleccion de respuestas no es valida, se esperan respuestas SI, NO, NULO

                    $error_tipo_pregunta = "La seleccion de opciones de respuestas no es valida, se esperaban las opciones de respuesta: SI, NO, NO ENVIO MENSAJE";                    

            }
            // FALTA DEFINIR SI LA PREGUNTA PADRE ES MENOR EN EL ORDEN DE LA PREGUNTA HIJA
            // BUSCAR EN EL ARREGLO SI LA POSICION DE LA PREGUNTA PADRE EXISTE LA RESPUESTA ESPERADA
        
            $pregunta_completa = $tipo_pregunta[$reactivos]; // se asigna el contador que define el tipo de pregunta
            if(isset($row['PREGUNTA_PADRE']) && !empty($row['PREGUNTA_PADRE']) && isset($row['RESPUESTA_ESPERADA']) && !empty($row['RESPUESTA_ESPERADA'])){
                $pregunta_completa['respuesta_esperada'] = $respuesta_esperada[$row['RESPUESTA_ESPERADA']];

            }
            if ($reactivos===1) {
                $pregunta_completa['is_error'] =  TRUE;
                $pregunta_completa['error'] = $error_tipo_pregunta;
            }

            return $pregunta_completa;

    }

    public function get_respuesta_esperada_ajax($res_val=null)
    {
            if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
                if (!is_null($this->input->post())) { //Se verifica que se haya recibido información por método post
                    //aqui va la nueva conexion a la base de datos del buscador
                    //Se guarda lo que se busco asi como la matricula de quien realizo la busqueda
                    $campos = $this->input->post();

                    $respuestas = $this->enc_mod->listado_respuestas_pregunta($campos['pregunta_padre']); //Datos del formulario se envían para generar la consulta segun los filtros
                    $data['respuestas']=dropdown_options($respuestas, 'reactivos_cve','texto');
                    $data['res_val'] = (isset($res_val)) ? $res_val : '';
                    echo $this->load->view('encuesta/respuesta_esperada', $data, TRUE);
                }

            } else {
            
                redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
            
            }
    }

    public function error_tipo_pregunta($instrumento=null)
    {
        if (!empty($instrumento)) {

            $errores=array('is_error'=>FALSE);
            foreach ($instrumento as $pregunta) {                   
                    if (isset($pregunta['tipo_pregunta']['is_error'])) {
                        //echo $pregunta['tipo_pregunta']['desc_error']."<br>";
                        $errores['is_error']=TRUE;
                        $errores['error'][]=$pregunta;
                    }
            }

            return $errores;

        }else{
            redirect(site_url('encuestas'));
        }
    }

    
    public function verificar_respuesta_padre($instrumento=null, $instrumento_id=null, $no_pregunta_padre=null, $respuesta_esperada=null)
    {
        if (!empty($instrumento)) {
            $error=TRUE;
            
            foreach ($instrumento as $row) {
                if ($instrumento_id == md5($row['NOMBRE_INSTRUMENTO'])){                    
                    if ($no_pregunta_padre == $row['NO_PREGUNTA']) {                        
                        if ( !empty($row[$respuesta_esperada]) && strtoupper($row[$respuesta_esperada])=='SI') {
                            $error=FALSE;

                        }
                    }
                }
            }

            return $error;

        }else{
            redirect(site_url('encuestas'));
        }
    }


    public function validar_registros($row=null)
    {
        
        if (!empty($row)) {
            $this->load->library('form_validation');
            
            $data = json_decode($row, true);
            $this->form_validation->set_data($data);
            $validations = $this->config->item('pregunta_instrumento');

            $this->form_validation->set_rules($validations);

            if ($this->form_validation->run() !== FALSE) {
                $this->form_validation->reset_validation();
                return TRUE;

            } else {

                $errores = array(
                        'NOMBRE_INSTRUMENTO' => form_error('NOMBRE_INSTRUMENTO'),
                        'FOLIO_INSTRUMENTO' => form_error('FOLIO_INSTRUMENTO'),
                        'ROL_A_EVALUAR' => form_error('ROL_A_EVALUAR'),
                        'ROL_EVALUADOR' => form_error('ROL_EVALUADOR'),
                        'TUTORIZADO' => form_error('TUTORIZADO'),
                        'NOMBRE_SECCION' => form_error('NOMBRE_SECCION'),
                        'NOMBRE_INDICADOR' => form_error('NOMBRE_INDICADOR'),
                        'NO_PREGUNTA' => form_error('NO_PREGUNTA'),
                        //'PREGUNTA_PADRE' => form_error('PREGUNTA_PADRE'),
                        //'RESPUESTA_ESPERADA' => form_error('RESPUESTA_ESPERADA'),
                        'PREGUNTA_BONO' => form_error('PREGUNTA_BONO'),
                        'OBLIGADA' => form_error('OBLIGADA'),
                        'PREGUNTA' => form_error('PREGUNTA'),
                        'NO_APLICA' => form_error('NO_APLICA'),
                        'VALIDO_NO_APLICA' => form_error('VALIDO_NO_APLICA'),
                        'NO_ENVIO_MENSAJE' => form_error('NO_ENVIO_MENSAJE'),
                        'SI' => form_error('SI'),
                        'NO' => form_error('NO'),
                        'SIEMPRE' => form_error('SIEMPRE'),
                        'CASI_SIEMPRE' => form_error('CASI_SIEMPRE'),
                        'ALGUNAS_VECES' => form_error('ALGUNAS_VECES'),
                        'CASI_NUNCA' => form_error('CASI_NUNCA'),
                        'NUNCA' => form_error('NUNCA'),
                        'RESPUESTA_ABIERTA' => form_error('RESPUESTA_ABIERTA'),
                        'TIPO_INSTRUMENTO' => form_error('TIPO_INSTRUMENTO'),
                        'EVA_TIPO' => form_error('EVA_TIPO'),
                    );
                
                $this->form_validation->reset_validation();

                return $errores;
                
            }            

        }else{
            redirect(site_url('encuestas'));
        }

    }

    /*
     * Exporta detalles de encuesta, preguntas y opciones de respuesta a archivo XLS. Mismo formato que plantilla utilizada para importación de datos de encuesta
     * @param   $id_instrumento integer Identificador del instrumento a exportar
     * @return  Archivo xls
     */
    public function exportar_xls($id_instrumento=0)
    {
        if($id_instrumento===0)
        {
            redirect(site_url('encuestas'));
        }
        $data = $this->enc_mod->exportar_xls_datos($id_instrumento); //Obtener datos
        ////Generación de cabeceras
        $archivo = "Exportar_instrumento_" . date("d-m-Y_H-i-s") . ".xls"; //Nombre de archivo
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8;");
        header("Content-Encoding: UTF-8");
        header("Content-Disposition: attachment; filename=$archivo");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM. Necesaria para que respete acentos
        echo $this->load->view('encuesta/exportar_xls', $data, TRUE);
    }
    
    /*
     * Exporta detalles de encuesta, preguntas y opciones de respuesta a archivo PDF.
     * @param   $id_instrumento integer Identificador del instrumento a exportar
     * @return  Archivo pdf
     */
    public function exportar_pdf($id_instrumento=0)
    {
        if($id_instrumento===0)
        {
            redirect(site_url('encuestas'));
        }
        $this->load->library('my_dompdf');
        
        $data = $this->enc_mod->exportar_xls_datos($id_instrumento); //Obtener datos
        
        $vista = $this->load->view('encuesta/exportar_pdf', $data, TRUE); //Obtener vista
        
        $this->my_dompdf->convert_html_to_pdf('Exportar_instrumento_'.date("d-m-Y_H-i-s"), $vista); //Exportar a PDF
    }
}
