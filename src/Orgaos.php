<?php
namespace Lopescte\PncpApi;

/**
 * Class Orgaos
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
class Orgaos
{
    public $response = NULL;
    
    /**
     * @method buscaOrgaoPorCnpj()
     * @string cnpj          // CNPJ do Orgao
     */
    public function buscaOrgaoPorCnpj($cnpj)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($cnpj)){
                throw new \Exception('CNPJ do órgão não pode ser vazio.');
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $cnpj);
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ]
                                        ]);
            
            $this->response = json_decode($result->getBody(), true);
            return ['response' => $this->response];               
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
     * @method insereOrgao()
     * @array parameters   //[
     *                         "cnpj" => "string",
     *                         "razaoSocial" => "string",
     *                         "poderId" => "L - Legislativo; E - Executivo; J - Judiciário; N - Não se aplica",
     *                         "esferaId" => "F - Federal; E - Estadual; M - Municipal; D - Distrital; N - Não se aplica"
     *                       ]
     */
    public function insereOrgao($parameters)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/orgaos/novoOrgao.json'));
            
            if(!empty($parameters) && is_array($parameters))
            {
                foreach($parameters as $key => $value)
                {
                    $data->$key = $value;
                }
            }else{
                throw new \Exception('Um array() de dados deve ser enviado. Consulte o schema.');
            } 
            
            // Validate
            $validator = new \JsonSchema\Validator();
            $validator->validate($data, $schema);
                                               
            if (!$validator->isValid()) {                
                $msg = NULL;
                foreach ($validator->getErrors() as $error) {
                    $msg = $msg . $error['property']. ' - ' . $error['message']."\n\n";
                }
                throw new \Exception($msg);
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/';
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('POST', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
                                        ]);
            
            $this->response['location'] = $result->getHeader('location')[0];
            return ['response' => $this->response];           
            
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
     * @method buscaUnidadesPorOrgaoCnpj()
     * @string cnpj          // CNPJ do Orgao
     */
    public function buscaUnidadesPorOrgaoCnpj($cnpj)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($cnpj)){
                throw new \Exception('CNPJ do órgão não pode ser vazio.');
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $cnpj) . '/unidades';
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ]
                                        ]);
            
            $this->response = json_decode($result->getBody(), true);
            return ['response' => $this->response];               
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
     * @method insereUnidade()
     * @string cnpj          // CNPJ do Orgao
     * @array parameters   //[
     *                         "codigoIBGE" => "string(7)",
     *                         "codigoUnidade" => "string(30)",
     *                         "nomeUnidade" => "string(100)"
     *                       ]
     */
    public function insereUnidade($cnpj, $parameters)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($cnpj)){
                throw new \Exception('CNPJ do órgão não pode ser vazio.');
            }
            
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/orgaos/novaUnidade.json'));
            
            if(!empty($parameters) && is_array($parameters))
            {
                foreach($parameters as $key => $value)
                {
                    $data->$key = $value;
                }
            }else{
                throw new \Exception('Um array() de dados deve ser enviado. Consulte o SCHEMA.');
            } 
            
            // Validate
            $validator = new \JsonSchema\Validator();
            $validator->validate($data, $schema);
                                               
            if (!$validator->isValid()) {                
                $msg = NULL;
                foreach ($validator->getErrors() as $error) {
                    $msg = $msg . $error['property']. ' - ' . $error['message']."\n\n";
                }
                throw new \Exception($msg);
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $cnpj) . '/unidades';
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('POST', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
                                        ]);
            
            $this->response['location'] = $result->getHeader('location')[0];
            return ['response' => $this->response];           
            
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
}
