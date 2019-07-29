<?php

/**
 * Class MakeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\NFSeGinfes\Make;
use PHPUnit\Framework\TestCase;

class MakeTest extends TestCase
{
    /**
     * object 
     */
    private $nfse;

    public function testDadosParaConexao()
    {
        $razaosocial = "SOFTWARE & HARDWARE INFORMATICA - ME";
        $cnpj = "11222333444455"; 
        $inscricaomunicipal = "11223";

        $this->assertEquals($razaosocial, $this->nfse->config->razaosocial);
        $this->assertEquals($cnpj, $this->nfse->config->cnpj);
        $this->assertEquals($inscricaomunicipal, $this->nfse->config->inscricaomunicipal);
    }

    protected function setUp()
    {
        parent::setUp();
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
		$this->nfse = new Make($configJson);
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->nfse);
    }
}
