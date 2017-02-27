<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_encuestas_contestadas extends CI_Model {

    public function __construct() {
        // Call the CI_Model constructor
        parent::__construct();
        $this->config->load('general');
        $this->load->database();
    }

    public function listado_evaluados_($params = null) {
        /* select u.username as matricula,
          concat(u.firstname,' ',u.lastname) as nombre,
          c.shortname as clave_curso,c.fullname as desc_curso,
          re.rol_evaluado_cve as role,(select name from public.mdl_role where id=re.rol_evaluado_cve) as nrol,re.rol_evaluador_cve as idrol,count(*) as evaluaciones

          from encuestas.sse_result_evaluacion_encuesta_curso ee
          left join public.mdl_user u on u.id=ee.evaluado_user_cve
          left join public.mdl_course c on c.id=ee.course_cve
          inner join encuestas.sse_encuestas en on en.encuesta_cve=ee.encuesta_cve
          inner join encuestas.sse_reglas_evaluacion re on re.reglas_evaluacion_cve=en.reglas_evaluacion_cve


          where ee.course_cve=823
          group by u.username, u.firstname,u.lastname,c.shortname,c.fullname,re.rol_evaluado_cve,re.rol_evaluador_cve
          order by u.firstname,u.lastname,c.shortname,c.fullname,re.rol_evaluado_cve,re.rol_evaluador_cve */

        $resultado = array();
        ///////////////////// Iniciar almacenado de parámetros en cache /////////////////////////
        $this->db->start_cache();
        $this->db->select('encuestas.sse_result_evaluacion_encuesta_curso.evaluacion_resul_cve');
        $this->db->where('encuestas.sse_result_evaluacion_encuesta_curso.course_cve', $params['curso']);


        $this->db->join('public.mdl_user', 'public.mdl_user.id=encuestas.sse_result_evaluacion_encuesta_curso.evaluado_user_cve', 'left');
        $this->db->join('public.mdl_course', 'public.mdl_course.id=encuestas.sse_result_evaluacion_encuesta_curso.course_cve', 'left');
        $this->db->join('encuestas.sse_encuestas', 'encuestas.sse_encuestas.encuesta_cve=encuestas.sse_result_evaluacion_encuesta_curso.encuesta_cve');
        $this->db->join('encuestas.sse_reglas_evaluacion', 'encuestas.sse_reglas_evaluacion.reglas_evaluacion_cve=encuestas.sse_encuestas.reglas_evaluacion_cve');
        //$this->db->join('encuestas.sse_curso_bloque_grupo cbg', 'cbg.course_cve = encuestas.sse_result_evaluacion_encuesta_curso.course_cve and cbg.mdl_groups_cve = encuestas.sse_result_evaluacion_encuesta_curso.group_id');

        $this->db->stop_cache();
        /////////////////////// Fin almacenado de parámetros en cache ///////////////////////////
        ///////////////////////////// Obtener número de registros ///////////////////////////////
        $nr = $this->db->get_compiled_select('encuestas.sse_result_evaluacion_encuesta_curso'); //Obtener el total de registros
        $num_rows = $this->db->query("SELECT count(*) AS total FROM (" . $nr . ") AS temp")->result();
        //pr($this->db1->last_query());
        /////////////////////////////// FIN número de registros /////////////////////////////////
        $busqueda = array(
            'mdl_user.username as matricula',
            'mdl_user.firstname as nombre',
            'mdl_user.lastname as apellidos',
            'mdl_course.shortname as clave_curso',
            'mdl_course.fullname as desc_curso',
            'encuestas.sse_reglas_evaluacion.rol_evaluado_cve as evaluado',
            '(select name from public.mdl_role where id=sse_reglas_evaluacion.rol_evaluado_cve) as nrolevaluado',
            'sse_reglas_evaluacion.rol_evaluador_cve as evaluador',
            '(select name from public.mdl_role where id=sse_reglas_evaluacion.rol_evaluador_cve) as nrolevaluador',
            'count(*) as evaluaciones',
            'encuestas.sse_result_evaluacion_encuesta_curso.group_id as grupo_id',
            'encuestas.sse_result_evaluacion_encuesta_curso.calif_emitida as calif_emitida',
            '(select name from public.mdl_groups where id=encuestas.sse_result_evaluacion_encuesta_curso.group_id) as ngrupo',
            '(select public.mdl_user.firstname ||  \'  \'  || public.mdl_user.lastname from public.mdl_user where id=sse_result_evaluacion_encuesta_curso.evaluador_user_cve) as nombreevaluador',
            '(select * from departments.get_rama_completa((select cve_departamental from public.mdl_user where id=evaluado_user_cve), 7)) as ramaevaluado',
            '(select * from departments.get_rama_completa((select cve_departamental from gestion.sgp_tab_preregistro_al where nom_usuario like (select username from public.mdl_user where id=evaluador_user_cve) and cve_curso=encuestas.sse_result_evaluacion_encuesta_curso.course_cve), 7)) as ramaevaluador',
            '(select username from public.mdl_user where id=evaluador_user_cve) as matricula_evaluador',
                //'cbg.bloque',
        );

        $this->db->select($busqueda);
        if (isset($params['order']) && !empty($params['order'])) {
            $tipo_orden = (isset($params['order_type']) && !empty($params['order_type'])) ? $params['order_type'] : "ASC";
            $this->db->order_by($params['order'], $tipo_orden);
        }
        if (isset($params['per_page']) && isset($params['current_row'])) { //Establecer límite definido para paginación 
            $this->db->limit($params['per_page'], $params['current_row']);
        }

        $this->db->group_by('sse_result_evaluacion_encuesta_curso.evaluacion_resul_cve,mdl_user.username, mdl_user.firstname,mdl_user.lastname,mdl_course.shortname,mdl_course.fullname,
            sse_reglas_evaluacion.rol_evaluado_cve,sse_reglas_evaluacion.rol_evaluador_cve' //, cbg.bloque'
        );

        $query = $this->db->get('encuestas.sse_result_evaluacion_encuesta_curso'); //Obtener conjunto de registros
//        pr($this->db->last_query());
        $resultado['total'] = $num_rows[0]->total;
        $resultado['columns'] = $query->list_fields();
        $resultado['data'] = $query->result_array();
        //pr($resultado['data']);
        $this->db->flush_cache();
        $query->free_result(); //Libera la memoria                                

        return $resultado;
    }

    public function getBusquedaEncContNoCont($params = null) {
        $scripts = new scripts_encuestas_c_nc_promedio(); //Instancia de la clase 
        $array_config = $scripts->get_encuestas_param($params); //Obtene array para generar consulta
//        pr($array_config);
//        exit();
        $this->db->start_cache();/**         * *************Inicio cache  *************** */
//        $this->db->from($this->confFind->getFrom());
        foreach ($array_config['join'] as $value) {//Aplica joins
            $this->db->join($value['tabla'], $value['on'], $value['escape']);
        }

        foreach ($array_config['group_by'] as $value) {//Aplica group by
            $this->db->group_by($value);
        }

        foreach ($array_config['where'] as $value) {//Aplica where
//            pr($value);
            $funcion = $value['escape'];
            $this->db->{$funcion}($value['campo'], $value['value']);
        }

        if (!empty($array_config['where_no_contestadas'])) {//Ejecutará query para obtener total de registros
            $this->db->where($array_config['where_no_contestadas']);
        }

        $this->db->stop_cache();
        $num_rows = $this->db->query($this->db->select('count(*) as total')->get_compiled_select($array_config['from']))->result();
        $total = count($num_rows);

//        pr($this->db->last_query());
        $this->db->reset_query(); //Reset de query 

        $this->db->select($array_config['select']); //Agrega select para traer los campos 
        if (isset($params['per_page']) && isset($params['current_row'])) { //Establecer límite definido para paginación 
            $this->db->limit($params['per_page'], $params['current_row']);
        }

        $order_type = (isset($params['order_type'])) ? $params['order_type'] : 'asc';
        if (isset($params['order']) and ! empty($params['order'])) { //Establecer límite definido para paginación 
            $orden = $params['order'];
            $this->db->order_by($orden, $order_type);
        }

        $ejecuta = $this->db->get($array_config['from']); //Prepara la consulta ( aún no la ejecuta)
        $query = $ejecuta->result_array();

        $this->db->flush_cache(); //Limpia la cache
        //pr($this->db->last_query());
//        exit();
        $result['data'] = $query;
        $result['total'] = $total;
        $result['view_res'] = $array_config['view_res'];
        $result['tutorizado'] = $array_config['tutorizado'];
        $result['text_export'] = $array_config['text_export'];
//        $query->free_result();
        return $result;
    }

}

