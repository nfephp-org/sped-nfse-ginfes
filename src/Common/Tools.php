<?php

namespace NFePHP\NFSeGinfes\Common;

/**
 * Auxiar Tools Class for comunications with NFSe webserver in Ginfes Standard
 *
 * @category  NFePHP
 * @package   NFePHP\NFSeGinfes
 * @copyright NFePHP Copyright (c) 2020
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Cleiton Perin <cperin20 at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-ginfes for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\NFSeGinfes\RpsInterface;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSeGinfes\Common\Signer;
use NFePHP\NFSeGinfes\Common\Soap\SoapInterface;
use NFePHP\NFSeGinfes\Common\Soap\SoapCurl;

class Tools
{
    public $lastRequest;

    protected $config;
    protected $prestador;
    protected $certificate;
    protected $wsobj;
    protected $soap;
    protected $environment;
    protected $version = "3";

    /**
     * Constructor
     * @param string $config
     * @param Certificate $cert
     */
    public function __construct($config, Certificate $cert)
    {
        $this->config = json_decode($config);
        $this->certificate = $cert;
        $this->wsobj = $this->loadWsobj($this->config->cmun);
        $this->environment = 'homologacao';
        if ($this->config->tpamb === 1) {
            $this->environment = 'producao';
        }
    }

    /**
     * load webservice parameters
     * @param string $cmun
     * @return object
     * @throws \Exception
     */
    protected function loadWsobj($cmun)
    {
        $path = realpath(__DIR__ . "/../../storage/urls_webservices.json");
        $urls = json_decode(file_get_contents($path), true);
        if (empty($urls[$cmun])) {
            throw new \Exception("Não localizado parâmetros para esse municipio.");
        }
        return (object)$urls[$cmun];
    }


    /**
     * SOAP communication dependency injection
     * @param SoapInterface $soap
     */
    public function loadSoapClass(SoapInterface $soap)
    {
        $this->soap = $soap;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Sign XML passing in content
     * @param string $content
     * @param string $tagname
     * @param string $mark
     * @return string XML signed
     */
    public function sign($content, $tagname, $mark)
    {
        $xml = Signer::sign(
            $this->certificate,
            $content,
            $tagname,
            $mark
        );
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);
        return $dom->saveXML($dom->documentElement);
    }

    /**
     * Send message to webservice
     * @param string $message
     * @param string $operation
     * @return string XML response from webservice
     */
    public function send($message, $operation)
    {
        $action = $operation;
        $url = $this->wsobj->homologacao;
        if ($this->environment === 'producao') {
            $url = $this->wsobj->producao;
        }
        if (empty($url)) {
            throw new \Exception("Não está registrada a URL para o ambiente "
                . "de {$this->environment} desse municipio.");
        }
        $request = $this->createSoapRequest($message, $operation);
        $this->lastRequest = $request;

        if (empty($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
        $msgSize = strlen($request);
        // ;action="https://{$this->environment}.ginfes.com.br/ServiceGinfesImpl"
        $parameters = [
            "Content-Type: application/soap+xml;charset=utf-8",
            "SOAPAction: \"$action\"",
            "Content-length: $msgSize"
        ];
        $response = (string)$this->soap->send(
            $operation,
            $url,
            $action,
            $request,
            $parameters
        );
        return $this->extractContentFromResponse($response, $operation);
    }

    /**
     * Extract xml response from CDATA outputXML tag
     * @param string $response Return from webservice
     * @return string XML extracted from response
     */
    protected function extractContentFromResponse($response, $operation)
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($response);
        if (!empty($dom->getElementsByTagName('return')->item(0))) {
            $node = $dom->getElementsByTagName('return')->item(0);
            return $node->textContent;
        }
        return $response;
    }

    /**
     * Build SOAP request
     * @param string $message
     * @param string $operation
     * @return string XML SOAP request
     */
    protected function createSoapRequest($message, $operation)
    {
        $cabecalho = "<ns2:cabecalho versao=\"{$this->wsobj->version}\" xmlns:ns2=\"http://www.ginfes.com.br/cabecalho_v03.xsd\">"
            . "<versaoDados>{$this->wsobj->version}</versaoDados>"
            . "</ns2:cabecalho>";

        $ns1 = "{$this->environment}_soapns";
        $ns1 = $this->wsobj->$ns1;

        $env = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">"
            . "<soapenv:Header/>"
            . "<soapenv:Body>"
            . "<ns1:$operation xmlns:ns1=\"{$ns1}\">";
        if ($this->version == '3') {
            $env .= "<arg0>"
                . $cabecalho
                . "</arg0>"
                . "<arg1>"
                . $message
                . "</arg1>";
        } else {
            $env .= "<arg0>"
                . $message
                . "</arg0>";
        }
        $env .= "</ns1:$operation>"
            . "</soapenv:Body>"
            . "</soapenv:Envelope>";

        return $env;
    }

}