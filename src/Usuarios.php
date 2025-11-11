<?php
namespace Lopescte\PncpApi;

/**
 * Class Usuarios
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
class Usuarios
{
    public $response = NULL;
       
    /**
     * @method buscaUsuarioPorId()
     * @int id            // id do usuario
     */
    public function buscaUsuarioPorId($id)
    {
        try
        {  
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($id)){
                throw new \Exception('ID do usuário não pode ser vazio.');
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/usuarios/' . $id;
             
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
     * @method buscaUsuarioPorCpfCnpj()
     * @string cpfCnpj          // Cpf ou CNPJ do usuario
     */
    public function buscaUsuarioPorCpfCnpj($cpfCnpj)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($cpfCnpj)){
                throw new \Exception('CPF/CNPJ do usuário não pode ser vazio.');
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/usuarios/?cpfCnpj=' . $cpfCnpj;
             
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
     * @method atualizaUsuarioPorId()
     * @int id             //id do usuario
     * @array parameters   //[
     *                         "nome": "string",
     *                         "email": "string",
     *                         "senha": "string",
     *                         "entesAutorizados": [
     *                           "string", //cnpj 1
     *                           "string"  //cnpj 2
     *                         ]
     *                       ]
     */
    public function atualizaUsuarioPorId(int $id=NULL, array $parameters=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($id)){
                throw new \Exception('ID do usuário não pode ser vazio.');
            }
            
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/usuarios/atualizaUsuario.json'));
            
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
                    $msg = $msg . $error['property']. ' - ' . $error['message']."<br>";
                }
                throw new \Exception($msg);
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/usuarios/' . $id;
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('PUT', $url, [
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
     * @method insereEntesUsuarioPorId()
     * @int id             //id do usuario
     * @array cnpj         //[cnpj1, cnpj2, etc]
     */
    public function insereEntesUsuarioPorId(int $id=NULL, string $cnpj=NULL)
    {
        try
        {  
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($id)){
                throw new \Exception('ID do usuário não pode ser vazio.');
            }
            
            if(empty($cnpj)){
                throw new \Exception('CNPJ do ente não pode ser vazio.');
            }
            
            $parameters = ['entesAutorizados' => [preg_replace("/\D/", "", $cnpj)]];
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/usuarios/' . $id .'/orgaos';
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('POST', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
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
     * @method removeEntesUsuarioPorId()
     * @int id             //id do usuario
     * @array cnpj         //[cnpj1, cnpj2, etc]
     */
    public function removeEntesUsuarioPorId($id, $cnpj)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($id)){
                throw new \Exception('ID do usuário não pode ser vazio.');
            }
            
            if(empty($cnpj)){
                throw new \Exception('CNPJ do ente não pode ser vazio.');
            }
            
            $parameters = ['entesAutorizados' => [preg_replace("/\D/", "", $cnpj)]];
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/usuarios/' . $id .'/orgaos';
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('DELETE', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
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
}
