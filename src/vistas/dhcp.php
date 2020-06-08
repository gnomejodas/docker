<?php


require_once 'vendor/autoload.php';
use Symfony\Component\Process\Process;

//use Gnomejodas\Modelos\Person;
use Spatie\Docker\DockerContainer;
use Spatie\Docker\DockerContainerInstance;

//Vamos a cargar las redes que existen

function getCurrentNetworks($info="todo"){
    
    $redesConectadas = Process::fromShellCommandline("ip addr show | grep -F 'net ' | tr -s ' ' | cut -d ' ' -f 3");
    $redesConectadas->run();
    $redesConectadas = $redesConectadas->getOutput();
    $redesConectadas = explode("\n", $redesConectadas);
    array_pop($redesConectadas);
    $ipLista =[];
    $mascLista=[];
    $ipmaskLista=[];
    foreach($redesConectadas as $value){
        
        $red = explode("/", $value);
        $ip = $red[0];
        $mascara = $red[1];
        $mascLista[] = $mascara;
        $mascarabinario = " ";
        for ($i=0;$i < $mascara ; $i++){
            
            $mascarabinario = $mascarabinario . "1";
            
        }
        $mascarabinario =  str_pad($mascarabinario,32,"0",STR_PAD_RIGHT);
        
        $ipBin = str_pad(base_convert(ip2long($ip), 10, 2),32,0,STR_PAD_LEFT);
        $redBin = "";
        
        for ($j =0 ; $j<32 ; $j++){
            
            if( $ipBin[$j] && $mascarabinario[$j] ){
                
                $redBin = $redBin . "1";
                                
            }
            else{
                
                $redBin = $redBin . "0";
                
            }
            
        }
        
        $ipbinLista[] = $redBin;
        $redDec =long2ip(bindec($redBin));
        
        $ipLista[] = $redDec;
        
        $ipmaskLista[] = array( $redDec);
        
    }
    
    if ($info == "ipdec"){
        return $ipLista;
    }
    else if ($info = "mask"){
        
        return $mascLista;
    }
    else if ($info == "ipbin"){
        
        return $ipbinLista;
    }
    else if ($info = "todo"){
        
        return $ipmaskLista;
    }
       
}

function checkDhcpConf(){
    
//    $datosDHCP = [];
    
    if(file_exists('./datos/dhcpd.conf')){

    $configuracionDhcp = Process::fromShellCommandline("head -6 ./datos/dhcpd.conf | cut -d '#' -f 2");
    $configuracionDhcp->run();
    $configuracionDhcp = $configuracionDhcp->getOutput();
    
    $datosDHCP = explode("\n", $configuracionDhcp);

//    $instalacion=1;
    }
    else{

        $datosDHCP[] = "No existe ningún DHCP configurado";

    }
    
    return $datosDHCP;
    
}

function getCurrentContainers($imagen = "Up"){
    
$process = Process::fromShellCommandline("docker ps | grep $imagen |  cut -d ' ' -f 1");
$process->run();
return $process->getOutput();
}

if(isset($_POST['pedirConfiguracion'])){
    
    $arrayenPHP = [];
    $configuracion= checkDhcpConf();
    if (getCurrentContainers("dhcpd")){
        
        $estado = "<tr><td style='text-align:center;color:green'>". "DHCP ACTIVO". '</td> </tr>';
    }
    else if (!getCurrentContainers("dhcpd")){
        
        $estado = "<tr><td style='text-align:center;color:red'>". "DHCP NO ACTIVO". '</td> </tr>';
    }
    
    $respuesta = [$configuracion,$estado];
    
    exit(json_encode($respuesta));
    
}



if (isset($_POST['pedirRedes'])){
    
    $arrayenPHP = [];
    $redesServidor= getCurrentNetworks("ipdec");
    $mascaras = getCurrentNetworks("mask");
    $contador = 0;
    foreach ($redesServidor as $value){
        
        $arrayenPHP[] = [$value,$mascaras[$contador]];
        $contador++;
    }
    
    exit(json_encode($arrayenPHP));
    
}

if (isset($_POST['iniciar'])){
    
//    docker run -it --rm --init --net host -v "$(pwd)/datos":/data networkboot/dhcpd


$containerInstance = DockerContainer::create("networkboot/dhcpd","dhcpd")
//    ->daemonize()
//    ->stopOnDestruct()
//    ->doNotCleanUpAfterExit()
    ->network("host")
    ->volume("$(pwd)/datos","/data")
//    ->mapPort("80", "80")
    ->start();
exit($containerInstance->getDockerIdentifier());
}

if (isset($_POST['detener'])){

$container = new DockerContainer("networkboot/dhcpd","dhcpd");
$containerInstance = new DockerContainerInstance($container,$_POST["id"],"dhcpd");
$containerInstance->stop();

exit($containerInstance->getDockerIdentifier());

//$mensaje = $containerInstance->getDockerIdentifier();

}


