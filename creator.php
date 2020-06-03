<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//mkdir("./datos/prueba");
//$ficheros = scandir("./datos");
//foreach($ficheros as $fichero){
//    if (is_dir($fichero)){
//    echo $fichero . "<br>";
//    }
//}
require_once 'vendor/autoload.php';
use Symfony\Component\Process\Process;

$process = Process::fromShellCommandline("docker ps | grep $(docker ps -q)");
$process->run();
echo $process->getOutput();
?>