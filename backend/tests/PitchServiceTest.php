<?php

class PitchServiceTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
        error_reporting(0);

        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['DOCUMENT_ROOT'] = '/Applications/XAMPP/xamppfiles/htdocs/';
        include_once("../core/core.php");
        include_once("../rest/TeamService/TeamServiceImpl.php");

        $this->PitchServiceImpl = new PitchServiceImpl();

        R::begin();
    }

    public function tearDown(){
        R::rollback();
    }

    public function  testShouldThrowValidationError(){

        $url = 'localhost/cmpBeta/backend/rest/PitchService/uploadPitch';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, null);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $json = json_decode($response, true);
        curl_close($ch);

        $this->assertEquals($json['success'], false);
    }

    /**
     * @expectedException ValidationException
     */
    public function testWithNullParameter(){
        $this->PitchServiceImpl->uploadPitch(null,null,null,null,null,null,null,null,null,null,null);
    }
}