class scripts_encuestas_c_nc_promedio {

    /**
     * @author LEAS
     * @fecha 23/01/2017
     * @param type $param parametros o filtros del reporte
     * @return type Description estructura sql para generar consulta SQL
     */
    function get_encuestas_param($param) {
        if (isset($param['enc_con_ncon'])) {//Valida el tipo de encuestas contestadas y no contestadas
            if ($param['enc_con_ncon'] === 'e_c') {//Reporte de encuestas contestadas
                return $this->get_contestadas($param);
            } else {//"e_nc" Reporte de encuestas no contestadas
                return $this->get_no_contestadas($param);
            }
        }
    }

    private function get_no_contestadas($param) {
        $query['view_res'] = 'curso/listado_enc_no_contestadas';
        $enc_no_contestadas = new EncNoContestadas(); //Instancia de encuestas no contestadas
        $query['tutorizado'] = 0; //Indica al query que es no tutorizado (adelante lo cambia si es preciso)
        $query['select'] = $enc_no_contestadas->getSelectBasico();
        $query['join'] = $enc_no_contestadas->getJoinBasicos();
        $query['group_by'] = $enc_no_contestadas->getGroupBy_basico();
        $query['from'] = $enc_no_contestadas->getFromNoContestadas();

        if ($param['tutorizado'] == 1) {//Si son tutorizados, se aplica el filtro por bloques
            $query['text_export'] = 'RepEncNoCon_Tutorizado_' . $param['curso'] . '_';
            $query['where_no_contestadas'] = $enc_no_contestadas->whereEncContestadasNoContestadasTutorizado(); //Bandera para calcular total de registros
            $query['tutorizado'] = 1;
            $query['select'] = array_merge($query['select'], $enc_no_contestadas->getSelectTutorizados());
            $query['join'] = array_merge($query['join'], $enc_no_contestadas->getJoinBloqueGrupoCurso());
            $query['group_by'] = array_merge($query['group_by'], $enc_no_contestadas->getGroupBy_Tutorizados());
        } else {//Si el curso es no tutorizado carga los siguienetes datos extra para la consulta 
            $query['text_export'] = 'RepEncNoCon_NoTutorizado_' . $param['curso'] . '_';
            $query['where_no_contestadas'] = $enc_no_contestadas->whereEncContestadasNoContestadasNoTutorizado(); //Bandera para calcular total de registros
            $query['select'] = array_merge($query['select'], $enc_no_contestadas->getSelectNoTutorizados());
            $query['group_by'] = array_merge($query['group_by'], $enc_no_contestadas->getGroupBy_NoTutorizados());
        }
//        unset($param['']) 
        $class_where = new WhereGeneral();
        $query['where'] = $class_where->getCondiciones($param); //Obtiene las condiciones de los filtros actuales

        return $query;
    }

