<?php   defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que gestiona el login
 * @version     : 1.0.0
 * @autor       : Pablo José
 */
class Resultadocursoindicador extends CI_Controller
{
    /**
     * * Carga de clases para el acceso a base de datos y para la creación de elementos del formulario
     * * @access        : public
     * * @modified  : 
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->library('form_complete');
        $this->load->library('form_validation');
        $this->load->model('Reporte_model', 'rep_mod'); // modelo de reporte
        $this->load->model('Curso_model', 'cur_mod'); // modelo de cursos
        $this->load->model('Encuestas_model', 'encur_mod'); // modelo de cursos
        $this->load->helper(array('form'));
    }

    public function index($curso=null)
    {
        //redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        
        /*$anios = $this->lista_anios();
        
        //$datos_grupos = $this->cur_mod->listar_grupos_curso(array('cur_id'=>$curso));
        $datos_indicador = $this->encur_mod->get_indicador_curso(array('conditions'=>array('eva.course_cve'=>$curso, 'eva.evaluador_rol_id'=>$this->config->item('ENCUESTAS_ROL_EVALUADOR')['ALUMNO']), 'fields'=>'distinct(ind.*)'));
        $data['datos_indicador'] = dropdown_options($datos_indicador, 'indicador_cve', 'descripcion');
        $data['datos_bono'] = array('0'=>'Bloque 1', '1'=>'Bloque 2', '2'=>'Bloque 3', '3'=>'Bloque 4', '4'=>'Bloque 5');
        //pr($datos_curso);
        //pr($datos_indicador);

        $data['order_columns'] = array('tipo_indicador_cve' => 'Indicador', 'is_bono' => 'Bono');
        

        $main_contet = $this->load->view('curso/cur_enc_indicador', $data, true);*/
        $this->load->model('Reporte_model', 'rep_mod'); // modelo de reporte
        //Obtiene los filtros para reporte
        $data = $this->rep_mod->get_filtros_generales_reportes();
        $data['datos_curso'] = $this->cur_mod->listado_cursos(array('cur_id'=>$curso));
        //Quitar lo que no se utiliza
        $unset = array('buscar_por');
        foreach ($unset as $k_value) {
            unset($data[$k_value]);
        }
        $data['curso']=$curso;
        $main_contet = $this->load->view('curso/cur_enc_indicador', $data, true);
        $this->template->setMainTitle('Reporte de encuestas por indicador');
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();        
    }  

    public function get_data_ajax( $curso = null, $current_row=null)
    {
        if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
            if (isset($curso) && !empty($curso)) {            
            
                if ($this->input->post()) { //Se verifica que se haya recibido información por método post
                    //aqui va la nueva conexion a la base de datos del buscador
                    //Se guarda lo que se busco asi como la matricula de quien realizo la busqueda
                    $filtros = $this->input->post(null, true);                    
                    $filtros['current_row'] = (isset($current_row) && !empty($current_row)) ? $current_row : 0;
                    $filtros['curso_cve'] = (isset($curso) && !empty($curso)) ? $curso : '';
                    $filtros['conditions'] = ' ev.evaluador_rol_id='.$this->config->item('ENCUESTAS_ROL_EVALUADOR')['ALUMNO'].' AND ';
                    $data=$filtros;
                    $data['current_row'] = $filtros['current_row'];
                    $data['per_page'] = $this->input->post('per_page');
                    $data['curso']= $filtros['curso'];
                    //$data['encuestacve']='';
                    $error = "";
                    $data['error']=$error;
                    
                    //Checar el tipo de curso
                    $datos_curso=$this->cur_mod->listado_cursos(array('cur_id'=>$curso));
                                         
                    $resultado = $this->encur_mod->get_promedio_encuesta_indicador($filtros); //Datos del formulario se envían para generar la consulta segun los filtros
                    //pr($resultado);
                   
                    $data['total_empleados'] = $resultado['total'];
                    $data['registros'] = $resultado['data'];
                    $data['indicadores'] = $resultado['indicadores'];                                    
                    
                    $this->listado_resultado($data, array('form_recurso' => '#form_curso', 'elemento_resultado' => '#listado_resultado')); //Generar listado en caso de obtener datos
                
                }
            }else{
            redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        
            }
        }else{
        
            redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        
        }
    }

    private function listado_resultado($data, $form){
        echo $data['error'].'<br>';
        $data['encuestacve']=0;
   
        $pagination = $this->template->pagination_data_curso_encuesta($data); //Crear mensaje y links de paginación
        //$links = "<div class='col-sm-5 dataTables_info' style='line-height: 50px;'>".$pagination['total']."</div><div class='col-sm-7 text-right'>".$pagination['links']."</div>";
        $links = '';
        echo $links.$this->load->view('curso/listado_indicador', $data, TRUE).$links.'
            <script>
            $("ul.pagination li a").click(function(event){
                data_ajax($(this).attr("href"), "'.$form['form_recurso'].'", "'.$form['elemento_resultado'].'");
                event.preventDefault();
            });
            </script>';
    }

    private function lista_anios()
    {
        $anios = $this->rep_mod->get_anios();
        foreach ($anios as $key => $value) {
          $anios[] = array('anio_id'=>$value['fecha'], 'anio_desc'=>$value['fecha']);
        }
        return $anios;
    }

    public function export_data($curso=null){
     if (isset($curso) && !empty($curso)) {
       if ($this->input->post()) {
            //Se verifica que se haya recibido información por método post
            $filtros = $this->input->post(null, true);
                    
            $filtros['current_row'] = (isset($current_row) && !empty($current_row)) ? $current_row : 0;
            $filtros['curso_cve'] = (isset($curso) && !empty($curso)) ? $curso : '';
            $data=$filtros;
            $data['current_row'] = $filtros['current_row'];
            $data['per_page'] = $this->input->post('per_page');
            $data['curso']= $filtros['curso'];
            //$data['encuestacve']='';
            $error = "";
            $data['error']=$error;
                    
            //Checar el tipo de curso
            $datos_curso=$this->cur_mod->listado_cursos(array('cur_id'=>$curso));
            //pr($datos_curso);
            //die();
                                         
            $resultado = $this->encur_mod->get_promedio_encuesta_indicador($filtros); //Datos del formulario se envían para generar la consulta segun los filtros
            //pr($resultado);
               
            $data['total_empleados'] = $resultado['total'];
            $data['registros'] = $resultado['data'];
            $data['indicadores'] = $resultado['indicadores'];
            //pr($data['total_empleados']);
            
            if($data['total_empleados'] > 0){
                //$this->listado_resultado($data_sesiones, array('form_recurso'=>'#form_buscador', 'elemento_resultado'=>'#listado_resultado')); //Generar listado en caso de obtener datos
                $filename="Export_".date("d-m-Y_H-i-s")."_".$datos_curso['data'][0]['cur_id'].".xls";
                header("Content-Type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=$filename");
                header("Pragma: no-cache");
                header("Expires: 0");
                echo $this->load->view('curso/listado_indicador', $data, TRUE);
            } else {
                echo data_not_exist('No han sido encontrados datos con los criterios seleccionados. <script> $("#btn_export").hide(); </script>'); //Mostrar mensaje de datos no existentes
            }
        }
    
   }
  }

}
