<?php

//Este se va a utilizar para averiguar datos sobre docker que no están ligados con los contenedores

require_once '../../../vendor/autoload.php';
use Symfony\Component\Process\Process;

if (isset ($_POST['pedirContenedores'])){
    
    if ($_POST['pedirContenedores']=="todo"){
        
        exit(getCurrentContainers());
    }
    
    exit(getCurrentContainers($_POST['pedirContenedores']));
    
    
}
if (isset($_POST['pedirRedes'])){
    
    exit(getCurrentNetworks());
    
}


//Devuelve los ID de los contenedores que están corriendo esa imagen

function getCurrentContainers($imagen = "Up"){
    
$process = Process::fromShellCommandline("docker ps | grep $imagen |  cut -d ' ' -f 1");
$process->run();
return $process->getOutput();
}

function getCurrentNetworks(){
    
    $process = Process::fromShellCommandline("ip addr show | grep -F 'net ' | tr -s ' ' | cut -d ' ' -f 3");
    $process->run();
    return $process->getOutput();
    
}