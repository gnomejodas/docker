/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

            
