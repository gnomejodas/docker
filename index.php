<?php

//require_once 'vendor/autoload.php';
////use Gnomejodas\Modelos\Person;
//use Spatie\Docker\DockerContainer;
//use Spatie\Docker\DockerContainerInstance;
//
////$manolo = new Person("Juan","Martinez","2018-12-05");
//
//if (isset($_POST['iniciar'])){
//
//$containerInstance = DockerContainer::create("gnomejodas/lopz:1.0","lopz")
//    ->daemonize()
////    ->doNotCleanUpAfterExit()
//    ->mapPort("80", "80")
//    ->start();
//exit($containerInstance->getDockerIdentifier());
//}
//
//if (isset($_POST['detener'])){
//
//$container = new DockerContainer("gnomejodas/lopz:1.0","lopz");
//$containerInstance = new DockerContainerInstance($container,$_POST['id'],"lopz");
//$containerInstance->stop();

//exit(var_dump($containerInstance));

//$mensaje = $containerInstance->getDockerIdentifier();

//}



?>

<?php
//
//if ($_GET['sitio'] = "index"){
//    
//    include_once 'index.php';
//}
//elseif ($_GET['sitio'] = "dhcp"){
//    
//    include_once 'dhcp.php';
//}
//
//?>


<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="./lib/jss/jquery-3.4.1.min.js"></script>
        <script src="./lib/jss/dhcp.js"></script>
        
        <!--<script src="myscripts.js"></script>-->
        <link rel="stylesheet" type="text/css" href="lib/css/index.css">

    </head>
    <body>
        <ul>
        <li><a class="active">Inicio</a></li>
        <li><a href="?site=dhcp">DHCP</a></li>
        <!--  <li><a href="#contact">Contact</a></li>
          <li><a href="#about">About</a></li>-->
        </ul>


        

<?php
if ($_GET['site'] == "dhcp"){
    require_once './src/vistas/dhcp.php';
}
else {

include_once './src/vistas/index.php';
}


?>


        
    </body>
</html>