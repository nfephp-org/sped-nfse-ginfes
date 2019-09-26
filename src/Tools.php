<?php

namespace NFePHP\NFSeGinfes;

/**
 * Class responsible for communication with SEFAZ extends
 * NFePHP\NFSeGinfes\Common\Tools
 *
 * @category  NFePHP
 * @package   NFePHP\NFSeGinfes\Tools
 * @copyright NFePHP Copyright (c) 2008-2017
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-ginfes for the canonical source repository
 */

use NFePHP\Common\Strings;
use NFePHP\Common\Signer;
use NFePHP\Common\UFList;
use NFePHP\NFSeGinfes\Common\Tools as ToolsCommon;
use RuntimeException;
use InvalidArgumentException;

class Tools extends ToolsCommon
{

    /**
     * Request authorization to issue NFSe in batch with one or more documents
     * @param array $cXml 
     * @return string 
     */
    public function EnviaLoteNFSe(
        $cXml
    ) {
        $servico = 'EnviarLoteRpsEnvio';
        $this->servico(
            $servico
        );
        $this->lastRequest = $cXml;
        $this->lastResponse = $this->sendRequest($cXml);
        return $this->lastResponse;
    }
    
    /**
     * Check status of Batch of NFSe sent by receipt of this shipment
     * @param string $numNFSe
     * @param string $serie
     * @param string $tipo
     * @return string
     */
    public function ConsultaNFSe($numNFSe, $serie = '1', $tipo = '1')
    {
        $servico = 'ConsultarNfseRpsEnvio';
        $this->servico(
            $servico
        );

        $xml  = '<ConsultarNfseRpsEnvio xmlns="http://www.ginfes.com.br/servico_consultar_nfse_rps_envio_v03.xsd" ' ;
        $xml .= 'xmlns:tipos="http://www.ginfes.com.br/tipos_v03.xsd">' ;
        $xml .= '<IdentificacaoRps>' ;
			$xml .= '<tipos:Numero>' . $numNFSe . '</tipos:Numero>' ; 
			$xml .= '<tipos:Serie>' . $serie . '</tipos:Serie>' ;
			$xml .= '<tipos:Tipo>' . $tipo . '</tipos:Tipo>' ;
        $xml .= '</IdentificacaoRps>' ;
        $xml .= '<Prestador>' ;
			$xml .= '<tipos:Cnpj>' . $this->config->cnpj . '</tipos:Cnpj>' ;
			$xml .= '<tipos:InscricaoMunicipal>' . $this->config->inscricaomunicipal . '</tipos:InscricaoMunicipal>' ; 
        $xml .= '</Prestador>' ;
        $xml .= '</ConsultarNfseRpsEnvio>';

        //assinatura dos dados
        $signed = Signer::sign(
            $this->certificate,
            $xml,
            'ConsultarNfseRpsEnvio',
            'Id',
            $this->algorithm,
            $this->canonical
        );
        
        $request = Strings::clearXmlString($signed, true);
        $this->isValid($this->versao, $request);

        $this->lastRequest = $request;
        $response = $this->sendRequest($request);
        return $response;
    }

    /**
     * Check status of Batch of NFSe sent by receipt of this shipment
     * @param string $numNFSe
     * @return string
     */
    public function SituacaoNFSe($protocolo)
    {
        $servico = 'ConsultarSituacaoLoteRpsEnvio';
        $this->servico(
            $servico
        );

        $xml  = '<ConsultarSituacaoLoteRpsEnvio ' ;
        $xml .= 'xmlns="http://www.ginfes.com.br/servico_consultar_situacao_lote_rps_envio_v03.xsd" ' ;
        $xml .= 'xmlns:tipos="http://www.ginfes.com.br/tipos_v03.xsd">' ;
        $xml .= '<Prestador>' ;
			$xml .= '<tipos:Cnpj>' . $this->config->cnpj . '</tipos:Cnpj>' ;
			$xml .= '<tipos:InscricaoMunicipal>' . $this->config->inscricaomunicipal . '</tipos:InscricaoMunicipal>' ;
        $xml .= '</Prestador>' ;
        $xml .= '<Protocolo>' . $protocolo . '</Protocolo>';
        $xml .= '</ConsultarSituacaoLoteRpsEnvio>';

        //assinatura dos dados
        $signed = Signer::sign(
            $this->certificate,
            $xml,
            'ConsultarSituacaoLoteRpsEnvio',
            'Id',
            $this->algorithm,
            $this->canonical
        );
        
        $request = Strings::clearXmlString($signed, true);
        $this->isValid($this->versao, $request);

        $this->lastRequest = $request;
        $response = $this->sendRequest($request);
        return $response;
    }

