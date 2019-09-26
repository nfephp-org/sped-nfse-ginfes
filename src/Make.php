<?php
namespace NFePHP\NFSeGinfes;
/**
 * Classe para construção do xml da NFSe padrao Ginfes
 * Esta classe basica está estruturada para montar XML da NFSe para o
 * layout versão 3.00.
 *
 * @category  API
 * @package   NFePHP\NFSe\Ginfes
 * @copyright Copyright (c) 2008-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Marcio de Souza <marcio dot ti dot souza at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-ginfes for the canonical source repository
 */

use NFePHP\Common\Keys;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Strings;
use stdClass;
use RuntimeException;
use InvalidArgumentException;
use DOMElement;
use DateTime;

class Make
{
    /**
     * config class
     * @var \stdClass
     */
    public $config;
    /**
     * @var array
     */
    public $erros = [];
    /**
     * @var DOMElement
     */
    protected $NFSe;
    /**
     * @var DOMElement
     */
    protected $LoteRps;
    /**
     * @var String
     */
    public $Id;
    /**
     * @var DOMElement
     */
    protected $NumeroLote;
    /**
     * @var DOMElement
     */
    protected $Cnpj;
    /**
     * @var DOMElement
     */
    protected $InscricaoMunicipal;
    /**
     * @var DOMElement
     */
    protected $QuantidadeRps;
    /**
     * @var DOMElement
     */
    protected $InfRps;
    /**
     * @var DOMElement
     */
    protected $ListaRps;
    /**
     * @var DOMElement
     */
    protected $Rps;
    /**
     * @var DOMElement
     */
    protected $IdentificacaoRps;
    /**
     * @var DOMElement
     */
    protected $Servico;
    /**
     * @var DOMElement
     */
    protected $Valores;
    /**
     * @var DOMElement
     */
    protected $Prestador;
    /**
     * @var DOMElement
     */
    protected $Tomador;
    /**
     * @var string
     */
    public $xml;
    /**
     * @var \NFePHP\Common\DOMImproved
     */
    public $dom;

    public function __construct($configJson)
    {
        $this->config = json_decode($configJson);
        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
		$this->LoteRps = $this->dom->createElement("LoteRps");
		$this->InfRps = $this->dom->createElement("tipos:InfRps");
    }

    /**
     * Retorns the xml
     * @return xml
     */
    public function getXML()
    {
        if (empty($this->xml)) {
            $this->montaNFSe();
        }
        return $this->xml;
    }

    /**
     * buildNFSe
     * tag NFSe
     * @return DOMElement
     */

    protected function buildNFSe()
    {
        if (empty($this->NFSe)){
            $this->NFSe = $this->dom->createElement("EnviarLoteRpsEnvio");
            $this->NFSe->setAttribute("xmlns", "http://www.ginfes.com.br/servico_enviar_lote_rps_envio_v03.xsd");
        }
        return $this->NFSe;
    }

   /**
     * Call method of xml assembly. For compatibility only.
     * @return boolean
     */
   public function montaNFSe()
    {
        return $this->monta();
    }

    /**
     * NFSe xml mount method
     * this function returns TRUE on success or FALSE on error
     * The xml of the NFe must be retrieved by the getXML() function or
     * directly by the public property $xml
     * @return boolean
     */
    public function monta()
    {
        $this->erros = $this->dom->errors;
        if (count($this->erros) > 0) {
            return false;
        }
        //cria a tag raiz da NFSe
        $this->buildNFSe();
        
        $this->dom->appChild($this->LoteRps, $this->NumeroLote, 'Falta tag "LoteRps"');
        $this->dom->appChild($this->LoteRps, $this->Cnpj, 'Falta tag "LoteRps"');
        $this->dom->appChild($this->LoteRps, $this->InscricaoMunicipal, 'Falta tag "LoteRps"');
        $this->dom->appChild($this->LoteRps, $this->QuantidadeRps, 'Falta tag "LoteRps"');
		
        $this->ListaRps = $this->dom->createElement("tipos:ListaRps");
        $this->Rps = $this->dom->createElement("tipos:Rps");

        $this->dom->appChild($this->Rps, $this->InfRps, 'Falta tag "Rps"');
        $this->dom->appChild($this->ListaRps, $this->Rps, 'Falta tag "ListaRps"');
        $this->dom->appChild($this->LoteRps, $this->ListaRps, 'Falta tag "LoteRps"');
        $this->dom->appChild($this->NFSe, $this->LoteRps, 'Falta tag "NFSe"');
        // tag NFSe
        $this->dom->appendChild($this->NFSe);
        $this->xml = $this->dom->saveXML();
	}
	
