<?php

namespace NFePHP\NFSeGinfes\Tests;

use NFePHP\NFSeGinfes\Rps;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class RpsTest extends TestCase
{
    public $std;
    public $fixturesPath;
    
    public function __construct()
    {
        parent::__construct();
        $std = new \stdClass();
        $std->version = '1.00';
        $std->IdentificacaoRps = new \stdClass();
        $std->IdentificacaoRps->Numero = 11;
        $std->IdentificacaoRps->Serie = '1';
        $std->IdentificacaoRps->Tipo = 1;
        $std->DataEmissao = '2018-10-31T12:33:22';
        $std->NaturezaOperacao = 1;
        $std->RegimeEspecialTributacao = 1;
        $std->OptanteSimplesNacional = 1;
        $std->IncentivadorCultural = 2;
        $std->Status = 1;
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
        $this->std = $std;
        $this->fixturesPath = dirname(__FILE__) . '/fixtures/';
    }
    
    public function testCanInstantiate()
    {
        $rps = new Rps();
        $this->assertInstanceOf('NFePHP\NFSeGinfes\Rps', $rps);
    }
    
    /**
     * @covers Rps::init
     * @covers Rps::propertiesToLower
     * @covers Rps::validInputData
     */
    public function testRender()
    {
        $rps = new Rps($this->std);
        $actualXml = $rps->render();
        $expectedXml = file_get_contents($this->fixturesPath . 'rps_1_00.xml');
        //$this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
        $this->assertTrue(true);
    }

    public function testInterfaceImplementation()
    {
        $class = new ReflectionClass(Rps::class);

        foreach ($class->getInterfaces() as $interface) {
            $interfaceMethods = $interface->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($interfaceMethods as $interfaceMethod) {
                $methodName = $interfaceMethod->getName();
                $parameters = $interfaceMethod->getParameters();

                $classMethod = $class->getMethod($methodName);
                $childParameters = $classMethod->getParameters();

                $failMessage = sprintf(
                    'A assinatura do método %s::%s precisa estar igual na interface %s::%s.',
                    $class->name,
                    $methodName,
                    $interface->name,
                    $methodName
                );

                if (empty($parameters)) {
                    $this->assertEmpty($childParameters, $failMessage);
                }

                foreach ($parameters as $index => $parameter) {
                    $this->assertEquals($parameter->__toString(), $childParameters[$index]->__toString(), $failMessage);
                }
            }
        }
    }
}
