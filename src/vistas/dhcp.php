<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (isset($_POST['nuevo'])){
    
    if (filter_var($_POST['red'],FILTER_VALIDATE_IP)){
    
        $fileConfig=fopen("./data/dhcp/$nombre", "w");
            fwrite($fileConfig, '<?php'."\n");
            fwrite($fileConfig, '	$host="'.$_POST['servidorBD'].'";'."\n");
            fwrite($fileConfig, '	$user="'.$_POST['usuarioBD'].'";'."\n");
            fwrite($fileConfig, '	$passwordDB="'.$_POST['passwordBD'].'";'."\n");
            fwrite($fileConfig, '	$db="'.strtolower($_POST['database']).'";'."\n");
            fwrite($fileConfig, '	$port="'.$_POST['puertoBD'].'";'."\n");
            fwrite($fileConfig, '?>');
        fclose($fileConfig);
        
    }
    else {
        
        echo "<p> Esa no es una Dirección Ip válida </p>";
        
    }
}
?>

<form method="POST">

    <!-- Estos nos van a permitir conectarnos a la base de datos que
    vamos a emplear en la aplicación-->

    <h3>Datos del nuevo DHCP</h3>
        <label for="fname">First name:</label><br>
        <input type="text" id="fname" name="fname" value="John"><br>
        <label for="lname">Last name:</label><br>
        <input type="text" id="lname" name="lname" value="Doe"><br><br>
        <input type="submit" value="Submit">

</form>

        