    private function get_contestadas($param) {
        $enc_contestadas = new EncContestadas();
        $query['tutorizado'] = 0; //Indica al query que es no tutorizado (adelante lo cambia si es preciso)
        $query['select'] = $enc_contestadas->getSelect_basico();
        $query['join'] = $enc_contestadas->getJoinBasicos();
        $query['group_by'] = $enc_contestadas->getGroupBy_basico();
        $query['from'] = $enc_contestadas->getFromContestadas();
        $query['view_res'] = 'curso/listado_enc_contestadas';
        $query['where_no_contestadas'] = ''; //No aplica el where para las encuestas contestadas
        if ($param['tutorizado'] == 1) {//Si son tutorizados, se aplica el filtro por bloques
            $query['text_export'] = 'RepEncCon_Tutorizado_' . $param['curso'] . '_';
            $query['tutorizado'] = 1;
            $query['select'] = array_merge($query['select'], $enc_contestadas->getSelect_Tutorizados());
            $query['join'] = array_merge($query['join'], $enc_contestadas->getJoin_Tutorizados());
            $query['group_by'] = array_merge($query['group_by'], $enc_contestadas->getGroupBy_Tutorizados());
        } else {
            $query['text_export'] = 'RepEncCon_NoTutorizado_' . $param['curso'] . '_';
            $query['select'] = array_merge($query['select'], $enc_contestadas->getSelect_NoTutorizados());
            $query['group_by'] = array_merge($query['group_by'], $enc_contestadas->getGroupBy_NoTutorizados());
        }
//        unset($param['']) 
        $class_where = new WhereGeneral();
        $query['where'] = $class_where->getCondiciones($param); //Obtiene las condiciones de los filtros actuales

        return $query;
    }

}

class EncContestadas {

    function getSelect_basico() {
        return array(
            "ec.course_cve", "ccfg.tutorizado", "reec.encuesta_cve", "reec.evaluador_user_cve", "reec.evaluado_user_cve",
            "enc.cve_corta_encuesta", "enc.descripcion_encuestas",
            //evaluado
            "mrdo.id rid_do", 'mrdo."name" rolname_do', "uedo.username as matricula_do", "concat(uedo.firstname, ' ', uedo.lastname) nom_evaluado",
            "cattutdo.des_clave", "cattutdo.nom_nombre",
            //"concat(depdo.cve_depto_adscripcion, ' - ', depdo.des_unidad_atencion) depart_do", "depdor.nom_delegacion del_do", "depdo.name_region reg_do",
            "(select * from departments.get_unidad(depdo.cve_depto_adscripcion, 7)) depart_do", "depdor.nom_delegacion del_do", "depdo.name_region reg_do",
            "cattutdor.des_clave clave_cattut_do", "cattutdor.nom_nombre name_cattut_do",
            //evaluador
            "mrdor.id rid_dor", 'mrdor."name" rolname_dor', "uedor.username as matricula_dor", "concat(uedor.firstname, ' ', uedor.lastname) nom_evaluador",
            "cattutdor.des_clave clave_cattut_dor", "cattutdor.nom_nombre name_cattut_dor", "catpredor.des_clave clave_catpre_dor", "catpredor.nom_nombre name_catpre_dor",
            //"concat(depdor.cve_depto_adscripcion, ' - ', depdor.des_unidad_atencion) depart_dor", "depdor.nom_delegacion delegacion_dor", "depdor.name_region reg_dor",
            "(select * from departments.get_unidad(depdor.cve_depto_adscripcion, 7)) depart_dor", "depdor.nom_delegacion delegacion_dor", "depdor.name_region reg_dor",
            //"concat(deppredor.cve_depto_adscripcion, ' - ', deppredor.des_unidad_atencion) departpre_dor", "deppredor.nom_delegacion delpre_dor", "deppredor.name_region regpre_dor",
            "(select * from departments.get_unidad(deppredor.cve_depto_adscripcion, 7)) departpre_dor", "deppredor.nom_delegacion delpre_dor", "deppredor.name_region regpre_dor",
            "reec.calif_emitida", "reec.calif_emitida_napb",
            ////////////// Se agregan para autoevaluaciones a
            "autoevaluacion.evaluador_user_cve as autoeva_user_cve", "usuario_autoevaluacion.username as autoeva_username", "usuario_autoevaluacion.firstname as autoeva_nombre", "usuario_autoevaluacion.lastname as autoeva_apellido", 
            "autoevaluacion.evaluador_rol_id as autoeva_rol_id", "rol_autoevaluacion.name as autoeva_rol_nombre", "tutor_autoevaluacion.cve_departamento as autoeva_cve_departamento",
            "depto_tut_autoevaluacion.nom_depto_adscripcion as autoeva_nom_depto", "depto_tut_autoevaluacion.cve_regiones as autoeva_cve_regiones", 
            "depto_tut_autoevaluacion.name_region as autoeva_name_region", "depto_tut_autoevaluacion.cve_delegacion as autoeva_cve_delegacion", 
            "depto_tut_autoevaluacion.nom_delegacion as autoeva_nom_delegacion", "(select * from departments.get_unidad(tutor_autoevaluacion.cve_departamento, 7)) as rama_tut_autoevaluacion",
            "tutor_autoevaluacion.cve_categoria as autoeva_cve_categoria", "cat_tut_autoevaluacion.nom_nombre as autoeva_cat_nombre"
        );
    }

    function getSelect_Tutorizados() {
        return array(
//            '(select string_agg(mgs."name", \', \' order by mgs."name") from public.mdl_groups mgs where mgs.id = any (string_to_array(reec.grupos_ids_text, \',\')::int8[])) as name_grupos',
            //'(select string_agg(mgs."name", \', \') from public.mdl_groups mgs where mgs.id = any (string_to_array(reec.grupos_ids_text, \',\')::int8[])) as name_grupos',
            'array_agg(distinct mg."id") as ids_grupos',
            'string_agg(distinct mg."name", \', \') as names_grupos',
            'cbg.bloque',
        );
    }

    function getSelect_NoTutorizados() {
        return array(
            'mg."id" as ids_grupos',
            'mg."name" as names_grupos',
        );
    }