    /**
     * Requires NFSe cancellation
     * @param  string $numNFSe NFSe number
     * @return string
     */
    public function CancelarNFSe_V3($numNFSe)
    {
        $servico = 'CancelarNfseEnvio_V3';
        $this->servico(
            $servico
        );
		/* 
		 * Versão 3.0 não funciona em Guarulhos 
		 */
        $xml  = '<CancelarNfseEnvio ' ;
        $xml .= 'xmlns="http://www.ginfes.com.br/servico_cancelar_nfse_envio_v03.xsd" ' ;
        $xml .= 'xmlns:tipos="http://www.ginfes.com.br/tipos_v03.xsd">' ;
			$xml .= '<Pedido xmlns:tipos="http://www.ginfes.com.br/tipos_v03.xsd">' ;
				$xml .= '<tipos:InfPedidoCancelamento Id="'.$numNFSe.'">' ;
					$xml .= '<tipos:IdentificacaoNfse>' ;
						$xml .= '<tipos:Numero>' . $numNFSe . '</tipos:Numero>';
						$xml .= '<tipos:Cnpj>' . $this->config->cnpj . '</tipos:Cnpj>';
						$xml .= '<tipos:InscricaoMunicipal>' . $this->config->inscricaomunicipal . '</tipos:InscricaoMunicipal>';
						$xml .= '<tipos:CodigoMunicipio>' . $this->config->codigomunicipio . '</tipos:CodigoMunicipio>';
					$xml .= '</tipos:IdentificacaoNfse>';
					$xml .= '<tipos:CodigoCancelamento>1</tipos:CodigoCancelamento>';
				$xml .= '</tipos:InfPedidoCancelamento>';
			$xml .= '</Pedido>';

        $xml .= '</CancelarNfseEnvio>';

        //assinatura dos dados
        $signed = Signer::sign(
            $this->certificate,
            $xml,
            'InfPedidoCancelamento',
            '',
            $this->algorithm,
            $this->canonical,
            'Pedido'
        );
        $signed = Signer::sign(
            $this->certificate,
            $signed,
            'Pedido',
            '',
            $this->algorithm,
            $this->canonical,
            'CancelarNfseEnvio'
        );

        $request = Strings::clearXmlString($signed, true);
        $this->lastRequest = $request;
        $response = $this->sendRequest($request);
        return $response;
    }

    /**
     * Requires NFSe cancellation
     * @param  string $numNFSe NFSe number
     * @return string
     */
    public function CancelarNFSe_V2($numNFSe)
    {
        $servico = 'CancelarNfseEnvio_V2';
        $this->servico(
            $servico
        );
		/* 
		 * Versão 2.0 funciona em Guarulhos 
		 */
        $xml  = '<CancelarNfseEnvio ' ;
        $xml .= 'xmlns="http://www.ginfes.com.br/servico_cancelar_nfse_envio" ' ;
        $xml .= 'xmlns:tipos="http://www.ginfes.com.br/tipos" ' ;
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' ;
			$xml .= '<Prestador>' ;
				$xml .= '<tipos:Cnpj>' . $this->config->cnpj . '</tipos:Cnpj>';
				$xml .= '<tipos:InscricaoMunicipal>' . $this->config->inscricaomunicipal . '</tipos:InscricaoMunicipal>';
			$xml .= '</Prestador>';
			$xml .= '<NumeroNfse>' . $numNFSe . '</NumeroNfse>';
        $xml .= '</CancelarNfseEnvio>';

        //assinatura dos dados
        $signed = Signer::sign(
            $this->certificate,
            $xml,
            'CancelarNfseEnvio',
            'Id',
            $this->algorithm,
            $this->canonical
        );
        
        $request = Strings::clearXmlString($signed, true);
        $resp = $this->isValid($this->versao, $signed);
        $this->lastRequest = $request;
        $response = $this->sendRequest($request);
        return $response;
    }
    
}
