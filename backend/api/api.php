<?php

$RootFolderName = "selos/backend";

require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/core/core.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/core/class/Bewerb.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/core/class/Gruppe.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/core/class/Team.php");
require_once("rest.php");
require_once("Valider.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/php/lib/swift_required.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$RootFolderName/php/google/vendor/autoload.php");

abstract class GENERIC_API extends GENERIC_REST
{
    //An empty array implies that for all methods admins rights are needed
    protected $PERMISSIONS = array("");
    private $user;

    public function __construct(){
        parent::__construct();
        $this->processApi();
    }

    /*
     * Method Hooks
     */
    public function beforeCreate($entity){}
    public function afterCreate($entity){}
    public function beforeGet(){}
    public function afterGet($data){}
    public function afterGetList($data){}
    public function beforeUpdate($entity){}
    public function afterUpdate($entity){}
    public function beforeDelete($data){}
    public function afterDelete(){}

    /**
     * Verarbeitet einen Rest-Service Aufruf indem die Methode des Services aufgerufen wird
     */
    public function processApi() {

        $request = @$_REQUEST['request'];
        $requestArray = explode("/",$request);

        $func = strtolower(trim(@$requestArray[0]));
        $objectMethod = strtolower(trim(@$requestArray[1]));

        $id = null;
        if(is_numeric($func)){
            $id = $func;
        }

        //Load and set User
        $this->setUser();

        switch ($this->get_request_method()) {

            case "GET":

                if (empty($func)) {

                    if($this->checkPermissions(Method::GET_LIST)){
                        $this->getList();
                    }

                } else if(is_numeric($func)) {

                    if($this->checkPermissions(Method::GET_OBJECT)){
                        $this->get($func);
                    }

                }else if((int)method_exists($this,$func) > 0) {

                    if($this->checkPermissions($func)) {
                        $this->handleListMethodCall($func);
                    }

                }else{
                    $this->makeResponse(null,"Die Methode '$func' konnte nicht gefunden werden",null,1,400);
                }

                return;

            case "POST":

                //Call object method
                if($id != null && !empty($objectMethod)){

                    if($this->checkPermissions($objectMethod)) {
                        $this->handleObjectMethodCall($id,$objectMethod);
                    }

                }else if(empty($func) && empty($objectMethod)){  //Create new entity

                    if($this->checkPermissions(Method::CREATE_OBJECT)) {
                        $this->create();
                    }

                }else if((int)method_exists($this,$func) > 0) {

                    if($this->checkPermissions($func)) {
                        $this->handleListMethodCall($func);
                    }

                }
                else{
                    $this->makeResponse(null,"Die Methode '$func' konnte nicht gefunden werden",null,1,400);
                }
                return;

            case "PUT":

                if(!empty($func) && is_numeric($func)) {

                    if($this->checkPermissions(Method::UPDATE_OBJECT)) {
                        $this->update();
                    }

                }else{
                    $this->makeResponse(null, "PUT auf diese URL nicht erlaubt", null, true, 405);
                }

                return;

            case "DELETE":
                if (!empty($func) && is_numeric($func)) {

                    if($this->checkPermissions(Method::DELETE_OBJECT)) {
                        $this->delete($func);
                    }

                }else{
                    $this->makeResponse(null, "DELETE auf diese URL nicht erlaubt", null, true, 405);
                }

                return;

            default:
                $this->makeResponse(null, $this->get_request_method()." auf diese URL nicht erlaubt", null, true, 405);
                return;
        }
    }

