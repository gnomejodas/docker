/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var id = "";
arrayRedes = [];
function arrancar(boton){

     $.post( "index.php?site=dhcp", 
                            { iniciar: "1" },
                            function( data ) {
                               //alert(data);
                               id =  data;
//                               alert(id);
                            }
                            );

}

function detener(boton){
//    alert(id);
     $.post( "index.php?site=dhcp", 
                            { detener: "1", id: id },
                            function( data ) {
//                            alert(data);

                            }
                            );



}

function pedirContainers(imagen ="todo"){
        $.post( "./src/controladores/docker/DockerStats.php", 
                            { pedirContenedores: imagen},
                            function( data ) {
                                
//                             alert(data);
//                            contLista = data.split("\n");

//                             alert(data);
                            
                                
                            tabla = document.getElementById("dhcplist");
                            tabla.innerHTML= "";
//                            contLista = data.split("\n");
                            
                            for (i =0; i< contLista.length ; i++){
                                
                                tabla.innerHTML += "<tr><td>" + contLista[i] + "</tr> </td>";
                            }

                            }
                            );

}

function pedirRedes(){
    
//            $.post( "./src/controladores/docker/DockerStats.php", 
//                            { pedirRedes: "1"},
//                            function( data ) {
//                                
//                             alert(data);
////                            contLista = data.split("\n");
//
//                            }
//                            );

        $.post( "index.php?site=dhcp", 
                            { pedirRedes: "1"},
                            function( data ) {
                            var redes = JSON.parse(data);
//                            redes.pop();
                            var tabla = document.getElementById("networklist");
                            arrayredes= [];
                            tabla.innerHTML= "";
//                            contLista = data.split("\n");
                            
                            for (i =0; i< redes.length ; i++){
//                                alert(redes.length);
                                var ipRed = redes[i].split("/", 1);
                                var n = redes[i].search("/") +1 ;
                                var mascRed = redes[i].substr(n);
//                                var redDec = cualesmireddec(redes[i],"decimal");
                                                                                                
                                    tabla.innerHTML += "<tr><td>" + ipRed + "</td> <td>"+ mascRed + " </td></tr>";
                            }

                            }
                            );
    
    
}



//            Todas las funciones van a aceptar solamente valores en binario salvo la última

function dectobin(dec){

    return (dec >>> 0).toString(2).padStart(8,0);

}
function bintodec(bin){

    return parseInt(bin, 2).toString(10);

}
function mask(mask){

    var mascara= ""

    for (i=0;i<mask;i++){

        mascara += "1";
    }

    return mascara.padEnd(32,0);

}
function cualesmired(ip, mascara){

    var ipdec = bintodec(ip);
    var mascdec = bintodec(mascara);
    var comp = ipdec & mascdec;
    return dectobin(comp).padStart(32,0);


}
//Igual que cualesmired pero admite la red en formato puntuado y con la barra:
// Ejemplo: 192.168.1.10/24
function cualesmireddec(ipreducida, tipo ="binario"){
    

    
    var ippuntuada = ipreducida.split("/", 1);
    
    var n = ipreducida.search("/") +1 ;
    
    var arrayip = ippuntuada.toString().split(".");
     
    var mascarareducida = ipreducida.substr(n);
    
    var mascarabinario = mask(mascarareducida);
    
    var ip1 = dectobin(arrayip[0]).toString();
    var ip2 = dectobin(arrayip[1]).toString();
    var ip3 = dectobin(arrayip[2]).toString();
    var ip4 = dectobin(arrayip[3]).toString();
    var ipbin = ip1 + ip2 + ip3 + ip4;
    var redBin = cualesmired(ipbin,mascarabinario);
    if (tipo=="binario"){
    
        return redBin;
    }
    else if (tipo=="decimal"){
        
        var red1= bintodec(redBin.substr(0,8));
        var red2= bintodec(redBin.substr(8,8));
        var red3= bintodec(redBin.substr(16,8));
        var red4= bintodec(redBin.substr(24,8));
        var redDec = red1 + "." + red2 + "." + red3 + "." + red4;
        return redDec;
    }
        
}

//            Devuelve la porción de host de una dirección IP, se le introduce
//            la máscara en forma reducida  1-24
function miporciondehost(ip,reducida){

    return ip.substr(reducida, 31);

}
//            Esta devuelve la porción de red en binario
function miporciondered(ip, reducida){

    return ip.substr(0,reducida-1);
}
//            Esta devuelve la dirección de broadcast en binario
function mibroadcast(ip,reducida){

    return miporciondered(ip,reducida).padEnd(32,1);

}


      

