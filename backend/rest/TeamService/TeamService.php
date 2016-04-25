<?php

if($_SERVER['SERVER_NAME'] == 'localhost'){
    $RootFolderName = "rb/backend";
    require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/rest/api.php");
}else{
    $RootFolderName = "backend";
    require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/rest/api.php");
}

class TeamService extends API
{
    private $PitchServiceImpl;
    function __construct(){
        require_once("TeamServiceImpl.php");
        $this->TeamServiceImpl = new TeamServiceImpl();
        parent::__construct();
    }

    public function update(){
        try{
            Val::checkMethod($this,"POST");

            $team = json_decode(file_get_contents('php://input'),true);
            Val::assert(empty($team),"Kein Team übergeben");

            //Convert to bean
            $team['_type'] = "team";
            $team = R::dispense($team);

            $team = $this->TeamServiceImpl->update($team);


            $this->returnObject($team);
           // $this->makeResponse(null,"Team wurde erfolgreich gespeichert");
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

    public function query(){
        try{
            Val::checkMethod($this,"GET");

            $data = $this->TeamServiceImpl->query();

            $this->returnList($data);
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

    public function getAnwesenheiten(){
        try{
            Val::checkMethod($this,"GET");

            $data = $this->TeamServiceImpl->getAnwesenheiten();

            $this->response($this->json($data),200);
           // $this->makeResponse($data,"Anwesenheiten wurden erfolgreich geladen");
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

    public function getLogs(){
        Val::checkMethod($this, "GET");

        try{
            $pitchID =  @$this ->_request["pitchID"];
            Val::assert(empty($pitchID),"Der Parameter 'pitchID' wurde nicht angegeben");

            $data = $this->PitchServiceImpl->getLogs($pitchID);
            $this->makeResponse($data,"Logs erfolgreich gealden");
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

}
$TeamService = new TeamService();
?>