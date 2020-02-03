<?php

namespace NFePHP\NFSeGinfes\Legacy;

use Carbon\Carbon;

class Common
{

    /**
     * Extrai o valor do node DOM
     * @param  object $theObj Instancia de DOMDocument ou DOMElement
     * @param  string $keyName identificador da TAG do xml
     * @param  string $extraTextBefore prefixo do retorno
     * @param  string extraTextAfter sufixo do retorno
     * @param  number itemNum numero do item a ser retornado
     * @return string
     */
    protected function getTagValue($theObj, $keyName, $extraTextBefore = '', $extraTextAfter = '', $itemNum = 0)
    {
        if (empty($theObj)) {
            return '';
        }
        $vct = $theObj->getElementsByTagName($keyName)->item($itemNum);
        if (isset($vct)) {
            $value = trim($vct->nodeValue);
            if (strpos($value, '&') !== false) {
                //existe um & na string, então deve ser uma entidade
                $value = html_entity_decode($value);
            }
            return $extraTextBefore . $value . $extraTextAfter;
        }
        return '';
    }

    /**
     * Recupera e reformata a data do padrão da NFe para dd/mm/aaaa
     * @author Marcos Diez
     * @param  DOM    $theObj
     * @param  string $keyName   identificador da TAG do xml
     * @param  string $extraText prefixo do retorno
     * @return string
     */
    protected function getTagDate($theObj, $keyName, $extraText = '')
    {
        if (!isset($theObj) || !is_object($theObj)) {
            return '';
        }
        $vct = $theObj->getElementsByTagName($keyName)->item(0);
        if (isset($vct)) {
            $theDate = explode("-", $vct->nodeValue);
            return $extraText . $theDate[2] . "/" . $theDate[1] . "/" . $theDate[0];
        }
        return '';
    }

    /**
     * camcula digito de controle modulo 11
     * @param  string $numero
     * @return integer modulo11 do numero passado
     */
    protected function modulo11($numero = '')
    {
        if ($numero == '') {
            return '';
        }
        $numero = (string) $numero;
        $tamanho = strlen($numero);
        $soma = 0;
        $mult = 2;
        for ($i = $tamanho - 1; $i >= 0; $i--) {
            $digito = (int) $numero[$i];
            $r = $digito * $mult;
            $soma += $r;
            $mult++;
            if ($mult == 10) {
                $mult = 2;
            }
        }
        $resto = ($soma * 10) % 11;
        return ($resto == 10 || $resto == 0) ? 1 : $resto;
    }

    /**
     * Converte datas no formato YMD (ex. 2009-11-02) para o formato brasileiro 02/11/2009)
     * @param  string $data Parâmetro extraido da NFe
     * @return string Formatada para apresentação da data no padrão brasileiro
     */
    protected function ymdTodmy($data = '')
    {
        if ($data == '') {
            return '';
        }
        $needle = "/";
        if (strstr($data, "-")) {
            $needle = "-";
        }
        $dt = explode($needle, $data);
        return "$dt[2]/$dt[1]/$dt[0]";
    }

    /**
     * Converte data da NFe YYYY-mm-ddThh:mm:ss-03:00 para timestamp unix
     *
     * @param string $input
     *
     * @return integer
     */
    public function toTimestamp($input)
    {
        $regex = '^(2[0-9][0-9][0-9])[-](0?[1-9]'
            . '|1[0-2])[-](0?[1-9]'
            . '|[12][0-9]'
            . '|3[01])T([0-9]|0[0-9]'
            . '|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]-(00|01|02|03|04):00$';
        
        if (!preg_match("/$regex/", $input)) {
            return '';
        }
        return Carbon::createFromFormat("Y-m-d\TH:i:sP", $input)->timestamp;
    }

