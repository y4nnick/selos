<?php

class GENERIC_REST {

    public $json_content_type = "application/json";
    public $_request = array();

    public function __construct(){
        $this->inputs();
    }

    /**
     * Makes the response to the browser
     * @param $data the data to display
     * @param $status the http-status-code
     */
    private function response($data,$status){
        $code = ($status)?$status:200;
        $this->set_headers($code);
        echo $data;
    }

    /**
     * Delivers the status message according the http-status code
     * @param $code the http-status code
     * @return mixed
     */
    private function get_status_message($code){
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');
        return ($status[$code])?$status[$code]:$status[500];
    }

    /**
     * Transform the given data to transform
     * @param $data Umzuwandelnde Daten
     * @return string Daten in JSON-Format
     */
    private function json($data)
    {
        if(is_array($data)){
            return json_encode($data);
        }else{
            return $data;
        }
    }

    /**
     * Delivers the request method
     * @return mixed the request method
     */
    public function get_request_method(){
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Transforms the recieved data according the request-Method
     */
    private function inputs()
    {
        switch($this->get_request_method()){
            case "GET":
                $this->_request = $this->sanitize($_GET);
                break;
            case "DELETE":
                $this->_request = $this->sanitize($_GET);
                break;
            case "POST":

                if($filecontent = file_get_contents("php://input") !== false){
                //if(!(file_get_contents("php://input"))){
                    $this->_request = $this->sanitize(file_get_contents("php://input"));
                }else{
                    if(!empty($_POST["data"])){
                        $_POST = $_POST["data"];
                    }

                    $this->_request = json_decode($this->sanitize($_POST));
                }

                break;

            case "PUT":

              //  print_r(file_get_contents("php://input"));

                $fileContent = file_get_contents("php://input");

                //$this->_request = $this->sanitize(file_get_contents("php://input"));
               // if($filecontent = file_get_contents("php://input") !== false){
                if(strlen($fileContent) != 0){
                    $this->_request = $this->sanitize($fileContent);
                }else{

                    if(!empty($_POST["data"])){
                        $_POST = $_POST["data"];
                    }

                    $this->_request = json_decode($this->sanitize($_POST));
                }


                break;
            default:
                $this->response('',406);
                break;
        }
    }

    /**
     * Sets the header for content-type and status-code
     * @param $code the http-status code
     */
    private function set_headers($code){
        header("HTTP/1.1 ".$code." ".$this->get_status_message($code));
        header("Content-Type:".$this->json_content_type);
    }

    /**
     * Returniert die übergebenen Daten
     * @param $data Die zu übergebenden Daten
     * @param $msg Die Response Nachricht
     * @param null $error Den opionalen Fehler
     */
    protected function makeResponse($data,$msg,$load = array(),$error = null,$errorCode = 400){

        if($error == null){
            $response = array("message"=>$msg,"success"=>true);

            if($data != null){
                $response["data"] = R::exportAll($data,false,$this->generateLoadArray($load));
            }
            $this->response($this->json($response,false),200);
        }
        else{
            $response = array("message"=>$msg,"success"=>false,"error"=>$error);
            $this->response($this->json($response),$errorCode);
        }
    }

    /**
     * Builds and makes a response with the given object and HTTP-Code
     * @param $data the object to return
     * @param int $code the http status code (default=200)
     */
    protected function returnObject($data,$code = 200){
        $data = R::exportAll($data,true)[0];
        $this->response($this->json($data,false),$code);
    }

    /**
     * Builds and make a response with the given array of objects
     * @param $data the objects array
     * @param int $code the http status code (default=200)
     */
    protected function returnList($data,$code = 200){
        $this->response($this->json(R::exportAll($data,false,$this->generateLoadArray($data))),$code);
    }

    protected function returnCustomObject($data,$code = 200){
        $this->response($this->json($data),$code);
    }

    /**
     * Generiert aus einem assozativen Array ein LoadArray für R::exportAll.
     * @param $load Als Key muss der Parameter des Rest-Services angegebene werden,
     *              ist dieser Parameter gesetzt und true, so wird der String oder das String-Array zu dem Load-Array hinzugefügt
     * @return array
     */
    private function generateLoadArray($load){

        $array = array("");

        if($load != null && is_array($load)){
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

    /**
     * Deletes javascript, HTML- and style-Tags and multiline Comments
     * @param $input the input
     * @return mixed the cleaned output
     */
    private function cleanInput($input) {

        $search = array(
            '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
        );

        $output = preg_replace($search, '', $input);
        return $output;
    }

    /**
     * Transforms the $inpuf into clean output
     * @param $input the input
     * @return null|string the cleaned output
     */
    public function sanitize($input) {
        $output = null;
        if (is_array($input)) {
            foreach($input as $var=>$val) {
                $output[$var] = $this->sanitize($val);
            }
        }
        else {
            $input  = $this->cleanInput($input);
            $input = strip_tags($input);
            $output = $input;
        }
        return $output;
    }
}