<?php

namespace NFePHP\NFSeGinfes\Common;

/**
 * Class for identification of eletronic documents in xml
 * used for comunications an convertion to other formats
 *
 * @category  library
 * @package   NFePHP\NFSeGinfes
 * @copyright NFePHP Copyright (c) 2020
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Cleiton Perin <cperin20 at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-ginfes for the canonical source repository
 */

use DOMDocument;
use stdClass;
use InvalidArgumentException;
use NFePHP\Common\Validator;

class Standardize
{
    /**
     * @var string
     */
    public $node = '';
    /**
     * @var string
     */
    public $json = '';
    /**
     * @var array
     */
    public $rootTagList = [
        'CancelarNfseEnvio',
        'ConsultarLoteRpsEnvio',
        'ConsultarNfseEnvio',
        'ConsultarNfseFaixaEnvio',
        'ConsultarNfseRpsEnvio',
        'EnviarLoteRpsEnvio',
        'GerarNfseEnvio',
        'CancelarNfseResposta',
        'ConsultarSituacaoLoteRpsResposta',
        'ConsultarNfseResposta',
        'ConsultarNfseFaixaResposta',
        'ConsultarNfseRpsResposta',
        'EnviarLoteRpsResposta',
        'ConsultarLoteRpsResposta',
        'GerarNfseEnvio',
        'GerarNfseResposta',
        'RPS'
    ];

    public function __construct($xml = null)
    {
        $this->toStd($xml);
    }

    /**
     * Identify node and extract from XML for convertion type
     * @param string $xml
     * @return string identificated node name
     * @throws InvalidArgumentException
     */
    public function whichIs($xml)
    {
        if (!Validator::isXML($xml)) {
            throw new InvalidArgumentException(
                "O argumento passado não é um XML válido."
            );
        }
        $xml = $this->removeNS($xml);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);
        foreach ($this->rootTagList as $key) {
            $node = !empty($dom->getElementsByTagName($key)->item(0))
                ? $dom->getElementsByTagName($key)->item(0)
                : '';
            if (!empty($node)) {
                $this->node = $dom->saveXML($node);
                return $key;
            }
        }
        throw new InvalidArgumentException(
            "Este xml não pertence ao projeto NFSe Ginfes."
        );
    }

    /**
     * Returns extract node from XML
     * @return string
     */
    public function __toString()
    {
        return $this->node;
    }

    /**
     * Returns stdClass converted from xml
     * @param string $xml
     * @return stdClass
     */
    public function toStd($xml = null)
    {
        if (!empty($xml)) {
            $this->whichIs($xml);
        }
        $sxml = simplexml_load_string($this->node);
        $this->json = str_replace(
            '@attributes',
            'attributes',
            json_encode($sxml, JSON_PRETTY_PRINT)
        );
        return json_decode($this->json);
    }

    /**
     * Retruns JSON string form XML
     * @param string $xml
     * @return string
     */
    public function toJson($xml = null)
    {
        if (!empty($xml)) {
            $this->toStd($xml);
        }
        return $this->json;
    }

    /**
     * Returns array from XML
     * @param string $xml
     * @return array
     */
    public function toArray($xml = null)
    {
        if (!empty($xml)) {
            $this->toStd($xml);
        }
        return json_decode($this->json, true);
    }

    /**
     * Remove all namespaces from XML
     * @param string $xml
     * @return string
     */
    protected function removeNS($xml)
    {
        $sxe = new \SimpleXMLElement($xml);
        $dom_sxe = dom_import_simplexml($sxe);
        $dom = new \DOMDocument('1.0');
        $dom_sxe = $dom->importNode($dom_sxe, true);
        $dom_sxe = $dom->appendChild($dom_sxe);
        $element = $dom->childNodes->item(0);
        foreach ($sxe->getDocNamespaces() as $name => $uri) {
            $element->removeAttributeNS($uri, $name);
        }
        $xml = $dom->saveXML();
        if (stripos($xml, 'xmlns=') !== false) {
            $xml = preg_replace('~[\s]+xmlns=[\'"].+?[\'"]~i', null, $xml);
            $xml = str_replace('default:', '', $xml);
            $xml = preg_replace('~[\s]+xmlns:default=[\'"].+?[\'"]~i', null, $xml);
        }
        return $xml;
    }
}