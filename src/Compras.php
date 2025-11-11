<?php
namespace Lopescte\PncpApi;

use Transliterator;
/**
 * Class Compras
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
class Compras
{
    public $response = NULL;
    
    /**
     * @method consultaCompra()
     * @string controle   // ID de Controle do PNCP
     */
    public function consultaCompra($controle = NULL)
    {             
        try
        {
            $id = Pncp::validaControlePncp($controle);
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'];
            
            $url = preg_replace("/api\/pncp/", "api/consulta", $url);
            
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            $this->response['uri'] = $url;
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
     * @method consultaCompraPorUrl()
     * @string controle   // ID de Controle do PNCP
     */
    public function consultaCompraPorUrl($url = NULL)
    {             
        try
        {
            if(empty($url)){
                throw new \Exception('URL da compra PNCP não pode ser vazio.');
            }
            
            $url = preg_replace("/pncp-api/", "api/consulta", $url);
            
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            $this->response['uri'] = $url;
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
     * @method consultaItensCompra()
     * @string controle      // ID de Controle do PNCP
     * @string item          // Numero do item da compra
     * @string pagina        // Numero da pagina em paginacao
     * @string tamanhoPagina // Quantidade de itens por pagina (o PNCP retorna 10 itens por padrao)
     */
    public function consultaItensCompra(string $controle=NULL, int $item=NULL, int $pagina=1, int $tamanhoPagina=9999)
    {
        try
        {
            if(empty($controle)){
                throw new \Exception('ID de controle da compra não pode ser vazio.');
            }
            
            $id = Pncp::validaControlePncp($controle);
            
            $parameters = ['cnpj'=>$id['cnpj'],
                           'ano'=>$id['ano'],
                           'sequencial'=>intval($id['numero']),
                           'pagina'=>$pagina,
                           'tamanhoPagina'=>$tamanhoPagina];
            
            $id_item = (isset($item)) ? '/'.$item : '';
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'] .'/itens' . $id_item;

            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ],
                                            'query' => $parameters
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
     * @method consultaDocumentosCompra()
     * @string controle      // ID de Controle do PNCP
     */
    public function consultaDocumentosCompra(string $controle=NULL)
    {
        try
        {
            if(empty($controle)){
                throw new \Exception('ID de controle da compra não pode ser vazio.');
            }
            
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            $id = Pncp::validaControlePncp($controle);
                        
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'] .'/arquivos';
             
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
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
     * @method baixarDocumentoCompra()
     * @string uri      // URI do documento no PNCP
     */
    public function baixarDocumentoCompra(string $uri=NULL)
    {
        try
        {
            // Valida a URI
            if (!filter_var($uri, FILTER_VALIDATE_URL)) {
                throw new \Exception('URL inválida.');
            }
            
            // Verifica o dominio
            if(!preg_match('/(^|\.)pncp\.gov\.br$/i', parse_url($uri, PHP_URL_HOST))){
                throw new \Exception('URI do documento não pertence ao PNCP.');
            }
             
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('GET', $uri, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                            ]
                                        ]);
            
            $body = $res->getBody()->getContents();
            $headers = $res->getHeaders();
                        
            $contentType = Pncp::getHeaderValue($headers, 'Content-Type') ?? 'application/octet-stream';
            
            $filename = uniqid();
            $contentDisposition = Pncp::getHeaderValue($headers, 'Content-Disposition');
            
            if ($contentDisposition && preg_match('/filename="?([^"]+)"?/i', $contentDisposition, $matches)) {
                $filename = $matches[1];
            } else {
                // Deduz a extensão com base no Content-Type
                $ext = match ($contentType) {
                    'application/pdf' => 'pdf',
                    'application/zip' => 'zip',
                    'application/rtf' => 'rtf',
                    'application/msword' => 'doc',
                    'application/vnd.ms-excel' => 'xls',
                    'application/vnd.ms-powerpoint' => 'ppt',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                    'application/vnd.oasis.opendocument.text' => 'odt',
                    'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'text/plain' => 'txt',
                    default => 'bin',
                };
                $filename .= '.' . $ext;
            }
            
            $this->response['filename'] = $filename;
            $this->response['content'] = $body;
            
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
     * @method insereCompra()
     * @array parameters   //[
     *                          "codigoUnidadeCompradora" => "1010", 
     *                          "tipoInstrumentoConvocatorioId" => "1", 
     *                          "modalidadeId" => "6", 
     *                          "modoDisputaId" => "1", 
     *                          "numeroCompra" => "1", 
     *                          "anoCompra" => 2022, 
     *                          "numeroProcesso" => "1/2021", 
     *                          "objetoCompra" => "Compra para exemplificar uso da aplicação", 
     *                          "informacaoComplementar" => "", 
     *                          "srp" => false, 
     *                          "orcamentoSigiloso" => false, 
     *                          "dataAberturaProposta" => "2022-07-21T08:00:00", 
     *                          "dataEncerramentoProposta" => "2022-07-21T17:00:00", 
     *                          "amparoLegalId" => "1", 
     *                          "linkSistemaOrigem" => "url do sistema de origem para envio de proposta", 
     *                          "itensCompra" => [
     *                                [
     *                                   "numeroItem" => 1, 
     *                                   "materialOuServico" => "S", 
     *                                   "tipoBeneficioId" => "4", 
     *                                   "incentivoProdutivoBasico" => false, 
     *                                   "descricao" => "Item para exemplificar uso da aplicação", 
     *                                   "quantidade" => 1000, 
     *                                   "unidadeMedida" => "Unidade", 
     *                                   "valorUnitarioEstimado" => 1.5001, 
     *                                   "valorTotal" => 1500, 
     *                                   "criterioJulgamentoId" => "1" 
     *                                ]
     *                             ] 
     *                       ]
     */
    public function insereCompra(string $cnpj=NULL, string $documento=NULL, string $nome_documento=NULL, int $tipo_documento=NULL, array $parameters=NULL)
    {
        try
        {    
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($cnpj)){
                throw new \Exception('CNPJ do órgão não pode ser vazio.');
            }
            
            if(empty($documento) || !file_exists(urldecode($documento))){
                throw new \Exception('Documento não pode ser vazio.');
            }
                    
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/compras/novaCompra.json'));
            
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
                throw new \Exception('Dados inválidos para inserir Contratação. <br><br>'.$msg);
            }
            
            $tmpfile = tempnam(sys_get_temp_dir(), uniqid().'.json');
            file_put_contents($tmpfile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/'. preg_replace("/\D/", "", $cnpj) . '/compras';
                         
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
                                                    'name' => 'compra',
                                                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen(urldecode($tmpfile), 'r'),
                                                    'headers'  => ['Content-Type' => mime_content_type(urldecode($tmpfile))]
                                                ],
                                                [
                                                    'name' => 'documento',
                                                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen(urldecode($documento), 'r'),
                                                    'headers'  => ['Content-Type' => mime_content_type(urldecode($documento))]
                                                ]
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
     * @method insereItemCompra()
     * @string controle    // ID de controle da compra no PNCP
     * @array parameters   //[
     *                          [
     *                            "numeroItem" => 1, 
     *                            "materialOuServico" => "M", 
     *                            "tipoBeneficioId" => "4", 
     *                            "incentivoProdutivoBasico" => false, 
     *                            "descricao" => "Item exemplificativo", 
     *                            "quantidade" => 100, 
     *                            "unidadeMedida" => "Unidade", 
     *                            "valorUnitarioEstimado" => 1, 
     *                            "valorTotal" => 100, 
     *                            "criterioJulgamentoId" => "1" 
     *                           ] 
     *                        ]
     */
    public function insereItemCompra(string $controle=NULL, array $parameters=NULL)
    {
        try
        {   
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
             
            if(empty($controle)){
                throw new \Exception('ID de Controle da Compra não pode ser vazio.');
            }
            
            if(empty($parameters) || !is_array($parameters))
            {
                throw new \Exception('Um array() de dados deve ser enviado. Consulte o schema.');
            }   
            
            $id = Pncp::validaControlePncp($controle);
             
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/compras/novoItem.json'));
            
            // Validate
            $validator = new \JsonSchema\Validator();
            $validator->validate($parameters, $schema);
                                               
            if (!$validator->isValid()) {                
                $msg = NULL;
                foreach ($validator->getErrors() as $error) {
                    $msg .= $error['property']. ' - ' . $error['message']."<br>";
                }
                throw new \Exception('Dados inválidos para inserir Item de Contratação. <br><br>'.$msg);
            } 
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/'. preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'] . '/itens';
             
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
     * @method alteraCompra()
     * @string controle   // ID de Controle do PNCP
     */
    public function alteraCompra($controle = NULL, array $parameters=NULL)
    {             
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception('Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.');
            }
             
            $id = Pncp::validaControlePncp($controle);            
                                
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/compras/alteraCompra.json'));
            
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
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'];
             
            $client = new \GuzzleHttp\Client();            
            $res = $client->request('PUT', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $parameters
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
     * @method alteraItemCompra()
     * @array parameters   //[
     *                         "materialOuServico" => "M", 
     *                         "tipoBeneficioId" => "4", 
     *                         "incentivoProdutivoBasico" => false, 
     *                         "descricao" => "Item exemplificativo", 
     *                         "quantidade" => 100, 
     *                         "unidadeMedida" => "Unidade", 
     *                         "valorUnitarioEstimado" => 1, 
     *                         "valorTotal" => 100, 
     *                         "situacaoCompraItemId" => "1",
     *                         "criterioJulgamentoId" => "1",
     *                         "justificativa" => "motivo/justificativa para a retificação dos atributos do item da compra"
     *                       ] 
     */
    public function alteraItemCompra(string $controle=NULL, int $item=NULL, array $parameters=NULL)
    {
        try
        {   
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception('Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.');
            }
             
            $id = Pncp::validaControlePncp($controle);
            
            if(empty($item)){
                throw new \Exception('Número do item da compra não pode ser vazio.');
            }
                    
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/compras/alteraItem.json'));
            
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
                throw new \Exception('Dados inválidos para alterar Item da Contratação. <br><br>'.$msg);
            }    
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/'. preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'] . '/itens/' . $item;
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('PATCH', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $data
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
     * @method cancelaItemCompra()
     * @array parameters   //[
     *                         "materialOuServico" => "M", 
     *                         "tipoBeneficioId" => "4", 
     *                         "incentivoProdutivoBasico" => false, 
     *                         "descricao" => "Item exemplificativo", 
     *                         "quantidade" => 100, 
     *                         "unidadeMedida" => "Unidade", 
     *                         "valorUnitarioEstimado" => 1, 
     *                         "valorTotal" => 100, 
     *                         "situacaoCompraItemId" => "1",
     *                         "criterioJulgamentoId" => "1",
     *                         "justificativa" => "motivo/justificativa para a retificação dos atributos do item da compra"
     *                       ] 
     */
    public function cancelaItemCompra(string $controle=NULL, int $item=NULL, array $parameters=NULL)
    {
        try
        {    
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            }
            
            $id = Pncp::validaControlePncp($controle);
            
            if(empty($item)){
                throw new \Exception('Número do item da compra não pode ser vazio.');
            }
                    
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/compras/cancelaItem.json'));
            
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
                throw new \Exception('Dados inválidos para Cancelar Item de Contratação. <br><br>'.$msg);
            }    
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/'. preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'] . '/itens/' . $item;
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('PATCH', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $data
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
     * @method insereDocumentoCompra()
     * @string controle       // Número de Controle no PNCP
     * @string arquivo        // URL do arquivo
     * @string nome_documento // Nome do Documento (Ex.: Aviso de Licitação)
     * @int tipo_documento    // ID do tipo de documento na tabela de Dominio do PNCP
     */
    public function insereDocumentoCompra(string $controle=NULL, string $arquivo=NULL, string $nome_documento=NULL, int $tipo_documento=NULL)
    {
        try
        {    
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            $id = Pncp::validaControlePncp($controle);            
                        
            if(empty(urldecode($arquivo)) || !file_exists(urldecode($arquivo))){
                throw new \Exception('Arquivo não localizado ou não informado.');
            }
            
            if(empty($nome_documento)){
                throw new \Exception('Nome do Documento não pode ser vazio.');
            }
            
            if(empty($tipo_documento)){
                throw new \Exception('ID do tipo do Documento não pode ser vazio.');
            }            
                                                            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'] . '/arquivos';
             
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
                                                    'headers'  => ['Content-Type' => mime_content_type(urldecode($arquivo))]
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
     * @method insereResultadoItem()
     * @array parameters   //{
     *                             "quantidadeHomologada": 0,
     *                             "valorUnitarioHomologado": 0,
     *                             "valorTotalHomologado": 0,
     *                             "percentualDesconto": 0,
     *                             "tipoPessoaId": "PJ",
     *                             "niFornecedor": "string",
     *                             "nomeRazaoSocialFornecedor": "string",
     *                             "porteFornecedorId": "1",
     *                             "codigoPais": "BRA",
     *                             "indicadorSubcontratacao": true,
     *                             "ordemClassificacaoSrp": 1,
     *                             "dataResultado": "2023-01-20",
     *                             "naturezaJuridicaId": "string"
     *                        }
     */
    public function insereResultadoItem(string $controle=NULL, int $item=NULL, array $parameters=NULL)
    {
        try
        { 
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
               
            $id = Pncp::validaControlePncp($controle);
            
            if(empty($item)){
                throw new \Exception('Número do item da compra não pode ser vazio.');
            }
                    
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/compras/novoResultadoItem.json'));
            
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
                throw new \Exception('Dados inválidos para Inserir Resultado de Contratação. <br><br>'.$msg);
            }            
                
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/'. preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'] . '/itens/' . $item . '/resultados';
             
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
    
    /** alteraResultadoItem()
     * @array parameters   //{
     *                             "quantidadeHomologada" => 0,
     *                             "valorUnitarioHomologado" => 0,
     *                             "valorTotalHomologado" => 0,
     *                             "percentualDesconto" => 0,
     *                             "tipoPessoaId" => "PJ",
     *                             "niFornecedor" => "string",
     *                             "nomeRazaoSocialFornecedor" => "string",
     *                             "porteFornecedorId" => "1",
     *                             "codigoPais" => "BRA",
     *                             "indicadorSubcontratacao" => true,
     *                             "ordemClassificacaoSrp" => 1,
     *                             "dataResultado" => "2023-01-20",
     *                             "naturezaJuridicaId" => "string"
     *                        }
     */
    public function alteraResultadoItem(string $controle=NULL, int $item=NULL, int $resultado=NULL, array $parameters=NULL)
    {
        try
        {  
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
              
            $id = Pncp::validaControlePncp($controle);
            
            if(empty($item)){
                throw new \Exception('Número do item da compra não pode ser vazio.');
            }
            
            if(empty($resultado)){
                throw new \Exception('Número do resultado do item da compra não pode ser vazio.');
            }
                    
            $data = new \StdClass;
            $schema = json_decode(file_get_contents(__DIR__.'/schemas/compras/alteraResultadoItem.json'));
            
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
                throw new \Exception('Dados inválidos para Alterar Resultado de Contratação. <br><br>'.$msg);
            }            
               
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/'. preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'] . '/itens/' . $item . '/resultados/' . $resultado;
             
            $client = new \GuzzleHttp\Client();            
            $result = $client->request('PUT', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json',
                                                'Authorization' => Pncp::getAccessToken()
                                            ],
                                            'json' => $data
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
     * @method deletaCompra()
     * @string controle      // ID de controle no PNCP
     * @string justificativa // Justificativa para a exclusão da compra
     */
    public function deletaCompra(string $controle=NULL, string $justificativa=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            $id = Pncp::validaControlePncp($controle);
            
            if(empty($justificativa)){
                throw new \Exception('Justificativa da exclusão não pode ser vazio.');
            }
            
            $parameters = ['justificativa' => $justificativa];
            
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/orgaos/' . preg_replace("/\D/", "", $id['cnpj']) . '/compras/' . $id['ano'] . '/' . $id['numero'];
             
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
     * @method deletaDocumentoPorUrl()
     * @string URL           // URL do documento no PNCP
     * @string justificativa // Justificativa para a exclusão da compra
     */
    public function deletaDocumentoPorUrl(string $url=NULL, string $justificativa=NULL)
    {
        try
        {
            if (empty(Pncp::getAccessToken())) {
                throw new \Exception("Esta operação requer autenticação. Inicialize a Conexão ao PNCP primeiro.");
            } 
            
            if(empty($url)){
                throw new \Exception('URL do documento no PNCP não pode ser vazio.');
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
