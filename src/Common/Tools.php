<?php

namespace NFePHP\NFSeGinfes\Common;

/**
 * Class base responsible for communication with SEFAZ
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Ginfes\Common\Tools
 * @copyright NFePHP Copyright (c) 2008-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfe for the canonical source repository
 */

use NFePHP\NFSeGinfes\Common\Soap\SoapCurl;
use DOMDocument;
use InvalidArgumentException;
use RuntimeException;
use NFePHP\Common\Certificate;
use NFePHP\Common\Signer;
use NFePHP\Common\Strings;
use NFePHP\Common\TimeZoneByUF;
use NFePHP\Common\Validator;

class Tools
{
    /**
     * config class
     * @var \stdClass
     */
    public $config;
    /**
     * Path to storage folder
     * @var string
     */
    public $pathwsfiles = '';
    /**
     * Path to schemes folder
     * @var string
     */
    public $pathschemes = '';
    /**
     * ambiente
     * @var string
     */
    public $ambiente = 'homologacao';
    /**
     * Environment
     * @var int
     */
    public $tpAmb = 2;
    /**
     * contingency class
     * @var Contingency
     */
    public $contingency;
    /**
     * soap class
     * @var SoapInterface
     */
    public $soap;
    /**
     * Application version
     * @var string
     */
    public $verAplic = '';
    /**
     * last soap request
     * @var string
     */
    public $lastRequest = '';
    /**
     * last soap response
     * @var string
     */
    public $lastResponse = '';
    /**
     * certificate class
     * @var Certificate
     */
    protected $certificate;
    /**
     * Sign algorithm from OPENSSL
     * @var int
     */
    protected $algorithm = OPENSSL_ALGO_SHA1;
    /**
     * Canonical conversion options
     * @var array
     */
    protected $canonical = [false,false,null,null];
    /**
     * Model of CTe 57 or 67
     * @var int
     */
    protected $modelo = 57;
    /**
     * Version of layout
     * @var string
     */
    protected $versao = 'v03';
    /**
     * urlPortal
     * Instância do WebService
     *
     * @var string
     */
    protected $urlPortal = 'https://homologacao.ginfes.com.br/ServiceGinfesImpl';
    /**
     * urlcUF
     * @var string
     */
    protected $urlcUF = '';
    /**
     * urlVersion
     * @var string
     */
    protected $urlVersion = '';
    /**
     * urlService
     * @var string
     */
    protected $urlService = '';
    /**
     * @var string
     */
    protected $urlMethod = '';
    /**
     * @var string
     */
    protected $urlOperation = '';
    /**
     * @var string
     */
    protected $urlNamespace = '';
    /**
     * @var string
     */
    protected $urlAction = '';
    /**
     * @var \SOAPHeader
     */
    protected $objHeader;
    /**
     * @var string
     */
    protected $urlHeader = '';
    /**
     * @var string
     */
    protected $xsdFile = '';
    /**
     * @var string
     */
    protected $method = '';
    /**
     * @var array
     */
    protected $soapnamespaces = [
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
        'xmlns:soap' => "http://www.w3.org/2003/05/soap-envelope"
    ];
    /**
     * @var array
     */
    protected $availableVersions = [
        'v03' => 'Ginfes_V3',
        'v02' => 'Ginfes_V2'
    ];

	protected $urls = [
        'EnviarLoteRpsEnvio' => [
            'method' => 'RecepcionarLoteRpsV3',
            'version' => 'v03',
            'xsd' => 'servico_enviar_lote_rps_envio'
        ],
        'ConsultarLoteRpsEnvio' => [
            'method' => 'ConsultarLoteRpsV3',
            'version' => 'v03',
            'xsd' => 'servico_consultar_lote_rps_envio'
        ],
        'ConsultarSituacaoLoteRpsEnvio' => [
            'method' => 'ConsultarSituacaoLoteRpsV3',
            'version' => 'v03',
            'xsd' => 'servico_consultar_situacao_lote_rps_envio'
        ],
        'ConsultarNfseRpsEnvio' => [
            'method' => 'ConsultarNfsePorRpsV3',
            'version' => 'v03',
            'xsd' => 'servico_consultar_nfse_rps_envio'
        ],
        'ConsultarNfseEnvio' => [
            'method' => 'ConsultarNfseV3',
            'version' => 'v03',
            'xsd' => 'servico_consultar_nfse_envio'
        ],
        'CancelarNfseEnvio_V3' => [
            'method' => 'CancelarNfseV3',
            'version' => 'v03',
            'xsd' => 'servico_cancelar_nfse_envio'
        ],
        'CancelarNfseEnvio_V2' => [
            'method' => 'CancelarNfse',
            'version' => 'v02',
            'xsd' => 'servico_cancelar_nfse_envio'
        ]
    ];