if (isset($_POST['nuevoDHCP'])){
    
//    if (filter_var($_POST['red'],FILTER_VALIDATE_IP)){
    
        $fileConfig=fopen("./datos/dhcpd.conf", "w");
            
            fwrite($fileConfig, "#Red ".$_POST['red']."\n");
            fwrite($fileConfig, "#Máscara ".$_POST['mask'] ."\n");
            fwrite($fileConfig, "#Ip de Inicio ".$_POST['inicio'] ."\n");
            fwrite($fileConfig, "#Ip Final ".$_POST['fin']. "\n");
            fwrite($fileConfig, "#Gateway ".$_POST['gateway']. "\n");
            fwrite($fileConfig, "#Servidor DNS ".$_POST['dns']. "\n");
            
            fwrite($fileConfig, 'subnet '. $_POST['red'] .' netmask '. $_POST['mascara'] . " {" ."\n");
            fwrite($fileConfig, 'option routers '. $_POST['gateway'] .';'."\n");
            fwrite($fileConfig, 'option domain-name-servers ' . $_POST['dns'] . ';'."\n");
            fwrite($fileConfig, 'range ' . $_POST['inicio'] . ' '. $_POST['fin'] . ';'."\n");
            fwrite($fileConfig, '}');
        fclose($fileConfig);
        
        if(file_exists('./datos/dhcpd.conf')){
            
            exit("Fichero de configuración creado con éxito");
        }
        else{
            exit("Error en la creación del fichero de configuración, revise los permisos");
        }
        
        
        
//    }
//    else {
//        
//        echo "<p> Esa no es una Dirección Ip válida </p>";
//        
//    }
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

        <div class ="todo">

        <div class="formulario">
            <form method="POST" onsubmit="return validarDHCP(this);">

                <h3>Datos del Nuevo DHCP</h3>

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
                <br>

                Gateway<br>
                <input type="number" class="ip" min="0" max="255" name="gate1"/>
                <input type="number" class="ip" min="0" max="255" name="gate2"/>
                <input type="number" class="ip" min="0" max="255" name="gate3"/>
                <input type="number" class="ip" min="0" max="255" name="gate4"/>
                <br>

                DNS<br>
                <input type="number" class="ip" min="1" max="255" name="dns1"/>
                <input type="number" class="ip" min="0" max="255" name="dns2"/>
                <input type="number" class="ip" min="0" max="255" name="dns3"/>
                <input type="number" class="ip" min="0" max="255" name="dns4"/>
                <br><br>

                <input type="submit" name="startDHCP" value="Crear nuevo DHCP"/>
            </form>
        </div>
<!--
        <div class="controles" >
        <button onclick="arrancar(this)"; >Arrancar</button>
        <button onclick="detener(this)"; >detener</button>
        <button onclick="pedirContainers()">Pedir Lista Contenedores</button>
        <button onclick="pedirRedes()">Pedir Lista Redes</button>
        </div>-->
        
        <div class="datos">
        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        <h3>
                            Lista de Redes para <br>habilitar DHCP
                        </h3>
                    </th>
                </tr>
                
                <tr>
                    <th>
                        Red
                    </th>
                    <th>
                        Máscara
                    </th>
                </tr>
            </thead>
            <tbody id="networklist">
                <?php
                    $redesServidor= getCurrentNetworks("ipdec");
                    $mascaras = getCurrentNetworks("mask");
                    $contador = 0;
                    foreach ($redesServidor as $value){
                        
                        
                        echo '<tr>';
                        echo "<td>". $value. '</td>';
                        echo "<td>". $mascaras[$contador]. '</td>';
                        echo '</tr>';
                        $contador++;
                                                
                    }
                
                ?>
            </tbody>
        </table>
            
        <button onclick="pedirRedes()">Actualizar Redes</button>
            
        </div>

         <div class="controles">
        <table>
            <thead>
                <tr>
                    <th>
                        <h3>
                            Configuración DHCP Actual
                        </h3>
                    </th>
                </tr>
                
<!--                <tr>
                    <th>
                        Red
                    </th>
                    <th>
                        Máscara
                    </th>
                </tr>-->
            </thead>
            <tbody id="dhcpdata">
                <?php
                    $datosDhcp = checkDhcpConf();
                    $containerArrancado = getCurrentContainers("dhcpd");
                    foreach ($datosDhcp as $value){
                                                
                        echo '<tr>';
                        echo "<td>". $value. '</td>';
                        echo '</tr>';
                                                
                    }
                    if($containerArrancado){
                        
                        echo '<tr>';
                        echo "<td style='text-align:center;color:green'>". "DHCP ACTIVO". '</td>';
                        echo '</tr>';
                    }
                    else if (!$containerArrancado){
                        
                        echo '<tr>';
                        echo "<td style='text-align:center;color:red'>". "DHCP NO ACTIVO". '</td>';
                        echo '</tr>';
                    }
                
                ?>
            </tbody>
        </table>
            
        <button onclick="pedirConfiguracion()">Comprobar </button>
        <button onclick="arrancar(this)"; >Arrancar</button>
        <button onclick="detener(this)"; >Detener</button>
            
        </div>

        </div>
        
        <?php
////                var_dump($datosDHCP);
//            foreach ($datosDHCP as $value){
//                
//                echo  $value . "<br>";
//                }
//        
//        ?>
        
    </body>
</html>