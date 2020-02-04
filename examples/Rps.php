<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\NFSeGinfes\Rps;

$std = new \stdClass();
$std->version = '1.00'; //indica qual JsonSchema USAR na validação
$std->IdentificacaoRps = new \stdClass();
$std->IdentificacaoRps->Numero = 11; //limite 15 digitos
$std->IdentificacaoRps->Serie = '1'; //BH deve ser string numerico
$std->IdentificacaoRps->Tipo = 1; //1 - RPS 2-Nota Fiscal Conjugada (Mista) 3-Cupom
$std->DataEmissao = '2018-10-31T12:33:22';
$std->NaturezaOperacao = 1; // 1 – Tributação no município
                            // 2 - Tributação fora do município
                            // 3 - Isenção
                            // 4 - Imune
                            // 5 – Exigibilidade suspensa por decisão judicial
                            // 6 – Exigibilidade suspensa por procedimento administrativo

$std->RegimeEspecialTributacao = 1;    // 1 – Microempresa municipal
                                       // 2 - Estimativa
                                       // 3 – Sociedade de profissionais
                                       // 4 – Cooperativa
                                       // 5 – MEI – Simples Nacional
                                       // 6 – ME EPP – Simples Nacional

$std->OptanteSimplesNacional = 1; //1 - SIM 2 - Não
$std->IncentivadorCultural = 2; //1 - SIM 2 - Não
$std->Status = 1;  // 1 – Normal  2 – Cancelado

$std->Tomador = new \stdClass();
$std->Tomador->Cnpj = "99999999000191";
$std->Tomador->Cpf = "12345678901";
$std->Tomador->RazaoSocial = "Fulano de Tal";

$std->Tomador->Endereco = new \stdClass();
$std->Tomador->Endereco->Endereco = 'Rua das Rosas';
$std->Tomador->Endereco->Numero = '111';
$std->Tomador->Endereco->Complemento = 'Sobre Loja';
$std->Tomador->Endereco->Bairro = 'Centro';
$std->Tomador->Endereco->CodigoMunicipio = 3106200;
$std->Tomador->Endereco->Uf = 'MG';
$std->Tomador->Endereco->Cep = 30160010;

$std->Servico = new \stdClass();
$std->Servico->ItemListaServico = '11.01';
$std->Servico->CodigoTributacaoMunicipio = '522310000';
$std->Servico->Discriminacao = 'Teste de RPS';
$std->Servico->CodigoMunicipio = 3106200;

$std->Servico->Valores = new \stdClass();
$std->Servico->Valores->ValorServicos = 100.00;
$std->Servico->Valores->ValorDeducoes = 10.00;
$std->Servico->Valores->ValorPis = 10.00;
$std->Servico->Valores->ValorCofins = 10.00;
$std->Servico->Valores->ValorInss = 10.00;
$std->Servico->Valores->ValorIr = 10.00;
$std->Servico->Valores->ValorCsll = 10.00;
$std->Servico->Valores->IssRetido = 1;
$std->Servico->Valores->ValorIss = 10.00;
$std->Servico->Valores->OutrasRetencoes = 10.00;
$std->Servico->Valores->Aliquota = 5;
$std->Servico->Valores->DescontoIncondicionado = 10.00;
$std->Servico->Valores->DescontoCondicionado = 10.00;

$std->IntermediarioServico = new \stdClass();
$std->IntermediarioServico->RazaoSocial = 'INSCRICAO DE TESTE SIATU - D AGUA -PAULINO S'; 
$std->IntermediarioServico->Cnpj = '99999999000191';
$std->IntermediarioServico->InscricaoMunicipal = '8041700010';

$std->ConstrucaoCivil = new \stdClass();
$std->ConstrucaoCivil->CodigoObra = '1234';
$std->ConstrucaoCivil->Art = '1234';

$rps = new Rps($std);

header("Content-type: text/xml");
echo $rps->render();

/*
echo "<pre>";
print_r(json_encode($std));
echo "</pre>";
*/