    /**
     * Constructor
     * load configurations,
     * load Digital Certificate,
     * map all paths,
     * set timezone and
     * @param string $configJson content of config in json format
     * @param Certificate $certificate
     */
    public function __construct($configJson, Certificate $certificate)
    {
        $this->config = json_decode($configJson);
        $this->setEnvironmentTimeZone($this->config->siglaUF);
        $this->certificate = $certificate;
        $this->setEnvironment($this->config->tpAmb);
        $this->servico('EnviarLoteRpsEnvio');

    }
    
    /**
     * Sets environment time zone
     * @param string $acronym (ou seja a sigla do estado)
     * @return void
     */
    public function setEnvironmentTimeZone($acronym)
    {
        date_default_timezone_set(TimeZoneByUF::get($acronym));
    }
    
    /**
     * Set application version
     * @param string $ver
     */
    public function setVerAplic($ver)
    {
        $this->verAplic = $ver;
    }

    /**
     * Load Soap Class
     * Soap Class may be \NFePHP\Common\Soap\SoapNative
     * or \NFePHP\Common\Soap\SoapCurl
     * @param SoapInterface $soap
     * @return void
     */
    public function loadSoapClass(SoapInterface $soap)
    {
        $this->soap = $soap;
        $this->soap->loadCertificate($this->certificate);
    }
    
    /**
     * Set OPENSSL Algorithm using OPENSSL constants
     * @param int $algorithm
     * @return void
     */
    public function setSignAlgorithm($algorithm = OPENSSL_ALGO_SHA1)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * Set or get model of document CTe = 57 or CTeOS = 67
     * @param int $model
     * @return int modelo class parameter
     */
    public function model($model = null)
    {
        if ($model == 57 || $model == 67) {
            $this->modelo = $model;
        }
        return $this->modelo;
    }
    
    /**
     * Set or get parameter layout version
     * @param string $version
     * @return string
     * @throws InvalidArgumentException
     */
    public function version($version = '')
    {
        if (!empty($version)) {
            if (!array_key_exists($version, $this->availableVersions)) {
                throw new \InvalidArgumentException('Essa versão de layout não está disponível');
            }
            $this->versao = $version;
            $this->config->schemes = $this->availableVersions[$version];
            $this->pathschemes = realpath(
                __DIR__ . '/../../schemes/'. $this->config->schemes
            ).'/';
        }
        return $this->versao;
    }
    
    /**
     * Recover cUF number from state acronym
     * @param string $acronym Sigla do estado
     * @return int number cUF
     */
    public function getcUF($acronym)
    {
        return UFlist::getCodeByUF($acronym);
    }
    
    /**
     * Recover state acronym from cUF number
     * @param int $cUF
     * @return string acronym sigla
     */
    public function getAcronym($cUF)
    {
        return UFlist::getUFByCode($cUF);
    }
    
    /**
     * Validate cUF from the key content and returns the state acronym
     * @param string $chave
     * @return string
     * @throws InvalidArgumentException
     */
    public function validKeyByUF($chave)
    {
        $uf = $this->config->siglaUF;
        if ($uf != UFList::getUFByCode(substr($chave, 0, 2))) {
            throw new \InvalidArgumentException(
                "A chave do CTe indicado [$chave] não pertence a [$uf]."
            );
        }
        return $uf;
    }
    
    /**
     * Sign NFSe
     * @param  string  $xml NFSe xml content
     * @return string singed NFSe xml
     * @throws RuntimeException
     */
    public function signNFSe($xml)
    {
        //remove all invalid strings
        $xml = Strings::clearXmlString($xml);
        // remove acentos
        $xml = Strings::squashCharacters($xml);
        $signed = Signer::sign(
            $this->certificate,
            $xml,
            'LoteRps',
            'Id',
            $this->algorithm,
            $this->canonical
        );
        $this->isValid($this->versao, $signed);
        
        return $signed;
    }

    /**
     * @todo
     * Corret NFe fields when in contingency mode is set
     * @param string $xml NFe xml content
     * @return string
     */
    protected function correctNFeForContingencyMode($xml)
    {
        if ($this->contingency->type == '') {
            return $xml;
        }
        $xml = ContingencyNFe::adjust($xml, $this->contingency);
        return $this->signNFe($xml);
    }

    /**
     * Performs xml validation with its respective
     * XSD structure definition document
     * NOTE: if dont exists the XSD file will return true
     * @param string $version layout version
     * @param string $body
     * @param string $method
     * @return boolean
     */
    protected function isValid($version, $body)
    {
		
        $schema = $this->pathschemes.$this->xsdFile."_$version.xsd";
        if (!is_file($schema)) {
            return true;
        }
        return Validator::isValid(
            $body,
            $schema
        );
        
    }
    
