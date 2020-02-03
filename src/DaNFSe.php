<?php

namespace NFePHP\NFSeGinfes;

/**
 * Esta classe gera do PDF do NFSe, conforme regras e estruturas
 * estabelecidas pela prefeitura.
 *
 * @category  Library
 * @package   nfephp-org/sped-nfse-ginfes
 * @name      Danfse.php
 * @copyright 2009-2019 NFePHP
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @link      http://github.com/nfephp-org/sped-nfse-ginfes for the canonical source repository
 * @author    Marcio Souza <marcio.ti.souza@gmail.com>
 */

use NFePHP\DA\Legacy\Dom;
use NFePHP\NFSeGinfes\Legacy\Common;
use NFePHP\NFSeGinfes\Legacy\Pdf;
use RuntimeException;
use InvalidArgumentException;

class DaNFSe extends Common 
{
    
    protected $cxml;
    protected $config ;
    protected $pdf ;
	protected $gif_brasao_prefeitura;
	protected $gif_logo_empresa;
    protected $B=0;
    protected $I=0;
    protected $U=0;
    protected $HREF='';
    protected $ALIGN='';
	
	protected $naturezaoperacao = [
								   '1 - Tributação no municipio',
								   '2 - Tributação fora do municipio',
								   '3 - Isenção',
							       '4 - Imune',
								   '5 - Exigibilidade suspensa por decisão judicial',
								   '6 - Exigibilidade suspensa por procedimento administrativo.'
								  ];
	protected $regimeespecial = [
								 '0 - Nenhum',
								 '1 - Microempresa Municipal',
								 '2 - Estimativa',
								 '3 - Sociedade de Profissionais',
								 '4 - Cooperativa',
								 '5 - Microempresário Individual (MEI)',
								 '6 - Microempresário e EPP'
								];
    
    public function __construct(
		$configJson,
		$gif_logo_empresa
    ) {
		$this->config = json_decode($configJson);
		if ( $this->config->codigomunicipio == '3518800' ) { // Guarulhos
		}
		$this->gif_brasao_prefeitura = dirname(__dir__).'/images/'.$this->config->codigomunicipio.'/logo.gif';
		$this->gif_logo_empresa = $gif_logo_empresa;
    }