function validarDHCP(formulario){

//               Convertimos todos los campos del formulario a Binario
    var red =  (dectobin(formulario.net1.value)).toString() + 
            (dectobin(formulario.net2.value)).toString() +
            (dectobin(formulario.net3.value)).toString() +
            (dectobin(formulario.net4.value)).toString();


    var mascara = mask(formulario.m1.value);
    var ipstart = (dectobin(formulario.n1.value)).toString() + 
            (dectobin(formulario.n2.value)).toString() +
            (dectobin(formulario.n3.value)).toString() +
            (dectobin(formulario.n4.value)).toString();
    var ipend =  (dectobin(formulario.f1.value)).toString() + 
            (dectobin(formulario.f2.value)).toString() +
            (dectobin(formulario.f3.value)).toString() +
            (dectobin(formulario.f4.value)).toString();

    var gate = (dectobin(formulario.gate1.value)).toString() + 
            (dectobin(formulario.gate2.value)).toString() +
            (dectobin(formulario.gate3.value)).toString() +
            (dectobin(formulario.gate4.value)).toString();

//                Reducida es la versión reducida de la máscara de subred en decimal
    var reducida = formulario.m1.value;


//                Ahora vamos a comprobar si la red es una dirección de red
//                Si la dirección de red con su máscara nos devuelve la red, tod ok
    if (cualesmired(red,mascara) != red){

        alert("La dirección " + formulario.net1.value + "." +
                formulario.net2.value + "." +
                formulario.net3.value + "." +
                formulario.net4.value + "/" +
                formulario.m1.value + " no es una dirección de red");
        return false;
    }
//                En este paso comprobamos si la primera dirección corresponde a la red y que no sean iguales
    else if (cualesmired(ipstart,mascara) != red | ipstart == red | ipstart == mibroadcast(ipstart,reducida)){
        alert("La IP de inicio debe pertenecer a la red indicada, no ser la dirección de red y no ser la de broadcast")
        return false;
    }
//                Aquí comprobamos lo mismo para la segunda dirección
    else if (cualesmired(ipend,mascara) != red | ipend == red | ipend == mibroadcast(ipend,reducida)){
        alert("La IP de FIN debe pertenecer a la red indicada, no ser la dirección de red y no ser la de broadcast")
        return false;
    }
//                Aquí comprobamos si la primera dirección es anterior a la segunda
    else if (bintodec(miporciondehost(ipstart,reducida)) >= bintodec(miporciondehost(ipend,reducida))){

        alert("la primera dirección de cesión debe ser menor a la segunda");

        return false;
    }
//                Aqui comprobamos que el gateway pertenezca a la misma red, que no sea la red

    else if (cualesmired(gate, mascara) != red | gate == red ){

        alert("El gateway debe tener una dirección de host dentro de la subred");

        return false;

    }

    else{
//                Si pasa todos los controles, permitimos que se envíe el formulario
//                
//                formulario.style.visibility = "hidden";
    var redDHCP = formulario.net1.value + "." +
                formulario.net2.value + "." +
                formulario.net3.value + "." +
                formulario.net4.value;

    var mascaraDHCP = bintodec(mascara.substr(0,8)) + "." +
                    bintodec(mascara.substr(8,8)) + "." +
                    bintodec(mascara.substr(16,8)) + "." +
                    bintodec(mascara.substr(24,8));

    var inicioDHCP = formulario.n1.value + "." +
                formulario.n2.value + "." +
                formulario.n3.value + "." +
                formulario.n4.value;

    var finDHCP = formulario.f1.value + "." +
                formulario.f2.value + "." +
                formulario.f3.value + "." +
                formulario.f4.value;

    var gateDHCP = formulario.gate1.value + "." +
                formulario.gate2.value + "." +
                formulario.gate3.value + "." +
                formulario.gate4.value;

    var dnsDHCP = formulario.dns1.value + "." +
                formulario.dns2.value + "." +
                formulario.dns3.value + "." +
                formulario.dns4.value;



//                alert(dnsDHCP);

         $.post( "./index.php?site=dhcp", 
                { nuevoDHCP: "1", red: redDHCP, mascara: mascaraDHCP, inicio: inicioDHCP,
                fin: finDHCP, gateway: gateDHCP, dns: dnsDHCP},
                function( data ) {
                   //alert(data);
                 alert(data);
                }
                );
    return false;
    }
}


            
            
