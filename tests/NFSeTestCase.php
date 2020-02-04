<?php

namespace NFePHP\NFSeGinfes\Tests;

use PHPUnit\Framework\TestCase;

class NFSeTestCase extends TestCase
{
    public $fixturesPath = '';
    public $configJson = '';
    public $contentpfx = '';
    public $passwordpfx = '';

    public function __construct()
    {
        parent::__construct();
        $this->fixturesPath = dirname(__FILE__) . '/fixtures/';
        $config = [
            'cnpj' => '99999999000191',
            'im' => '1733160024',
            'cmun' => '4314902', //ira determinar as urls e outros dados
            'razao' => 'Empresa Test Ltda',
            'tpamb' => 2 //1-producao, 2-homologacao
        ];
        $this->contentpfx = file_get_contents($this->fixturesPath . "expired_certificate.pfx");
        $this->passwordpfx = 'associacao';
        $this->configJson = json_encode($config);
    }
}
