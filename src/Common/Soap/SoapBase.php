<?php

namespace NFePHP\NFSeGinfes\Common\Soap;

use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\RuntimeException;
use NFePHP\Common\Strings;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Psr\Log\LoggerInterface;

/**
 * Soap base class
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Soap\SoapBase
 * @copyright NFePHP Copyright (c) 2017-2019
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */
abstract class SoapBase implements SoapInterface
{
    /**
     * @var int
     */
    protected $soapprotocol = self::SSL_DEFAULT;
    /**
     * @var int
     */
    protected $soaptimeout = 20;
    /**
     * @var string
     */
    protected $proxyIP;
    /**
     * @var int
     */
    protected $proxyPort;
    /**
     * @var string
     */
    protected $proxyUser;
    /**
     * @var string
     */
    protected $proxyPass;
    /**
     * @var array
     */
    protected $prefixes = [1 => 'soapenv', 2 => 'soap'];
    /**
     * @var Certificate
     */
    protected $certificate;
    /**
     * @var LoggerInterface|null
     */
    protected $logger;
    /**
     * @var string
     */
    protected $tempdir;
    /**
     * @var string
     */
    protected $certsdir;
    /**
     * @var string
     */
    protected $debugdir;
    /**
     * @var string
     */
    protected $prifile;
    /**
     * @var string
     */
    protected $pubfile;
    /**
     * @var string
     */
    protected $certfile;
    /**
     * @var string
     */
    protected $casefaz;
    /**
     * @var bool
     */
    protected $disablesec = false;
    /**
     * @var bool
     */
    protected $disableCertValidation = false;
    /**
     * @var \League\Flysystem\Adapter\Local
     */
    protected $adapter;
    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;
    /**
     * @var string
     */
    protected $temppass = '';
    /**
     * @var bool
     */
    protected $encriptPrivateKey = false;
    /**
     * @var integer
     */
    protected $httpver;
    /**
     * @var bool
     */
    protected $debugmode = false;
    /**
     * @var string
     */
    public $response;
    /**
     * @var string
     */
    public $responseHead;
    /**
     * @var string
     */
    public $responseBody;
    /**
     * @var string
     */
    public $requestHead;
    /**
     * @var string
     */
    public $requestBody;
    /**
     * @var string
     */
    public $soaperror;
    /**
     * @var array
     */
    public $soapinfo = [];
    /**
     * @var int
     */
    public $waitingTime = 45;
    
    /**
     * SoapBase constructor.
     * @param Certificate|null $certificate
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Certificate $certificate = null,
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger;
        $this->loadCertificate($certificate);
    }

    /**
     * Check if certificate is valid to currently used date
     * @param Certificate $certificate
     * @return void
     * @throws Certificate\Exception\Expired
     */
    private function isCertificateExpired(Certificate $certificate = null)
    {
        if (!$this->disableCertValidation) {
            if (null !== $certificate && $certificate->isExpired()) {
                throw new Certificate\Exception\Expired($certificate);
            }
        }
    }

    /**
     * Destructor
     * Clean temporary files
     */
    public function __destruct()
    {
        $this->removeTemporarilyFiles();
    }

    /**
     * Disables the security checking of host and peer certificates
     * @param bool $flag
     * @return bool
     */
    public function disableSecurity($flag = false)
    {
        return $this->disablesec = $flag;
    }

    /**
     * ONlY for tests
     * @param bool $flag
     * @return bool
     */
    public function disableCertValidation($flag = true)
    {
        return $this->disableCertValidation = $flag;
    }
    
    /**
     * Force http protocol version
     *
     * @param null|string $version
     */
    public function httpVersion($version = null)
    {
        switch ($version) {
            case '1.0':
                $this->httpver = CURL_HTTP_VERSION_1_0;
                break;
            case '1.1':
                $this->httpver = CURL_HTTP_VERSION_1_1;
                break;
            case '2.0':
                $this->httpver = CURL_HTTP_VERSION_2_0;
                break;
            default:
                $this->httpver = CURL_HTTP_VERSION_NONE;
        }
    }
    
    /**
     * Load path to CA and enable to use on SOAP
     * @param string $capath
     * @return void
     */
    public function loadCA($capath)
    {
        if (is_file($capath)) {
            $this->casefaz = $capath;
        }
    }

    /**
     * Set option to encrypt private key before save in filesystem
     * for an additional layer of protection
     * @param bool $encript
     * @return bool
     */
    public function setEncriptPrivateKey($encript = true)
    {
        $this->encriptPrivateKey = $encript;
        return $this->encriptPrivateKey;
    }
   