    function getGroupBy_basico() {
        return array(
            "ec.course_cve", "mcs.shortname", "ccfg.tutorizado", "reec.encuesta_cve", "reec.evaluador_user_cve", "reec.evaluado_user_cve",
            "enc.cve_corta_encuesta", "enc.descripcion_encuestas",
            //evaluado
            "mrdo.id", 'mrdo."name"', "uedo.username", "uedo.firstname", "uedo.lastname",
            "depdo.cve_depto_adscripcion", "depdo.des_unidad_atencion", "depdo.nom_delegacion", "depdo.name_region",
            "cattutdor.des_clave", "cattutdor.nom_nombre",
            //evaluador
            "mrdor.id", 'mrdor."name"', "uedor.username", "uedor.firstname", "uedor.lastname",
            "cattutdor.des_clave", "cattutdor.nom_nombre", "catpredor.des_clave", "catpredor.nom_nombre",
            "depdor.cve_depto_adscripcion", "depdor.des_unidad_atencion", "depdor.nom_delegacion", "depdor.name_region",
            "deppredor.cve_depto_adscripcion", "deppredor.des_unidad_atencion", "deppredor.nom_delegacion", "deppredor.name_region",
            //más
            "reec.calif_emitida", "reec.calif_emitida_napb",
            "cattutdo.des_clave", "cattutdo.nom_nombre ",
            "autoevaluacion.evaluador_user_cve", "usuario_autoevaluacion.username", "usuario_autoevaluacion.firstname", "usuario_autoevaluacion.lastname", "autoevaluacion.evaluador_rol_id", "rol_autoevaluacion.name", 
            "tutor_autoevaluacion.cve_departamento", "depto_tut_autoevaluacion.nom_depto_adscripcion", "depto_tut_autoevaluacion.cve_regiones", "depto_tut_autoevaluacion.name_region", "depto_tut_autoevaluacion.cve_delegacion", 
            "depto_tut_autoevaluacion.nom_delegacion", "tutor_autoevaluacion.cve_categoria", "cat_tut_autoevaluacion.nom_nombre",
        );
    }

    function getGroupBy_Tutorizados() {
        return array(
            'cbg.bloque'
        );
    }

    function getGroupBy_NoTutorizados() {
        return array(
            'mg."id"',
            'mg."name"',
        );
    }

    function getJoin_Tutorizados() {
        return array(
            array('tabla' => 'encuestas.sse_curso_bloque_grupo cbg', 'on' => 'cbg.course_cve = reec.course_cve and (cbg.mdl_groups_cve = reec.group_id or cbg.mdl_groups_cve = ANY (string_to_array(reec.grupos_ids_text, \',\')::int[]))', 'escape' => ''),
        );
    }

    function getJoinBasicos() {
        return array(
            array('tabla' => 'encuestas.sse_encuesta_curso ec', 'on' => 'reec.encuesta_cve = ec.encuesta_cve and reec.course_cve = ec.course_cve', 'escape' => ''),
            array('tabla' => 'public.mdl_groups mg', 'on' => 'mg.id = reec.group_id or mg.id = ANY (string_to_array(reec.grupos_ids_text, \',\')::int[])', 'escape' => ''),
            array('tabla' => 'public.mdl_course mcs', 'on' => 'mcs.id = ec.course_cve', 'escape' => ''),
            array('tabla' => 'public.mdl_course_config ccfg', 'on' => 'ccfg.course = ec.course_cve', 'escape' => ''),
            array('tabla' => 'encuestas.sse_encuestas enc', 'on' => 'enc.encuesta_cve = reec.encuesta_cve', 'escape' => ''),
            array('tabla' => 'encuestas.sse_reglas_evaluacion rege', 'on' => 'rege.reglas_evaluacion_cve = enc.reglas_evaluacion_cve', 'escape' => ''),
            //Evaluador
            array('tabla' => 'public.mdl_user uedor', 'on' => 'uedor.id = reec.evaluador_user_cve', 'escape' => ''),
            array('tabla' => 'public.mdl_role mrdor', 'on' => 'mrdor.id = rege.rol_evaluador_cve', 'escape' => ''),
            array('tabla' => 'gestion.sgp_tab_preregistro_al gpregdor', 'on' => 'gpregdor.nom_usuario = uedor.username and gpregdor.cve_curso = ec.course_cve and rege.rol_evaluador_cve = 5', 'escape' => 'left'),
            array('tabla' => 'nomina.ssn_categoria catpredor', 'on' => 'catpredor.cve_categoria = gpregdor.cve_cat', 'escape' => 'left'),
            array('tabla' => 'departments.ssv_departamentos deppredor', 'on' => 'deppredor.cve_depto_adscripcion = gpregdor.cve_departamental', 'escape' => 'left'),
            array('tabla' => 'tutorias.mdl_usertutor tutdor', 'on' => 'tutdor.nom_usuario=uedor.username and tutdor.id_curso=ec.course_cve', 'escape' => 'left'),
            array('tabla' => 'nomina.ssn_categoria cattutdor', 'on' => 'cattutdor.cve_categoria = tutdor.cve_categoria', 'escape' => 'left'),
            array('tabla' => 'departments.ssv_departamentos depdor', 'on' => 'depdor.cve_depto_adscripcion = tutdor.cve_departamento', 'escape' => 'left'),
            //Evaluado
            array('tabla' => 'public.mdl_user uedo', 'on' => 'uedo.id = reec.evaluado_user_cve', 'escape' => ''),
            array('tabla' => 'public.mdl_role mrdo', 'on' => 'mrdo.id = rege.rol_evaluado_cve', 'escape' => ''),
            array('tabla' => 'tutorias.mdl_usertutor tutdo', 'on' => 'tutdo.nom_usuario=uedo.username and tutdo.id_curso=ec.course_cve', 'escape' => 'left'),
            array('tabla' => 'nomina.ssn_categoria cattutdo', 'on' => 'cattutdo.cve_categoria = tutdo.cve_categoria', 'escape' => 'left'),
            array('tabla' => 'departments.ssv_departamentos depdo', 'on' => 'depdo.cve_depto_adscripcion = tutdo.cve_departamento', 'escape' => 'left'),
            ////////////// Se agregan para autoevaluaciones
            array('tabla' => 'encuestas.sse_designar_autoeveluaciones autoevaluacion', 'on' => 'autoevaluacion.des_autoevaluacion_cve=reec.des_autoevaluacion_cve', 'escape' => 'left'),
            array('tabla' => 'public.mdl_user usuario_autoevaluacion', 'on' => 'usuario_autoevaluacion.id=autoevaluacion.evaluador_user_cve', 'escape' => 'left'),
            array('tabla' => 'public.mdl_role rol_autoevaluacion', 'on' => 'rol_autoevaluacion.id=autoevaluacion.evaluador_rol_id', 'escape' => 'left'),
            array('tabla' => 'tutorias.mdl_usertutor tutor_autoevaluacion', 'on' => 'tutor_autoevaluacion.nom_usuario=usuario_autoevaluacion.username
                        and tutor_autoevaluacion.id_curso=autoevaluacion.course_cve and autoevaluacion.evaluador_rol_id <> 5', 'escape' => 'left'),
            array('tabla' => 'nomina.ssn_categoria cat_tut_autoevaluacion', 'on' => 'cat_tut_autoevaluacion.cve_categoria = tutor_autoevaluacion.cve_categoria', 'escape' => 'left'),
            array('tabla' => 'departments.ssv_departamentos depto_tut_autoevaluacion', 'on' => 'depto_tut_autoevaluacion.cve_depto_adscripcion=tutor_autoevaluacion.cve_departamento', 'escape' => 'left'),
        );
    }

    function getFromContestadas() {
        return "encuestas.sse_result_evaluacion_encuesta_curso reec";
    }

}

class EncNoContestadas {