    /**
     * Informações da NFSe 
     * tag NFSe/LoteRps
     * @param  stdClass $std
     * @return DOMElement
     */
    public function tagLoteRps(stdClass $std)
    {
        $this->Id = $std->Id;
        $this->LoteRps->setAttribute("xmlns:tipos", "http://www.ginfes.com.br/tipos_v03.xsd");
        $this->LoteRps->setAttribute("Id", $std->Id);
        $this->buildNumeroLote($std->Id);
        $this->buildCnpj($this->config->cnpj);
        $this->buildInscricaoMunicipal($this->config->inscricaomunicipal);
        $this->buildQuantidadeRps();
        return $this->LoteRps;
    }
	
    /**
     * Informações da NFSe 
     * tag NFSe/NumeroLote
     * @param  String
     * @return DOMElement
     */
    public function buildNumeroLote($Id)
    {
        $this->NumeroLote = $this->dom->createElement("tipos:NumeroLote",$Id);
        return $this->NumeroLote ;
	}

    /**
     * Informações da NFSe 
     * tag NFSe/Cnpj
     * @param  String
     * @return DOMElement
     */
    public function buildCnpj($cnpj)
    {
        $this->Cnpj = $this->dom->createElement("tipos:Cnpj",$cnpj);
        return $this->Cnpj ;
	}

    /**
     * Informações da NFSe 
     * tag NFSe/InscricaoMunicipal
     * @param  string
     * @return DOMElement
     */
    public function buildInscricaoMunicipal($inscricaoMunicipal)
    {
        $this->InscricaoMunicipal = $this->dom->createElement("tipos:InscricaoMunicipal",$inscricaoMunicipal);
        return $this->InscricaoMunicipal ;
	}

    /**
     * Informações da NFSe 
     * tag NFSe/QuantidadeRps
     * @return DOMElement
     */
    public function buildQuantidadeRps()
    {
        $this->QuantidadeRps = $this->dom->createElement("tipos:QuantidadeRps",1);
        return $this->QuantidadeRps ;
	}

    /**
     * Informações da NFSe 
     * tag NFSe/Identificacao
     * @param  stdClass $std
     * @return DOMElement
     */
    public function tagIdentificacao(stdClass $std)
    {
        $this->IdentificacaoRps = $this->dom->createElement("tipos:IdentificacaoRps");
		
        $Numero = $this->dom->createElement("tipos:Numero",$this->Id);
        $Serie = $this->dom->createElement("tipos:Serie",$std->Serie);
        $Tipo = $this->dom->createElement("tipos:Tipo",$std->Tipo);
        
        $this->dom->appChild($this->IdentificacaoRps, $Numero, 'Falta tag "IdentificacaoRps"');
        $this->dom->appChild($this->IdentificacaoRps, $Serie, 'Falta tag "IdentificacaoRps"');
        $this->dom->appChild($this->IdentificacaoRps, $Tipo, 'Falta tag "IdentificacaoRps"');

        $this->dom->appChild($this->InfRps, $this->IdentificacaoRps, 'Falta tag "InfRps"');
		
		$DataEmissao = $this->dom->createElement("tipos:DataEmissao", date("Y-m-d") . "T" . date("H:i:s")); 
		$NaturezaOperacao = $this->dom->createElement("tipos:NaturezaOperacao", $std->NaturezaOperacao);
		$RegimeEspecialTributacao = $this->dom->createElement("tipos:RegimeEspecialTributacao", $std->RegimeEspecialTributacao);
		$OptanteSimplesNacional = $this->dom->createElement("tipos:OptanteSimplesNacional", $std->OptanteSimplesNacional);
		$IncentivadorCultural = $this->dom->createElement("tipos:IncentivadorCultural", $std->IncentivadorCultural);
		$Status = $this->dom->createElement("tipos:Status", $std->Status);

        $this->dom->appChild($this->InfRps, $DataEmissao, 'Falta tag "InfRps"');
        $this->dom->appChild($this->InfRps, $NaturezaOperacao, 'Falta tag "InfRps"');
        $this->dom->appChild($this->InfRps, $RegimeEspecialTributacao, 'Falta tag "InfRps"');
        $this->dom->appChild($this->InfRps, $OptanteSimplesNacional, 'Falta tag "InfRps"');
        $this->dom->appChild($this->InfRps, $IncentivadorCultural, 'Falta tag "InfRps"');
        $this->dom->appChild($this->InfRps, $Status, 'Falta tag "InfRps"');
        
        return $this->InfRps ;
	}

