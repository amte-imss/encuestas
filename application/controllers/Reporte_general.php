<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que gestiona el login
 * @version 	: 1.0.0
 * @autor 		: Pablo José
 */
class Reporte_general extends CI_Controller {

    /**
     * * Carga de clases para el acceso a base de datos y para la creación de elementos del formulario
     * * @access 		: public
     * * @modified 	: 
     */
    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->library('form_complete');
        $this->load->library('form_validation');
        $this->load->model('Reporte_model', 'rep_mod'); // modelo de reporte
        $this->load->model('Curso_model', 'cur_mod'); // modelo de cursos
        $this->load->model('Encuestas_model', 'enc_mod');
    }

    public function index() {
        //Obtiene los filtros para reporte
        $data = $this->rep_mod->get_filtros_generales_reportes();
        
//        pr('holas');
        /*
          [2] => emp_matricula
          [3] => emp_nombre
          [11] => cat_nombre
          [15] => fch_pre_registro
          [17] => cur_clave
          [18] => cur_nom_completo
          [19] => fecha_inicio
          [20] => horascur
          [21] => fecha_fin
          [24] => grup_nom
          [25] => tutorizado
          [26] => curso_alcance
          [27] => rol_nom
          [28] => tipocur
         */

        /*
          $data['profesores'] = $this->rep_mod->reporte_usuarios(array('per_page'=>10, 'current_row'=>1));
          pr($data['profesores']);

         */

//        $main_contet = $this->load->view('reporte/general/general_cursos', $data, true);
//        $this->filtrosreportes_tpl->setMainTitle('Reporte general de cursos');
//        $this->filtrosreportes_tpl->setMainContent($main_contet);
//        $this->filtrosreportes_tpl->getTemplate();
        $this->filtrosreportes_tpl->getVista(FiltrosReportes_Tpl::RB_IMPLEMENTACION);
    }

    public function get_buscar_cursos_encuestas($current_row = null) {
        if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
            if (!is_null($this->input->post())) { //Se verifica que se haya recibido información por método post
                //aqui va la nueva conexion a la base de datos del buscador
                //Se guarda lo que se busco asi como la matricula de quien realizo la busqueda
//                $filtros = $this->input->post();
//                $filtros['current_row'] = (isset($current_row) && !empty($current_row)) ? $current_row : 0;
                $data = array();
                $result = $this->rep_mod->get_filtros_generales_reportes();
//                pr($result);
//                //pr($filtros);
//                $resultado = $this->rep_mod->reporte_usuarios($filtros); //Datos del formulario se envían para generar la consulta segun los filtros
//                $data = $filtros;
//                $data['total_empleados'] = $resultado['total'];
//                $data['empleados'] = $resultado['data'];
//                $data['current_row'] = $filtros['current_row'];
//                $data['per_page'] = $this->input->post('per_page');
//                //pr($data);
//                $this->listado_resultado($data, array('form_recurso' => '#form_empleado', 'elemento_resultado' => '#listado_resultado_empleado')); //Generar listado en caso de obtener datos
                echo $this->load->view('reporte/general/tb_result_rg', $data, TRUE);
            }
        } else {

            redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        }
    }

    private function listado_resultado($data, $form) {
        $pagination = $this->template->pagination_data_empleado($data, 'get_data_ajax2'); //Crear mensaje y links de paginación
        $links = "<div class='col-sm-5 dataTables_info' style='line-height: 50px;'>" . $pagination['total'] . "</div>
                <div class='col-sm-7 text-right'>" . $pagination['links'] . "</div>";
        echo $links . $this->load->view('reporte/general/tb_result_rg', $data, TRUE) . $links . '
            <script>
            $("ul.pagination li a").click(function(event){
                data_ajax(this, "' . $form['form_recurso'] . '", "' . $form['elemento_resultado'] . '");
                event.preventDefault();
            });
            </script>';
    }

    public function lista_anios($anio_inicio, $anio_fin) {
        $anios = array();
        for ($anio = $anio_inicio; $anio <= $anio_fin; $anio++) {
            $anios[] = array('anio_id' => $anio, 'anio_desc' => $anio);
        }
        return $anios;
    }

}
