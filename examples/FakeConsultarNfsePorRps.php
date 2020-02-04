<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeGinfes\Tools;
use NFePHP\NFSeGinfes\Common\Soap\SoapFake;
use NFePHP\NFSeGinfes\Common\FakePretty;
use NFePHP\NFSeGinfes\Common\Standardize;

try {

    $config = [
        'cnpj' => '02993595000159',
        'im' => '121782',
        'cmun' => '3547809',
        'razao' => 'Andriel Allison',
        'tpamb' => 2
    ];


    $configJson = json_encode($config);

    $content = file_get_contents('C:\Users\Cleiton\Downloads\ginfes\CERTIFICADO DIGITAL 2019 - THS.pfx');
    $password = '151610';
    $cert = Certificate::readPfx($content, $password);
    
    $soap = new SoapFake();
    $soap->disableCertValidation(true);
    
    $tools = new Tools($configJson, $cert);
    //$tools->loadSoapClass($soap);

    $numero = 123456;
    $serie = 1;
    $tipo = 1;

    $response = $tools->consultarNfsePorRps($numero, $serie, $tipo);

    //echo FakePretty::prettyPrint($response, '');
    //header("Content-type: text/plain");echo $response;

    $st = new Standardize();
    $std = $st->toStd($response);
    var_dump($std);
 
} catch (\Exception $e) {
    echo $e->getMessage();
}
