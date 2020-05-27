<?php


require_once 'vendor/autoload.php';
//use Gnomejodas\Modelos\Person;
use Spatie\Docker\DockerContainer;
use Spatie\Docker\DockerContainerInstance;

//$manolo = new Person("Juan","Martinez","2018-12-05");

if (isset($_POST['iniciar'])){

$containerInstance = DockerContainer::create("gnomejodas/lopz:1.0","lopz")
    ->daemonize()
//    ->doNotCleanUpAfterExit()
    ->mapPort("80", "80")
    ->start();
exit($containerInstance->getDockerIdentifier());
}

if (isset($_POST['detener'])){

$container = new DockerContainer("gnomejodas/lopz:1.0","lopz");
$containerInstance = new DockerContainerInstance($container,$_POST['id'],"lopz");
$containerInstance->stop();

exit(var_dump($containerInstance));

//$mensaje = $containerInstance->getDockerIdentifier();

}


if (isset($_POST['nuevo'])){
    
    if (filter_var($_POST['red'],FILTER_VALIDATE_IP)){
    
        $fileConfig=fopen("./data/dhcp/$nombre", "w");

            fwrite($fileConfig, '	$host="'.$_POST['servidorBD'].'";'."\n");
            fwrite($fileConfig, '	$user="'.$_POST['usuarioBD'].'";'."\n");
            fwrite($fileConfig, '	$passwordDB="'.$_POST['passwordBD'].'";'."\n");
            fwrite($fileConfig, '	$db="'.strtolower($_POST['database']).'";'."\n");
            fwrite($fileConfig, '	$port="'.$_POST['puertoBD'].'";'."\n");

        fclose($fileConfig);
        
    }
    else {
        
        echo "<p> Esa no es una Dirección Ip válida </p>";
        
    }
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
        <script src="./lib/jss/jquery-3.4.1.min.js"></script>
        <script src="./lib/jss/dhcp.js"></script>
        
        <!--<script src="myscripts.js"></script>-->
        <link rel="stylesheet" type="text/css" href="./lib/css/dhcp.css">

    </head>
    <body>
        
        <?php
        require_once './src/vistas/nav.php';
        ?>




        <form method="POST" onsubmit="return check(this);">
            
            <h3>Datos del DHCP</h3>
            
            Red<br>
            <input type="number" class="ip" min="0" max="255" name="net1"/>
            <input type="number" class="ip" min="0" max="255" name="net2"/>
            <input type="number" class="ip" min="0" max="255" name="net3"/>
            <input type="number" class="ip" min="0" max="255" name="net4"/>
            <br>
            
            Mascara <br>
            <input class="ip" min="1" max="24" type="number" name="m1"/><br>
            
            IP inicio<br>
            <input class="ip" min="0" max="255" type="number" name="n1"/>
            <input class="ip" min="0" max="255" type="number" name="n2"/>
            <input class="ip" min="0" max="255" type="number" name="n3"/>
            <input class="ip" min="0" max="255" type="number" name="n4"/><br>
            IP Fin<br>
            <input class="ip" min="0" max="255" type="number" name="f1"/>
            <input class="ip" min="0" max="255" type="number" name="f2"/>
            <input class="ip" min="0" max="255" type="number" name="f3"/>
            <input class="ip" min="0" max="255" type="number" name="f4"/>
            <br><br>
            
            <input type="submit" value="Crear nuevo DHCP"/>
        </form>


        <button onclick="arrancar(this)"; >Arrancar</button>
        <button onclick="detener(this)"; >detener</button>
        
        
        
    </body>
</html>