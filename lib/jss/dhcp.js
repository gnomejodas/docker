/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var id = "";
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
                return dectobin(comp);
                
                
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
                
//                Si pasa todos los controles, permitimos que se envíe el formulario
                return true;
            }
            
            
