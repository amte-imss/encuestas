<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase que gestiona el login
 * @version     : 1.0.0
 * @autor       : Pablo José
 */
class Curso extends CI_Controller {

    /**
     * * Carga de clases para el acceso a base de datos y para la creación de elementos del formulario
     * * @access        : public
     * * @modified  : 
     */
    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->library('form_complete'); // form complete
        $this->load->library('form_validation'); //implemantación de la libreria form validation
//        $this->config->load('general'); 
//        $this->config->load('form_validation'); // abrir el archivo general de validaciones
        //$this->load->model('Reporte_model', 'rep_mod'); // modelo de reporte
        $this->load->model('Curso_model', 'cur_mod'); // modelo de cursos
    }

    public function index() {

        if ($this->session->has_userdata('id')) {

            $anios = $this->lista_anios(2009, date('Y'));
            $rol = $this->config->item('rol_docente');
            $adscripcion;
            $categoria;

            //$data['categoria']=dropdown_options($categoria, 'cve_categoria','nom_nombre');
            //$data['adscripcion']=dropdown_options($adscripcion, '','');
            $data['anios'] = dropdown_options($anios, 'anio_id', 'anio_desc');
            //$data['rol']=dropdown_options($rol, 'rol_id','rol_nom');
            $datos['order_columns'] = array('emp_matricula' => 'Matrícula', 'cve_depto_adscripcion' => 'Adscripción', 'cat_nombre' => 'Categoría', 'grup_nom' => 'BD');

            /*
              #
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
            $main_contet = $this->load->view('curso/cursos', $data, true);
            $this->template->setMainContent($main_contet);
            $this->template->getTemplate();
        } else {
            $url_sied = $this->config->item('url_sied');
            redirect($url_sied);
            //redirect('http://11.32.41.13/kio/sied');        # code...
        }
    }

    public function info($curso = null) {

        $data['curso'] = $this->cur_mod->listado_cursos(array('cur_id' => $curso));
        $data['roles'] = $this->cur_mod->listar_roles_curso(array('cur_id' => $curso));
        $data['grupos'] = $this->cur_mod->listar_grupos_curso(array('cur_id' => $curso));
        //pr($data); exit();

        $main_contet = $this->load->view('curso/info', $data, true);
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();
    }

    public function curso_bloque_grupos($curso = null) {
//        $data['datos_curso'] = $this->cur_mod->listado_cursos(array('cur_id'=>$curso));
//        $data['grupos'] = $this->cur_mod->listar_grupos_curso(array('cur_id'=>$curso));
        $data['curso'] = $curso;
        if (is_null($curso) || !is_numeric($curso)) {
            $data['mensaje'] = 'No se encontró información del curso o los parámetros son incorrectos.<br>Por Favor repita el proceso';
            $data['tipo_alert'] = En_general::WARNING;
        } else {

            $data['datos_curso'] = $this->cur_mod->detalle_curso(array('vdc.idc ' => $curso));
            $result = $this->cur_mod->getGruposBloques(array('vdc.idc' => $curso));
//            pr($result);
            $data_tabla = $result;
            $num_max = ($result['max_boque'] > 0) ? $result['max_boque'] : 5;
            for ($i = 1; $i <= $num_max; $i++) {
                $bloques[$i] = 'Bloque ' . $i;
            }
            $data_tabla['max_boque'] = $num_max;
            $data_tabla['bloques'] = $bloques;
//            pr($data_tabla);
//        pr($result);total_grupos, max_boque
//           [idc] => 838
//                    [clave] => CES-DD-I2-15
//                    [namec] => Formación de Directivos en Salud
//                    [tex_tutorizado] => Tutorizado
//                    [tipo_curso] => Diplomado
//                    [bloque] => 
//                    [id] => 11858
//                    [name] => ZACATECAS
//                )
            $data['vista'] = $this->load->view('curso/tabla_bloque_grupo', $data_tabla, true);
        }
        $main_contet = $this->load->view('curso/curso_bloque_grupos', $data, true);
        $this->template->setMainTitle('Gestión de bloques por curso');
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();
    }

    public function guardar_curso_bloque_grupos() {
        if ($this->input->post()) {
            $data_post = $this->input->post(NULL, TRUE);
//            pr($data_post);
//            exit();
            $validation = array();
            foreach ($data_post as $key => $value) {//Genera validaciones
                $explode = explode("_", $key);
                if ($explode[0] == 'b') {//Si es numeric, esta validando los grupos
                    $validation[] = array('field' => $key, 'label' => 'bloque', 'rules' => 'trim|required');
                }
            }
            $this->form_validation->set_rules($validation); //Carga validaciones

            if ($this->form_validation->run()) {//Ejecuta las validaciones
                //Guardar los bloques
                $result = $this->cur_mod->insertUpdate_CursoBloqueGrupo($data_post);
                if ($result === 1) {//Se guardo exitosamente la relacion curso-grupo-bloque
                    $html['mensaje'] = 'Los datos se almacenaron correctamente';
                    $html['tipo_alert'] = En_general::SUCCESS;
                } else {
                    $html['mensaje'] = 'Ocurrio un error, por favor intentelo más tarde';
                    $html['tipo_alert'] = En_general::DANGER;
                }
            }

            $result = $this->cur_mod->getGruposBloques(array('vdc.idc' => $data_post['curso']));
            $data_tabla = $result;
            $num_max = ($result['max_boque'] > $data_post['max_bloques']) ? $result['max_boque'] : $data_post['max_bloques'];
            for ($i = 1; $i <= $num_max; $i++) {
                $bloques[$i] = 'Bloque ' . $i;
            }
            $data_tabla['max_boque'] = $num_max;
            $data_tabla['bloques'] = $bloques;
            $html['html'] = $this->load->view('curso/tabla_bloque_grupo', $data_tabla, true);
            echo json_encode($html);
        }
    }

    public function get_data_ajax($current_row = null) {
        if ($this->input->is_ajax_request()) { //Sólo se accede al método a través de una petición ajax
            if (!is_null($this->input->post())) { //Se verifica que se haya recibido información por método post
                //aqui va la nueva conexion a la base de datos del buscador
                //Se guarda lo que se busco asi como la matricula de quien realizo la busqueda
                $filtros = $this->input->post();
                $filtros['current_row'] = (isset($current_row) && !empty($current_row)) ? $current_row : 0;

                //pr($filtros);
                $resultado = $this->cur_mod->listado_cursos($filtros); //Datos del formulario se envían para generar la consulta segun los filtros
                $data = $filtros;
                $data['total_empleados'] = $resultado['total'];
                $data['empleados'] = $resultado['data'];
                $data['current_row'] = $filtros['current_row'];
                $data['per_page'] = $this->input->post('per_page');
                //pr($data);
                $this->listado_resultado($data, array('form_recurso' => '#form_curso', 'elemento_resultado' => '#listado_resultado')); //Generar listado en caso de obtener datos
            }
        } else {

            redirect(site_url()); //Redirigir al inicio del sistema si se desea acceder al método mediante una petición normal, no ajax
        }
    }

    private function listado_resultado($data, $form) {
        $pagination = $this->template->pagination_data_curso($data); //Crear mensaje y links de paginación
        $links = "<div class='col-sm-5 dataTables_info' style='line-height: 50px;'>" . $pagination['total'] . "</div>
                <div class='col-sm-7 text-right'>" . $pagination['links'] . "</div>";
        echo $links . $this->load->view('curso/listado_cursos', $data, TRUE) . $links . '
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

    public function curso_encuesta2() {
        $anios = $this->lista_anios(2009, date('Y'));
        $rol = $this->config->item('rol_docente');
        $adscripcion;
        $categoria;
        $data['anios'] = dropdown_options($anios, 'anio_id', 'anio_desc');
        $datos['order_columns'] = array('emp_matricula' => 'Matrícula', 'cve_depto_adscripcion' => 'Adscripción', 'cat_nombre' => 'Categoría', 'grup_nom' => 'BD');

        $main_contet = $this->load->view('curso/curso_encuesta', $data, true);
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();
    }

    public function info_curso($curso = null) {
        if ($this->session->has_userdata('id')) {
            $data['curso'] = $this->cur_mod->listado_cursos(array('cur_id' => $curso));
            $data['roles'] = $this->cur_mod->listar_roles_curso(array('cur_id' => $curso));
//        $data['grupos'] = $this->cur_mod->listar_grupos_curso(array('cur_id' => $curso));
            $data += $this->cur_mod->getGruposBloques(array('vdc.idc' => $curso));
//        pr($data); exit();

            $main_contet = $this->load->view('curso/info_curso', $data, true);
            $this->template->setMainContent($main_contet);
            $this->template->getTemplate();
        } else {
            $url_sied = $this->config->item('url_sied');
            redirect($url_sied);
            //redirect('http://11.32.41.13/kio/sied');        # code...
        }
    }

    public function curso_encuesta() {
        $anios = $this->lista_anios(2009, date('Y'));
        $rol = $this->config->item('rol_docente');
        $adscripcion;
        $categoria;
        $data['anios'] = dropdown_options($anios, 'anio_id', 'anio_desc');
        $datos['order_columns'] = array('emp_matricula' => 'Matrícula', 'cve_depto_adscripcion' => 'Adscripción', 'cat_nombre' => 'Categoría', 'grup_nom' => 'BD');

        $main_contet = $this->load->view('curso/curso_encuesta', $data, true);
        $this->template->setMainContent($main_contet);
        $this->template->getTemplate();
    }

}