    function getSelectBasico() {
        return array(
            "rege.reglas_evaluacion_cve regeva", "ec.course_cve", "mcs.shortname",
            //Encuestas
            "enc.cve_corta_encuesta", "enc.descripcion_encuestas",
            //Evaluador 
            "mrdor.id rid_do", 'mrdor."name" rolname_dor', 'uedor.username matricula_dor',
            "concat(uedor.firstname, ' ', uedor.lastname) nom_evaluador",
            "cattutdor.des_clave clave_cattut_dor", "cattutdor.nom_nombre name_cattut_dor", "catpredor.des_clave clave_catpre_dor",
            "catpredor.nom_nombre name_catpre_dor",
            "concat(depdor.cve_depto_adscripcion, ' - ', depdor.des_unidad_atencion) depart_dor",
            "depdor.nom_delegacion delegacion_dor", "depdor.name_region reg_dor",
            "concat(deppredor.cve_depto_adscripcion, ' - ', deppredor.des_unidad_atencion) departpre_dor",
            "deppredor.nom_delegacion delpre_dor", "deppredor.name_region regpre_dor",
            //Evaluado
            "mrdo.id rid_dor", 'mrdo."name" rolname_do', 'uedo.username matricula_do',
            "concat(uedo.firstname, ' ', uedo.lastname) nom_evaluado"
            , "cattutdo.des_clave", "cattutdo.nom_nombre",
            "concat(depdo.cve_depto_adscripcion, ' - ', depdo.des_unidad_atencion) depart_do",
            "depdor.nom_delegacion del_do", "depdo.name_region reg_do",
            "cattutdor.des_clave clave_cattut_do", "cattutdor.nom_nombre name_cattut_do",
            ////////////// Se agregan para autoevaluaciones
            "autoevaluacion.evaluador_user_cve as autoeva_user_cve", "usuario_autoevaluacion.username as autoeva_username", "usuario_autoevaluacion.firstname as autoeva_nombre", "usuario_autoevaluacion.lastname as autoeva_apellido", 
            "autoevaluacion.evaluador_rol_id as autoeva_rol_id", "rol_autoevaluacion.name as autoeva_rol_nombre", "tutor_autoevaluacion.cve_departamento as autoeva_cve_departamento",
            "depto_tut_autoevaluacion.nom_depto_adscripcion as autoeva_nom_depto", "depto_tut_autoevaluacion.cve_regiones as autoeva_cve_regiones", 
            "depto_tut_autoevaluacion.name_region as autoeva_name_region", "depto_tut_autoevaluacion.cve_delegacion as autoeva_cve_delegacion", 
            "depto_tut_autoevaluacion.nom_delegacion as autoeva_nom_delegacion", "(select * from departments.get_unidad(tutor_autoevaluacion.cve_departamento, 7)) as rama_tut_autoevaluacion",
            "tutor_autoevaluacion.cve_categoria as autoeva_cve_categoria", "cat_tut_autoevaluacion.nom_nombre as autoeva_cat_nombre"
        );
    }

    function getSelectTutorizados() {
        return array(
            //comprueba encuesta contestada
            'array_agg(distinct mg."id") as ids_grupos',
            'string_agg(distinct mg."name", \', \') as names_grupos',
            "cbg.bloque",
        );
    }

    /**
     * 
     * @return type Campos exclusivos para la consulta de cursos no tutorizados
     */
    function getSelectNoTutorizados() {
        return array(
            'mg.id as ids_grupos',
            'mg.name as names_grupos',
        );
    }

    /**
     * 
     * @return string query que indica las encuestas contestadas  = "contestada" > 0, 
     * si "contestada" = 0 se dice que la encuesta no ha sido contestada 
     */
    function whereEncContestadasNoContestadasTutorizado() {
        return '(select count(*)
                from encuestas.sse_encuestas encp
                join encuestas.sse_reglas_evaluacion regep on  regep.reglas_evaluacion_cve = encp.reglas_evaluacion_cve
                join encuestas.sse_result_evaluacion_encuesta_curso reecp on reecp.encuesta_cve = encp.encuesta_cve 
                join encuestas.sse_curso_bloque_grupo cbgp on cbgp.course_cve = reecp.course_cve and cbgp.bloque = cbg.bloque  
                where reecp.evaluado_user_cve = expe.userid and reecp.evaluador_user_cve = uedor.id
                ) = 0';
    }

