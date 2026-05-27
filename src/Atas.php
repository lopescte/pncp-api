<?php
namespace Lopescte\PncpApi;

use Transliterator;

/**
 * Class Atas
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
class Atas
{
    public $response = NULL;
    

    /**
     * @method consultaAtasPorCompra()
     * @string cnpj       // CNPJ do Orgao
     * @int ano           // Ano da Compra
     * @int compra        // Numero sequencial da compra
     */
    public function consultaAtasPorCompra(string $cnpj=NULL, int $ano=NULL, int $compra=NULL)
    {             
        try
        {
            if(empty($cnpj)){
                throw new \Exception('CNPJ do órgão não pode ser vazio.');
            }
            if(empty($ano)){
                throw new \Exception('Ano da compra não pode ser vazio.');
            }
            if(empty($compra)){
                throw new \Exception('ID da compra não pode ser vazio.');
            }
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $cnpj) . '/compras/' . $ano . '/' . $compra .'/atas';
             
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*'
                                            ]
                                        ]);
            
            if($res->getStatusCode() === 200 && $body = json_decode($res->getBody(), true))
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
     * @method consultaAtaPorUrl()
     * @string url       // URL da Ata no PNCP
     */
    public function consultaAtaPorUrl(string $url=NULL)
    {             
        try
        {
            if(empty($url)){
                throw new \Exception('URL da Ata não pode ser vazia.');
            }
                         
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*'
                                            ]
                                        ]);
            
            if($res->getStatusCode() === 200 && $body = json_decode($res->getBody(), true))
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
     * @method consultaAtaPorControle()
     * @string controle       // Controle da Ata no PNCP
     */
    public function consultaAtaPorControle(string $controleata=NULL)
    {             
        try
        {
            if(empty($controleata)){
                throw new \Exception('Número de Controle da Ata não pode ser vazio.');
            }
             
            $ata = preg_split('/\W+/', $controleata, -1, PREG_SPLIT_NO_EMPTY);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $ata[0]) . '/compras/' . $ata[3] . '/' . $ata[2] . '/atas/' . $ata[4];

            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*'
                                            ]
                                        ]);
            
            if($res->getStatusCode() === 200 && $body = json_decode($res->getBody(), true))
            {
                $this->response = $body;
                $this->response['location'] = $url;
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
     * @method consultaContratosAtaPorUrl()
     * @string url       // URL da Ata no PNCP
     */
    public function consultaContratosAtaPorUrl(string $url=NULL, int $pagina=1, int $tamanhoPagina=999)
    {             
        try
        {
            if(empty($url)){
                throw new \Exception('URL da Ata não pode ser vazia.');
            }
            
            $url .= '/contratos';
                     
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ],
                                            'json' => [ 'pagina' => $pagina, 'tamanhoPagina' => $tamanhoPagina ]
                                        ]);
            
            if($res->getStatusCode() === 200 && $body = json_decode($res->getBody(), true))
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
     * @method consultaDocumentosAtaPorUrl()
     * @string url       // URL da Ata no PNCP
     */
    public function consultaDocumentosAtaPorUrl(string $url=NULL)
    {             
        try
        {
            if(empty($url)){
                throw new \Exception('URL da Ata não pode ser vazia.');
            }
            
            $url .= '/arquivos';
                     
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*'
                                            ]
                                        ]);
            
            if($res->getStatusCode() === 200 && $body = json_decode($res->getBody(), true))
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
     * @method consultaPartesEnvolvidasAtaPorUrl()
     * @string url       // URL da Ata no PNCP
     */
    public function consultaPartesEnvolvidasAtaPorUrl(string $url=NULL, int $pagina=1, int $tamanhoPagina=999)
    {             
        try
        {
            if(empty($url)){
                throw new \Exception('URL da Ata não pode ser vazia.');
            }
            
            $url .= '/partesenvolvidas';
            
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ],
                                            'json' => [ 'pagina' => $pagina, 'tamanhoPagina' => $tamanhoPagina ]
                                        ]);
            
            if($res->getStatusCode() === 200 && $body = json_decode($res->getBody(), true))
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
     * @method consultaParteEnvolvidaAtaPorControle()
     * @string controleata          // URL do arquivo
     * @array parameters            // {
     *                                    "tipoParteEnvolvidaId": 1,
     *                                    "cnpj": "00394460000141",
     *                                    "codigoUnidadeCompradora": "1"
     *                                 }
     */
    public function consultaParteEnvolvidaAtaPorControle(string $controleata=NULL, array $parameters=NULL)
    {             
        try
        {
            if(empty($controleata)){
                throw new \Exception('Número de Controle da ATA no PNCP não pode ser vazio.');
            }            
                        
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/atas/novoParticipante.json'));
            
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
                                                
            $ata = preg_split('/\W+/', $controleata, -1, PREG_SPLIT_NO_EMPTY);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $ata[0]) . '/compras/' . $ata[3] . '/' . $ata[2] . '/atas/' . $ata[4] . '/partesenvolvidas/'.$parameters['cnpj'].'/'.$parameters['codigoUnidadeCompradora'].'/'.$parameters['tipoParteEnvolvidaId'];
            
            
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            if($res->getStatusCode() === 200 && $body = json_decode($res->getBody(), true))
            {
                $this->response = $body;
                $this->response['location'] = $url;
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
     * @method insereAta()
     * @string controle      // ID de controle da compra no PNCP
     * @array  parameters    // [
     *                            "numeroAtaRegistroPreco" => "1/2021", 
     *                            "anoAta" => 2021, 
     *                            "dataAssinatura" => "2021-07-01", 
     *                            "dataInicioVigencia" => "2021-07-01", 
     *                            "dataFimVigencia" => "2021-07-01", 
     *                            "possibilidadeAdesao" => TRUE,
     *                            "partesEnvolvidas" => [
     *                            {
     *                                "tipoParteEnvolvidaId" => 1,
     *                                "cnpj" => "10000000000003",
     *                                "codigoUnidadeCompradora" => "1010"
     *                            }
     *                          ]
     */
    public function insereAta(string $controle=NULL, array $parameters=NULL, string $documento=NULL, string $nome_documento=NULL, int $tipo_documento=11)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($controle)){
                throw new \Exception('ID Controle da compra não pode ser vazio.');
            }
            
            if (empty($documento)) {
                throw new \Exception('Documento não pode ser vazio.');
            }
            
            $tmpdoc = Pncp::getFile($documento);
                        
            $data = new \StdClass;     
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/atas/novaAta.json'));
            
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
                    $msg .= $error['property']. ' - ' . $error['message']."<br>";
                }
                throw new \Exception($msg);
            }            
            
            $partes = preg_split('/\W+/', $controle, -1, PREG_SPLIT_NO_EMPTY);
            
            $tmpfile = tempnam(sys_get_temp_dir(), uniqid().'.json');
            file_put_contents($tmpfile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $partes[0]) . '/compras/' . $partes[3] . '/' . $partes[2] . '/atas';
             
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
                                                    'name' => 'ata',
                                                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen(urldecode($tmpfile), 'r'),
                                                    'headers'  => ['Content-Type' => mime_content_type(urldecode($tmpfile))]
                                                ],
                                                [
                                                    'name' => 'documento',
                                                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen(urldecode($tmpdoc), 'r'),
                                                    'headers'  => ['Content-Type' => mime_content_type(urldecode($tmpdoc))]
                                                ]
                                            ]
                                        ]);
            
            if($result->getStatusCode() === 200 && $body = json_decode($result->getBody(), true))
            {
                $this->response = $body;
                $this->response['location'] = $result->getHeader('location')[0];
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
     * @method insereDocumentoAta()
     * @string controle       // Número de Controle no PNCP
     * @string arquivo        // URL do arquivo
     * @string nome_documento // Nome do Documento (Ex.: Aviso de Licitação)
     * @int tipo_documento    // ID do tipo de documento na tabela de Dominio do PNCP
     */
    public function insereDocumentoAta(string $controlecompra=NULL, string $controleata=NULL, string $arquivo=NULL, string $nome_documento=NULL, int $tipo_documento=NULL)
    {
        try
        {    
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($controlecompra)){
                throw new \Exception('Número de Controle da Compra no PNCP não pode ser vazio.');
            }
            
            if(empty($controleata)){
                throw new \Exception('Número de Controle da ATA no PNCP não pode ser vazio.');
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
                                                
            $compra = preg_split('/\W+/', $controlecompra, -1, PREG_SPLIT_NO_EMPTY);
            $ata = preg_split('/\W+/', $controleata, -1, PREG_SPLIT_NO_EMPTY);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $compra[0]) . '/compras/' . $compra[3] . '/' . $compra[2] . '/atas/' . $ata[4] . '/arquivos';
            
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('POST', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Titulo-Documento' => transliterator_transliterate('Any-Latin; Latin-ASCII', $nome_documento),
                                                'Tipo-Documento' => $tipo_documento,
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'multipart' => [
                                                [
                                                    'name' => 'arquivo',
                                                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen(urldecode($arquivo), 'r'),
                                                    'headers'  => ['Content-Type' => mime_content_type(urldecode($arquivo))]
                                                ]
                                            ]
                                        ]);
            
            if($result->getStatusCode() === 200 && $location = $result->getHeader('location')[0])
            {
                $this->response['location'] = $location;
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
     * @method insereParteEnvolvidaAta()
     * @string controleata          // URL do arquivo
     * @array parameters            // {
     *                                    "tipoParteEnvolvidaId": 1,
     *                                    "cnpj": "00394460000141",
     *                                    "codigoUnidadeCompradora": "1"
     *                                 }
     */
    public function insereParteEnvolvidaAta(string $controleata=NULL, array $parameters=NULL)
    {
        try
        {    
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($controleata)){
                throw new \Exception('Número de Controle da ATA no PNCP não pode ser vazio.');
            }            
                        
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/atas/novoParticipante.json'));
            
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
                                                
            $ata = preg_split('/\W+/', $controleata, -1, PREG_SPLIT_NO_EMPTY);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $ata[0]) . '/compras/' . $ata[3] . '/' . $ata[2] . '/atas/' . $ata[4] . '/partesenvolvidas';
            
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('POST', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => [ $parameters ]
                                        ]);
            
            if($result->getStatusCode() === 200 && $location = $result->getHeader('location')[0])
            {
                $this->response['location'] = $location.'/'.$parameters['cnpj'].'/'.$parameters['codigoUnidadeCompradora'].'/'.$parameters['tipoParteEnvolvidaId'];
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
     * @method deletaAtaPorUrl()
     * @string url           // URL da ata no PNCP
     * @string justificativa // Justificativa para a exclusão da compra
     */
    public function deletaAtaPorUrl(string $url=NULL, string $justificativa=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            if(empty($url)){
                throw new \Exception('URL da ATA não pode ser vazio.');
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
            
            if($result->getStatusCode() === 200 && $res = $result->getHeaders())
            {
                $this->response = $res;
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
     * @method deletaParteEnvolvidaAtaPorUrl()
     * @string url           // URL da ata no PNCP
     * @string justificativa // Justificativa para a exclusão da compra
     */
    public function deletaParteEnvolvidaAtaPorUrl(string $url=NULL, string $justificativa=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            if(empty($url)){
                throw new \Exception('URL da ATA não pode ser vazio.');
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
                                        
            if($result->getStatusCode() === 200 && $res = $result->getHeaders())
            {
                $this->response = $res;
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
     * @method alteraAtaPorUrl()
     * @string url           // URL da ata no PNCP
     * @array  parameters    // [
     *                            "numeroAtaRegistroPreco" => "1/2021", 
     *                            "anoAta" => 2021, 
     *                            "dataAssinatura" => "2021-07-01", 
     *                            "dataInicioVigencia" => "2021-07-01", 
     *                            "dataFimVigencia" => "2021-07-01", 
     *                            "possibilidadeAdesao" => TRUE,
     *                            "partesEnvolvidas" => [
     *                            {
     *                                "tipoParteEnvolvidaId" => 1,
     *                                "cnpj" => "10000000000003",
     *                                "codigoUnidadeCompradora" => "1010"
     *                            }
     *                            "cancelado" => true,                         // apenas se para Cancelamento da ATA
     *                            "dataCancelamento" => "2023-01-01T12:00:00", // apenas se cancelado = true
     *                            "justificativa" => "motivo/justificativa para a retificação dos atributos da ATA"
     *                          ] 
     */
    public function alteraAtaPorUrl(string $url=NULL, array $parameters=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            if(empty($url)){
                throw new \Exception('URL da ATA não pode ser vazio.');
            }
            
            $data = new \StdClass;     
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/atas/alteraAta.json'));
            
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
                    $msg .= $error['property']. ' - ' . $error['message']."<br>";
                }
                throw new \Exception($msg);
            }
            
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('PUT', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
                                        ]);
                                        
            if($result->getStatusCode() === 200 && $res = $result->getHeaders())
            {
                $this->response = $res;
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
