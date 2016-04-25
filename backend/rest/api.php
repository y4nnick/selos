<?php

error_reporting(E_ALL);     // Entwicklung

//TODO organzie imports
require_once("rest.inc.php");
require_once("funcs.php");
require_once("Valider.php");

require_once("LogService/LogService.php");
require_once("LogService/LogServiceImpl.php");

$RootFolderName = "selos/backend";

require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/php/lib/swift_required.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/core/core.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/php/google/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/php/redbean/Preloader.php");

class API extends REST
{
    public $data = "";

    public function __construct(){
        parent::__construct();
        $this->processApi();
    }

    /**
     * Verarbeitet einen Rest-Service Aufruf indem die Methode des Services aufgerufen wird
     */
    public function processApi(){
        $func = strtolower(trim(str_replace("/","",$_REQUEST['request'])));

        if((int)method_exists($this,$func) > 0)
            $this->$func();
        else
            $this->makeResponse(null,"Die Methode '$func' konnte nicht gefunden werden",null,1);
    }

    /**
     * @param $data Umzuwandelnde Daten
     * @return string Daten in JSON-Format
     */
    protected  function json($data)
    {
        if(is_array($data)){
            return json_encode($data);
        }
        return $data;
    }

    /**
     * Returniert die übergebenen Daten
     * @param $data Die zu übergebenden Daten
     * @param $msg Die Response Nachricht
     * @param null $error Den opionalen Fehler
     */
    public function makeResponse($data,$msg,$load = array(),$error = null){

        if($error == null){
            $response = array("message"=>$msg,"success"=>true);

            if($data != null)
                $response["data"] = R::exportAll($data,false,$this->generateLoadArray($load));

            $this->response($this->json($response),200);
        }
        else{
            $response = array("message"=>$msg,"success"=>false,"error"=>$error);
            $this->response($this->json($response),400);
        }
    }

    public function returnObject($data){
        $this->response($this->json($data,false),200);
    }

    public function returnList($data){
        $this->response($this->json(R::exportAll($data,false,$this->generateLoadArray($data))));
    }

    /**
     * Generiert aus einem assozativen Array ein LoadArray für R::exportAll.
     * @param $load Als Key muss der Parameter des Rest-Services angegebene werden,
     *              ist dieser Parameter gesetzt und true, so wird der String oder das String-Array zu dem Load-Array hinzugefügt
     * @return array
     */
    private function generateLoadArray($load){
        $array = array("");

        if($load != null){
            foreach ($load as $param => $entity) {
                if(is_array($entity)){
                    foreach($entity as $innerEntity){
                        if(Val::getBool($this,$param)){
                            $array[] = $innerEntity;
                        }
                    }
                }else{
                    if(Val::getBool($this,$param))
                        $array[] = $entity;
                }
            }
        }

        return $array;
    }
}
?>