    /**
     * Informações da NFSe 
     * tag NFSe/Servico
     * @param  stdClass $std
     * @return DOMElement
     */
    public function tagServico(stdClass $std)
    {
        $this->Servico = $this->dom->createElement("tipos:Servico");
        $this->Valores = $this->dom->createElement("tipos:Valores");

        $ValorServicos = $this->dom->createElement("tipos:ValorServicos",number_format($std->ValorServicos,2,'.',''));
        $ValorDeducoes = $this->dom->createElement("tipos:ValorDeducoes",number_format($std->ValorDeducoes,2,'.',''));
        $ValorPis = $this->dom->createElement("tipos:ValorPis",number_format($std->ValorPis,2,'.',''));
        $ValorCofins = $this->dom->createElement("tipos:ValorCofins",number_format($std->ValorCofins,2,'.',''));
        $ValorInss = $this->dom->createElement("tipos:ValorInss",number_format($std->ValorInss,2,'.',''));
        $ValorIr = $this->dom->createElement("tipos:ValorIr",number_format($std->ValorIr,2,'.',''));
        $ValorCsll = $this->dom->createElement("tipos:ValorCsll",number_format($std->ValorCsll,2,'.',''));
        $IssRetido = $this->dom->createElement("tipos:IssRetido",$std->IssRetido);
        $ValorIss = $this->dom->createElement("tipos:ValorIss",number_format($std->ValorIss,2,'.',''));
        $ValorIssRetido = $this->dom->createElement("tipos:ValorIssRetido",number_format($std->ValorIssRetido,2,'.',''));
        $OutrasRetencoes = $this->dom->createElement("tipos:OutrasRetencoes",number_format($std->OutrasRetencoes,2,'.',''));
        $BaseCalculo = $this->dom->createElement("tipos:BaseCalculo",number_format($std->BaseCalculo,2,'.',''));
        $Aliquota = $this->dom->createElement("tipos:Aliquota",number_format($std->Aliquota/100,2,'.',''));
        $ValorLiquidoNfse = $this->dom->createElement("tipos:ValorLiquidoNfse",number_format($std->ValorLiquidoNfse,2,'.',''));
        $DescontoIncondicionado = $this->dom->createElement("tipos:DescontoIncondicionado",number_format($std->DescontoIncondicionado,2,'.',''));
        $DescontoCondicionado = $this->dom->createElement("tipos:DescontoCondicionado",number_format($std->DescontoCondicionado,2,'.',''));

        $ItemListaServico = $this->dom->createElement("tipos:ItemListaServico",$std->ItemListaServico);
        $CodigoTributacaoMunicipio = $this->dom->createElement("tipos:CodigoTributacaoMunicipio",$std->CodigoTributacaoMunicipio);
        $Discriminacao = $this->dom->createElement("tipos:Discriminacao",$std->Discriminacao);
        $CodigoMunicipio = $this->dom->createElement("tipos:CodigoMunicipio",$this->config->codigomunicipio);

        $this->dom->appChild($this->Valores, $ValorServicos, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorDeducoes, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorPis, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorCofins, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorInss, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorIr, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorCsll, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $IssRetido, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorIss, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorIssRetido, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $OutrasRetencoes, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $BaseCalculo, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $Aliquota, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $ValorLiquidoNfse, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $DescontoIncondicionado, 'Falta tag "Valores"');
        $this->dom->appChild($this->Valores, $DescontoCondicionado, 'Falta tag "Valores"');

        $this->dom->appChild($this->Servico, $this->Valores, 'Falta tag "Servico"');
		
        $this->dom->appChild($this->Servico, $ItemListaServico, 'Falta tag "Servico"');
        $this->dom->appChild($this->Servico, $CodigoTributacaoMunicipio, 'Falta tag "Servico"');
        $this->dom->appChild($this->Servico, $Discriminacao, 'Falta tag "DServico"');
        $this->dom->appChild($this->Servico, $CodigoMunicipio, 'Falta tag "Servico"');
		
        $this->dom->appChild($this->InfRps, $this->Servico, 'Falta tag "InfRps"');
        $this->buildPrestador();
	}

