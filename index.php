<?php

if ($_GET['site'] == "dhcp"){
    require_once './src/vistas/dhcp.php';
}
else {

include_once './src/vistas/indice.php';
}


?>