    function whereEncContestadasNoContestadasNoTutorizado() {
        return '(select count(*)
                from encuestas.sse_encuestas encp
                join encuestas.sse_reglas_evaluacion regep on  regep.reglas_evaluacion_cve = encp.reglas_evaluacion_cve
                join encuestas.sse_result_evaluacion_encuesta_curso reecp on reecp.encuesta_cve = encp.encuesta_cve and reecp.group_id = mg.id
                where reecp.evaluado_user_cve = expe.userid and reecp.evaluador_user_cve = uedor.id
                ) = 0';
    }

    function getFromNoContestadas() {
        return "tutorias.mdl_userexp expe"; //Expediente del evaluado 
    }

    function getJoinBasicos() {
        return array(
            array('tabla' => 'encuestas.sse_reglas_evaluacion rege', 'on' => 'rege.rol_evaluado_cve = expe."role"', 'escape' => ''),
            array('tabla' => 'encuestas.sse_encuestas enc', 'on' => 'enc.reglas_evaluacion_cve = rege.reglas_evaluacion_cve ', 'escape' => ''), //Obtiene encuestas relacionadas con la regla que aplica
            array('tabla' => 'encuestas.sse_encuesta_curso ec', 'on' => 'ec.encuesta_cve = enc.encuesta_cve', 'escape' => ''), //Obtiene relacion encuesta curso
            array('tabla' => 'public.mdl_course mcs', 'on' => 'mcs.id = ec.course_cve', 'escape' => ''),
            array('tabla' => 'public.mdl_course_config ccfg', 'on' => 'ccfg.course = mcs.id', 'escape' => ''),
            array('tabla' => 'public.mdl_groups mg', 'on' => 'mg.id = expe.grupoid and mg.courseid = ec.course_cve', 'escape' => ''),
            array('tabla' => 'mdl_user uedo', 'on' => 'uedo.id = expe.userid', 'escape' => ''),
            array('tabla' => 'public.mdl_role mrdo', 'on' => 'mrdo.id = rege.rol_evaluado_cve ', 'escape' => ''),
            array('tabla' => 'tutorias.mdl_usertutor tutdo', 'on' => 'tutdo.nom_usuario=uedo.username and tutdo.id_curso=ec.course_cve', 'escape' => 'left'),
            array('tabla' => 'nomina.ssn_categoria cattutdo', 'on' => 'cattutdo.cve_categoria = tutdo.cve_categoria', 'escape' => 'left'),
            array('tabla' => 'departments.ssv_departamentos depdo', 'on' => 'depdo.cve_depto_adscripcion = tutdo.cve_departamento', 'escape' => 'left'),
            //Evaluador
            array('tabla' => 'public.mdl_enrol enrdor', 'on' => 'enrdor.courseid = mcs.id', 'escape' => ''),
            array('tabla' => 'mdl_context ctxt', 'on' => 'ctxt.instanceid = mcs.id', 'escape' => ''),
            array('tabla' => 'mdl_role_assignments rss', 'on' => 'rss.contextid = ctxt.id', 'escape' => ''),
            array('tabla' => 'mdl_role mrdor', 'on' => 'mrdor.id = rss.roleid and mrdor.id = rege.rol_evaluador_cve', 'escape' => ''),
            //array('tabla' => 'mdl_user uedor', 'on' => 'uedor.id = rss.userid and uedor.id <> uedo.id', 'escape' => ''),
            array('tabla' => 'mdl_user uedor', 'on' => 'uedor.id = rss.userid', 'escape' => ''),
            array('tabla' => 'public.mdl_groups_members gm', 'on' => 'gm.userid = uedor.id AND gm.groupid = mg.id', 'escape' => 'RIGHT'),
            array('tabla' => 'gestion.sgp_tab_preregistro_al gpregdor', 'on' => 'gpregdor.nom_usuario = uedor.username and gpregdor.cve_curso = ec.course_cve and rege.rol_evaluador_cve = 5', 'escape' => 'left'),
            array('tabla' => 'nomina.ssn_categoria catpredor', 'on' => 'catpredor.cve_categoria = gpregdor.cve_cat', 'escape' => 'left'),
            array('tabla' => 'departments.ssv_departamentos deppredor', 'on' => 'deppredor.cve_depto_adscripcion = gpregdor.cve_departamental', 'escape' => 'left'),
            array('tabla' => 'tutorias.mdl_usertutor tutdor', 'on' => 'tutdor.nom_usuario=uedor.username and tutdor.id_curso=ec.course_cve', 'escape' => 'left'),
            array('tabla' => 'nomina.ssn_categoria cattutdor', 'on' => 'cattutdor.cve_categoria = tutdor.cve_categoria', 'escape' => 'left'),
            array('tabla' => 'departments.ssv_departamentos depdor', 'on' => 'depdor.cve_depto_adscripcion = tutdor.cve_departamento', 'escape' => 'left'),
            ////////////// Se agregan para autoevaluaciones
            //array('tabla' => 'encuestas.sse_designar_autoeveluaciones autoevaluacion', 'autoevaluacion.des_autoevaluacion_cve=eva.des_autoevaluacion_cve', 'left'),
            array('tabla' => 'encuestas.sse_designar_autoeveluaciones autoevaluacion', 'on' => 'autoevaluacion.course_cve=ec.course_cve AND autoevaluacion.encuesta_cve=enc.encuesta_cve AND autoevaluacion.evaluado_user_cve=uedo.id AND uedor.id=uedo.id', 'escape' => 'left'),
            array('tabla' => 'public.mdl_user usuario_autoevaluacion', 'on' => 'usuario_autoevaluacion.id=autoevaluacion.evaluador_user_cve', 'escape' => 'left'),
            array('tabla' => 'public.mdl_role rol_autoevaluacion', 'on' => 'rol_autoevaluacion.id=autoevaluacion.evaluador_rol_id', 'escape' => 'left'),
            array('tabla' => 'tutorias.mdl_usertutor tutor_autoevaluacion', 'on' => 'tutor_autoevaluacion.nom_usuario=usuario_autoevaluacion.username
                    and tutor_autoevaluacion.id_curso=autoevaluacion.course_cve and autoevaluacion.evaluador_rol_id <> 5', 'escape' => 'left'),
            array('tabla' => 'nomina.ssn_categoria cat_tut_autoevaluacion', 'on' => 'cat_tut_autoevaluacion.cve_categoria = tutor_autoevaluacion.cve_categoria', 'escape' => 'left'),
            array('tabla' => 'departments.ssv_departamentos depto_tut_autoevaluacion', 'on' => 'depto_tut_autoevaluacion.cve_depto_adscripcion=tutor_autoevaluacion.cve_departamento', 'escape' => 'left'),
        );
    }

    function getJoinBloqueGrupoCurso() {
        return array(
//            array('tabla' => 'encuestas.sse_curso_bloque_grupo cbg', 'on' => 'cbg.course_cve = reec.course_cve and (cbg.mdl_groups_cve = reec.group_id or cbg.mdl_groups_cve = ANY (string_to_array(reec.grupos_ids_text, \',\')::int[]))', 'escape' => ''),
            array('tabla' => 'encuestas.sse_curso_bloque_grupo cbg', 'on' => 'cbg.mdl_groups_cve = mg.id', 'escape' => ''),
        );
    }

    function getGroupBy_basico() {
        return array(
            "rege.reglas_evaluacion_cve", "ec.course_cve, mcs.shortname", "enc.cve_corta_encuesta", "enc.descripcion_encuestas",
//Evaluador
            "mrdor.id", 'mrdor."name"', "uedor.id", "uedor.username", "uedor.firstname", "uedor.lastname",
            "cattutdor.des_clave", "cattutdor.nom_nombre", "catpredor.des_clave", "catpredor.nom_nombre",
            "depdor.cve_depto_adscripcion", "depdor.des_unidad_atencion", "depdor.nom_delegacion", "depdor.name_region",
            "deppredor.cve_depto_adscripcion", "deppredor.des_unidad_atencion", "deppredor.nom_delegacion", "deppredor.name_region",
//Evaluado
            "mrdo.id", 'mrdo."name"', "expe.userid", "uedo.username", "uedo.firstname", "uedo.lastname"
            , "cattutdo.des_clave", "cattutdo.nom_nombre",
            "depdo.cve_depto_adscripcion", "depdo.des_unidad_atencion", "depdor.nom_delegacion", "depdo.name_region",
            "cattutdor.des_clave", "cattutdor.nom_nombre",
            "autoevaluacion.evaluador_user_cve", "usuario_autoevaluacion.username", "usuario_autoevaluacion.firstname", "usuario_autoevaluacion.lastname", "autoevaluacion.evaluador_rol_id", "rol_autoevaluacion.name", "tutor_autoevaluacion.cve_departamento",
            "depto_tut_autoevaluacion.nom_depto_adscripcion", "depto_tut_autoevaluacion.cve_regiones", "depto_tut_autoevaluacion.name_region", "depto_tut_autoevaluacion.cve_delegacion", 
            "depto_tut_autoevaluacion.nom_delegacion", "tutor_autoevaluacion.cve_categoria", "cat_tut_autoevaluacion.nom_nombre"
        );
    }

    function getGroupBy_Tutorizados() {
        return array(
            'cbg.bloque'
        );
    }

    function getGroupBy_NoTutorizados() {
        return array(
            'mg.id',
            'mg.name',
        );
    }

}

