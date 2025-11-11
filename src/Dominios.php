<?php
namespace Lopescte\PncpApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
/**
 * Class Dominios
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
class Dominios
{
    private $client;
    public $response = NULL;
    
    function __construct()
    {
        if(empty(Pncp::getBaseUrl()) || empty(Pncp::getVersion())){
            throw new Exception('Inicialize a conexão ao PNCP antes.');
        }
        
        $this->client = new Client();
    }
    
    /**
     * @method consultaAmparosLegais()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false, 
     *                          'tipoAmparoLegalId'=>$tipoAmparoLegalId
     *                        ]
     */
    public function consultaAmparosLegais($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/amparos-legais' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @method consultaCategoriaItemPca()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaCategoriaItemPca($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/categoriaItemPcas' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    } 
    
    /**
     * @method consultaCatalogo()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaCatalogo($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/catalogos' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    } 
    
    /**
     * @method consultaInstrumentosConvocatorios()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaTiposInstrumentoConvocatorio($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/tipos-instrumentos-convocatorios' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }      
        
    /**
     * @method consultaModalidades()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaModalidades($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/modalidades' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }  
    
    /**
     * @method consultaModosDisputa()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaModosDisputa($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/modos-disputas' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }   
    
    /**
     * @method consultaCriteriosJulgamento()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaCriteriosJulgamento($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/criterios-julgamentos' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }   
    
    /**
     * @method consultaTiposInstrumentoCobranca()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaTiposInstrumentoCobranca($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/tipos-instrumentos-cobranca' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @method consultaFontesOrcamentarias()
     * @array parameters   // [ 'id'=>$id, 
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaFontesOrcamentarias($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = (isset($parameters['id'])) ? '/'. $parameters['id'] : '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/fontes-orcamentarias' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @method consultaRegraInstrumentoModalidadeAmparo()
     * @array parameters   // [ 'amparoLegalId'=>$amparoLegalId, 
     *                          'modalidadeId'=>$modalidadeId,
     *                          'tipoInstrumentoConvocatorioId'=>$tipoInstrumentoConvocatorioId,
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaRegraInstrumentoModalidadeAmparo($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/instrumento-convocatorio-modalidade-amparo-legal' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @method consultaRegraInstrumentoModoDisputa()
     * @array parameters   // [ 'tipoInstrumentoConvocatorioId'=>$tipoInstrumentoConvocatorioId, 
     *                          'modoDisputaId'=>$modoDisputaId,
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaRegraInstrumentoModoDisputa($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/tipo-instrumento-convocatorio-modo-disputa' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @method consultaRegraModalidadeCriterio()
     * @array parameters   // [ 'modalidadeId'=>$modalidadeId, 
     *                          'criterioJulgamentoId'=>$criterioJulgamentoId,
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaRegraModalidadeCriterio($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/modalidade-criterio-julgamento' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @method consultaRegraModalidadeFonte()
     * @array parameters   // [ 'modalidadeId'=>$modalidadeId, 
     *                          'fonteOrcamentariaId'=>$fonteOrcamentariaId,
     *                          'statusAtivo'=>true/false
     *                        ]
     */
    public function consultaRegraModalidadeFonte($parameters = NULL)
    {             
        try
        { 
            $queryString = NULL;
                
            if(is_array($parameters))
            {
                $queryString = '?'. http_build_query($parameters);
            } 
                                                      
            $url = Pncp::getBaseUrl() . '/' . Pncp::getVersion() . '/modalidade-fonte-orcamentaria' . $queryString;
             
            $res = $this->client->request('GET', $url, [
                                            'headers' => [
                                                'Accept' => '*/*',
                                                'Content-Type' => 'application/json'
                                            ]
                                        ]);
            
            $this->response = json_decode($res->getBody(), true);
            return ['response' => $this->response];               
        }
        catch (RequestException $e) {
    	    if ($e->hasResponse()) {
    		$error = json_decode($e->getResponse()->getBody(), TRUE);
                	throw new Exception("{$error['error']} <br><br> {$error['message']}");
    	    }
    	    throw new Exception($e->getMessage());
        }
    }               
}
