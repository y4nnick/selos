<?php

require_once($_SERVER['DOCUMENT_ROOT']."/selos/backend/rest/api.php");

class ImportService extends API
{
    function __construct(){
        require_once("ImportServiceImpl.php");
        $this->TeamServiceImpl = new ImportServiceImpl();
        parent::__construct();
    }

    public function import(){
        try{
            Val::checkMethod($this,"GET");
            $data = $this->TeamServiceImpl->import(Val::getValue($this,"turnierID"));
            $this->makeResponse($data,"Daten wurden erfolgreich importiert");
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

    public function importg(){
        try{
            Val::checkMethod($this,"GET");
            $data = $this->TeamServiceImpl->importg(Val::getValue($this,"turnierID"));
            $this->makeResponse($data,"Daten wurden erfolgreich importiert");
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }
}
$ImportService = new ImportService();
?>