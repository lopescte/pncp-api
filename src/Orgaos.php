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
            if(empty($cnpj)){
                throw new \Exception('CNPJ do órgão não pode ser vazio.');
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $cnpj);
             
            $client = new \GuzzleHttp\Client(['timeout'=>15,'verify'=>true,'allow_redirects'=>true]);            
            $result = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*'
                                            ]
                                        ]);
            
            if($result->getStatusCode() === 200 && $body = json_decode($result->getBody(), true))
            {
                $this->response = $body;
                return ['response' => $this->response];
            }
            else{
                throw new \Exception('Nenhum retorno da API do PNCP. Tente novamente mais tarde.');
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
     * @method buscaUnidadesPorOrgaoCnpj()
     * @string cnpj          // CNPJ do Orgao
     */
    public function buscaUnidadesPorOrgaoCnpj($cnpj)
    {
        try
        {
            if(empty($cnpj)){
                throw new \Exception('CNPJ do órgão não pode ser vazio.');
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $cnpj) . '/unidades';
             
            $client = new \GuzzleHttp\Client(['timeout'=>15,'verify'=>true,'allow_redirects'=>true]);            
            $result = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*'
                                            ]
                                        ]);
            
            if($result->getStatusCode() === 200 && $body = json_decode($result->getBody(), true))
            {
                $this->response = $body;
                return ['response' => $this->response];
            }
            else{
                throw new \Exception('Nenhum retorno da API do PNCP. Tente novamente mais tarde.');
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
}
