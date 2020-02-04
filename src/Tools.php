<?php

namespace NFePHP\NFSeGinfes;

/**
 * Class for comunications with NFSe webserver in Ginfes Standard
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
use NFePHP\Common\Validator;
use NFePHP\NFSeGinfes\Common\Signer;
use NFePHP\NFSeGinfes\Common\Tools as BaseTools;

class Tools extends BaseTools
{
    const ERRO_EMISSAO = 1;
    const SERVICO_NAO_CONCLUIDO = 2;

    protected $xsdpath;

    public function __construct($config, Certificate $cert)
    {
        parent::__construct($config, $cert);
        $path = realpath(
            __DIR__ . '/../storage/schemes'
        );
        $this->xsdpath = $path;
    }

    /**
     * Envia LOTE de RPS para emissão de NFSe (ASSINCRONO)
     * @param array $arps Array contendo de 1 a 50 RPS::class
     * @param string $lote Número do lote de envio
     * @return string
     * @throws \Exception
     */
    public function recepcionarLoteRps($arps, $lote)
    {
        $operation = 'RecepcionarLoteRpsV3';
        $no_of_rps_in_lot = count($arps);
        if ($no_of_rps_in_lot > 50) {
            throw new \Exception('O limite é de 50 RPS por lote enviado.');
        }
        $content = '';
        foreach ($arps as $rps) {
            $rps->config($this->config);
            $content .= $rps->render();
        }
        $contentmsg = "<EnviarLoteRpsEnvio xmlns=\"http://www.ginfes.com.br/servico_enviar_lote_rps_envio_v03.xsd\">"
            . "<LoteRps Id=\"$lote\" xmlns:tipos=\"http://www.ginfes.com.br/tipos_v03.xsd\">"
            . "<tipos:NumeroLote>$lote</tipos:NumeroLote>"
            . "<tipos:Cnpj>" . $this->config->cnpj . "</tipos:Cnpj>"
            . "<tipos:InscricaoMunicipal>" . $this->config->im . "</tipos:InscricaoMunicipal>"
            . "<tipos:QuantidadeRps>$no_of_rps_in_lot</tipos:QuantidadeRps>"
            . "<tipos:ListaRps>"
            . $content
            . "</tipos:ListaRps>"
            . "</LoteRps>"
            . "</EnviarLoteRpsEnvio>";

        $content = Signer::sign(
            $this->certificate,
            $contentmsg,
            'LoteRps',
            'Id',
            OPENSSL_ALGO_SHA1,
            [false, false, null, null],
            'EnviarLoteRpsEnvio'
        );
        $content = str_replace(['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'], '', $content);
        Validator::isValid($content, $this->xsdpath . "/servico_enviar_lote_rps_envio_v03.xsd");
        return $this->send($content, $operation);
    }

    /**
     * Consulta Lote RPS (SINCRONO) após envio com recepcionarLoteRps() (ASSINCRONO)
     * complemento do processo de envio assincono.
     * Que deve ser usado quando temos mais de um RPS sendo enviado
     * por vez.
     * @param string $protocolo
     * @return string
     *
     * Código de situação de lote de RPS
     * 1 – Não Recebido
     * 2 – Não Processado
     * 3 – Processado com Erro
     * 4 – Processado com Sucesso
     */
    public function consultarSituacaoLote($protocolo)
    {
        $operation = "ConsultarSituacaoLoteRpsV3";
        $content = "<ConsultarSituacaoLoteRpsEnvio "
            . "xmlns=\"http://www.ginfes.com.br/servico_consultar_situacao_lote_rps_envio_v03.xsd\" "
            . "xmlns:tipos=\"http://www.ginfes.com.br/tipos_v03.xsd\">"
            . "<Prestador>"
            . "<tipos:Cnpj>" . $this->config->cnpj . "</tipos:Cnpj>"
            . "<tipos:InscricaoMunicipal>" . $this->config->im . "</tipos:InscricaoMunicipal>"
            . "</Prestador>"
            . "<Protocolo>$protocolo</Protocolo>"
            . "</ConsultarSituacaoLoteRpsEnvio>";

        //assinatura dos dados
        $content = Signer::sign(
            $this->certificate,
            $content,
            'ConsultarSituacaoLoteRpsEnvio',
            '',
            OPENSSL_ALGO_SHA1,
            [false, false, null, null]
        );
        $content = str_replace(['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'], '', $content);
        Validator::isValid($content, $this->xsdpath . '/servico_consultar_situacao_lote_rps_envio_v03.xsd');
        return $this->send($content, $operation);
    }

    /**
     * Consulta Lote RPS (SINCRONO) após envio com recepcionarLoteRps() (ASSINCRONO)
     * complemento do processo de envio assincono.
     * Que deve ser usado quando temos mais de um RPS sendo enviado
     * por vez.
     * @param string $protocolo
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $operation = "ConsultarLoteRpsV3";
        $content = "<ConsultarLoteRpsEnvio "
            . "xmlns:tipos=\"http://www.ginfes.com.br/tipos_v03.xsd\" "
            . "xmlns=\"http://www.ginfes.com.br/servico_consultar_lote_rps_envio_v03.xsd\">"
            . "<Prestador>"
            . "<tipos:Cnpj>" . $this->config->cnpj . "</tipos:Cnpj>"
            . "<tipos:InscricaoMunicipal>" . $this->config->im . "</tipos:InscricaoMunicipal>"
            . "</Prestador>"
            . "<Protocolo>$protocolo</Protocolo>"
            . "</ConsultarLoteRpsEnvio>";

        //assinatura dos dados
        $content = Signer::sign(
            $this->certificate,
            $content,
            'ConsultarLoteRpsEnvio',
            '',
            OPENSSL_ALGO_SHA1,
            [false, false, null, null]
        );
        $content = str_replace(['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'], '', $content);
        Validator::isValid($content, $this->xsdpath . '/servico_consultar_lote_rps_envio_v03.xsd');
        return $this->send($content, $operation);
    }

    /**
     * Consulta NFSe emitidas em um periodo e por tomador (SINCRONO)
     * @param string $dini
     * @param string $dfim
     * @param string $tomadorCnpj
     * @param string $tomadorCpf
     * @param string $tomadorIM
     * @return string
     */
    public function consultarNfse($dini, $dfim, $tomadorCnpj = null, $tomadorCpf = null, $tomadorIM = null)
    {
        $operation = 'ConsultarNfseV3';
        $content = "<ConsultarNfseEnvio "
            . "xmlns=\"http://www.ginfes.com.br/servico_consultar_nfse_envio_v03.xsd\" "
            . "xmlns:tipos=\"http://www.ginfes.com.br/tipos_v03.xsd\">"
            . "<Prestador>"
            . "<tipos:Cnpj>" . $this->config->cnpj . "</tipos:Cnpj>"
            . "<tipos:InscricaoMunicipal>" . $this->config->im . "</tipos:InscricaoMunicipal>"
            . "</Prestador>"
            . "<PeriodoEmissao>"
            . "<DataInicial>$dini</DataInicial>"
            . "<DataFinal>$dfim</DataFinal>"
            . "</PeriodoEmissao>";

        if ($tomadorCnpj || $tomadorCpf) {
            $content .= "<Tomador>"
                . "<CpfCnpj>";
            if (isset($tomadorCnpj)) {
                $content .= "<Cnpj>$tomadorCnpj</Cnpj>";
            } else {
                $content .= "<Cpf>$tomadorCpf</Cpf>";
            }
            $content .= "</CpfCnpj>";
            if (isset($tomadorIM)) {
                $content .= "<InscricaoMunicipal>$tomadorIM</InscricaoMunicipal>";
            }
            $content .= "</Tomador>";
        }
        $content .= "</ConsultarNfseEnvio>";
        //assinatura dos dados
        $content = Signer::sign(
            $this->certificate,
            $content,
            'ConsultarNfseEnvio',
            '',
            OPENSSL_ALGO_SHA1,
            [false, false, null, null]
        );
        $content = str_replace(['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'], '', $content);
        Validator::isValid($content, $this->xsdpath . '/servico_consultar_nfse_envio_v03.xsd');
        return $this->send($content, $operation);
    }

    /**
     * Consulta NFSe por RPS (SINCRONO)
     * @param integer $numero
     * @param string $serie
     * @param integer $tipo
     * @return string
     */
    public function consultarNfsePorRps($numero, $serie, $tipo)
    {
        $operation = "ConsultarNfsePorRpsV3";
        $content = "<ConsultarNfseRpsEnvio "
            . "xmlns=\"http://www.ginfes.com.br/servico_consultar_nfse_rps_envio_v03.xsd\" "
            . "xmlns:tipos=\"http://www.ginfes.com.br/tipos_v03.xsd\">"
            . "<IdentificacaoRps>"
            . "<tipos:Numero>$numero</tipos:Numero>"
            . "<tipos:Serie>$serie</tipos:Serie>"
            . "<tipos:Tipo>$tipo</tipos:Tipo>"
            . "</IdentificacaoRps>"
            . "<Prestador>"
            . "<tipos:Cnpj>" . $this->config->cnpj . "</tipos:Cnpj>"
            . "<tipos:InscricaoMunicipal>" . $this->config->im . "</tipos:InscricaoMunicipal>"
            . "</Prestador>"
            . "</ConsultarNfseRpsEnvio>";
        //assinatura dos dados
        $content = Signer::sign(
            $this->certificate,
            $content,
            'ConsultarNfseRpsEnvio',
            '',
            OPENSSL_ALGO_SHA1,
            [false, false, null, null]
        );
        $content = str_replace(['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'], '', $content);
        Validator::isValid($content, $this->xsdpath . '/servico_consultar_nfse_rps_envio_v03.xsd');
        return $this->send($content, $operation);
    }

    /**
     * Solicita o cancelamento de NFSe (SINCRONO)
     * @param integer $numero
     * @param integer $codigo
     * @param string $id
     * @param string $versao
     * @return string
     */
    public function cancelarNfse($numero, $codigo = self::ERRO_EMISSAO, $id = null, $versao = "2")
    {
        if ($versao == "3") {
            return $this->cancelarNfseV3($numero, $codigo, $id);
        }
        return $this->cancelarNfseV2($numero);
    }

    /**
     * Solicita o cancelamento de NFSe (SINCRONO)
     * @param integer $numero
     * @param integer $codigo
     * @param string $id
     * @return string
     */
    public function cancelarNfseV3($numero, $codigo = self::ERRO_EMISSAO, $id = null)
    {
        /*
         * Versão 3.0 não funciona em Guarulhos
         */
        if (empty($id)) {
            $id = $numero;
        }
        $operation = 'CancelarNfseV3';
        $xml = "<p:CancelarNfseEnvio "
            . "xmlns:p=\"http://www.ginfes.com.br/servico_cancelar_nfse_envio_v03.xsd\" "
            . "xmlns:p1=\"http://www.ginfes.com.br/tipos_v03.xsd\">"
            . "<Pedido>"
            . "<p1:InfPedidoCancelamento Id=\"$id\">"
            . "<p1:IdentificacaoNfse>"
            . "<p1:Numero>$numero</p1:Numero>"
            . "<p1:Cnpj>" . $this->config->cnpj . "</p1:Cnpj>"
            . "<p1:InscricaoMunicipal>" . $this->config->im . "</p1:InscricaoMunicipal>"
            . "<p1:CodigoMunicipio>" . $this->config->cmun . "</p1:CodigoMunicipio>"
            . "</p1:IdentificacaoNfse>"
            . "<p1:CodigoCancelamento>$codigo</p1:CodigoCancelamento>"
            . "</p1:InfPedidoCancelamento>"
            . "</Pedido>"
            . "</p:CancelarNfseEnvio>";

        $content = Signer::sign(
            $this->certificate,
            $xml,
            'InfPedidoCancelamento',
            'Id',
            OPENSSL_ALGO_SHA1,
            [false, false, null, null],
            'Pedido'
        );
        $content = Signer::sign(
            $this->certificate,
            $content,
            'Pedido',
            '',
            OPENSSL_ALGO_SHA1,
            [false, false, null, null],
            'CancelarNfseEnvio'
        );
        $content = str_replace(['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'], '', $content);
        Validator::isValid($xml, $this->xsdpath . '/servico_cancelar_nfse_envio_v03.xsd');
        $response = $this->send($content, $operation);
        return $response;
    }

    /**
     * Solicita o cancelamento de NFSe (SINCRONO)
     * @param integer $numero
     * @param integer $codigo
     * @param string $id
     * @return string
     */
    public function cancelarNfseV2($numero)
    {
        /*
         * Versão 2.0 funciona em Guarulhos
         */
        $operation = 'CancelarNfse';
        $xml = "<CancelarNfseEnvio "
            . "xmlns=\"http://www.ginfes.com.br/servico_cancelar_nfse_envio\" "
            . "xmlns:tipos=\"http://www.ginfes.com.br/tipos\">"
            . "<Prestador>"
            . "<tipos:Cnpj>" . $this->config->cnpj . "</tipos:Cnpj>"
            . "<tipos:InscricaoMunicipal>" . $this->config->im . "</tipos:InscricaoMunicipal>"
            . "</Prestador>"
            . "<NumeroNfse>$numero</NumeroNfse>"
            . "</CancelarNfseEnvio>";

        $content = Signer::sign(
            $this->certificate,
            $xml,
            'CancelarNfseEnvio',
            '',
            OPENSSL_ALGO_SHA1,
            [false, false, null, null]
        );
        $content = str_replace(['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'], '', $content);
        Validator::isValid($content, $this->xsdpath . '/servico_cancelar_nfse_envio_v02.xsd');
        $this->setVersion("2");
        $response = $this->send($content, $operation);
        return $response;
    }

}