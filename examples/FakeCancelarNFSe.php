<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeGinfes\Common\FakePretty;
use NFePHP\NFSeGinfes\Common\Soap\SoapFake;
use NFePHP\NFSeGinfes\Tools;

try {

    $config = [
        'cnpj' => '99999999000191',
        'im' => '1733160024',
        'cmun' => '2408102',
        'razao' => 'Empresa Test Ltda',
        'tpamb' => 2
    ];

    $configJson = json_encode($config);

    $content = file_get_contents('expired_certificate.pfx');
    $password = 'associacao';
    $cert = Certificate::readPfx($content, $password);

    $soap = new SoapFake();
    $soap->disableCertValidation(true);

    $tools = new Tools($configJson, $cert);
    $tools->loadSoapClass($soap);

    $id = 'C201800000000001';
    $numero = '1691';
    $versao = "3"; // versao 2 funciona em algumas cidades e a 3 em outras

    $response = $tools->cancelarNfse($numero, $tools::ERRO_EMISSAO, $id, $versao);

    echo FakePretty::prettyPrint($response, '');

} catch (\Exception $e) {
    echo $e->getMessage();
}