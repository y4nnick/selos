<?php

class ValidationException extends Exception{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class MethodException extends Exception{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class Val {


    /**
     * Ladet ein Bean, falls <code>check</code> true ist wird das Erfolgreiche laden des Beans überprüft
     * @param $beantype Beantype
     * @param $id BeanID
     * @param bool $check Wenn true wird das geladene Bean auf null überprüft
     * @return RedBean_OODBBean Geladenes Bean
     * @throws Exception falls check true ist und Bean nicht geladen werden konnte
     */
    public static function loadBean($beantype,$id,$check = true){

        $bean = R::load($beantype,$id);

        if($check)Val::assert($bean == null || $bean-> id == 0,"Das '$beantype'-Objekt mit der id: $id konnte nicht geladen werden");

        return $bean;
    }

    /**
     * Stellt sicher das keine error vorhanden ist.
     * Falls doch wird eine Exception mit der $msg geworfen
     * @param $error Der Sicherzustellende Error, "true" oder "false"
     * @param $msg Die Excpetion-Nachricht im Fehlerfall
     * @throws Exception
     */
    public static function assert($error,$msg){
        if($error){
            throw new ValidationException($msg);
        }
    }

    /**
     * Überprüft ob der Rest-Aufruf die angegebene Rest-Methode verwenden
     * @param $service Rest-Service
     * @param $methode Rest-Methode
     * @throws Exception falls die übergebenen Methode nicht mit jener des Rest-Aufrufs übereinstimmt
     */
    public static function checkMethod($service,$methode){
        if($service->get_request_method() != $methode){
            throw new MethodException("Nur $methode erlaubt");
        }
    }

    /**
     * Liefert den Bool-Wert eines Rest-Parameters
     * @param $service Das Rest-Service
     * @param $param Der zu überprüfende Parameter
     * @return bool true wenn der Request den angegebenen Parameter enthält und dieser einer der folgenden Werte annimmt:1,"1","true"
     */
    public static function getBool($service,$param){
        $val = Val::getValue($service,$param);
        return(!empty($val) && ($val == 1 || $val == "1" || $val == "true"));
    }

    /**
     * Liefert der Wert eines Rest-Parameters
     * @param $service Das Rest-Service
     * @param $param Der Parameter
     * @return mixed Der Wert des Parameters
     */
    public static function getValue($service,$param){
        return @$service->_request[$param];
    }
}