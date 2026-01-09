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
class Contratos
{
    public $response = NULL;
    
    /**
     * @method consultaContratoPorUrl()
     * @string url       // URL do contrato no PNCP
     */
    public function consultaContratoPorUrl(string $url=NULL)
    {             
        try
        {
            if(empty($url)){
                throw new \Exception('URL não pode ser vazio.');
            }
                                
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
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
     * @method consultaContratoPorControle()
     * @string url       // ID Controle do contrato no PNCP
     */
    public function consultaContratoPorControle(string $controle=NULL)
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
     * @method insereContrato()
     * @string cnpj       // CNPJ do Orgao
     * @array parameters  // array()
     */
    public function insereContrato(string $cnpj=NULL, array $parameters=NULL)
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
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/contratos/novoContrato.json'));
            
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
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $cnpj) . '/contratos';
             
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
     * @method insereDocumentoContrato()
     * @string controle       // Número de Controle no PNCP
     * @string arquivo        // URL do arquivo
     * @string nome_documento // Nome do Documento (Ex.: Contrato nº. 001/2023)
     * @int tipo_documento    // ID do tipo de documento na tabela de Dominio do PNCP
     */
    public function insereDocumentoContrato(string $controle=NULL, string $arquivo=NULL, string $nome_documento=NULL, int $tipo_documento=NULL)
    {             
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($controle)){
                throw new \Exception('Número de Controle do PNCP não pode ser vazio.');
            }            
                        
            if(empty(urldecode($arquivo)) || !file_exists(urldecode($arquivo))){
                throw new \Exception('Arquivo não localizado ou não informado.');
            }
            
            if(empty($nome_documento)){
                throw new \Exception('Nome do Documento não pode ser vazio.');
            }
            
            if(empty($tipo_documento)){
                throw new \Exception('ID do tipo do Documento não pode ser vazio.');
            }            
                                                
            $id = Pncp::validaControlePncp($controle);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/contratos/' . $id['ano'] . '/' . $id['numero'] . '/arquivos';
                         
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('POST', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Titulo-Documento' => transliterator_transliterate('Any-Latin; Latin-ASCII', $nome_documento),
                                                'Tipo-Documento-Id' => $tipo_documento,
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'multipart' => [
                                                [
                                                    'name' => 'arquivo',
                                                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen(urldecode($arquivo), 'r'),
                                                    'type' => mime_content_type(urldecode($arquivo))
                                                ]
                                            ]
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
     * @method consultaContratoPorUrl()
     * @string url       // URL do contrato no PNCP
     */
    public function consultaTermoContratoPorUrl(string $url=NULL)
    {             
        try
        {
            if(empty($url)){
                throw new \Exception('URL não pode ser vazio.');
            }
                        
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
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
     * @method insereTermoContrato()
     * @string controle       // ID Controle do Contrato no PNCP
     * @array parameters      // array([
     *                                      "tipoTermoContratoId" => 1, 
     *                                      "numeroTermoContrato" => "string", 
     *                                      "objetoTermoContrato" => "string", 
     *                                      "qualificacaoAcrescimoSupressao" => true, 
     *                                      "qualificacaoVigencia" => true, 
     *                                      "qualificacaoFornecedor" => true, 
     *                                      "qualificacaoInformativo" => true, 
     *                                      "qualificacaoReajuste" => true, 
     *                                      "dataAssinatura" => "2023-04-12", 
     *                                      "niFornecedor" => "string", 
     *                                      "tipoPessoaFornecedor" => "string", 
     *                                      "nomeRazaoSocialFornecedor" => "string", 
     *                                      "niFornecedorSubContratado" => "string", 
     *                                      "tipoPessoaFornecedorSubContratado" => "string", 
     *                                      "nomeRazaoSocialFornecedorSubContratado" => "string", 
     *                                      "informativoObservacao" => "string", 
     *                                      "fundamentoLegal" => "string", 
     *                                      "valorAcrescido" => 0, 
     *                                      "numeroParcelas" => 0, 
     *                                      "valorParcela" => 0, 
     *                                      "valorGlobal" => 0, 
     *                                      "prazoAditadoDias" => 0, 
     *                                      "dataVigenciaInicio" => "2023-04-12", 
     *                                      "dataVigenciaFim" => "2023-04-12" 
     *                                   ])
     */
    public function insereTermoContrato(string $controle=NULL, array $parameters=NULL)
    {             
        try
        {
            
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            if(empty($controle)){
                throw new \Exception('ID controle do contrato no PNCP não pode ser vazio.');
            }   
            
            $id = Pncp::validaControlePncp($controle);         
                        
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/contratos/novoTermoContrato.json'));
            
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
            
            //$partes = preg_split('/\W+/', $controle, -1, PREG_SPLIT_NO_EMPTY);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/contratos/' . $id['ano'] . '/' . $id['numero'] . '/termos';

            $client = new \GuzzleHttp\Client();            
            $result = $client->request('POST', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $data
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
     * @method insereDocumentoTermoContrato()
     * @string url            // URL do termo de contrato no PNCP
     * @string arquivo        // URL do arquivo
     * @string nome_documento // Nome do Documento (Ex.: Aditivo nº. 001/2023)
     * @int tipo_documento    // ID do tipo de documento na tabela de Dominio do PNCP
     */
    public function insereDocumentoTermoContrato(string $url=NULL, string $arquivo=NULL, string $nome_documento=NULL, int $tipo_documento=NULL)
    {             
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            if(empty($url)){
                throw new \Exception('URL do Termo de Contrato não pode ser vazio.');
            }
            
            if(empty(urldecode($arquivo)) || !file_exists(urldecode($arquivo))){
                throw new \Exception('Arquivo não localizado ou não informado.');
            }
            
            if(empty($nome_documento)){
                throw new \Exception('Nome do Documento não pode ser vazio.');
            }
            
            if(empty($tipo_documento)){
                throw new \Exception('ID do tipo do Documento não pode ser vazio.');
            }            
             
            $url = $url . '/arquivos';
              
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('POST', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Titulo-Documento' => transliterator_transliterate('Any-Latin; Latin-ASCII', $nome_documento),
                                                'Tipo-Documento-Id' => $tipo_documento,
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'multipart' => [
                                                [
                                                    'name' => 'arquivo',
                                                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen(urldecode($arquivo), 'r'),
                                                    'type' => mime_content_type(urldecode($arquivo))
                                                ]
                                            ]
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
    public function alteraContrato(string $controle=NULL, array $parameters=NULL)
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
     * @method deletaContrato()
     * @string controle      // Número de Controle do PNCP
     * @string justificativa // Justificativa para a exclusão da compra
     */
    public function deletaContrato(string $controle=NULL, string $justificativa=NULL)
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
    
    /**
     * @method deletaTermoContrato()
     * @string url           // URL do Termo no PNCP
     * @string justificativa // Justificativa para a exclusão do termo
     */
    public function deletaTermoContrato(string $url=NULL, string $justificativa=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            if(empty($url)){
                throw new \Exception('URL do Termo não pode ser vazio.');
            }
            
            if(empty($justificativa)){
                throw new \Exception('Justificativa da exclusão não pode ser vazio.');
            }
            
            $parameters = ['justificativa' => $justificativa];
            
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('DELETE', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
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
    
    /**
     * @method deletaDocumentoContrato()
     * @string URL           // URL do documento no PNCP
     * @string justificativa // Justificativa para a exclusão do documento
     */
    public function deletaDocumentoContrato(string $url=NULL, string $justificativa=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            if(empty($url)){
                throw new \Exception('URL do documento não pode ser vazio.');
            }
            
            if(empty($justificativa)){
                throw new \Exception('Justificativa da exclusão não pode ser vazio.');
            }
                                    
            $parameters = ['justificativa' => $justificativa];
            
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('DELETE', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
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
    
    /**
     * @method deletaDocumentoTermoContrato()
     * @string URL           // URL do documento no PNCP
     * @string justificativa // Justificativa para a exclusão do termo
     */
    public function deletaDocumentoTermoContrato(string $url=NULL, string $justificativa=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            if(empty($url)){
                throw new \Exception('URL do documento não pode ser vazio.');
            }
            
            if(empty($justificativa)){
                throw new \Exception('Justificativa da exclusão não pode ser vazio.');
            }
            
            $parameters = ['justificativa' => $justificativa];
            
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('DELETE', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
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
