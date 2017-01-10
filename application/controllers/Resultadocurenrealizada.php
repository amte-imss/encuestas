<?php   defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que muestra resultado de encuestas realizadas
 * @version     : 1.0.0
 * @autor       : Hilda Trejo
 */
class Resultadocurenrealizada extends CI_Controller
{
    /**
     * * Carga de clases para el acceso a base de datos y para la creación de elementos del formulario
     * * @access        : public
     * * @modified  : 
     */
    
    // Implementa dataTabes en el metodo resultadorealizado
    const DATA_TABLES_RESULTADO_REALIZADO =  true;
    
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->library('form_complete');
        $this->load->library('form_validation');
        //$this->load->model('Reporte_model', 'rep_mod'); // modelo de reporte
        $this->load->model('Curso_model', 'cur_mod'); // modelo de cursos
        $this->load->model('Encuestas_model', 'encur_mod'); // modelo de cursos
        $this->load->helper(array('form'));
        $this->load->model('Reporte_model', 'rep_mod'); // modelo de reporte
    }

    public function index()
    {
        redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        
    }    

    public function info($curso=null)
    {        

        $data['curso'] = $this->cur_mod->listado_cursos(array('cur_id'=>$curso));
        $data['roles'] = $this->cur_mod->listar_roles_curso(array('cur_id'=>$curso));
        $data['grupos'] = $this->cur_mod->listar_grupos_curso(array('cur_id'=>$curso));
        //pr($data); exit();
        
        $main_contet = $this->load->view('curso/info', $data, true);
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();

    }    

    public function get_data_ajax( $curso = null, $current_row=null)
    {
        if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
            if (is_null($curso) && !empty($curso)) {            
            
                if ($this->input->post()) { //Se verifica que se haya recibido información por método post
                    //aqui va la nueva conexion a la base de datos del buscador
                    //Se guarda lo que se busco asi como la matricula de quien realizo la busqueda
                    $filtros = $this->input->post();
                    
                    $filtros['current_row'] = (isset($current_row) && !empty($current_row)) ? $current_row : 0;
                    $filtros['curso'] = (isset($curso) && !empty($curso)) ? $curso : '';
                    $data=$filtros;
                    $data['current_row'] = $filtros['current_row'];
                    $data['per_page'] = $this->input->post('per_page');
                    $data['curso']= $filtros['curso'];

                    $error = "";
                    $data['error']=$error;
                    
//                    $datos_curso=$this->cur_mod->listado_cursos(array('cur_id'=>$curso));
                                         
                    $resultado = $this->resultadorealizado($filtros); //Datos del formulario se envían para generar la consulta segun los filtros
                   
                    $data['total_empleados'] = $resultado['contador'];
                    $data['datos_user_aeva'] = $resultado['datos_user_aeva'];
                    
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
   
        if(self::DATA_TABLES_RESULTADO_REALIZADO){
            $pagination = $this->template->pagination_data_reporte($data); //Crear mensaje y links de paginación
            $links = "<div class='col-sm-5 dataTables_info' style='line-height: 50px;'>".$pagination['total']."</div>
                    <div class='col-sm-7 text-right'>".$pagination['links']."</div>";
            echo $links.$this->load->view('reporte/listado_usuariosrep', $data, TRUE).$links.'
                <script>
                $("ul.pagination li a").click(function(event){
                    data_ajax(this, "'.$form['form_recurso'].'", "'.$form['elemento_resultado'].'");
                    event.preventDefault();
                });
                </script>';
        }else{
            echo $this->load->view('reporte/listado_encuesta_realizada', $data, TRUE);              
        }
    }


    public function lista_anios($anio_inicio, $anio_fin)
    {
        $anios = array();
        for ($anio = $anio_inicio; $anio<= $anio_fin; $anio++) 
        {
            $anios[] = array('anio_id'=>$anio, 'anio_desc'=>$anio);
        }
        return $anios;
    }


    public function info_curso($curso=null)
    {        

        $data['curso'] = $this->cur_mod->listado_cursos(array('cur_id'=>$curso));
        $data['roles'] = $this->cur_mod->listar_roles_curso(array('cur_id'=>$curso));
        $data['grupos'] = $this->cur_mod->listar_grupos_curso(array('cur_id'=>$curso));
        //pr($data); exit();
        
        $main_contet = $this->load->view('curso/info_curso', $data, true);
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();

    }

    public function curso_encuesta_general($curso=null)
    {
        
        $anios = $this->lista_anios(2009,date('Y'));
        $rol = $this->config->item('rol_docente');
        $rol_evalua = $this->config->item('ENCUESTAS_ROL_EVALUA');
        $rol_evaluador = $this->config->item('ENCUESTAS_ROL_EVALUADOR');
        
        $datos_curso=$this->cur_mod->listado_cursos(array('cur_id'=>$curso));
        $data['datos_curso']=$datos_curso;
                
        //$data['anios']=dropdown_options($anios, 'anio_id','anio_desc');

//        $datos['order_columns'] = array('nombre'=>'Nombre','nrolevaluador'=>'Rol evaluador','nrolevaluado' => 'Rol evaluado', 'ngrupo' => 'Grupo');
        $data['curso']=$curso;
               
        if(self::DATA_TABLES_RESULTADO_REALIZADO){
            $main_contet = $this->load->view('reporte/encuestasrealizadas', $data, true);
        }else{
            $main_contet = $this->load->view('reporte/encuestasrealizadas_DT', $data, true);
        }
        
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();
        
    }

    public function export_data($curso=null){        
       if ($this->input->post()) {
            $filtros = $this->input->post();
            $filtros['current_row'] = 0;
            $filtros['per_page'] = "5";
            $filtros['order'] = 'firstname';
            $filtros['order_type'] = 'DESC';
            $filtros['bactiva'] = "0";
            $datos_curso=$this->cur_mod->listado_cursos(array('cur_id'=>$curso));
            $data = $this->resultadorealizado($filtros, false);
            $filename="Export_".date("d-m-Y_H-i-s")."_".$datos_curso['data'][0]['cur_id'].".xls";
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename");
            header("Pragma: no-cache");
            header("Expires: 0");
            $this->load->view('reporte/listado_usuariosrep_xsl', $data);
        } else {
            echo data_not_exist('No han sido encontrados datos con los criterios seleccionados. <script> $("#btn_export").hide(); </script>'); //Mostrar mensaje de datos no existentes
        }
    }


  public function resultadorealizado($filtros=null, $resultsetAll=false){
        
        //usuarios enrolados en el curso
        $datos_user_aeva=array();
        $contador=0;
        if(isset($filtros['current_row']) && !empty($filtros['current_row']) ){
           $current_row = $filtros['current_row'];
           $filtros['current_row'] = 0;
        }else{
            $current_row = "0";
        }
        $idcurso=$filtros['curso'];
        $datos_curso=$this->cur_mod->listado_cursos(array('cur_id'=> $idcurso));
        $usuarioscurso=$this->rep_mod->listado_usuariosenc($idcurso);
//        pr($datos_curso);
//        pr($usuarioscurso);
        //pr($usuarioscurso);
//        exit();
        foreach ($usuarioscurso as $keyuc => $valueuc) 
        {
            $rolescusercurso=$this->encur_mod->get_roles_usercurso(array('user_id' => $valueuc['cve_usuario'],'cur_id'=>$idcurso));
            //var_dump($rolescusercurso);
            $idusuario=$valueuc['cve_usuario'];
            foreach ($rolescusercurso as $key => $value) 
            {
                $reglas_validas=$this->encur_mod->get_reglas_validas_cur(array('role_evaluador' => $value,
                      'tutorizado'=> $datos_curso['data'][0]['tutorizado'],'cur_id'=> $idcurso,'ord_prioridad' => '1'));
                if(isset($reglas_validas) && !empty($reglas_validas))
                {
                      //echo "entra";
                      //pr($reglas_validas);
                      foreach ($reglas_validas as $keyr => $valuer) 
                      {
                         //pr($value['is_excepcion']);
                         if($valuer['is_excepcion'] == 0)
                         {
                          //no hay excepciones armar el arreglo para buscar usuarios
                          $reglasgral[]=array('reglas_evaluacion_cve' => $valuer['reglas_evaluacion_cve'],
                                              'rol_evaluado_cve' => $valuer['rol_evaluado_cve'],
                                              'encuesta_cve' => $valuer['encuesta_cve'],
                                              'eva_tipo' => $valuer['eva_tipo']
                                              );
                         
                         }
                         else
                         {
                          //
                           $reglas_validas=$this->enc_mod->get_reglas_validas_cur(array('role_evaluador' => $value,
                          'tutorizado'=> $datos_curso['data'][0]['tutorizado'],'cur_id'=> $idcurso,'ord_prioridad' => '2'));

                            $reglasgral[]=array('reglas_evaluacion_cve' => $reglas_validas['reglas_evaluacion_cve'],
                                              'rol_evaluado_cve' => $reglas_validas['rol_evaluado_cve'],
                                              'encuesta_cve' => $reglas_validas['encuesta_cve'],
                                              'eva_tipo' => $reglas_validas['eva_tipo']
                                              );
                          }

               
                      }
                      //pr($reglasgral);
                      foreach ($reglasgral as $keyrg => $valuerg) 
                      {
                        //pr($valuerg['eva_tipo']);

                                if($valuerg['eva_tipo'] == 1)
                                {
                                  //por persona
                                  //echo "por persona";
                                  $datos_user_aeva[]=$this->rep_mod->listado_evaluados(array('role_evaluado' => $valuerg['rol_evaluado_cve'],
                                    'cur_id' => $idcurso,'encuesta_cve' => $valuerg['encuesta_cve'],
                                    'evaluador_user_cve' => $idusuario,'role_evaluador' => $value,'filtros' => $filtros)
                                  );
                                  $datos_user_aeva[$contador][0]['evaludador_nombre'] = $valueuc['nombres'].' '.$valueuc['apellidos'];
                                  $contador++;       
                                    
                                }
                                else
                                {
                                  //por grupo
                                   //echo "por grupo";
                                   $datos_usuario=$this->encur_mod->get_datos_usuarios(array('user_id' => $idusuario,'cur_id'=>$idcurso,'rol_evaluado_cve' => $valuerg['rol_evaluado_cve']));
                                   //pr($datos_usuario);
                                   if(isset($datos_usuario) || isset($datos_curso) || !empty($datos_usuario) || !empty($datos_curso))
                                   {
                                     foreach ($datos_usuario as $keyd => $valued) 
                                     {

                                        //role evaluador
                                        $role_evaluador=$valued['cve_rol'];
                                        //pr($role_evaluador);
                                        //grupo del evaluador
                                        $gpo_evaluador=$valued['cve_grupo'];# code...
                                        
                                        $datos_user_aeva[]=$this->rep_mod->listado_evaluados(array('gpo_evaluador' => $gpo_evaluador,'role_evaluado' => $valuerg['rol_evaluado_cve'],
                                            'cur_id' => $idcurso,'encuesta_cve' => $valuerg['encuesta_cve'],
                                            'evaluador_user_cve' => $idusuario,
                                            'role_evaluador' => $role_evaluador,'filtros' => $filtros)
                                        );
                                        $datos_user_aeva[$contador][0]['evaludador_nombre'] = $valueuc['nombres'].' '.$valueuc['apellidos'];
                                        $contador++;
                                     }
                                  
                                    }

                                }
                        }
                }       
            }

        }

        if(self::DATA_TABLES_RESULTADO_REALIZADO && !$resultsetAll){
            $resultado['datos_user_aeva']=$datos_user_aeva;
        }else{
            $resultado['datos_user_aeva'] = array();
            for( $i=$current_row; $i<( $current_row + $filtros['per_page'] ); $i++ ){
                if(isset($datos_user_aeva[$i]) && !empty($datos_user_aeva[$i])){
                    $resultado['datos_user_aeva'][] = $datos_user_aeva[$i];
                }
            }
        }
        $resultado['contador']=$contador; 
        return $resultado;

    }

}