    /**
     * Função de formatação de strings onde o cerquilha # é um coringa
     * que será substituido por digitos contidos em campo.
     * @param  string $campo   String a ser formatada
     * @param  string $mascara Regra de formatção da string (ex. ##.###.###/####-##)
     * @return string Retorna o campo formatado
     */
    protected function formatField($campo = '', $mascara = '')
    {
        if ($campo == '' || $mascara == '') {
            return $campo;
        }
        //remove qualquer formatação que ainda exista
        $sLimpo = preg_replace("(/[' '-./ t]/)", '', $campo);
        // pega o tamanho da string e da mascara
        $tCampo = strlen($sLimpo);
        $tMask = strlen($mascara);
        if ($tCampo > $tMask) {
            $tMaior = $tCampo;
        } else {
            $tMaior = $tMask;
        }
        //contar o numero de cerquilhas da mascara
        $aMask = str_split($mascara);
        $z = 0;
        $flag = false;
        foreach ($aMask as $letra) {
            if ($letra == '#') {
                $z++;
            }
        }
        if ($z > $tCampo) {
            //o campo é menor que esperado
            $flag = true;
        }
        //cria uma variável grande o suficiente para conter os dados
        $sRetorno = '';
        $sRetorno = str_pad($sRetorno, $tCampo + $tMask, " ", STR_PAD_LEFT);
        //pega o tamanho da string de retorno
        $tRetorno = strlen($sRetorno);
        //se houve entrada de dados
        if ($sLimpo != '' && $mascara != '') {
            //inicia com a posição do ultimo digito da mascara
            $x = $tMask;
            $y = $tCampo;
            $cI = 0;
            for ($i = $tMaior - 1; $i >= 0; $i--) {
                if ($cI < $z) {
                    // e o digito da mascara é # trocar pelo digito do campo
                    // se o inicio da string da mascara for atingido antes de terminar
                    // o campo considerar #
                    if ($x > 0) {
                        $digMask = $mascara[--$x];
                    } else {
                        $digMask = '#';
                    }
                    //se o fim do campo for atingido antes do fim da mascara
                    //verificar se é ( se não for não use
                    if ($digMask == '#') {
                        $cI++;
                        if ($y > 0) {
                            $sRetorno[--$tRetorno] = $sLimpo[--$y];
                        } else {
                            //$sRetorno[--$tRetorno] = '';
                        }
                    } else {
                        if ($y > 0) {
                            $sRetorno[--$tRetorno] = $mascara[$x];
                        } else {
                            if ($mascara[$x] == '(') {
                                $sRetorno[--$tRetorno] = $mascara[$x];
                            }
                        }
                        $i++;
                    }
                }
            }
            if (!$flag) {
                if ($mascara[0] != '#') {
                    $sRetorno = '(' . trim($sRetorno);
                }
            }
            return trim($sRetorno);
        } else {
            return '';
        }
    }

    protected function tipoPag($tPag)
    {
        switch ($tPag) {
            case '01':
                $tPagNome = 'Dinheiro';
                break;
            case '02':
                $tPagNome = 'Cheque';
                break;
            case '03':
                $tPagNome = 'Cartão de Crédito';
                break;
            case '04':
                $tPagNome = 'Cartão de Débito';
                break;
            case '05':
                $tPagNome = 'Crédito Loja';
                break;
            case '10':
                $tPagNome = 'Vale Alimentação';
                break;
            case '11':
                $tPagNome = 'Vale Refeição';
                break;
            case '12':
                $tPagNome = 'Vale Presente';
                break;
            case '13':
                $tPagNome = 'Vale Combustível';
                break;
            case '14':
                $tPagNome = 'Duplicata Mercantil';
                break;
            case '15':
                $tPagNome = 'Boleto Bancário';
                break;
            case '90':
                $tPagNome = 'Sem Pagamento';
                break;
            case '99':
                $tPagNome = 'Outros';
                break;
            default:
                $tPagNome = '';
                // Adicionado default para impressão de notas da 3.10
        }
        return $tPagNome;
    }

    /**
     * Get description country
     * @param string $codigo
     * @return string\stdClass
     * @see storage/ibge_municipios.json
     */
    protected function getCidade($codigo)
    {
        $autfile = realpath(__DIR__ . '/../../storage/ibge_municipios.json');
        $ibge_municipios = json_decode(file_get_contents($autfile), true);
        if (!key_exists($codigo, $ibge_municipios)) {
            throw new \RuntimeException("Nao existe está Cidade [$codigo]");
        }
        $description = $ibge_municipios[$codigo];
        return $description;
    }

    /**
     * Get description country
     * @param string $item
     * @param string $codigo
     * @return string\stdClass
     * @see storage/ibge_municipios.json
     */
    protected function getCodTributario($municipio,$lista,$codigo)
    {
        $autfile = realpath(__DIR__ . '/../../storage/codigostributarios.json');
        $codtributario = json_decode(file_get_contents($autfile), true);
        if (!key_exists($lista, $codtributario[$municipio])) {
            throw new \RuntimeException("Nao existe item da lista de serviço do municipio [$municipio]");
        }
        $listServico = $codtributario[$municipio][$lista] ;
        if (!key_exists($codigo, $codtributario[$municipio][$lista])) {
            throw new \RuntimeException("Nao existe código tributário na lista de serviço do municipio [$municipio]");
        }
        $description = $codtributario[$municipio][$lista][$codigo];
        return $description;
    }  
    