class WhereGeneral {

    function getWere() {
        return array(
            'tutorizado' => array('campo' => 'ccfg.tutorizado', 'escape' => 'where', 'value' => ''),
            'anio' => array('campo' => "to_char(to_timestamp((mcs.startdate)::double precision), 'YYYY'::text)", 'escape' => 'where', 'value' => ''),
            //'regionr' => array('campo' => 'depdor.cve_regiones', 'escape' => 'where', 'value' => ''),
            'region' => array('campo' => 'depdo.cve_regiones', 'escape' => 'where', 'value' => ''),
            'curso' => array('campo' => 'mcs.id', 'escape' => 'where', 'value' => ''),
            'instrumento_regla' => array('campo' => 'rege.reglas_evaluacion_cve', 'escape' => 'where', 'value' => ''),
            //'umaedor' => array('campo' => 'depdor.cve_depto_adscripcion', 'escape' => 'where', 'value' => ''),
            'umae' => array('campo' => 'depdo.cve_depto_adscripcion', 'escape' => 'where', 'value' => ''),
            //'delegacion_dor' => array('campo' => 'depdor.cve_delegacion', 'escape' => 'where', 'value' => ''),
            'delegacion' => array('campo' => 'depdo.cve_delegacion', 'escape' => 'where', 'value' => ''),
            //'rol_evaluador' => array('campo' => 'rege.rol_evaluador_cve', 'escape' => 'where', 'value' => ''),
            'rol_evaluado' => array('campo' => 'rege.rol_evaluado_cve', 'escape' => 'where', 'value' => ''),
        );
    }

