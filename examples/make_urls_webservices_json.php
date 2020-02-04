<?php


$urls = [
    '4314902' => ['municipio' => 'Porto Alegre', 'uf' => 'RS', 'homologacao' => 'http://nfse-hom.procempa.com.br/nfe-ws', 'producao' => 'http://nfe.portoalegre.rs.gov.br/nfe-ws', 'version' => '1.00', 'soapns' => 'http://ws.bhiss.pbh.gov.br'],
    '3106200' => ['municipio' => 'Belo Horizonte', 'uf' => 'MG', 'homologacao' => 'https://bhisshomologa.pbh.gov.br/bhiss-ws/nfse', 'producao' => 'https://bhissdigital.pbh.gov.br/bhiss-ws/nfse', 'version' => '1.00', 'soapns' => 'http://ws.bhiss.pbh.gov.br']
];


$json = json_encode($urls, JSON_PRETTY_PRINT);

file_put_contents('../storage/urls_webservices.json', $json);