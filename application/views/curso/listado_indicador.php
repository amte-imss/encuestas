<?php
if (isset($registros) && !empty($registros))
{
//echo form_open('cursoencuesta/guardar_asociacion', array('id'=>'form_asignar', 'class'=>'form-horizontal'));
    ?>
    <div style="width: 100%; overflow: auto;">
        <div class="table-responsive" style="width: 100%; max-width: 900px;">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">Bloque</th>
                        <th rowspan="2">Curso</th>
                        <th rowspan="2">Grupo</th>
                        <th rowspan="2">Nombre del evaluador</th>
                        <th rowspan="2">Rol del evaluador</th>
                        <th rowspan="2">Región del evaluador</th>
                        <th rowspan="2">Delegación / UMAE del evaluador</th>
                        <th rowspan="2">Departamento del evaluador</th>
                        <th rowspan="2">Categoría del evaluador</th>
                        <th rowspan="2">Nombre del evaluado</th>
                        <th rowspan="2">Rol del evaluado</th>
                        <th rowspan="2">Región del evaluado</th>
                        <th rowspan="2">Delegación / UMAE del evaluado</th>
                        <th rowspan="2">Departamento del evaluado</th>
                        <th rowspan="2">Categoría del evaluado</th>
                        <?php
                        $html_head = '';
                        foreach ($indicadores as $indicador)
                        {
                            if (isset($indicadores_disponibles['ind' . $indicador['indicador_cve']]))
                            {
                                $html_head .= '<th class="indicador_column' . $indicador['indicador_cve'] . '" style="text-align:center;">' . $indicador['descripcion'] . '</th>';
                            }
                        }
                        echo '<th colspan="' . count($indicadores) . '">Indicadores</th>';
                        ?>
                    </tr>
                    <?php echo $html_head; ?>
                </thead>
                <tbody>
                    <?php
                    foreach ($registros as $registro)
                    {
                        ?>
                        <tr>
                            <td><?php echo $registro['BLN']; ?></td>
                            <td><?php echo $registro['CN']; ?></td>
                            <td><?php echo $registro['GN']; ?></td>
                            <td><?php echo $registro['UN1']; ?></td>
                            <td><?php echo $registro['URN1']; ?></td>
                            <td><?php echo $registro['evaluador_rol_id'] == 5 ? $registro['region_evaluador1'] : $registro['region_evaluador2']; ?></td>
                            <td><?php echo $registro['evaluador_rol_id'] == 5 ? $registro['delegacion_evaluador1'] : $registro['delegacion_evaluador2']; ?></td>
                            <td><?php echo $registro['evaluador_rol_id'] == 5 ? $registro['unidad_evaluador1'] : $registro['unidad_evaluador2']; ?></td>
                            <td><?php echo $registro['evaluador_rol_id'] == 5 ? $registro['categoria_evaluador1'] : $registro['categoria_evaluador2']; ?></td>
                            <td><?php echo $registro['UN2']; ?></td>
                            <td><?php echo $registro['URN2']; ?></td>
                            <td><?php echo $registro['region_evaluado']; ?></td>
                            <td><?php echo $registro['delegacion_evaluado']; ?></td>
                            <td><?php echo $registro['unidad_evaluado']; ?></td>
                            <td><?php echo $registro['categoria_evaluado']; ?></td>
                            <?php
                            foreach ($indicadores as $indicador)
                            {
                                ?>
                                <?php
                                if (isset($indicadores_disponibles['ind' . $indicador['indicador_cve']]))
                                {
                                    echo '<td>';
                                    if (!empty($registro['indP' . $indicador['indicador_cve']]))
                                    {
                                        echo round($registro['indP' . $indicador['indicador_cve']], 2);
                                    } else if ($registro['indP' . $indicador['indicador_cve']] != "")
                                    {
                                        echo '0';
                                    } else
                                    {
                                        echo '--';
                                    }
                                    echo '</td>';
                                }
                                ?>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
} else
{
    ?>
    <br><br>
    <div class="row">
        <div class="jumbotron"><div class="container"> <p class="text_center">No se encontraron datos registrados con esta busqueda</p> </div></div>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('#btn_export').show();
    });
</script>