    /**
     * 
     * @param type $arquivo_pdf_destino
     */
    public function printNFSe(
		$xml,
		$aParser,
		$arquivo_pdf_destino
	) {
        $doc = new Dom();
        $doc->loadXML($xml);

        $numNfse = $doc->getElementsByTagName("Numero")->item(0)->nodeValue;        
        // Instanciation of inherited class
        $this->pdf = new Pdf('P', 'cm', 'A4');
		$margSup = 7;
		$margEsq = 7;
		$margDir = 7;
		// posição inicial do relatorio
		$xInic = 7;
		$yInic = 7;
		$maxW = 210;
		$maxH = 297;
        // estabelece contagem de paginas
        $this->pdf->AliasNbPages();
        // fixa as margens
        $this->pdf->SetMargins($margEsq, $margSup, $margDir);
        $this->pdf->SetDrawColor(0, 0, 0);
        $this->pdf->SetFillColor(255, 255, 255);
        // inicia o documento
		$this->pdf->state = 1;

        $this->pdf->SetAutoPageBreak(1, 1);
        $this->pdf->AddPage('P', 'A4');
        $this->pdf->setMargins(0, 0, 0);
        $this->pdf->SetFont('Arial', '', 8);

		$this->Header($doc, $aParser, $xml);

        $maxLineSize = 80;
		$discriminacao = $doc->getElementsByTagName("Discriminacao")->item(0)->nodeValue;
        $servico = explode(";", $discriminacao);
        $lines = count($servico);
        $y = $this->pdf->getY()+0.2;
        $this->pdf->SetXY(0.5, $y);
        $this->pdf->setX(2.0);
        $this->pdf->SetFont('Arial', '', 10);
        $zNum = count($servico) - 1;
        if ($zNum <= 15) {
            for ($z = 0; $z <= $zNum; $z++) {
                $this->pdf->setX(0.5);
                $this->Cell(20, 0.4, trim($servico[$z]), 0, 1);
            }
            for ($z = 0; $z < (15-$zNum); $z++) {
                $this->pdf->setX(0.5);
                $this->Cell(20, 0.4, "", 0, 1);
            }
        } else {
            for ($z = 0; $z <= ($zNum); $z++) {
                $this->pdf->setX(0.5);
                $this->Cell(20, 0.4, trim($servico[$z]), 0, 1);
            }

            for ($j = 0; $j < 44 - $zNum; $j++) {
                if ($j == 43 - $zNum) {
                    $this->pdf->setX(0.5);
                    $this->Cell(20, 0.4, "CONTINUA NA PROXIMA PAGINA", 0, 1, "C");
                } else {
                    $this->pdf->setX(0.5);
                    $this->Cell(20, 0.4, " ", 0, 1);
                }
            }

            $this->pdf->setX(0.5);
            $this->Cell(20, 0.4, trim($servico[$zNum]), 0, 1);
        }
        
        if ($lines - count($servico) > 5) {
            $addLine = (($lines - count($servico)) * 0.4);
        } else {
            $addLine = 2.0;
        }
        // CODIGO DO SERVICO
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->SetFillColor(150, 150, 150);
        $y = $this->pdf->getY();
        $this->pdf->SetXY(0.5, $y);
        $this->Cell(20, 0.7, "Código do Serviço / Atividade", 1, 1, "C", 1);
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->setX(0.5);
        $OrgaoGerador = $doc->getElementsByTagName("OrgaoGerador")->item(0);
        $codmunicipio = $this->getTagValue($OrgaoGerador, "CodigoMunicipio");
        $itemlista = $doc->getElementsByTagName("ItemListaServico")->item(0)->nodeValue;
        $codtributario = $doc->getElementsByTagName("CodigoTributacaoMunicipio")->item(0)->nodeValue;
		$descricao = $this->getCodTributario($codmunicipio, $itemlista, $codtributario);
        $this->Cell(20, 0.6, $doc->getElementsByTagName("ItemListaServico")->item(0)->nodeValue . " / " . $doc->getElementsByTagName("CodigoTributacaoMunicipio")->item(0)->nodeValue . " - " . $descricao, 1, 1, 'C', 0);
        // FIM CODIGO DO SERVIÇO
        // OBRAS
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->SetFillColor(150, 150, 150);
        $this->pdf->SetX(0.5);
        $this->Cell(20, 0.7, "Detalhamento Específico da Construção Civil", 1, 1, "C", 1);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->setX(0.5);
        $this->Cell(5, 0.6, 'Código da Obra', 1, 0, 'C', 1);
        $this->Cell(5, 0.6, "", 1, 0, 'C', 0);
        $this->Cell(5, 0.6, 'Código ART', 1, 0, 'C', 1);
        $this->Cell(5, 0.6, "", 1, 1, 'C', 0);
        // FIM OBRAS
        
        // TRIBUTOS FEDERAIS
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->SetFillColor(150, 150, 150);
        $this->pdf->SetX(0.5);
        $this->Cell(20, 0.7, "Tributos Federais", 1, 1, "C", 1);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->setX(0.5);
        $this->Cell(2, 0.6, 'PIS', 1, 0, 'C', 1);
        $this->Cell(2, 0.6, number_format($doc->getElementsByTagName("ValorPis")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $this->Cell(2, 0.6, 'COFINS', 1, 0, 'C', 1);
        $this->Cell(2, 0.6, number_format($doc->getElementsByTagName("ValorCofins")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $this->Cell(2, 0.6, 'IR (R$)', 1, 0, 'C', 1);
        $this->Cell(2, 0.6, number_format($doc->getElementsByTagName("ValorIr")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $this->Cell(2, 0.6, 'INSS (R$)', 1, 0, 'C', 1);
        $this->Cell(2, 0.6, number_format($doc->getElementsByTagName("ValorInss")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $this->Cell(2, 0.6, 'CSLL (R$)', 1, 0, 'C', 1);
        $this->Cell(2, 0.6, number_format($doc->getElementsByTagName("ValorCsll")->item(0)->nodeValue, 2, ",", "."), 1, 1, 'C', 0);
        // FIM TRIBUTOS FEDERAIS
        
        // DETALHAMENTOS, RETENÇÕES E CALCULOS ISSQN
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetFillColor(150, 150, 150);
        $this->pdf->SetX(0.5);
        $this->Cell(7.5, 0.7, "Detalhamento de valores - Prestador dos Serviços", 1, 0, "C", 1);
        $this->Cell(5.0, 0.7, "Outras Retenções", 1, 0, "C", 1);
        $this->Cell(7.5, 0.7, "Cálculo do ISSQN devido no Município", 1, 1, "C", 1);
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->setX(0.5);
        $this->Cell(4.5, 0.7, 'Valor dos Serviços R$', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("ValorServicos")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $this->Cell(5.0, 0.7, 'Natureza Operação', 1, 0, 'C', 1);
        $this->Cell(4.5, 0.7, 'Valor dos Serviços R$', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("ValorServicos")->item(0)->nodeValue, 2, ",", "."), 1, 1, 'C', 0);
        $this->pdf->setX(0.5);
        $this->Cell(4.5, 0.7, '(-) Desconto Incondicionado', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("DescontoIncondicionado")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $this->Cell(5.0, 0.7, $this->naturezaoperacao[$doc->getElementsByTagName("NaturezaOperacao")->item(0)->nodeValue-1], 1, 0, 'C', 0);
        $this->Cell(4.5, 0.7, '(-) Deduções permitidas em lei', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("DescontoCondicionado")->item(0)->nodeValue, 2, ",", "."), 1, 1, 'C', 0); //GABRIEL / HUGO VERIFICAR CONTEUDO
        $this->pdf->setX(0.5);
        $this->Cell(4.5, 0.7, '(-) Desconto Condicionado', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("DescontoCondicionado")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $this->Cell(5.0, 0.7, 'Regime Especial Tributação', 1, 0, 'C', 1);
        $this->Cell(4.5, 0.7, '(-) Desconto Incondicionado', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("DescontoIncondicionado")->item(0)->nodeValue, 2, ",", "."), 1, 1, 'C', 0);
        $this->pdf->setX(0.5);
        $this->Cell(4.5, 0.7, 'Retenções Federais', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format(0, 2, ",", "."), 1, 0, 'C', 0); //
        $this->Cell(5.0, 0.7, $this->regimeespecial[$doc->getElementsByTagName("RegimeEspecialTributacao")->item(0)->nodeValue], 1, 0, 'C', 0);
        $this->Cell(4.5, 0.7, 'Base de Cálculo', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("BaseCalculo")->item(0)->nodeValue, 2, ",", "."), 1, 1, 'C', 0);
        $this->pdf->setX(0.5);
        $this->Cell(4.5, 0.7, 'Outras Retenções', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("OutrasRetencoes")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $this->Cell(5.0, 0.7, 'Opção Simples Nacional', 1, 0, 'C', 1);
        $this->Cell(4.5, 0.7, '(x) Alíquota %', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("Aliquota")->item(0)->nodeValue*100, 2, ",", "."), 1, 1, 'C', 0);
        $this->pdf->setX(0.5);
        $this->Cell(4.5, 0.7, '(-) ISS Retido', 1, 0, 'C', 1);
        $this->Cell(3.0, 0.7, number_format($doc->getElementsByTagName("ValorIssRetido")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        if ($doc->getElementsByTagName("OptanteSimplesNacional")->item(0)->nodeValue == 0) {
            $optanteSimplesNacional = $doc->getElementsByTagName("OptanteSimplesNacional")->item(0)->nodeValue . " - Não";
        } else {
            $optanteSimplesNacional = $doc->getElementsByTagName("OptanteSimplesNacional")->item(0)->nodeValue . " - Sim";
        }
        $this->Cell(5.0, 0.7, $optanteSimplesNacional, 1, 0, 'C', 1);
        $this->Cell(4.5, 0.7, 'ISS a Reter', 1, 0, 'C', 1);
        if ($doc->getElementsByTagName("IssRetido")->item(0)->nodeValue == 1) {
			$this->Cell(3.0, 0.7, '(X) Sim ( ) Não', 1, 1, 'C', 0);
		}else{
			$this->Cell(3.0, 0.7, '( ) Sim (X) Não', 1, 1, 'C', 0);
		}
        
        $this->pdf->setX(0.5);
        $this->Cell(4.5, 1, '(=) Valor Líquido   R$', 1, 0, 'C', 1);
        $this->Cell(3.0, 1, number_format($doc->getElementsByTagName("ValorLiquidoNfse")->item(0)->nodeValue, 2, ",", "."), 1, 0, 'C', 0);
        $x = $this->pdf->getX();
        $y = $this->pdf->getY();
        $this->Cell(5.0, 0.5, 'Incentivador Cultural', 1, 1, 'C', 1);
        $this->pdf->setX($x);
        if ($doc->getElementsByTagName("IncentivadorCultural")->item(0)->nodeValue == 2) {
            $incentivadorCultural = $doc->getElementsByTagName("IncentivadorCultural")->item(0)->nodeValue . " - Não";
        } else {
            $incentivadorCultural = $doc->getElementsByTagName("IncentivadorCultural")->item(0)->nodeValue . " - Sim";
        }
        $this->Cell(5.0, 0.5, $incentivadorCultural, 1, 0, 'C', 0);
        $this->pdf->setXY($x + 5, $y);
        $this->Cell(4.5, 1, '(=) Valor do ISS     R$', 1, 0, 'C', 1);
        $this->Cell(3.0, 1, number_format($doc->getElementsByTagName("ValorIss")->item(0)->nodeValue, 2, ",", "."), 1, 1, 'C', 0);

        $municipio = strtolower($this->getCidade($this->getTagValue($OrgaoGerador, "CodigoMunicipio")));

        $this->pdf->setX(0.5);
        $this->Cell(1.5, 0.8, 'Avisos', 1, 0, 'C', 0);
        $this->pdf->SetFont('Arial', '', 6);
        $this->Cell(9.5, 0.5,"1- Uma via desta Nota Fiscal será enviada através do e-mail fornecido pelo Tomador dos Serviços.", 0, 0, 'C', 0 ) ;
        $this->pdf->setX(0.5);
		$this->Cell(15.7, 1.0,"2- A autenticidade desta Nota Fiscal poderá ser verificada no site, $municipio.ginfes.com.br com a utilização do Código de Verificação", 0, 0, 'C', 0 ) ;
        
        $y = $this->pdf->getY();
        $this->pdf->Line(0.5, $y, 0.5, $y+0.8); // lateral esquerda
        $this->pdf->Line(20.5, $y, 20.5, $y+0.8); // lateral direita
        $this->pdf->Line(0.5, $y+0.8, 20.5, $y+0.8); // linha do rodape final

        $this->pdf->Output($arquivo_pdf_destino,'F');
    } 

    /**
     * 
     */
    public function Header($doc,$aParser,$xml) {

        //DESENHA AS BORDAS DO HEADER
        $this->pdf->Line(0.5, 1, 20.5, 1); // linha superior
        $this->pdf->Line(0.5, 1, 0.5, 20.2); // lateral esquerda 
        $this->pdf->Line(3.7, 1, 3.7, 3.1); // lateral direita do logo da prefeitura 
        $this->pdf->Line(17.1, 1, 17.1, 3.1); // lateral esquerda do Numero da NFSe 
        $this->pdf->Line(20.5, 1, 20.5, 20.2); // lateral direita
        // CABEÇALHO
        // LOGO
        if (is_file($this->gif_brasao_prefeitura)){
            $this->pdf->Image($this->gif_brasao_prefeitura, 1.1, 1.2, 2);
        }

        // Nome da Prefeitura
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetXY(3.7, 1.2);

        $OrgaoGerador = $doc->getElementsByTagName("OrgaoGerador")->item(0) ;
        $municipio = strtoupper($this->getCidade($this->getTagValue($OrgaoGerador, "CodigoMunicipio")));
        $this->Cell(13.4, 0.7, "PREFEITURA MUNICIPAL DE $municipio", 0, 0, "C", 0);

		// Departamento da Prefeitura
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetXY(3.7, 1.7);
        $this->Cell(13.4, 0.7, "SECRETARIA DE FINANÇAS", 0, 0, "C", 0);

		// Titulo do Documento
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetXY(3.7, 2.2);
        $this->Cell(13.4, 0.7, "NOTA FISCAL ELETRÔNICA DE SERVIÇO - NFS-e", 0, 0, "C", 0);

        // Titulo da NFSe
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetXY(17.1, 1.2);
        $this->Cell(3.4,0.7,"Número da NFS-e" , 0, 0, "C", 0);

        // No. da NFSe
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetXY(17.1, 1.7);
        $this->Cell(3.4,0.7,$doc->getElementsByTagName("Numero")->item(0)->nodeValue, 0, 0, "C", 0);

        // No. da Página
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->SetXY(17.1, 2.2);
        $this->Cell(3.4,0.7,"Pag. " . $this->pdf->PageNo() . "/{nb}", 0, 0, "C", 0);

        // CAMPO DE INFORMAÇÕES
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetFillColor(230, 230, 230);

        //LINHA 1
        $this->pdf->SetXY(0.5, 3.1);
        $this->Cell(3.2, 0.6, "Data e Hora da Emissão", 1, 0, "C", 1);
        $InfNfse = $doc->getElementsByTagName("InfNfse")->item(0);
        $DataEmissao = $this->pConvertTime($this->getTagValue($InfNfse, "DataEmissao"));
        $DataEmissao = date("d/m/Y H:i:s", $this->pConvertTime($this->getTagValue($InfNfse, "DataEmissao")));
             
        $this->Cell(3.2, 0.6, $DataEmissao, 1, 0, "C", 0);
        $this->Cell(3.4, 0.6, "Competência", 1, 0, "C", 1);
		
        $Competencia = Substr($DataEmissao,0,2).'/'.Substr($DataEmissao,3,2).'/'.Substr($DataEmissao,6,4) ;

        $this->Cell(3.4, 0.6, $Competencia, 1, 0, "C");
        $this->Cell(3.4, 0.6, "Código de Verificação", 1, 0, "C", 1);
        $this->Cell(3.4, 0.6, $this->getTagValue($InfNfse,"CodigoVerificacao"), 1, 1, "C"); 

        //LINHA 2
        $IdentificacaoRps = $doc->getElementsByTagName("IdentificacaoRps")->item(0) ;
        $this->pdf->SetXY(0.5, 3.7);
        $this->Cell(3.2, 0.6, "Número RPS", 1, 0, "C", 1);
        $this->Cell(3.2, 0.6, $this->getTagValue($IdentificacaoRps, "Numero" ), 1, 0, "C");
        $this->Cell(3.4, 0.6, "No.da NFS-e Substituída", 1, 0, "C", 1);
        $NFSubst = "";
        $this->Cell(3.4, 0.6, $NFSubst, 1, 0, "C");

        $OrgaoGerador = $doc->getElementsByTagName("OrgaoGerador")->item(0) ;

        $this->Cell(3.4, 0.6, "Local da Prestação", 1, 0, "C", 1);
        $this->Cell(3.4, 0.6, strtoupper($this->getCidade($this->getTagValue($OrgaoGerador, "CodigoMunicipio"))) . " - " . $this->getTagValue($OrgaoGerador, "Uf"), 1, 1, "C");
        //FIM CAMPO DE INFORMAÇÕES
        // FIM CABEÇALHO
        // INICIO PRESTADOR
        $this->pdf->SetFillColor(150, 150, 150);
        $this->pdf->SetXY(0.5, 4.3);
        $this->pdf->SetFont('Arial', '', 12);
        $this->Cell(20, 0.7, "Dados do Prestador de Serviços", 1, 1, "C", 1);

        // LOGO
        
        if (is_file($this->gif_logo_empresa)){
            $this->pdf->Image($this->gif_logo_empresa, 0.7, 5.6, 2.8);
        }
        $PrestadorServico = $doc->getElementsByTagName("PrestadorServico")->item(0) ;
        // DADOS DOS PRESTADOR
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->SetFont('Arial', '', 7);
        $this->pdf->setXY(3.7, 5);
        $this->Cell(2.6, 0.5, "Nome / Razão Social", 1, 0, "C", 1);
        $this->Cell(14.2, 0.5, $this->getTagValue($PrestadorServico, "RazaoSocial"), 1, 1);

        $this->pdf->setXY(3.7, 5.5);
        $this->Cell(2.6, 0.5, "Nome Fantasia", 1, 0, "C", 1);
        $this->Cell(14.2, 0.5, $this->getTagValue($PrestadorServico, "NomeFantasia"), 1, 1);

        $this->pdf->setXY(3.7, 6);
        $this->Cell(2.6, 0.5, "CPF/CNPJ", 1, 0, "C", 1);

        $CnpjPrestador = $this->getTagValue($PrestadorServico, "Cnpj");
        $CnpjPrestador = substr($CnpjPrestador, 0, 2) . "." . substr($CnpjPrestador, 2, 3) . "." . substr($CnpjPrestador, 5, 3) . "/" . substr($CnpjPrestador, 8, 4) . "-" . substr($CnpjPrestador, 12, 2);

		$enderecoCompleto =  $PrestadorServico->getElementsByTagName("Endereco")->item(0);;
        $this->Cell(3, 0.5, $CnpjPrestador, 1);
        $this->Cell(2.3, 0.5, "Inscrição Municipal", 1, 0, "C", 1);
        $this->Cell(2.6, 0.5, $this->getTagValue($PrestadorServico, "InscricaoMunicipal"), 1);
        $this->Cell(1.2, 0.5, "Município", 1, 0, "C", 1);
        $this->Cell(5.1, 0.5, strtoupper($this->getCidade($this->getTagValue($enderecoCompleto, "CodigoMunicipio"))) . " - " . $this->getTagValue($enderecoCompleto, "Uf"), 1, 1);

		$endereco =  $this->getTagValue($enderecoCompleto, "Endereco" ) ;
        $numero = $this->getTagValue($enderecoCompleto, "Numero") ;
        $bairro = $this->getTagValue($enderecoCompleto, "Bairro") ;
        $complemento = $this->getTagValue($enderecoCompleto, "Complemento") ;
        $cep = Subs($this->getTagValue($enderecoCompleto, "Cep"),0,5).'-'.Subs($this->getTagValue($enderecoCompleto, "Cep"),5,3) ;

        $endereco = $endereco. ", " . $numero ;
        $endereco.= " - " . $bairro . " - CEP: " . $cep ;

		$telefone =  $this->getTagValue($PrestadorServico , "Telefone");
		$email =  $this->getTagValue($PrestadorServico , "Email");

        $this->pdf->setXY(3.7, 6.5);
        $this->Cell(2.6, 0.5, "Endereço e CEP", 1, 0, "C", 1);

        $this->Cell(14.2, 0.5, $endereco, 1, 1);

        $this->pdf->setXY(3.7, 7);
        $this->Cell(2.6, 0.5, "Complemento", 1, 0, "C", 1);
        $this->Cell(3, 0.5, $complemento, 1);
        $this->Cell(2.3, 0.5, "Telefone", 1, 0, "C", 1);
        $this->Cell(2.6, 0.5, $telefone, 1);
        $this->Cell(1.2, 0.5, "E-mail", 1, 0, "C", 1);
        $this->pdf->SetFont('Arial', '', 5);
        $this->Cell(5.1, 0.5, $email, 1, 1);
        // FIM DADOS PRESTADOR
        // FIM PRESTADOR
        
        // INICIO TOMADOR

        $this->pdf->SetFillColor(150, 150, 150);
        $this->pdf->SetXY(0.5, 7.5);
        $this->pdf->SetFont('Arial', '', 12);
        $this->Cell(20, 0.7, "Dados do Tomador de Serviços", 1, 1, "C", 1);

        // DADOS TOMADOR
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->SetFont('Arial', '', 7);

        $Tomador = $doc->getElementsByTagName("TomadorServico")->item(0) ;
        $RazaoSocial = $this->getTagValue($Tomador, "RazaoSocial");

        $this->pdf->SetXY(0.5, 8.2);
        $this->Cell(3.2, 0.5, "Razão Social/Nome", 1, 0, "C", 1);
        $this->Cell(16.8, 0.5, $RazaoSocial, 1, 1);

        $this->pdf->SetXY(0.5, 8.7);
        $this->Cell(3.2, 0.5, "CPF/CNPJ", 1, 0, "C", 1);

        $DocTomador =  $this->getTagValue($Tomador, "Cnpj");
        if (strlen($DocTomador) <> "") {
            $DocTomador = substr($DocTomador, 0, 2) . "." . substr($DocTomador, 2, 3) . "." . substr($DocTomador, 5, 3) . "/" . substr($DocTomador, 8, 4) . "-" . substr($DocTomador, 12, 2);
        }else{
			$DocTomador = $this->getTagValue($Tomador, "Cpf");
            if (strlen($DocTomador) <> "") {
                $DocTomador = substr($DocTomador, 0, 3) . "." . substr($DocTomador, 3, 3) . "." . substr($DocTomador, 6, 3) . "-" . substr($DocTomador, 9, 2);
            }else{
                $DocTomador = "";
            }
        }
		$enderecoCompleto =  $Tomador->getElementsByTagName("Endereco")->item(0);
        $municipio = strtoupper($this->getCidade($this->getTagValue($enderecoCompleto, "CodigoMunicipio")));
        $this->Cell(3, 0.5, $DocTomador, 1);
        $this->Cell(2.6, 0.5, "Inscrição Municipal", 1, 0, "C", 1);
        $this->Cell(2.3, 0.5, "", 1);
        $this->Cell(1.5, 0.5, "Município", 1, 0, "C", 1);
        $this->Cell(7.4, 0.5, 	$municipio . " - " .  $this->getTagValue($Tomador, "Uf"), 1, 1);

        $this->pdf->SetXY(0.5, 9.2);
        $this->Cell(3.2, 0.5, "Endereço e CEP", 1, 0, "C", 1);
		
		$endereco =  $this->getTagValue($enderecoCompleto, "Endereco" ) ;
        $numero = $this->getTagValue($enderecoCompleto, "Numero") ;
        $bairro = $this->getTagValue($enderecoCompleto, "Bairro") ;
        $cep = Subs($this->getTagValue($enderecoCompleto, "Cep"),0,5).'-'.Subs($this->getTagValue($enderecoCompleto, "Cep"),5,3) ;
        $codigomunicipio = $this->getTagValue($enderecoCompleto, "CodigoMunicipio") ;
        $uf = $this->getTagValue($enderecoCompleto, "Uf") ;

        $endereco = $endereco. ", " . $numero ;
        $endereco.= " - " . $bairro . " - CEP: " . $cep ;
        $this->Cell(16.8, 0.5, $endereco, 1, 1);

		$Contato =  $Tomador->getElementsByTagName("Contato")->item(0);
		$Email =  $this->getTagValue($Contato , "Email");
        $this->pdf->SetXY(0.5, 9.7);
        $this->Cell(3.2, 0.5, "Complemento", 1, 0, "C", 1);
        $this->Cell(3, 0.5, "" );
        $this->Cell(2.6, 0.5, "Telefone", 1, 0, "C", 1);
        $this->Cell(2.3, 0.5, "" , 1);
        $this->Cell(1.5, 0.5, "E-mail", 1, 0, "C", 1);
        $this->Cell(7.4, 0.5, $Email , 1, 1);
        // FIM DADOS TOMADOR
        // FIM TOMADOR
        // TITULO DISCRIMINAÇÃO DOS SERVIÇOS
        
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->SetFillColor(150, 150, 150);
        $this->pdf->SetXY(0.5, 10.2);
        $this->Cell(20, 0.7, "Discriminação dos Serviços", 1, 1, "C", 1);
        
    }

    /**
     * 
     * @param type $w
     * @param type $h
     * @param type $txt
     * @param type $border
     * @param type $ln
     * @param type $align
     * @param type $fill
     * @param type $link
     */
    
    public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link=''){
         $this->pdf->Cell($w, $h, utf8_decode($txt), $border, $ln, $align, $fill, $link);
    }    

}