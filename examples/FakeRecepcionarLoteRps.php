<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeGinfes\Common\FakePretty;
use NFePHP\NFSeGinfes\Common\Soap\SoapFake;
use NFePHP\NFSeGinfes\Rps;
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

    $arps = [];

    $std = new \stdClass();
    $std->version = '1.00';
    $std->IdentificacaoRps = new \stdClass();
    $std->IdentificacaoRps->Numero = 1675; //limite 15 digitos
    $std->IdentificacaoRps->Serie = 'THS'; //BH deve ser string numerico
    $std->IdentificacaoRps->Tipo = 1; //1 - RPS 2-Nota Fiscal Conjugada (Mista) 3-Cupom
    $std->DataEmissao = '2020-02-03T12:33:22';
    $std->NaturezaOperacao = 1; // 1 – Tributação no município
    // 2 - Tributação fora do município
    // 3 - Isenção
    // 4 - Imune
    // 5 – Exigibilidade suspensa por decisão judicial
    // 6 – Exigibilidade suspensa por procedimento administrativo

    $std->RegimeEspecialTributacao = 5;    // 1 – Microempresa municipal
    // 2 - Estimativa
    // 3 – Sociedade de profissionais
    // 4 – Cooperativa
    // 5 – MEI – Simples Nacional
    // 6 – ME EPP – Simples Nacional

    $std->OptanteSimplesNacional = 1; //1 - SIM 2 - Não
    $std->IncentivadorCultural = 2; //1 - SIM 2 - Não
    $std->Status = 1;  // 1 – Normal  2 – Cancelado

    $std->Tomador = new \stdClass();
    $std->Tomador->Cnpj = "12827946000105";
    $std->Tomador->Cpf = "12345678901";
    $std->Tomador->RazaoSocial = "Fulano de Tal";
    $std->Tomador->Email = "financeiro@simulada.com.br";

    $std->Tomador->Endereco = new \stdClass();
    $std->Tomador->Endereco->Endereco = 'Rua das Rosas';
    $std->Tomador->Endereco->Numero = '111';
    $std->Tomador->Endereco->Complemento = 'Sobre Loja';
    $std->Tomador->Endereco->Bairro = 'Centro';
    $std->Tomador->Endereco->CodigoMunicipio = 3547809;
    $std->Tomador->Endereco->Uf = 'SP';
    $std->Tomador->Endereco->Cep = '08320370';

    $std->Servico = new \stdClass();
    $std->Servico->ItemListaServico = '1.07';
    $std->Servico->CodigoTributacaoMunicipio = '1.07 / 620910000';
    $std->Servico->Discriminacao = 'Teste de RPS';
    $std->Servico->CodigoMunicipio = 3547809;

    $std->Servico->Valores = new \stdClass();
    $std->Servico->Valores->ValorServicos = 100.00;
    $std->Servico->Valores->ValorDeducoes = 0.00;
    $std->Servico->Valores->ValorPis = 0.00;
    $std->Servico->Valores->ValorCofins = 0.00;
    $std->Servico->Valores->ValorInss = 0.00;
    $std->Servico->Valores->ValorIr = 0.00;
    $std->Servico->Valores->ValorCsll = 0.00;
    $std->Servico->Valores->IssRetido = 2;
    $std->Servico->Valores->ValorIss = 2.00;
    $std->Servico->Valores->ValorIssRetido = 0.00;
    $std->Servico->Valores->OutrasRetencoes = 0.00;
    $std->Servico->Valores->BaseCalculo = 100.00;
    $std->Servico->Valores->Aliquota = 0.02;
    $std->Servico->Valores->ValorLiquidoNfse = 100.00;
    $std->Servico->Valores->DescontoIncondicionado = 0.00;
    $std->Servico->Valores->DescontoCondicionado = null;

    $std->IntermediarioServico = new \stdClass();
    $std->IntermediarioServico->RazaoSocial = 'INSCRICAO DE TESTE SIATU - D AGUA -PAULINO S';
    $std->IntermediarioServico->Cnpj = '99999999000191';
    $std->IntermediarioServico->InscricaoMunicipal = '8041700010';

    $std->ConstrucaoCivil = new \stdClass();
    $std->ConstrucaoCivil->CodigoObra = '1234';
    $std->ConstrucaoCivil->Art = '1234';

    $arps[] = new Rps($std);

    $lote = time();
    $response = $tools->recepcionarLoteRps($arps, $lote);

    echo FakePretty::prettyPrint($response, '');

} catch (\Exception $e) {
    echo $e->getMessage();
}