    /**
     * pConvertTime
     * Converte a informação de data e tempo contida na NFe
     *
     * @param string $DH Informação de data e tempo extraida da NFe
     * @return timestamp UNIX Para uso com a funçao date do php
     */
    protected function pConvertTime($DH = '')
    {
        if ($DH == '') {
            return '';
        }
        //printobj($DH) ;
        $aDH = explode('T', $DH);
        //printobj($aDH) ;
        $adDH = explode('-', $aDH[0]);
        //printobj($adDH) ;
        if( count($aDH) > 1 ){
            if(strpos($aDH[1], '-') !== FALSE ) {
                $inter = explode('-', $aDH[1]);
            }else if(strpos($aDH[1], '+') !== FALSE) {
                $inter = explode('+', $aDH[1]);
            }else{//else adicionado para instanciar a variável $inter[0] quando $aDH[1] não passa nos testes de de busca de '-' ou '+' por não possuir informação do tomezone.
                $inter[0] = $aDH[1];
            }
            $atDH = explode(':', $inter[0]);
			//printobj($atDH) ;
			$timestampDH = mktime($atDH[0], $atDH[1], $atDH[2], $adDH[1], $adDH[2], $adDH[0]);
        }else{
            $timestampDH = mktime($month = $adDH[1], $day =  $adDH[2], $year = $adDH[0]);
        }
        return $timestampDH;
    }

    /**
     * nodeXML
     * retira uma tag expecifica do XML
     *
     * @param string $cElemento Tag Inicial que será resgatada
     * @param string $cStringXML XML usado para pesquisa
     * @param string $cElemento2 Tag Final que será resgatada
     * @param string $carac01 Caracter delimitador da Tag Inicial
     * @param string $carac02 Caracter delimitador da Tag Final
     * @return timestamp UNIX Para uso com a funçao date do php
     */

 	protected function nodeXML($cElemento, 
						  $cStringXML,
						  $cElemento2 = '',
						  $carac01 = '<',
						  $carac02 = '>' ){
		$InicioDoDado = '' ;
		$FinalDoDado = '' ;
		$nPosIni = -1 ;
		$nPosIniTag = 0 ;
		$nPosFim = -1 ;
		$nPosFimTag = 0 ;
		$cRet = '' ;
		$cXml = $cStringXML ;
		if( Empty($cXml) ){
			return $cRet ;
		}
		if( At('=',$cElemento) < 0 ){
			$InicioDoDado = iif( Empty($cElemento2),$carac01.$cElemento.$carac02 , $carac01.$cElemento ) ;
			$FinalDoDado  = iif( Empty($cElemento2),$carac01."/".$cElemento.$carac02,$carac01.'/'.$cElemento2.$carac02) ;
		}else{
			$InicioDoDado = $cElemento ;
			$FinalDoDado  = $cElemento2 ;
		}
		$nPosIni = At($InicioDoDado,$cXml) ;
		$nPosIniTag = At($InicioDoDado,$cXml) ;
		if ( $nPosIniTag == 0 ) {
			$nPosIniTag = 1 ;
		}
		if( $nPosIni < 0 ){
			$InicioDoDado = $carac01.$cElemento ;
			$nPosIni = At($InicioDoDado,$cXml) ;
			if( $nPosIni >= 0 ){
				$nPosIni = At($InicioDoDado,$cXml)+1 ;
				$nPosIniTag = At($InicioDoDado,$cXml) ;
				if ( $nPosIniTag == 0 ) {
					$nPosIniTag = 1 ;
				}
				for ( $X = $nPosIni ; $X < Len($cXml) ; $X++ ) {
					if ( Subs($cXml,$X,1) == $carac02 ) {
						$nPosIni = $X+1 ;
						break ;
					}
				}
			}
		}else{
			$nPosIni +=1 ;
			for ( $X = $nPosIni ; $X < Len($cXml) ; $X++ ) {
				if ( Subs($cXml,$X,1) == $carac02 ) {
					$nPosIni = $X+1 ;
					break ;
				}
			}
		}
		if( $nPosIni == -1 ){
			return $cRet ;
		}
		if( !Vazio($cElemento2) && $nPosIni >= 0 ){
			$cXml = Subs($cXml,$nPosIni) ;
			$nPosIni = 1 ;
		}
		if( $nPosIni >= 0 ){
			$nPosFim = At($FinalDoDado,$cXml) ;
			if( $nPosFim >= 0 ){
				$nPosFim +=1 ;
				for ( $X = $nPosFim ; $X <= Len($cXml) ; $X++ ) {
					if ( Subs($cXml,$X,1) == $carac02 ) {
						$nPosFimTag = $X+1 ;
						break ;
					}
				}
			}
		}
		if( $nPosIni < 0 || $nPosFim < 0 ){
			return $cRet ;
		}
		$cRet = Subs($cXml,$nPosIni,$nPosFim-$nPosIni) ;
	    return $cRet ;
	}
    
}
