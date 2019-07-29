<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\NFSeGinfes\Make;

$config = [
   "atualizacao" => "2019-06-15 08:29:21",
   "tpAmb" => 2,
   "razaosocial" => "SOFTWARE & HARDWARE INFORMATICA - ME",
   "siglaUF" => "SP",
   "cnpj" => "11222333444455",
   "inscricaomunicipal" => "11223",
   "codigomunicipio" => "3518800",
   "schemes" => "V3",
   "versao" => "3.00"
];

$configJson = json_encode($config);

$nfse = new NFePHP\NFSeGinfes\Make($configJson);

$std = new \stdClass();
$std->Id = '1710';
$nfse->tagLoteRps($std);

$std = new \stdClass();
$std->Serie = '1';
$std->Tipo = '1';
$std->NaturezaOperacao = '1';
$std->RegimeEspecialTributacao = '6';
$std->OptanteSimplesNacional = '2';
$std->IncentivadorCultural = '2';
$std->Status = '1';
$nfse->tagIdentificacao($std);

$std = new \stdClass();
$std->ValorServicos = 116.00;
$std->ValorDeducoes = 0.00;
$std->ValorPis = 0.00;
$std->ValorCofins = 0.00;
$std->ValorInss = 0.00;
$std->ValorIr = 0.00;
$std->ValorCsll = 0.00;
$std->IssRetido = 2;
$std->ValorIss = 2.32;
$std->ValorIssRetido = 0.00;
$std->OutrasRetencoes = 0.00;
$std->OutrasRetencoes = 0.00;
$std->BaseCalculo = 116.00;
$std->Aliquota = 0.0200;
$std->ValorLiquidoNfse = 116.00;
$std->DescontoIncondicionado = 0.00;
$std->DescontoCondicionado = 0.00;
$std->ItemListaServico = '1.01';
$std->CodigoTributacaoMunicipio = '620150101';
$Discriminacao  = 'SUPORTE E USO MENSAL DO SISTEMA | ';
$Discriminacao .= 'Em atendimento a Lei 12.741/2012 (Lei do Imposto na Nota Fiscal) ';
$Discriminacao .= 'informamos que o valor aproximado dos | tributos incidentes sobre ';
$Discriminacao .= 'as operacoes deste estabelecimento e de: 8,65% assim distribuidos: | ';
$Discriminacao .= 'Estado: 3,65% | Uniao: 0,00% | Municipio: 5,00%';
$std->Discriminacao = $Discriminacao;
$nfse->tagServico($std);

$std = new \stdClass();
$std->Cnpj = '99888777666655';
$std->RazaoSocial = 'SIMULADA S/A';
$std->Endereco = 'AV CENTRAL';
$std->Numero = '100';
$std->Bairro = 'CENTRO';
$std->CodigoMunicipio = '3550308';
$std->Uf = 'SP';
$std->Cep = '07000000';
$std->Email = 'financeiro@simulada.com.br';
$nfse->tagTomador($std);

$xml = $nfse->getXML(); // O conteúdo do XML fica armazenado na variável $xml
file_put_contents($nfse->Id.'.xml',$xml);
