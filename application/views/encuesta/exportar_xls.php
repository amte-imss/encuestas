<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<table>
    <thead>
        <tr>
            <?php
            foreach ($head as $encabezado) {
                if($encabezado!="INSTRUCCIONES")
                {
                    echo '<th>'.$encabezado.'</th>';
                }
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($data)){
            foreach ($data as $datos) {
                echo '<tr>';
                foreach ($head as $encabezado) {
                    if($encabezado!="INSTRUCCIONES")
                    {
                        echo '<td>'.$datos[$encabezado].'</td>';
                    }
                }
                echo '</tr>';
            }
        } else {
            echo '<tr><td>No existen registros relacionados con esos parámetros de búsqueda.</td></tr>';
        } ?>
    </tbody>
</table>