    /**
     * Verifies the existence of the service
     * @param string $service
     * @throws RuntimeException
     */
    protected function checkContingencyForWebServices($service)
    {
        $permit = [
            55 => ['SVCAN', 'SVCRS', 'EPEC', 'FSDA'],
            65 => ['FSDA', 'EPEC', 'OFFLINE']
        ];
        
        $type = $this->contingency->type;
        $mod = $this->modelo;
        if (!empty($type)) {
            if (array_search($type, $permit[$mod]) === false) {
                throw new RuntimeException(
                    "Esse modo de contingência [$type] não é aceito "
                    . "para o modelo [$mod]"
                );
            }
        }
        
        //se a contingencia é OFFLINE ou FSDA nenhum servidor está disponivel
        //se a contigencia EPEC está ativa apenas o envio de Lote está ativo,
        //então gerar um RunTimeException
        if ($type == 'FSDA'
            || $type == 'OFFLINE'
            || ($type == 'EPEC' && $service != 'RecepcaoEvento')
        ) {
            throw new RuntimeException(
                "Quando operando em modo de contingência ["
                . $this->contingency->type
                . "], este serviço [$service] não está disponível."
            );
        }
    }
    
    /**
     * Alter environment from "homologacao" to "producao" and vice-versa
     * @param int $tpAmb
     * @return void
     */
    public function setEnvironment($tpAmb = 2)
    {
        if (!empty($tpAmb) && ($tpAmb == 1 || $tpAmb == 2)) {
            $this->tpAmb = $tpAmb;
            $this->ambiente = ($tpAmb == 1) ? 'producao' : 'homologacao';
        }
    }
    
    /**
     * Set option for canonical transformation see C14n
     * @param array $opt
     * @return array
     */
    public function canonicalOptions($opt = [true,false,null,null])
    {
        if (!empty($opt) && is_array($opt)) {
            $this->canonical = $opt;
        }
        return $this->canonical;
    }
    
    /**
     * Assembles all the necessary parameters for soap communication
     * @param string $service
     * @param string $uf
     * @param string $tpAmb
     * @param bool $ignoreContingency
     * @return void
     */
    protected function servico($service)
    {

        $this->urlPortal = "https://{$this->ambiente}.ginfes.com.br/ServiceGinfesImpl";
        $url = json_decode(json_encode($this->urls[$service]));
		$this->xsdFile = $url->xsd;
		$this->method = $url->method;
        $this->versao = $url->version;
        $this->version($this->versao);

    }
    
    /**
     * Send request message to webservice
     * @param array $parameters
     * @param string $request
     * @return string
     */
    protected function sendRequest($request)
    {
        $this->checkSoap();
        $retorno = $this->soap->send(
								$this->urlPortal,
								$request,
								$this->method,
								$this->ambiente,
								$this->versao
								);
        $this->lastResponse = $this->soap->response;
        return (string) $retorno;
    }
    
    /**
     * Recover path to xml data base with list of soap services
     * @return string
     */
    protected function getXmlUrlPath()
    {
        $file = $this->pathwsfiles
            . DIRECTORY_SEPARATOR
            . "wscte_".$this->versao."_mod57.xml";
        if (! file_exists($file)) {
            return '';
        }
        return file_get_contents($file);
    }
    
    /**
     * Add QRCode Tag to signed XML from a NFCe
     * @param DOMDocument $dom
     * @return string
     */
    protected function addQRCode(DOMDocument $dom)
    {
        $memmod = $this->modelo;
        $this->modelo = 65;
        $uf = UFList::getUFByCode(
            $dom->getElementsByTagName('cUF')->item(0)->nodeValue
        );
        $this->servico(
            'NfeConsultaQR',
            $uf,
            $dom->getElementsByTagName('tpAmb')->item(0)->nodeValue
        );
        $signed = QRCode::putQRTag(
            $dom,
            $this->config->CSC,
            $this->config->CSCid,
            $this->urlVersion,
            $this->urlService,
            $this->getURIConsultaNFCe($uf)
        );
        $this->modelo = $memmod;
        return Strings::clearXmlString($signed);
    }

    /**
     * Get URI for search NFCe by chave
     * NOTE: exists only in 4.00 layout
     * @param string $uf
     * @return string
     */
    protected function getURIConsultaNFCe($uf)
    {
        if ($this->versao < '4.00') {
            return '';
        }
        //existe no XML apenas para layout >= 4.x
        //os URI estão em storage/uri_consulta_nfce.json
        $std = json_decode(
            file_get_contents(
                $this->pathwsfiles.'uri_consulta_nfce.json'
            )
        );
        return $std->$uf;
    }
    
    /**
     * Verify if SOAP class is loaded, if not, force load SoapCurl
     */
    protected function checkSoap()
    {
        if (empty($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
    }
}
