<?php
$logueado = $this->session->userdata('logueado');
$nombre = $this->session->userdata("nombre");  //Tipo de usuario almacenado en sesiÃ³n
$secciones_acceso = $this->session->userdata("secciones_acceso");  //Tipo de usuario almacenado en sesión
$modulos_acceso = $this->session->userdata("modulos_acceso");  //Tipo de usuario almacenado en sesión
//pr($secciones_acceso);
//pr($this->uri->segment(1));
//pr($this->uri->segment(2));
$array_controlador = array('encuestausuario' => array('lista_encuesta_usuario', 'instrumento_asignado', 'guardar_encuesta_usuario'));
$valida_menu = 1;
foreach ($array_controlador as $controlador => $metodos) {
    if ($controlador == $this->uri->segment(1)) {
        foreach ($metodos as $value) {
            if ($value == $this->uri->segment(2)) {
                $valida_menu = 0;
            }
        }
    }
}
if (isset($logueado) && !empty($logueado)) {
    ?>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <?php if ($valida_menu) { ?>
                <div class="navbar-header right" onclick="window.close(this);">
                    <a class="navbar-brand" href="<?php echo site_url('login/cerrar_session'); ?>" onclick="window.close(this);">Cerrar sesión
                        <span class="glyphicon glyphicon-log-out" ></span></a>
                </div>
                <ul class="nav navbar-nav">
                    <?php if (isset($secciones_acceso[En_modulos::ENCUESTAS])) { ?>
                        <li class="active"><a href="<?php echo site_url('encuestas/index'); ?>">Encuestas</a></li>
                    <?php } ?>
                    <?php if (isset($secciones_acceso[En_modulos::IMPLEMENTACIONES])) { ?>
                        <li><a href="<?php echo site_url('curso'); ?>" class="a_nav_sied" >Implementaciones</a></li>
                    <?php } ?>
                    <?php if (isset($secciones_acceso[En_modulos::REPORTES])) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Reportes
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <!--<li><a href="<?php // echo site_url('reporte_general');                 ?>" class="a_menu">Reporte general</a></li>-->
                                <?php if (isset($modulos_acceso[En_modulos::REPORTES_BONOS]) and $modulos_acceso[En_modulos::REPORTES_BONOS]['acceso'] == 1) { ?>
                                    <li><a href="<?php echo site_url('reporte'); ?>" class="a_menu">Reporte resumen de bonos</a></li>
                                <?php } ?>
                                <?php if (isset($modulos_acceso[En_modulos::REPORTES_IMPLEMENTACION])and $modulos_acceso[En_modulos::REPORTES_IMPLEMENTACION]['acceso'] == 1) { ?>
                                    <li><a href="<?php echo site_url('reporte_bonos'); ?>" class="a_menu">Reporte de implementación</a></li>
                                <?php } ?>
                                <?php if (isset($modulos_acceso[En_modulos::REPORTES_GENERAL])and $modulos_acceso[En_modulos::REPORTES_GENERAL]['acceso'] == 1) { ?>
                                    <li><a href="<?php echo site_url('reporte_general'); ?>" class="a_menu">Reporte general</a></li>
                                <?php } ?>
                                <?php if (isset($modulos_acceso[En_modulos::REPORTES_DETALLE_ENCUESTAS])and $modulos_acceso[En_modulos::REPORTES_DETALLE_ENCUESTAS]['acceso'] == 1) { ?>
                                    <li><a href="<?php echo site_url('reporte_detallado'); ?>" class="a_menu">Reporte detalle de encuestas</a></li>
                                <?php } ?>
                                <?php if (isset($modulos_acceso[En_modulos::REPORTES_INDICADORES])and $modulos_acceso[En_modulos::REPORTES_INDICADORES]['acceso'] == 1) { ?>
                                    <li><a href="<?php echo site_url('resultadocursoindicador'); ?>" class="a_menu">Reporte por indicadores</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if (isset($secciones_acceso[En_modulos::GESTION])) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                Gestión
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if (isset($modulos_acceso[En_modulos::GESTION_REGLAS_EVALUACION])and $modulos_acceso[En_modulos::GESTION_REGLAS_EVALUACION]['acceso'] == 1) { ?>
                                    <li><a href="<?php echo site_url('reglas_evaluacion/index'); ?>" class="a_menu">Gestión de Reglas de evaluación</a></li>
                                <?php } ?>
                                <?php if (isset($modulos_acceso[En_modulos::GESTION_DESIGNAR_AUTOEVALUACION])and $modulos_acceso[En_modulos::GESTION_DESIGNAR_AUTOEVALUACION]['acceso'] == 1) { ?>
                                    <li><a href="<?php echo site_url('encuestausuario/lista_encuesta_usuario_autoevaluado'); ?>" class="a_menu">Gestión de usuarios autoevaluados</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if (isset($secciones_acceso[En_modulos::CATALOGOS])) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Catálogos
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('catalogos/departamentos'); ?>" class="a_menu">Departamentos</a></li>
                            </ul>
                        </li>    
                    <?php } ?>

                    <li>
                        <a href="<?php echo site_url('login/regresar_sied'); ?>" class="a_nav_sied">
                            Regresar a SIED
                        </a>
                    </li>
                </ul>
            <?php } else { ?>
                <div class="navbar-header right">
                    <a class="navbar-brand" href="<?php echo site_url('login/cerrar_session/edu'); ?>">Cerrar sesión
                        <span class="glyphicon glyphicon-log-out"></span></a>
                </div>
            <?php } ?>
        </div>
    </nav>

    <?php if ($valida_menu) { ?>
        <div class="row">
            <div style="text-align:right; margin-right: 15px;"><?php echo $nombre; ?></div>
        </div>        
        <div class="clearfix"></div>
    <?php } ?>
    <?php
} 