    /**
     * Set another temporayfolder for saving certificates for SOAP utilization
     * @param string | null $folderRealPath
     * @return void
     */
    public function setTemporaryFolder($folderRealPath = null)
    {
        if (empty($folderRealPath)) {
            $path = '/sped-'
                . $this->uid()
                .'/'
                . $this->certificate->getCnpj()
                . '/' ;
            $folderRealPath = sys_get_temp_dir().$path;
        }
        // error_log('folderRealPath:'.$folderRealPath);
        
        if (substr($folderRealPath, -1) !== '/') {
            $folderRealPath .= '/';
        }
        $this->tempdir = $folderRealPath;
        $this->setLocalFolder($folderRealPath);
    }
    
    /**
     * Return uid from user
     * @return string
     */
    protected function uid()
    {
        if (function_exists('posix_getuid')) {
            return posix_getuid();
        } else {
            return getmyuid();
        }
    }
 
    /**
     * Set Local folder for flysystem
     * @param string $folder
     */
    protected function setLocalFolder($folder = '')
    {
        $this->adapter = new Local($folder);
        $this->filesystem = new Filesystem($this->adapter);
    }

    /**
     * Set debug mode, this mode will save soap envelopes in temporary directory
     * @param bool $value
     * @return bool
     */
    public function setDebugMode($value = false)
    {
        return $this->debugmode = $value;
    }

    /**
     * Set certificate class for SSL communications
     * @param Certificate $certificate
     * @return void
     */
    public function loadCertificate(Certificate $certificate = null)
    {
        $this->isCertificateExpired($certificate);
        if (null !== $certificate) {
            $this->certificate = $certificate;
        }
    }

    /**
     * Set logger class
     * @param LoggerInterface $logger
     * @return LoggerInterface
     */
    public function loadLogger(LoggerInterface $logger)
    {
        return $this->logger = $logger;
    }

    /**
     * Set timeout for communication
     * @param int $timesecs
     * @return int
     */
    public function timeout($timesecs)
    {
        return $this->soaptimeout = $timesecs;
    }

    /**
     * Set security protocol
     * @param int $protocol
     * @return int
     */
    public function protocol($protocol = self::SSL_DEFAULT)
    {
        return $this->soapprotocol = $protocol;
    }

    /**
     * Set prefixes
     * @param array $prefixes
     * @return string[]
     */
    public function setSoapPrefix($prefixes = [])
    {
        return $this->prefixes = $prefixes;
    }

    /**
     * Set proxy parameters
     * @param string $ip
     * @param int    $port
     * @param string $user
     * @param string $password
     * @return void
     */
    public function proxy($ip, $port, $user, $password)
    {
        $this->proxyIP = $ip;
        $this->proxyPort = $port;
        $this->proxyUser = $user;
        $this->proxyPass = $password;
    }

    /**
     * @param string $url
     * @param string $operation
     * @param string $action
     * @param int $soapver
     * @param array $parameters
     * @param array $namespaces
     * @param string $request
     * @param null $soapheader
     * @return mixed
     */
    abstract public function send(
		$urlwebservice, 
		$dados, 
		$metodo,
		$ambiente,
		$versao
    );

    /**
     * Mount soap envelope
     * @param string $request
     * @param array $namespaces
     * @param int $soapVer
     * @param \SoapHeader $header
     * @return string
     */
    protected function makeEnvelopeSoap(
		$dados, 
		$ambiente, 
		$metodo,
		$versao
    ) {
		
		if ( $versao == 'v03' ) {
			$dados = str_replace('<?xml version="1.0"?>','' , $dados) ;
			$xml  = '';
			$xml .= '<?xml version="1.0" encoding="utf-8"?>';
			$xml .= '<soapenv:Envelope ' ; 
			$xml .= 'xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" ' ;
			$xml .= 'xmlns:hom="http://'.$ambiente.'.ginfes.com.br">' ;
			$xml .= '<soapenv:Header/>' ;
			$xml .= '<soapenv:Body>';
				$xml .= '<hom:'.$metodo.'>' ;
					$xml .= '<arg0>';
						$xml .= '<ns2:cabecalho versao="3" ';
							$xml .= 'xmlns:ns2="http://www.ginfes.com.br/cabecalho_v03.xsd" ';
							$xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' ;
							$xml .= '<versaoDados>3</versaoDados>' ;
						$xml .= '</ns2:cabecalho>' ;
					$xml .= '</arg0>';
					$xml .= '<arg1>';
						$xml .= $dados;
					$xml .= '</arg1>';
				$xml .= '</hom:'.$metodo.'>' ;
			$xml .= '</soapenv:Body>' ;
			$xml .= '</soapenv:Envelope>';

		} else {

			$dados = str_replace('<?xml version="1.0"?>','' , $dados) ;
			$xml  = '';
			$xml .= '<?xml version="1.0" encoding="utf-8"?>';
			$xml .= '<soapenv:Envelope ' ; 
			$xml .= 'xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" ' ;
			$xml .= 'xmlns:hom="http://'.$ambiente.'.ginfes.com.br">' ;
			$xml .= '<soapenv:Header/>' ;
			$xml .= '<soapenv:Body>';
				$xml .= '<hom:'.$metodo.'>' ;
					$xml .= '<arg0>';
						$xml .= $dados;
					$xml .= '</arg0>';
				$xml .= '</hom:'.$metodo.'>' ;
			$xml .= '</soapenv:Body>' ;
			$xml .= '</soapenv:Envelope>';
			
		}
	
        return $xml;
    }