    function getWereText() {
        return array(
            'matriculado' => array('campo' => 'lower(uedo.username) like', 'escape' => 'where', 'value' => 'lower(\'%~~%\')'),
            'namedocentedo' => array('campo' => "lower(translate(concat(uedo.firstname, ' ', uedo.lastname),'áéíóúÁÉÍÓÚüÜ','aeiouAEIOUuU')) like", 'escape' => 'where', 'value' => "lower(translate('%~~%','áéíóúÁÉÍÓÚüÜ','aeiouAEIOUuU'))"),
            'claveadscripcion' => array('campo' => 'lower(depdo.cve_depto_adscripcion) like', 'escape' => 'where', 'value' => 'lower(\'%~~%\')'),
            'categoria' => array('campo' => 'lower(cattutdo.nom_nombre) like', 'escape' => 'where', 'value' => 'lower(\'%~~%\')'),
            //'matriculador' => array('campo' => 'lower(uedor.username) like', 'escape' => 'where', 'value' => 'lower(\'%~~%\')'),
            'matriculador' => array('campo' => '(lower(uedor.username) like lower(\'%~~%\') OR usuario_autoevaluacion.username like lower(\'%~~%\'))', 'escape' => 'where', 'value' => ''),
            'namedocentedor' => array('campo' => "lower(translate(concat(uedor.firstname, ' ', uedor.lastname),'áéíóúÁÉÍÓÚüÜ','aeiouAEIOUuU')) like", 'escape' => 'where', 'value' => "lower(translate('%~~%','áéíóúÁÉÍÓÚüÜ','aeiouAEIOUuU'))"),
            'claveadscripciondor' => array('campo' => '(lower(depdor.cve_depto_adscripcion) like lower(\'%~~%\') or lower(deppredor.cve_depto_adscripcion) like lower(\'%~~%\'))', 'escape' => 'where', 'value' => ''),
            //'categoriar' => array('campo' => '(lower(cattutdor.des_clave) like lower(\'%~~%\') or lower(catpredor.des_clave) like lower(\'%~~%\'))', 'escape' => 'where', 'value' => ''),
            'categoriar' => array('campo' => '(lower(cattutdor.nom_nombre) like lower(\'%~~%\') or lower(catpredor.nom_nombre) like lower(\'%~~%\') or lower(cat_tut_autoevaluacion.nom_nombre) like lower(\'%~~%\'))', 'escape' => 'where', 'value' => ''),
            'regionr' => array('campo' => '(depdor.cve_regiones=~~ OR depto_tut_autoevaluacion.cve_regiones=~~)', 'escape' => 'where', 'value' => ''),
            'delegacion_dor' => array('campo' => '(depdor.cve_delegacion=\'~~\' OR depto_tut_autoevaluacion.cve_delegacion=\'~~\')', 'escape' => 'where', 'value' => ''),
            'rol_evaluador' => array('campo' => '(rege.rol_evaluador_cve=\'~~\' OR autoevaluacion.evaluador_rol_id=\'~~\')', 'escape' => 'where', 'value' => ''),
            'umaedor' => array('campo' => '(depdor.cve_depto_adscripcion=\'~~\' OR tutor_autoevaluacion.cve_departamento=\'~~\')', 'escape' => 'where', 'value' => ''),
        );
    }

    function getWereCamposText() {
        return array(
            //Evaluado
            'text_buscar_docente_evaluado' => 'tipo_buscar_docente_evaluado',
            'text_buscar_adscripcion' => 'tipo_buscar_adscripcion',
            'text_buscar_categoria' => 'tipo_buscar_categoria',
            //Evaluador
            'text_buscar_docente_evaluador' => 'tipo_buscar_docente_evaluador',
            'text_buscar_adscripcion_dor' => 'tipo_buscar_adscripcion_dor',
            'text_buscar_categoriar' => 'tipo_buscar_categoriar',
            'regionr' => 'regionr',
            'delegacion_dor' => 'delegacion_dor',
            'rol_evaluador' => 'rol_evaluador',
            'umaedor' => 'umaedor'
        );
    }

    function getCondiciones($param) {

        $array_where = $this->getWere(); //Array de condiciones where
        $where = array();
        foreach ($array_where as $key => $filter) {//Recorre las condiciones de filtros por identificador del catálogo where
            if (isset($param[$key]) AND ( !empty($param[$key]) || strlen($param[$key]) > 0)) {//Verifica si exite el filtro en el array, si existe, valida que no venga vacio
                $tmp = $filter;
                $tmp['value'] = $param[$key]; //Asigna el valor al filtro 
                $where[] = $tmp; //Agrega la condición al where
            }
        }

        $array_where = $this->getWereCamposText(); //Obtiene los where de texto
        $array_where_text = $this->getWereText(); //Obtiene los where real para buscar por texto
//        pr("------------------------------------------**************");
//        pr($array_where);
        $llave_valor = array(); //pr($array_where); pr($param);
        foreach ($array_where as $key => $filText) {//Recorre los campos de texto 
            if (isset($param[$key]) AND ( !empty($param[$key]) || strlen($param[$key]) > 0)) {//Verifica si exite el filtro en el array, si existe, valida que no venga vacio
                if (isset($param[$filText]) AND ( !empty($param[$filText]) || strlen($param[$filText]) > 0)) {//Verifica si exite el filtro tipo, si existe, valida que no venga vacio
                    //pr('key:'.$key.' pk:'. $param[$key].' filText:'. $filText.' pf:'.$param[$filText]);
                    $tmp = ($filText==$key) ? $array_where_text[$filText] : $array_where_text[$param[$filText]];
                    //pr($tmp);
                    $value_tmp = $tmp['value'];
                    $value_tmpp = str_replace('~~', $param[$key], $value_tmp);
                    $tmp['campo'] = str_replace('~~', $param[$key], $tmp['campo']);
                    $tmp['campo'] .= ' ' . $value_tmpp;
                    $tmp['value'] = null; //Asigna el valor al filtro 
                    $llave_valor[$param[$filText]] = $tmp;
                    $where[] = $tmp;
                }
            }
        }
//        pr($llave_valor);
//        pr("------------------------------------------**************");
//        exit();
        return $where;
    }

}
