<?php

$homologacao = "https://homologacao.ginfes.com.br/ServiceGinfesImpl";
$producao = "https://producao.ginfes.com.br/ServiceGinfesImpl";
$version = "3";
$homologacao_soapns = "http://homologacao.ginfes.com.br";
$producao_soapns = "http://producao.ginfes.com.br";

$muns = [
    ['Amparo', 'SP', '3501905'],
    ['Ananindeua', 'PA', '1500800'],
    ['Araraquara', 'SP', '3503208'],
    ['Bertioga', 'SP', '3506359'],
    ['Betim', 'MG', '3106705'],
    ['Campos dos Goytacazes', 'RJ', '3301009'],
    ['Capivari', 'SP', '3510401'],
    ['Caruaru', 'PE', '2604106'],
    ['Cataguases', 'MG', '3115300'],
    ['Colina', 'SP', '3512001'],
    ['Conceicao do Mato Dentro', 'MG', '3117504'],
    ['Contagem', 'MG', '3118601'],
    ['Diadema', 'SP', '3513801'],
    ['Embu-Guacu', 'SP', '3515103'],
    ['Franca', 'SP', '3516200'],
    ['Guararema', 'SP', '3518305'],
    ['Guaruja', 'SP', '3518701'],
    ['Guarulhos', 'SP', '3518800'],
    ['Hortolandia', 'SP', '3519071'],
    ['Itaborai', 'RJ', '3301900'],
    ['Itajuba', 'MG', '3132404'],
    ['Itauna', 'MG', '3133808'],
    ['Itu', 'SP', '3523909'],
    ['Jaboticabal', 'SP', '3524303'],
    ['Jardinopolis', 'SP', '3525102'],
    ['Jundiai', 'SP', '3525904'],
    ['Lagoa Santa', 'MG', '3137601'],
    ['Maceio', 'AL', '2704302'],
    ['Marechal Deodoro', 'AL', '2704708'],
    ['Marica', 'RJ', '3302700'],
    ['Matao', 'SP', '3529302'],
    ['Maua', 'SP', '3529401'],
    ['Mineiros', 'GO', '5213103'],
    ['Mococa', 'SP', '3530508'],
    ['Morro Agudo', 'SP', '3531902'],
    ['Muriae', 'MG', '3143906'],
    ['Olimpia', 'SP', '3533908'],
    ['Oliveira', 'MG', '3145604'],
    ['Para de Minas', 'MG', '3147105'],
    ['Paranagua', 'PR', '4118204'],
    ['Paulinia', 'SP', '3536505'],
    ['Porto Ferreira', 'SP', '3540705'],
    ['Pouso Alegre', 'MG', '3152501'],
    ['Registro', 'SP', '3542602'],
    ['Ribeirao Pires', 'SP', '3543303'],
    ['Ribeirao Preto', 'SP', '3543402'],
    ['Rio Bonito', 'RJ', '3304300'],
    ['Rio Claro', 'SP', '3543907'],
    ['Sacramento', 'MG', '3156908'],
    ['Salto', 'SP', '3545209'],
    ['Santarem', 'PB', '2513653'],
    ['Santarém', 'PA', '1506807'],
    ['Santo Andre', 'SP', '3547809'],
    ['Santos', 'SP', '3548500'],
    ['Sao Bernardo do Campos', 'SP', '3548708'],
    ['Sao Caetano do Sul', 'SP', '3548807'],
    ['Sao Carlos', 'SP', '3548906'],
    ['Sao Jose do Rio Preto', 'SP', '3549805'],
    ['Sao Roque', 'SP', '3550605'],
    ['Sao Sebastiao', 'SP', '3550704'],
    ['Suzano', 'SP', '3552502'],
    ['Taquaritinga', 'SP', '3553708'],
    ['Ubatuba', 'SP', '3555406'],
    ['Umuarama', 'PR', '4128104'],
    ['Varginha', 'MG', '3170701'],
    ['Votuporanga', 'SP', '3557105'],
];


$urls = [];
foreach ($muns as $mun) {
    $cod = $mun[2];
    $urls[$cod] = [
        "municipio"          => $mun[0],
        "uf"                 => $mun[1],
        "homologacao"        => $homologacao,
        "producao"           => $producao,
        "version"            => $version,
        "homologacao_soapns" => $homologacao_soapns,
        "producao_soapns"    => $producao_soapns
    ];
}

$json = json_encode($urls, JSON_PRETTY_PRINT);

echo "<pre>";
print_r($json);
echo "</pre>";

file_put_contents("../storage/urls_webservices.json", $json);