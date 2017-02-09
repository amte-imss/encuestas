<?php
$logueado = $this->session->userdata('logueado');
$nombre = $this->session->userdata("nombre");  //Tipo de usuario almacenado en sesiÃ³n
//pr($this->uri->segment(1));
//pr($this->uri->segment(2));
$array_controlador = array('encuestausuario' => array('lista_encuesta_usuario'));
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
                <div class="navbar-header right">
                    <a class="navbar-brand" href="<?php echo site_url('login/cerrar_session'); ?>">Cerrar sesión
                        <span class="glyphicon glyphicon-log-out"></span></a>
                </div>
                <ul class="nav navbar-nav">
                    <li class="active"><a href="<?php echo site_url('encuestas/index'); ?>">Encuestas</a></li>
                    <li><a href="<?php echo site_url('curso'); ?>" class="a_nav_sied" >Implementaciones</a>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Reportes
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('reporte'); ?>" class="a_menu">Reporte resumen de bonos</a></li>
                            <li><a href="<?php echo site_url('reporte_bonos'); ?>" class="a_menu">Reporte de implementación</a></li>
                            <li><a href="<?php echo site_url('reporte_general'); ?>" class="a_menu">Reporte general</a></li>
                            <li><a href="<?php echo site_url('reporte_detallado'); ?>" class="a_menu">Reporte detalle de encuestas</a></li>
                            <li><a href="<?php echo site_url('Resultadocursoindicador'); ?>" class="a_menu">Reporte por indicadores</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            Gestión
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('reglas_evaluacion/index'); ?>" class="a_menu">Gestión de Reglas de evaluación</a></li>
                            <li><a href="<?php echo site_url('reglas_evaluacion/index'); ?>" class="a_menu">Gestión de usuarios autoevaluados</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Catálogos
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('catalogos/departamentos'); ?>" class="a_menu">Departamentos</a></li>
                        </ul>
                    </li>
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