    /**
     * Informações da NFSe 
     * tag NFSe/Prestador
     * @return DOMElement
     */
    public function buildPrestador()
    {
        $this->Prestador = $this->dom->createElement("tipos:Prestador");
        
        $Cnpj = $this->dom->createElement("tipos:Cnpj",$this->config->cnpj);
        $InscricaoMunicipal = $this->dom->createElement("tipos:InscricaoMunicipal",$this->config->inscricaomunicipal);
        $this->dom->appChild($this->Prestador, $Cnpj, 'Falta tag "Prestador"');
        $this->dom->appChild($this->Prestador, $InscricaoMunicipal, 'Falta tag "Prestador"');
        $this->dom->appChild($this->InfRps, $this->Prestador, 'Falta tag "InfRps"');
	}

    /**
     * Informações da NFSe 
     * tag NFSe/Tomador
     * @param  stdClass $std
     * @return DOMElement
     */
    public function tagTomador(stdClass $std)
    {
        $this->Tomador = $this->dom->createElement("tipos:Tomador");
        
        $IdentificacaoTomador = $this->dom->createElement("tipos:IdentificacaoTomador");
        $CpfCnpj = $this->dom->createElement("tipos:CpfCnpj");
        $tagsEndereco = $this->dom->createElement("tipos:Endereco");
        $Contato = $this->dom->createElement("tipos:Contato");
       
        $Cnpj = $this->dom->createElement("tipos:Cnpj",$std->Cnpj);
        $RazaoSocial = $this->dom->createElement("tipos:RazaoSocial",$std->RazaoSocial);
        $Endereco = $this->dom->createElement("tipos:Endereco",$std->Endereco);
        $Numero = $this->dom->createElement("tipos:Numero",$std->Numero);
        $Bairro = $this->dom->createElement("tipos:Bairro",$std->Bairro);
        $CodigoMunicipio = $this->dom->createElement("tipos:CodigoMunicipio",$std->CodigoMunicipio);
        $Uf = $this->dom->createElement("tipos:Uf",$std->Uf);
        $Cep = $this->dom->createElement("tipos:Cep",$std->Cep);
        $Email = $this->dom->createElement("tipos:Email",$std->Email);

        $this->dom->appChild($CpfCnpj, $Cnpj, 'Falta tag "CpfCnpj"');
        $this->dom->appChild($IdentificacaoTomador, $CpfCnpj, 'Falta tag "IdentificacaoTomador"');
        $this->dom->appChild($tagsEndereco, $Endereco, 'Falta tag "tagsEndereco"');
        $this->dom->appChild($tagsEndereco, $Numero, 'Falta tag "tagsEndereco"');
        $this->dom->appChild($tagsEndereco, $Bairro, 'Falta tag "tagsEndereco"');
        $this->dom->appChild($tagsEndereco, $CodigoMunicipio, 'Falta tag "tagsEndereco"');
        $this->dom->appChild($tagsEndereco, $Uf, 'Falta tag "tagsEndereco"');
        $this->dom->appChild($tagsEndereco, $Cep, 'Falta tag "tagsEndereco"');

        $this->dom->appChild($Contato, $Email, 'Falta tag "Contato"');

        $this->dom->appChild($this->Tomador, $IdentificacaoTomador, 'Falta tag "Tomador"');
        $this->dom->appChild($this->Tomador, $RazaoSocial, 'Falta tag "Tomador"');
        $this->dom->appChild($this->Tomador, $tagsEndereco, 'Falta tag "Tomador"');
        $this->dom->appChild($this->Tomador, $Contato, 'Falta tag "Tomador"');

        $this->dom->appChild($this->InfRps, $this->Tomador, 'Falta tag "InfRps"');
        $this->InfRps->appendChild($this->Tomador);

    }
}
