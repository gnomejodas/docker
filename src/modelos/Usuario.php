<?php
namespace Gnomejodas\Modelos;
/**
 * Description of Person
 *
 * @author leo
 */


//Esto es para que las dependencias no choquen entre sí si hay 
//varias clases iguales??

//namespace WebtrainingZone\Models;


class usuario {
    
    //Atributos (estado)
    protected $login;
    
    public function __construct($login) {
        $this->login = $login;
    }
    
}
