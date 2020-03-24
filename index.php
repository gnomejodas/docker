<?php

require_once 'vendor/autoload.php';

use Spatie\Docker\DockerContainer;
use Spatie\Docker\DockerContainerInstance;


if (isset($_POST['iniciar'])){

$containerInstance = DockerContainer::create("gnomejodas/lopz:1.0","lopz")
    ->daemonize()
    ->mapPort("80", "80")
    ->start();
exit($containerInstance->getDockerIdentifier());
}

if (isset($_POST['detener'])){

$container = new DockerContainer("gnomejodas/lopz:1.0","lopz");
$containerInstance = new DockerContainerInstance($container,$_POST['id'],"lopz");
$containerInstance->stop();

//exit(var_dump($containerInstance));

//$mensaje = $containerInstance->getDockerIdentifier();

}



?>




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
        <script src="./jquery-3.4.1.min.js"></script>
        <script>
            var id = "";
            function arrancar(boton){

                 $.post( "index.php", 
                                        { iniciar: "1" },
                                        function( data ) {
                                           //alert(data);
                                           id =  data;
                                        }
                                        );

            }

            function detener(boton){
                //alert(id);
                 $.post( "index.php", 
                                        { detener: "1", id: id },
                                        function( data ) {
                                        //alert(data);
                                           
                                        }
                                        );



            }



        </script>
    </head>
    <body>
        <?php
        // put your code here
        
        echo "<button onclick=arrancar(this); >Arrancar</button>";
        echo "<button onclick=detener(this); >detener</button>";


        ?>
    </body>
</html>
