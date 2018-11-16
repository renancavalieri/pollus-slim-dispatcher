<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Dispatcher;

use Pollus\Slim\Annotations\AnnotationReaderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;
use Pollus\Slim\Exceptions\MethodNotAllowedException;

/**
 * A interface ControllerReflectionInterface é utilizada para determinar 
 */
interface ControllerReflectionInterface 
{
    /**
     * O método construtor deverá seguir este padrão em todas as implementações,
     * pois ele é utilizado e construído dentro do {@see SlimDispatcher}.
     * 
     * Também é possível utilizar suas implementações de forma desacoplada do
     * {@see SlimDispatcher}
     * 
     * @param string $controller
     * @param string $method
     * @param ServerRequestInterface
     * @param ResponseInterface
     */
    public function __construct(string $controller, string $method, ServerRequestInterface $request, ResponseInterface $response, ContainerInterface $container);
    
    /**
     * Define os argumentos que serão enviados para o método do controller.
     * 
     * @param array $args
     * @return ControllerReflectionInterface
     */
    public function setMethodArgs(array $args) : ControllerReflectionInterface;
    
    /**
     * Retorna os argumentos que serão enviados para o método do controller
     * 
     * @return array
     */
    public function getMethodArgs() : array;
    
    /**
     * Retorna as anotações do método solicitado
     * 
     * @return Reader
     */
    public function getMethodAnnotations() : AnnotationReaderInterface;
    
    /**
     * Retorna as anotações do controller solicitado
     * 
     * @return Reader
     */
    public function getControllerAnnotations() : AnnotationReaderInterface;
    
    /**
     * Executa o controller e retorna seu resultado.
     * 
     * @return ResponseInterface
     */
    public function run() : ResponseInterface;
    
    /**
     * Define os argumentos do construtor do controller
     * 
     * Quando NULL for informado, o controller será instanciado utilizando o
     * construtor padrão, de acordo com a interface {@see ControllerInterface}
     * 
     * @param array $args
     */
    public function setConstructorArgs(?array $args) : ControllerReflectionInterface;
    
    /**
     * Retorna os argumentos do controller
     * 
     * @return array
     */
    public function getConstructorArgs() : array;
    
    /**
     * Retorna uma {@see \ReflectionClass} do Controller
     * 
     * @return \ReflectionClass
     */
    public function getClassReflection() : \ReflectionClass;
    
    /**
     * Retorna uma {@see \ReflectionMethod} do método do Controller
     * 
     * @return \ReflectionMethod
     */
    public function getMethodReflection() : \ReflectionMethod;
    
    /**
     * Define a classe que realizará a leitura dos blocos phpdoc
     * 
     * Padrão: {@see Pollus\Slim\Annotations\AnnotationReader}
     * 
     * @param string|null $className
     * @return ControllerReflectionInterface
     */
    public function setAnnotationReaderClass(?string $className) : ControllerReflectionInterface;
    
    
     /**
     * Valida o método HTTP aceito pelos controllers.
      * 
      * Todos os controllers devem especificar qual tipo de método
     * aceitam, utilizando uma anotação no formato phpdoc, como demonstram os
     * exemplos abaixo:
     * 
     * EXEMPLOS
     * 
     *  Aceita solicitações do tipo POST
     *  @method POST
     * 
     *  Aceita solicitações do tipo GET
     *  @method GET
     * 
     *  Aceita solicitações do tipo GET e POST
     *  @method GET
     *  @method POST
     * 
     * Caso esta função não seja chamada, os controllers não precisam conter 
      * estas anotações.
     * 
     * @param bool $status
     * @return ControllerReflectionInterface
      * @throws MethodNotAllowedException
     */
    public function validateMethodType() : ControllerReflectionInterface;
    
    /**
     * Adiciona um evento para ser acionado antes do método Run();
     * 
     * Os eventos adicionados serão executados na ordem em que são adicionados e
     * estes podem ou não retornar resultados.
     * 
     * No caso de um evento retornar um resultado (no caso, obrigatoriamente um
     * objeto que implemente a interface {@see ResponseInterface}), a execução será
     * interrompida e os eventos subsequentes, incluindo a própria execução do
     * controller, não ocorrerão.
     * 
     * @param ControllerEventInterface $event
     */
    public function hookEvent(ControllerEventInterface $event) : ControllerReflectionInterface;
    
    /**
     * Retorna o objeto de requisição
     * 
     * @return ServerRequestInterface
     */
    public function getRequest() : ServerRequestInterface;
    
    /**
     * Define o objeto da requisição
     * 
     * @param ServerRequestInterface $request
     * @return ControllerReflectionInterface
     */
    public function setRequest(ServerRequestInterface $request) : ControllerReflectionInterface;
    
    /**
     * Retorna o objeto de resposta
     * 
     * @return ResponseInterface
     */
    public function getResponse() : ResponseInterface;
    
    /**
     * Define o objeto de reposta
     * 
     * @param ResponseInterface $response
     * @return ControllerReflectionInterface
     */
    public function setResponse(ResponseInterface $response) : ControllerReflectionInterface;
    
    /**
     * Retorna o container
     * 
     * @return ContainerInterface
     */
    public function getContainer() : ContainerInterface;
    
    /**
     * Define o container
     * 
     * @param ContainerInterface $container
     * @return ControllerReflectionInterface
     */
    public function setContainer(ContainerInterface $container) : ControllerReflectionInterface;
    
    
    /**
     * Remove todos os eventos
     * 
     * @return ControllerReflectionInterface
     */
    public function clearEvents() : ControllerReflectionInterface;
    
}
