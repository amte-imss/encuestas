<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Curso_model extends CI_Model {

    public function __construct() {
        // Call the CI_Model constructor
        parent::__construct();
        $this->config->load('general');
        // $this->load->database();
    }

    public function listado_cursos($params = null) {
        $resultado = array();

        //$guarda_busqueda = false;
        /*
          $this->db->select("GROUP_CONCAT(pd_base_datos.bd_id)"); //////Ejemplo de compilación de consulta
          $this->db->join('pub_bd', 'pub_bd.bd_id = base_datos.bd_id');
          $this->db->where('pub_bd.pub_id=publicacion.pub_id');
          $base_datos = $this->db->get_compiled_select('base_datos');
         */
        /*
          -- ANIO
          -- TIPO_CURSO
          -- TEXTO_PLANO
          -- tutorizado
          -- alcance
         */
        //pr($params);exit();
        ///////////////////// Iniciar almacenado de parámetros en cache /////////////////////////
        $this->db->start_cache();
        $this->db->select('mdl_course.id');

        if (isset($params['cur_clave']) && !empty($params['cur_clave'])) { ////// Ejemplo - Like 
            $this->db->like('mdl_course.shortname', $params['cur_clave']);
            //$guarda_busqueda = true;
        }/*
          if(isset($params['tipo_curso']) && !empty($params['tipo_curso']))
          {
          //$guarda_busqueda = true;
          $this->db->where('mdl_course_config.tipocur',$params['tipo_curso']);
          } */
        if (isset($params['cur_id']) && !empty($params['cur_id'])) {
            //$guarda_busqueda = true;
            $this->db->where('mdl_course.id', $params['cur_id']);
        }
        if (isset($params['anio']) && !empty($params['anio'])) {
            //$guarda_busqueda = true;
            $this->db->where("TO_CHAR(TO_TIMESTAMP(mdl_course.startdate),'YYYY')='" . $params['anio'] . "'");
        }/**/

        //pr($params);
        $this->db->join('public.mdl_course_config', 'mdl_course_config.course=mdl_course.id');
        $this->db->join('public.mdl_course_categories', 'mdl_course_categories.id=mdl_course.category');
        //$this->db->group_by("mdl_user.id");

        $this->db->stop_cache();
        /////////////////////// Fin almacenado de parámetros en cache ///////////////////////////
        ///////////////////////////// Obtener número de registros ///////////////////////////////
        $nr = $this->db->get_compiled_select('mdl_course'); //Obtener el total de registros
        $num_rows = $this->db->query("SELECT count(*) AS total FROM (" . $nr . ") AS temp")->result();
        //pr($this->db1->last_query());
        /////////////////////////////// FIN número de registros /////////////////////////////////
        $busqueda = array(
            'mdl_course.id AS cur_id',
            'mdl_course.shortname AS cur_clave',
            'mdl_course.fullname AS cur_nom_completo',
            'mdl_course.category AS cat_cve',
            'mdl_course_categories."name" AS cat_nom',
            'TO_TIMESTAMP(mdl_course.startdate) AS fecha_inicio',
            "TO_CHAR(TO_TIMESTAMP(mdl_course.startdate),'YYYY') AS anio",
            'mdl_course_config.horascur',
            'mdl_course_config.modalidad',
            'mdl_course_config.tipocur',
            'mdl_course_config.startdatepre',
            'mdl_course_config.tutorizado',
                //'mdl_course_config.curso_alcance'
        );

        /*
          cur_id
          cur_clave
          cur_nom_completo
          cat_cve
          cat_nom
          fecha_inicio
          anio
          horascur
          tipocur
          modalidad
          startdatepre
          tutorizado
          curso_alcance

         */
        $this->db->select($busqueda);
        if (isset($params['order']) && !empty($params['order'])) {
            $tipo_orden = (isset($params['order_type']) && !empty($params['order_type'])) ? $params['order_type'] : "ASC";
            $this->db->order_by($params['order'], $tipo_orden);
        }
        if (isset($params['per_page']) && isset($params['current_row'])) { //Establecer límite definido para paginación 
            $this->db->limit($params['per_page'], $params['current_row']);
        }

        //$this->db->order_by("title", "desc");
        $this->db->order_by('mdl_course.fullname', 'ASC');
        $query = $this->db->get('mdl_course'); //Obtener conjunto de registros
//        pr($this->db->last_query());                                  
        $resultado['total'] = $num_rows[0]->total;
        $resultado['columns'] = $query->list_fields();
        $resultado['data'] = $query->result_array();
        //pr($resultado['data']);
        $this->db->flush_cache();
        $query->free_result(); //Libera la memoria                                
        /*
          if(isset($guarda_busqueda) && $guarda_busqueda == true){
          $this->guarda_busqueda($params);
          } */

        return $resultado;
    }

    public function listar_roles_curso($params = null) {
        $resultado = array();
        //pr($params); exit();
        $this->db->start_cache(); // inicia historial de busqueda
        $this->db->select('mdl_role.id');

        $this->db->where('mdl_course.id', $params['cur_id']);

        $this->db->join('public.mdl_context', 'mdl_context.instanceid=mdl_course.id');
        $this->db->join('public.mdl_role_assignments', 'mdl_context.id=mdl_role_assignments.contextid');
        $this->db->join('public.mdl_role', 'mdl_role.id=mdl_role_assignments.roleid');
        $this->db->join('public.mdl_user', 'mdl_user.id=mdl_role_assignments.userid');
        $this->db->group_by('mdl_role.id');

        $this->db->stop_cache(); // hasta aqui el historial de busqueda
        ///////////////////////////// Obtener número de registros ///////////////////////////////
        $nr = $this->db->get_compiled_select('mdl_course'); //Obtener el total de registros

        $num_rows = $this->db->query("SELECT count(*) AS total FROM (" . $nr . ") AS temp")->result();

        //pr($num_rows);

        $busqueda = array(
            'mdl_role.id AS rol_id',
            'mdl_role.name AS nom_rol',
            'COUNT(mdl_role.id) AS usuarios_por_rol'
        );

        $this->db->select($busqueda);
        $this->db->order_by("nom_rol", "asc");
        $query = $this->db->get('mdl_course');

        //pr($this->db->last_query()); 

        $resultado['total'] = $num_rows[0]->total; // obtenemos el total de filas de la consulta
        $resultado['columns'] = $query->list_fields(); // obtenemos las columnas de la consulta
        $resultado['data'] = $query->result_array(); //guardamos en data el resultado de la consulta
        //pr($resultado['data']);

        $this->db->flush_cache(); // limpiamos la cache
        $query->free_result(); //Libera la memoria 

        return $resultado;
    }

    public function listar_grupos_curso($params = null) {
        $resultado = array();
        //pr($params); exit();

        $this->db->start_cache(); // inicia historial de busqueda
        $this->db->select('mdl_groups.id');
        $this->db->where('mdl_groups.courseid', $params['cur_id']);
        $this->db->stop_cache(); // hasta aqui el historial de busqueda


        $nr = $this->db->get_compiled_select('public.mdl_groups'); //Obtener el total de registros
        $num_rows = $this->db->query("SELECT count(*) AS total FROM (" . $nr . ") AS temp")->result();

        $busqueda = array('mdl_groups.id AS grup_id', 'mdl_groups.name AS grup_nom');

        $this->db->select($busqueda);
        $this->db->order_by("grup_nom", "asc");
        $query = $this->db->get('public.mdl_groups');

        $resultado['total'] = $num_rows[0]->total; // obtenemos el total de filas de la consulta
        $resultado['columns'] = $query->list_fields(); // obtenemos las columnas de la consulta
        $resultado['data'] = $query->result_array(); //guardamos en data el resultado de la consulta
        //pr($resultado['data']);

        $this->db->flush_cache(); // limpiamos la cache
        $query->free_result(); //Libera la memoria 

        return $resultado;
    }

    public function listar_usuarios_grupo($params = null) {
        $resultado = array();
        //pr($params); exit();

        $this->db->start_cache(); // inicia historial de busqueda
        $this->db->select('mdl_groups.id');
        $this->db->where('mdl_groups.courseid', $params['cur_id']);
        $this->db->stop_cache(); // hasta aqui el historial de busqueda


        $nr = $this->db->get_compiled_select('public.mdl_groups'); //Obtener el total de registros
        $num_rows = $this->db->query("SELECT count(*) AS total FROM (" . $nr . ") AS temp")->result();

        $busqueda = array('mdl_groups.id AS grup_id', 'mdl_groups.name AS grup_nom');

        $this->db->select($busqueda);
        $query = $this->db->get('public.mdl_groups');

        $resultado['total'] = $num_rows[0]->total; // obtenemos el total de filas de la consulta
        $resultado['columns'] = $query->list_fields(); // obtenemos las columnas de la consulta
        $resultado['data'] = $query->result_array(); //guardamos en data el resultado de la consulta
        //pr($resultado['data']);

        $this->db->flush_cache(); // limpiamos la cache
        $query->free_result(); //Libera la memoria 

        return $resultado;
    }

    /**
     * 
     * @author LEAS 
     * @fecha LEAS 
     * @param type $param where de la consulta, caso de uso en especial es el 
     * identificador del curso y su valor ejem. "array('vdc.idc'=>838)"
     * @return type Grupos abiertos en el curso, maximo valor de 
     * bloque (numerico entero si existe, si no, retorna cero) y total de registros(grupos)
     * $result['grupos'] = $query->result_array();
     * $result['total_grupos'] = 20;
     * $result['max_boque'] = 5;
     */
    function getGruposBloques($param) {
        //Obtiene información del coordinador de tutores 
        $select_ct = array("concat(muser.firstname,' ', muser.lastname,' (',muser.username,')') as name_ct",
            "mg.id as grupoid", "cursoid", "expe.id userid"
        );
        $this->db->join('public.mdl_user muser', 'muser.id= expe.userid');
        $this->db->join('public.mdl_role mr', 'mr.id= expe.role and mr.id IN(18)');
        $this->db->join('public.mdl_course c', 'c.id=expe.cursoid ');
        $this->db->join('public.mdl_groups mg', 'mg.id=expe.grupoid and c.id = mg.courseid');
        $this->db->select($select_ct);
        $this->db->where('expe.cursoid', $param['vdc.idc']);
        $this->db->order_by('grupoid');
        $expediente = $this->db->get('tutorias.mdl_userexp expe');
        $this->db->reset_query();
        $expe_p = array();
        foreach ($expediente->result_array() as $value) {
            if (isset($expe_p[$value['grupoid']])) {
                $tmp = $expe_p[$value['grupoid']];
                $tmp_cts = $tmp . ', ' . $value['name_ct'];
                $expe_p[$value['grupoid']] = $tmp_cts;
            } else {
                $expe_p[$value['grupoid']] = $value['name_ct'];
            }
        }



        $select = array(
            'vdc.idc', 'vdc.clave',
            'cbg.bloque', 'mdlg.id', 'mdlg."name"'
        );
        $group_by = array(
            'vdc.idc, vdc.clave',
            'cbg.bloque', 'mdlg.id', 'mdlg."name"'
        );
        $this->db->start_cache(); //Inicio cache -------------------------------

        $this->db->join('public.mdl_groups mdlg', 'mdlg.courseid = vdc.idc');
        $this->db->join('encuestas.sse_curso_bloque_grupo cbg', 'cbg.course_cve = vdc.idc and cbg.mdl_groups_cve = mdlg.id', 'left');
        foreach ($param as $key => $value) {
            $this->db->where($key, $value);
        }

        $this->db->stop_cache(); //Fin cache ------------------------------------
        $num_rows = $this->db->query($this->db->select('count(*) as total')->get_compiled_select('encuestas.view_datos_curso vdc'))->result_array();
        $this->db->reset_query(); //Reset de query 
        $max_bloque = $this->db->query($this->db->select('max(cbg.bloque) as max_bloque')->get_compiled_select('encuestas.view_datos_curso vdc'))->result_array();
        $this->db->reset_query(); //Reset de query 
        //Agrega los agrupamientos
        foreach ($group_by as $g) {
            $this->db->group_by($g);
        }
        //Agrega el order by
        $this->db->order_by('mdlg."name"');
//      pr($max_bloque);
//      pr($num_rows);
        $this->db->select($select);
        $query = $this->db->get('encuestas.view_datos_curso vdc');
        $result['grupos'] = $query->result_array();
        $result['cts'] = $expe_p;
        $result['total_grupos'] = $num_rows[0]['total'];
        $result['max_boque'] = (!empty($max_bloque[0]['max_bloque'])) ? $max_bloque[0]['max_bloque'] : 0;
        return $result;
    }

    public function detalle_curso($where) {
        $select = array('vdc.idc', "CONCAT(vdc.clave,'-',vdc.namec) as name_curso", 'vdc.tex_tutorizado', 'vdc.tipo_curso');
        $this->db->select($select);
        foreach ($where as $campo => $idc) {
            $this->db->where($campo, $idc);
        }
        $query = $this->db->get('encuestas.view_datos_curso vdc');
        $this->db->reset_query();
        return $query->result_array();
    }

    public function insertUpdate_CursoBloqueGrupo($post) {
        if (!isset($post['curso']) AND empty($post['curso']) AND ! is_numeric($post['curso'])) {
            return 0;
        }
        $curso = $post['curso'];
        $max_bloque = $post['max_bloques'];
        unset($post['curso']);
        unset($post['max_bloques']);
        $this->db->trans_begin();
        foreach ($post as $key => $value) {
            $explode = explode("_", $key);
            $grup = $explode[1];
            $result = $this->get_existBloqueGrupo(array('course_cve' => $curso, 'mdl_groups_cve' => $grup));
            if (!empty($result)) {//Actualización
                $this->db->where('course_cve', $curso);
                $this->db->where('mdl_groups_cve', $grup);
                $this->db->update('encuestas.sse_curso_bloque_grupo', array('bloque' => $value));
            } else {//Inserta
                $datos = array('course_cve' => $curso, 'mdl_groups_cve' => $grup, 'bloque' => $value);
                $this->db->insert('encuestas.sse_curso_bloque_grupo', $datos); //Almacena usuario
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 0;
        } else {
            $this->db->trans_commit();
            return 1;
        }
    }

    public function get_existBloqueGrupo($where) {
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        $query = $this->db->get('encuestas.sse_curso_bloque_grupo');
        $result = $query->result_array();
        return $result;
    }

}
