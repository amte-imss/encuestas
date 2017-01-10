<?php if (isset($empleados) && !empty($empleados)) { 

    $this->config->load('general');
    $pun_rol = $this->config->item('puntos_rol');
    $pun_tc = $this->config->item('puntos_tipo_curso_id');
    $pun_hor = $this->config->item('puntos_horas');

//    pr($empleados);
    ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="table-responsive" style="overflow-x: scroll;">
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>Matr&iacute;cula</th>
                    <th>Docente</th>
                    <th>Clave curso</th>
                    <th>Curso</th>
                    <th>Fecha inicio</th>
                    <th>Año</th>
                    <th>Tutorizado</th>
                    <th>Rol del docente evaluado</th>
                    <th>Puntos rol</th>
                    <th>Tipo curso</th>
                    <th>Puntos tipo curso</th>
                    <th>Duración horas</th>
                    <th>Puntos duracion</th>
                    <th class="success">Total puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                foreach ($empleados as $key => $val) {
                    //rol_id
//                    $puntos_horas=0;
//                    if(isset($val['horascur'])){
//                        if($val['horascur']>=120){
//                            $puntos_horas=$pun_hor['>120']['PUN'];
//                        }elseif($val['horascur']<120 && $val['horascur']>=80){
//                            $puntos_horas=$pun_hor['>80']['PUN'];
//                        }elseif($val['horascur']<80 && $val['horascur']>=40){
//                            $puntos_horas=$pun_hor['>40']['PUN'];
//                        }elseif($val['horascur']<40 && $val['horascur']>1){
//                            $puntos_horas=$pun_hor['<40']['PUN'];
//                        }else{
//                            $puntos_horas=$pun_hor['=0']['PUN'];
//                        }
//                    }
                    $total_puntos = 0;
                    $puntos_rol = intval($pun_rol[$val['rol_id'].'_'.$val['tutorizado']]);
                    $total_puntos += $puntos_rol;
                    $puntos_tp_curso = intval($pun_tc[$val['tipo_curso_id']]);
                    $total_puntos += $puntos_tp_curso;
                    $puntos_duracion = intval($val['puntaje_duracion']);
                    $total_puntos += $puntos_duracion;
//                    $total_puntos = intval($pun_rol) + intval($puntos_tp_curso) + intval($puntos_duracion);
                    echo '
                    <td>' . $val['emp_matricula'] . '</td>
                    <td>' . $val['emp_nombre'] .'</td > 
                    <td>' . $val['cur_clave'] . '</td>
                    <td>' . $val['cur_nom_completo']. '</td>
                    <td>' . $val['fecha_inicio'] . '</td>                   
                    <td>' . $val['anio'] . '</td>                   
                    <td>' . $val['tex_tutorizado'] . '</td>
                    <td>' . $val['rol_nom'] . '</td>
                    <td>'.$puntos_rol.'</td>
                    <td>' . $val['tipo_curso']. '</td>
                    <td>'.$puntos_tp_curso.'</td>
                    <td>' . $val['horascur'] . '</td>
                    <td>'.$puntos_duracion.'</td>
                    <td class="success">'. $total_puntos .'</td>
                    ';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        
        </div>

    </div>
    </div>

<?php } else { ?>
    <br><br>
    <div class="row">
        <div class="jumbotron"><div class="container"> <p class="text_center">No se encontraron datos registrados con esta busqueda</p> </div></div>
    </div>
<?php } ?>
