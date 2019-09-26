<?php

namespace NFePHP\NFSeGinfes\Common\Soap;

use DOMDocument;
use NFePHP\NFSeGinfes\Common\Soap\SoapBase;
use NFePHP\NFSeGinfes\Common\Soap\SoapInterface;
use NFePHP\Common\Exception\SoapException;
use NFePHP\Common\Certificate;
use Psr\Log\LoggerInterface;

class SoapCurl extends SoapBase implements SoapInterface
{
    /**
     * Tabela de codigos HTTP
     * @var array
     */
	protected $cCode = [
		0 => 'Indefinido',
        100 => "Continue",
        101 => "Switching Protocols",
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        306 => "(Unused)",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Requested Range Not Satisfiable",
        417 => "Expectation Failed",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported"
	];
/**
     * Constructor
     * @param Certificate $certificate
     * @param LoggerInterface $logger
     */
    public function __construct(Certificate $certificate = null, LoggerInterface $logger = null)
    {
        parent::__construct($certificate, $logger);
    }
    
    /**
     * Estabelece comunicaçao com servidor SOAP da SEFAZ Municipal,
     * usando as chaves publica e privada parametrizadas na contrução da classe, utiliza-se da classe NuSOAP. 
     * (ainda enfrentando problemas ao tentar fazer via curl e soap nativo)
     * 
     * @param string $urlwebservice
     * @param string $dados
     * @param string $metodo
     * @param string $ambiente
     * @return type
     */   
    public function send(
		$urlwebservice, 
		$request, 
		$metodo,
		$ambiente,
		$versao
	){
        $this->saveTemporarilyKeyFiles();
        
        $data = $this->makeEnvelopeSoap($request, $ambiente, $metodo, $versao);
        $tamanho = strlen($data);

        $parametros = Array('Content-Type: application/soap+xml;charset=utf-8;action="'.$urlwebservice.'"',
							'SOAPAction: "'.$metodo.'"',
							"Content-length: $tamanho");
							
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $urlwebservice.'');
        curl_setopt($oCurl, CURLOPT_PORT , 443);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1); //apresenta informações de conexão na tela
        curl_setopt($oCurl, CURLOPT_HEADER, 1); //retorna o cabeçalho de resposta
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 4); 
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($oCurl, CURLOPT_SSLCERT, $this->tempdir . $this->certfile);
		curl_setopt($oCurl, CURLOPT_SSLKEY, $this->tempdir . $this->prifile);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER,$parametros);
        $response = curl_exec($oCurl);
		$this->soaperror = curl_error($oCurl);
		$ainfo = curl_getinfo($oCurl);
		if (is_array($ainfo)) {
			$this->soapinfo = $ainfo;
		}
        curl_close($oCurl);
        $xml = $this->clearReturnSOAP($response);
		$this->response = $this->nodeXML('env:Envelope', $xml);
		$node = $this->nodeXML('ListaMensagemRetorno', $xml);
		if( empty($node) ){
			$node = $this->nodeXML('return', $xml);
			if( !empty($node) ){
				$node = str_replace('xmlns="http://www.w3.org/2000/09/xmldsig#" ', '' , $node);
				$node = str_replace('xmlns="http://www.ginfes.com.br/servico_consultar_nfse_rps_resposta_v03.xsd"', '' , $node);
				$node = str_replace('xmlns="http://www.ginfes.com.br/tipos_v03.xsd" ', '' , $node);
				$node = str_replace('xmlns="http://www.ginfes.com.br/servico_enviar_lote_rps_resposta_v03.xsd"', '' , $node);
				$node = str_replace('xmlns="http://www.ginfes.com.br/servico_consultar_situacao_lote_rps_resposta_v03.xsd"', '' , $node);
				$xml = $node;
			}	
		} else {
			$xml = $node;
		}
        return $xml;
        
    }
 
}
