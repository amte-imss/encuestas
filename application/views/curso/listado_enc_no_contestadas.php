<?php
$bloque = '';
if (isset($evaluaciones) && !empty($evaluaciones)) {
//echo form_open('cursoencuesta/guardar_asociacion', array('id'=>'form_asignar', 'class'=>'form-horizontal'));
//pr($empleado);
    ?>
    <div id="div_y" style="width:900px; height:500px; overflow: scroll;">
        <!--<div class="table-responsive">-->
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Matr&iacute;cula del evaluador</th>
                        <th>Nombre usuario evaluador</th>
                        <th>Rol evaluador</th>
                        <th>Categor&iacute;a evaluador</th>
                        <th>Departamento evaluador</th>
                        <th>Regi&oacute;n evaluador</th>
                        <th>Delegaci&oacute;n evaluador</th>
                        <th>Matr&iacute;cula del evaluado</th>
                        <th>Nombre docente evaluado</th>
                        <th>Rol evaluado</th>
                        <th>Categor&iacute;a evaluado</th>
                        <th>Departamento evaluado</th>
                        <th>Regi&oacute;n evaluado</th>
                        <th>Delegaci&oacute;n evaluado</th>
                        <th>Fol&iacute;o de la encesta</th>
                        <th>Nombre de la encuesta</th>
                        <th>Grupo(s)</th>
                        <?php if ($tutorizado == 1) { ?>
                            <th>Bloque</th>
                        <?php } ?>


                    </tr>
                </thead>
                <tbody>
                    <?php
//                    <th>Bloque</th>
//                    <td > ' . $val['bloque'] . '</td >

                    foreach ($evaluaciones as $key => $val) {
                        $bloque = '';
                        if ($tutorizado == 1) {
                            $bloque = '<td >' . $val['bloque'] . '</td >';
                            $grupos = $val['names_grupos']; //Agrega el nombre de todos los grupos de un  bloque 
//                        if ($val['group_id'] == 0 and isset($val['name_grupos'])) {//Valida la encuesta no haya sido evaluada por bloque
//                        }
                        } else {
                            $grupos = $val['names_grupos']; //Asigna el nombre del grupo 
                        }

                        if ($val['rid_dor'] == En_roles::ALUMNO) {//Verifica si es un alumno o pertenece a la plana docente
                            $region_dor = $val['regpre_dor'];
                            $delegacion_dor = $val['delpre_dor'];
                            $categoria_dor = $val['clave_catpre_dor'] . '-' . $val['name_catpre_dor'];
                            $departamento_dor = $val['departpre_dor'];
                        } else {
                            $region_dor = $val['reg_dor'];
                            $delegacion_dor = $val['delegacion_dor'];
                            $categoria_dor = $val['clave_cattut_dor'] . '-' . $val['name_cattut_dor'];
                            $departamento_dor = $val['depart_dor'];
                        }

                        echo '<tr>
                            <td >' . htmlentities($val['matricula_dor']) . '</td>
                            <td >' . $val['nom_evaluador'] . '</td >
                            <td >' . $val['rolname_dor'] . '</td >
                            <td >' . htmlentities($categoria_dor) . '</td >
                            <td >' . htmlentities($departamento_dor) . '</td >
                            <td >' . htmlentities($region_dor) . '</td >
                            <td >' . htmlentities($delegacion_dor) . '</td >
                            <td >' . htmlentities($val['matricula_do']) . '</td>
                            <td >' . $val['nom_evaluado'] . '</td>
                            <td >' . $val['rolname_do'] . '</td>
                            <td >' . htmlentities($val['clave_cattut_do']) . '-' . htmlentities($val['name_cattut_do']) . '</td >
                            <td >' . htmlentities($val['depart_do']) . '</td >
                            <td >' . htmlentities($val['reg_do']) . '</td >
                            <td >' . htmlentities($val['del_do']) . '</td > 
                            <td >' . htmlentities($val['cve_corta_encuesta']) . '</td > 
                            <td >' . htmlentities($val['descripcion_encuestas']) . '</td > 
                            <td >' . $grupos . '</td >' .
                        $bloque
                        ;
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>

        <!--</div>-->
    </div>
<?php } else { ?>
    <br><br>
    <div class="row">
        <div class="jumbotron"><div class="container"> <p class="text_center">No se encontraron datos registrados con esta busqueda</p> </div></div>
    </div>

<?php }
?>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('#btn_export').show();
    });
</script>