    /**
     * Delivers the loaded entity with the given id
     * @param $id the entity id
     */
    public function get($id){
        try{
            $this->beforeGet();

            $data = R::load($this->TABLE_NAME,$id);
            if($data->id == 0){
                throw new Exception($this->TABLE_NAME." mit id $id konnte nicht geladen werden");
            }

            $this->afterGet($data);
            $this->returnObject($data);
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

    /**
     * Delets the entity with the given id
     * @param $id the entity id
     */
    public function delete($id){
        try{

            $data = R::load($this->TABLE_NAME,$id);
            if($data->id == 0){
                throw new Exception($this->TABLE_NAME." mit id $id konnte nicht geladen werden");
            }

            $this->beforeDelete($data);
            R::trash($data);
            $this->afterDelete();

            $this->makeResponse($data,$this->TABLE_NAME." wurde erfolgreich gelöscht");
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

    /**
     * Updates the given entity, the id of the entity must be set
     * @param $entity the entity
     */
    public function update(){
        try{
            $entity = Val::getPayload($this,$this->TABLE_NAME);

            if(is_string($entity)){
                $entity = json_decode($entity,true);
            }

            $entity = ((array) $entity);

            $entity =  R::dispense($entity);
            if($entity->id == 0){
                throw new Exception("Speichern einer entity ist so nicht möglich");
            }

            $this->beforeUpdate($entity);
            R::store($entity);
            $this->afterUpdate($entity);

            $this->returnObject($entity,200);
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

    /**
     * Creates the given entity, the id of the entity must be 0
     * @param $entity the entity
     */
    public function create(){
        try{
            $entity = Val::getPayload($this,$this->TABLE_NAME);

            if(is_string($entity)){
                $entity = json_decode($entity,true);
            }

            $entity = (array) $entity;
            $entity['_type'] = $this->TABLE_NAME;
            $entity = R::dispense($entity);

            if($entity->id != 0){
                throw new Exception("Updaten einer entity ist so nicht möglich");
            }

            $this->beforeCreate($entity);
            R::store($entity);
            $this->afterCreate($entity);

            $this->returnObject($entity,201);
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }

    /**
     * Trys to make a method call to the given entity
     * @param $id the id of the entity
     * @param $method the method which should be called
     */
    public function handleObjectMethodCall($id,$method){

        //Load and check Bean
        $entity = Val::loadBean($this->TABLE_NAME,$id,false);
        if($entity->id == 0){
            $this->makeResponse(null,$this->TABLE_NAME." mit id $id konnte nicht geladen werden",null,true,405);
            return;
        }

        //Call method if it exists
        if((int) method_exists($entity->box(),$method) > 0){
            try{
                $entity->$method($this);
                $this->returnObject($entity,201);
            }catch(Exception $e){
                $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
            }
        }else{
            $this->makeResponse(null,"The method '$method' for the entity '$this->TABLE_NAME' could not be found",null,true,405);
        }
    }

    public function handleListMethodCall($method){
        //Call method if it exists
        if((int)method_exists($this,$method) > 0) {
            try{
                $data = $this->$method();
                $this->returnCustomObject($data,201);
            }catch(Exception $e){
                $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
            }
        }else{
            $this->makeResponse(null,"The method '$method' for the entity '$this->TABLE_NAME' could not be found",null,true,405);
        }
    }

    /**
     * Delivers a list of all entities
     */
    public function getList(){
        try{
            $data = R::findAll($this->TABLE_NAME);

            $this->afterGetList($data);

            $this->returnList($data);
        }catch (Exception $e){
            $this->makeResponse(null,$e->getMessage(),null,$e->getTraceAsString());
        }
    }


    //
    // Authorization Stuff
    //

    private function setUser(){
        $token = $this->getAuthToken();

        //Wurde kein Token gefunden ist der Zugriff nicht erlaubt
        if($token == null){
            $this->user = null;
        }

        //User auslesen
        $this->user = $this->getUserFromToken($token);
    }

    private function checkPermissions($method){
        if($this->isAllowed($method)){
            return true;
        }else{
            $this->makeResponse(null,"You don't have permission for this operation",null,$method." not allowed",401);
            return false;
        }
    }

    /**
     * Returns true if the method is allowed
     * @param $method the method to check
     * @return bool true if the method is allowed for the given user from the token
     */
    private function isAllowed($method){

        //Rb mode
        return true;

        $test = false;

        //Auslesen der minimalen Role welche vorhanden sein muss. Wird diese nicht in den PERMISSIONS festgelegt ist sie ADMIN
        $minimumRole = Role::ADMIN;
        if(array_key_exists($method,$this->PERMISSIONS)){
            $minimumRole = $this->PERMISSIONS[$method];
        }

        //Ist die minimale Role UNREGISTERED darf jeder zugreifen
        if($minimumRole == Role::UNREGISTERED){
            return true;
        }

        //
        // Ab hier ist ein User umbedingt erforderlich
        // d.h. wird kein token oder kein user zum token gefunden wird der Zugriff verweigert
        //

        //Authorization Token auslesen
        $token = $this->getAuthToken();

        //Wurde kein Token gefunden ist der Zugriff nicht erlaubt
        if($token == null){
            if($test)echo "Token == null";
            return false;
        }

        //User auslesen
        $user = $this->getUserFromToken($token);

        //Wurde kein User zum token gefunden ist der Zugriff nicht erlaubt
        if($user == null){
            if($test)echo "user == null";
            return false;
        }

        //Auslesen User Rolle
        $userRole = $user->rights;

        //Ist nun die User Rolle kleiner als die minimale Benötigte Rolle wird der Zugriff verweigert
        if($userRole < $minimumRole){
            if($test)echo "userRole < minimumRole => $userRole < $minimumRole";
            return false;
        }

        //Hier ist die UserRolle größer oder gleich der minimal benötigten Rolle
        return true;
    }

    /**
     * Returns the Authorization Token from the Authorization Header or null if not present
     * @return token or null if not present
     */
    private function getAuthToken(){
        $headers = apache_request_headers();

        if(!isset($headers['Authorization'])) {
            return null;
        }

        return $headers['Authorization'];
    }

    /**
     * Returns the user with the given token or null if the user could not be found
     * @param $token the token which must match with the stored token
     * @return the user or null if not found
     */
    private function getUserFromToken($token){
        $user = R::findOne("user","token = ?",array($token));
        return $user;
    }
}

abstract class Method
{
    const GET_LIST = "getlist";
    const GET_OBJECT = "getobject";
    const POST_LIST = "postlist";
    const CREATE_OBJECT = "createobject";
    const UPDATE_OBJECT = "updateobject";
    const DELETE_OBJECT = "putobject";
}

abstract class Role
{
    //Umso höher umso mehr Rechte
    const UNREGISTERED = 0;
    const USER = 1;
    const ADMIN = 2;

    public static function getRoleFromString($str){
        switch($str){
            case "admin":   return Role::ADMIN;
            case "user":    return Role::USER;
            default:        return Role::UNREGISTERED;
        }
    }

    public static function getStringFromRole($role){
        switch($role){
            case Role::ADMIN:   return "admin";
            case Role::USER:    return "user";
            default:            return "unregistered";
        }
    }
}
