<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

header("Content-type: text/plain");

require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeGinfes\Common\Standardize;
use NFePHP\NFSeGinfes\Tools;

$config = [
   "atualizacao" => "2019-06-15 08:29:21",
   "tpAmb" => 2,
   "razaosocial" => "SOFTWARE & HARDWARE INFORMATICA - ME",
   "siglaUF" => "SP",
   "cnpj" => "11222333444455",
   "inscricaomunicipal" => "11223",
   "codigomunicipio" => "3518800",
   "schemes" => "Ginfes_V3",
   "versao" => "v03"
];

try {
	$content = file_get_contents( 'teste.pfx' ) ;

    $certificate = Certificate::readPfx(
        $content,
        '123456'
    );

	$xml = file_get_contents('1710.xml' , '' );

    $tools = new Tools(json_encode($config), $certificate);

    $xmlAssimado = $tools->signNFSe($xml);

    $resp = $tools->EnviaLoteNFSe($xmlAssimado, rand(1, 10000));

    $st = new Standardize();
    $std = $st->toStd($resp);

} catch (Exception $e) {
    echo $e->getMessage();
}
