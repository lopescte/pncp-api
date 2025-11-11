<?php
namespace Lopescte\PncpApi;

/**
 * Class Pncp
 *
 * @category   library
 * @package    lopescte\PncpApi
 * @url        https://github.com/lopescte/PncpApi
 * @author     Marcelo Lopes <lopes.cte@gmail.com>
 * @copyright  Copyright (c) 2022 Reis & Lopes Assessoria e Sistemas. (https://www.reiselopes.com.br)
 * @license    http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 */
class Pncp
{
    private static $accessToken = null;
    private static $ambiente = null;    // 1 = Treinamento/Homologação, 2 = Produção
    private static $version = 'v1';     // versão da API
    private static $base_url = null;    // URL base do PNCP
    private static $manual = 'v2.3.8';  // Versão do Manual de Integração compatibilizada


    /**
     * Constructor method
     */
    function __construct($user, $password, $ambiente = 1)
    {
        if(empty($user) || empty($password)) {
            throw new \Exception('Credenciais do PNCP não informadas corretamente. Verifique!');
        }
                
        try
        {
            // start session if not exists
            if(session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // init BaseUrl
            self::setBaseUrl(($ambiente == 1) ? 'https://treina.pncp.gov.br/api/pncp' : 'https://pncp.gov.br/api/pncp');
            
            // check validity of session and token
            if(isset($_SESSION['pncp']) && $_SESSION['pncp']['timestamp'] > (time() - 3540) )
            {
                self::setAccessToken($_SESSION['pncp']['token']);
            }else{
            
                $client = new \GuzzleHttp\Client();            
                $res = $client->request('POST', self::$base_url . '/' . self::$version . '/usuarios/login', [
                                                'headers' => [
                                                    'Accept' => '*/*',
                                                    'Content-Type' => 'application/json'
                                                ],
                                                'json' => [
                                                    'login' => trim($user),
                                                    'senha' => trim($password),
                                                ]
                                            ]);
                
                self::setAccessToken($res->getHeader('authorization')[0]);
                $_SESSION['pncp']['timestamp'] = time();
                $_SESSION['pncp']['token'] = $res->getHeader('authorization')[0];
                
            }       
        }
        catch (\GuzzleHttp\Exception\RequestException $e) {
    	    if ($e->hasResponse()) {
        		$error = json_decode($e->getResponse()->getBody(), TRUE);
        		if(is_array($error) && isset($error['message'])){
                    throw new \Exception("{$error['error']} <br><br> {$error['message']}");
        		}elseif(is_array($error) && isset($error['erros'])){
        			throw new \Exception("{$error['erros'][0]['mensagem']}");
        		}else{
        			throw new \Exception($e->getMessage());
        		}
            }
        }
    }
    
    /**
     * method getAccessToken()
     *
     */
    public static function setAccessToken($Token)
    {
      self::$accessToken = $Token;
    }
    
    /**
     * method getAccessToken()
     *
     */
    public static function getAccessToken(): ?string
    {
        return self::$accessToken;
    }
    
    /**
     * method setBaseUrl()
     *
     */
    public static function setBaseUrl($url)
    {
        self::$base_url = $url;
    }

    /**
     * method getBaseUrl()
     *
     */
    public static function getBaseUrl(): ?string
    {
        return self::$base_url;
    }
        
    /**
     * method getVersion()
     *
     */
    public static function getVersion(): ?string
    {
        return self::$version;
    }   
    
    /**
     * method validaControlePncp()
     *
     */
    public static function validaControlePncp($data)
    {
        if(empty($data))
        {
            throw new \Exception('ID de Controle do PNCP não pode ser vazio.');
        }
        
        if(preg_match("/^[0-9]{14}\-[0-9]{1}\-[0-9]{6}\/[0-9]{4}$/", $data))
        {
            $partial = preg_split('/\W+/', $data, -1, PREG_SPLIT_NO_EMPTY);
            $result['cnpj'] = $partial[0];
            $result['ano'] = $partial[3];
            $result['numero'] = $partial[2];
            
            return $result;
        }else{
            throw new \Exception("O ID de controle do PNCP informado ({$data}) não é um formato válido.<br/> (Ex.: 99999999999999-9-999999/9999).");
        } 
    }
    
    /**
     * method getHeaderValue()
     *
     */
    public static function getHeaderValue(array $headers, string $name): ?string 
    {
        foreach ($headers as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return is_array($value) ? implode(';', $value) : $value;
            }
        }
        return null;
    }
}
