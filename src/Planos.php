<?php
namespace Lopescte\PncpApi;

use Transliterator;
/**
 * Class Contratos
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
class Planos
{
    public $response = NULL;
    
    /**
     * @method consultaContratoPorControle()
     * @string url       // ID Controle do contrato no PNCP
     */
    public function consultaPcaPorAno(string $controle=NULL)
    {             
        try
        {
            if(empty($controle)){
                throw new \Exception('Número de Controle não pode ser vazio.');
            }
            
            $id = Pncp::validaControlePncp($controle);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/contratos/' . $id['ano'] . '/' . $id['numero'];
             
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
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
     * @method inserePca()
     * @string cnpj       // CNPJ do Orgao
     * @array parameters  // array()
     */
    public function inserePca(string $cnpj=NULL, array $parameters=NULL)
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
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/planos/novoPca.json'));
            
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
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $cnpj) . '/pca/';
             
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
     * @method alteraContrato()
     * @string controle       // ID do contrato no PNCP
     * @array parameters      // array()
     */
    public function alteraPca(string $controle=NULL, array $parameters=NULL)
    {             
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($controle)){
                throw new \Exception('ID do contrato no PNCP não pode ser vazio.');
            }
            
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/contratos/alteraContrato.json'));
            
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
            
            $id = Pncp::validaControlePncp($controle);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/contratos/' . $id['ano'] . '/' . $id['numero'];
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('PUT', $url, [
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
     * @method deletaPca()
     * @string controle      // Número de Controle do PNCP
     * @string justificativa // Justificativa para a exclusão da compra
     */
    public function deletaPca(string $controle=NULL, string $justificativa=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($controle)){
                throw new \Exception('Número de Controle do PNCP não pode ser vazio.');
            }
            
            if(empty($justificativa)){
                throw new \Exception('Justificativa da exclusão não pode ser vazio.');
            }
            
            $id = Pncp::validaControlePncp($controle);
            
            $parameters = ['justificativa' => $justificativa];
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/contratos/' . $id['ano'] . '/' . $id['numero'];
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('DELETE', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ]
                                        ]);
            
            $this->response = $result->getHeaders();
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