    /**
     * Create a envelop string
     * @param string $envelopPrefix
     * @param string $envelopAttributes
     * @param string $header
     * @param string $bodyContent
     * @return string
     */
    private function mountEnvelopString(
        $envelopPrefix,
        $envelopAttributes = '',
        $header = '',
        $bodyContent = ''
    ) {
        return sprintf(
            '<%s:Envelope %s>' . $header . '<%s:Body>%s</%s:Body></%s:Envelope>',
            $envelopPrefix,
            $envelopAttributes,
            $envelopPrefix,
            $bodyContent,
            $envelopPrefix,
            $envelopPrefix
        );
    }

    /**
     * Create a haeader tag
     * @param string $envelopPrefix
     * @param \SoapHeader $header
     * @return string
     */
    private function mountSoapHeaders($envelopPrefix, $header = null)
    {
        if (null === $header) {
            return '';
        }
        $headerItems = '';
        foreach ($header->data as $key => $value) {
            $headerItems .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        return sprintf(
            '<%s:Header><%s xmlns="%s">%s</%s></%s:Header>',
            $envelopPrefix,
            $header->name,
            $header->namespace === null ? '' : $header->namespace,
            $headerItems,
            $header->name,
            $envelopPrefix
        );
    }

    /**
     * Get attributes
     * @param array $namespaces
     * @return string
     */
    private function getStringAttributes($namespaces = [])
    {
        $envelopeAttributes = '';
        foreach ($namespaces as $key => $value) {
            $envelopeAttributes .= $key . '="' . $value . '" ';
        }
        return $envelopeAttributes;
    }

    /**
     * Temporarily saves the certificate keys for use cURL or SoapClient
     * @return void
     */
    public function saveTemporarilyKeyFiles()
    {
        //certs already exists
        if (!empty($this->certsdir)) {
            return;
        }
        if (!is_object($this->certificate)) {
            throw new RuntimeException(
                'Certificate not found.'
            );
        }
        if (empty($this->filesystem)) {
            $this->setTemporaryFolder();
        }
        //clear dir cert
        $this->removeTemporarilyFiles();
        $this->certsdir = 'certs/';
        $this->prifile = $this->randomName();
        $this->pubfile = $this->randomName();
        $this->certfile = $this->randomName();
        $ret = true;
        //load private key pem
        $private = $this->certificate->privateKey;
        if ($this->encriptPrivateKey) {
            //replace private key pem with password
            $this->temppass = Strings::randomString(16);
            //encripta a chave privada entes da gravação do filesystem
            openssl_pkey_export(
                $this->certificate->privateKey,
                $private,
                $this->temppass
            );
        }
        $ret &= $this->filesystem->put(
            $this->prifile,
            $private
        );
        $ret &= $this->filesystem->put(
            $this->pubfile,
            $this->certificate->publicKey
        );
        $ret &= $this->filesystem->put(
            $this->certfile,
            $private . "{$this->certificate}"
        );
        if (!$ret) {
            throw new RuntimeException(
                'Unable to save temporary key files in folder.'
            );
        }
    }
    
    /**
     * Create a unique random file name
     * @param integer $n
     * @return string
     */
    protected function randomName($n = 10)
    {
        $name = $this->certsdir . Strings::randomString($n) . '.pem';
        if (!$this->filesystem->has($name)) {
            return $name;
        }
        $this->randomName($n+5);
    }

    /**
     * Delete all files in folder
     * @return void
     */
    public function removeTemporarilyFiles()
    {
        if (empty($this->filesystem) || empty($this->certsdir)) {
            return;
        }
        //remove os certificados
        $this->filesystem->delete($this->certfile);
        $this->filesystem->delete($this->prifile);
        $this->filesystem->delete($this->pubfile);
        //remove todos os arquivos antigos
        $contents = $this->filesystem->listContents($this->certsdir, true);
        $dt = new \DateTime();
        $tint = new \DateInterval("PT".$this->waitingTime."M");
        $tint->invert = 1;
        $tsLimit = $dt->add($tint)->getTimestamp();
        foreach ($contents as $item) {
            if ($item['type'] == 'file') {
                $timestamp = $this->filesystem->getTimestamp($item['path']);
                if ($timestamp < $tsLimit) {
                    $this->filesystem->delete($item['path']);
                }
            }
        }
    }

    /**
     * Save request envelope and response for debug reasons
     * @param string $operation
     * @param string $request
     * @param string $response
     * @return void
     */
    public function saveDebugFiles($operation, $request, $response)
    {
        if (!$this->debugmode) {
            return;
        }
        $this->debugdir = $this->certificate->getCnpj() . '/debug/';
        $now = \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
        $time = substr($now->format("ymdHisu"), 0, 16);
        try {
            $this->filesystem->put(
                $this->debugdir . $time . "_" . $operation . "_sol.txt",
                $request
            );
            $this->filesystem->put(
                $this->debugdir . $time . "_" . $operation . "_res.txt",
                $response
            );
        } catch (\Exception $e) {
            throw new RuntimeException(
                'Unable to create debug files.'
            );
        }
    }

    /**
     * Método que formata e sanitiza os retornos soap
     * @param string $soap : string contendo retorno 
     * @return string contendo o retorno soap sanitizado
     */
    protected function clearReturnSOAP($soap = '') {
        $soap = str_replace('&quot;', '"', $soap);
        $soap = str_replace('&gt;', '>', $soap);
        $soap = str_replace('ns4:', '', $soap);
        $soap = str_replace(':ns4', '', $soap);
        $soap = str_replace('ns3:', '', $soap);
        $soap = str_replace(':ns3', '', $soap);
        $soap = str_replace('ns2:', '', $soap);
        $soap = str_replace(':ns2', '', $soap);
        $soap = str_replace('&lt;', '<', $soap);
        $soap = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', '', $soap);
        //$soap = utf8_encode($soap);
        return $soap;
    } 

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
		if( empty($cXml) ){
			return $cRet ;
        }
        
		if( stripos('=',$cElemento) < 0 ){
			$InicioDoDado = empty($cElemento2) ? $carac01.$cElemento.$carac02 : $carac01.$cElemento  ;
			$FinalDoDado  = empty($cElemento2) ? $carac01."/".$cElemento.$carac02 : $carac01.'/'.$cElemento2.$carac02 ;
		}else{
			$InicioDoDado = $cElemento ;
			$FinalDoDado  = $cElemento2 ;
		}
		$nPosIni = stripos($InicioDoDado,$cXml) ;
		$nPosIniTag = stripos($InicioDoDado,$cXml) ;
		if ( $nPosIniTag == 0 ) {
			$nPosIniTag = 1 ;
		}
		if( $nPosIni < 0 ){
			$InicioDoDado = $carac01.$cElemento ;
			$nPosIni = stripos($InicioDoDado,$cXml) ;
			if( $nPosIni >= 0 ){
				$nPosIni = stripos($InicioDoDado,$cXml)+1 ;
				$nPosIniTag = stripos($InicioDoDado,$cXml) ;
				if ( $nPosIniTag == 0 ) {
					$nPosIniTag = 1 ;
				}
				for ( $X = $nPosIni ; $X < strlen($cXml) ; $X++ ) {
					if ( stripos($cXml,$X,1) == $carac02 ) {
						$nPosIni = $X+1 ;
						break ;
					}
				}
			}
		}else{
			$nPosIni +=1 ;
			for ( $X = $nPosIni ; $X < strlen($cXml) ; $X++ ) {
				if ( stripos($cXml,$X,1) == $carac02 ) {
					$nPosIni = $X+1 ;
					break ;
				}
			}
		}
		if( $nPosIni == -1 ){
			return $cRet ;
		}
		if( !empty($cElemento2) && $nPosIni >= 0 ){
			$cXml = stripos($cXml,$nPosIni) ;
			$nPosIni = 1 ;
		}
		if( $nPosIni >= 0 ){
			$nPosFim = stripos($FinalDoDado,$cXml) ;
			if( $nPosFim >= 0 ){
				$nPosFim +=1 ;
				for ( $X = $nPosFim ; $X <= strlen($cXml) ; $X++ ) {
					if ( stripos($cXml,$X,1) == $carac02 ) {
						$nPosFimTag = $X+1 ;
						break ;
					}
				}
			}
		}
		if( $nPosIni < 0 || $nPosFim < 0 ){
			return $cRet ;
		}
		$cRet = stripos($cXml,$nPosIni,$nPosFim-$nPosIni) ;
	    return $cRet ;
	}
}
