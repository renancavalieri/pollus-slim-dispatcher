<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Dispatcher;

use Psr\Http\Message\ResponseInterface;
use Pollus\Slim\Annotations\AnnotationReaderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Pollus\Slim\Annotations\AnnotationReader;
use Psr\Container\ContainerInterface;
use Pollus\Slim\Exceptions\NotFoundException;
use Pollus\Slim\Exceptions\MethodNotAllowedException;

/**
 * Esta classe prepara o controller para ser executado, repassando os argumentos
 * para o construtor e fornece métodos para obter suas anotações.
 * 
 * Esta classe é instanciada pelo método Prepare() do {@see SlimDispatcher}, no 
 * entanto é possível utilizá-la fora dela, fornecendo os devidos argumentos
 * em seu construtor.
 */
class ControllerReflection implements ControllerReflectionInterface
{
    /**
     * @var string
     */
    protected $controller_str;
    
    /**
     * @var string
     */
    protected $method_str;
    
    /**
     * @var array
     */
    protected $method_args = [];
    
    /**
     * Armazena o construtor padrão do Controller
     * 
     * Quando esta variável estiver NULL, o construtor padrão será utilizado, que
     * corresponde à interface {@see ControllerInterface}
     * 
     * @var array|null
     */
    protected $constructor_args = null;
    
    /**
     * @var ServerRequestInterface
     */
    protected $request;
    
    /**
     * @var ResponseInterface
     */
    protected $response;
    
    /**
     * @var string
     */
    protected $annotation_class_str;
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * Eventos
     * 
     * @var array [ControllerEventInterface]
     */
    protected $events = [];

    /**
     * Esta implementação verifica se o controller e o método solicitado existem,
     * caso contrário, lança uma {@see NotFoundException}.
     * 
     * @param string $controller
     * @param string $method
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param ContainerInterface $container
     * 
     * @throws NotFoundException
     */
    public function __construct(string $controller, string $method, ServerRequestInterface $request, ResponseInterface $response, ContainerInterface $container)
    {
        $this->controller_str = $controller;
        
        $this->method_str = $method;
        $this->response = $response;
        $this->request = $request;
        $this->container = $container;
        
        try
        {
            $reflection =  new \ReflectionClass($controller);
        } 
        catch (\Exception $ex) 
        {
            throw new NotFoundException($request, $response);
        }
        
        if ($reflection->hasMethod($method) === false)
        {
            throw new NotFoundException($request, $response);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function run(): ResponseInterface 
    {
        $result = null;
        
        foreach($this->events as $e)
        {    
            $result = $e->execute($this);
            
            if ($result !== null)
            {
                return $result;
            }
        }
        
        $controller = $this->Build();
        return call_user_func_array([$controller, $this->method_str], $this->getMethodArgs());
    }
    
    /**
     * Constrói o controller
     */
    protected function Build()
    {
        $reflection = $this->getClassReflection();
        return call_user_func_array([$reflection, "newInstance"], $this->getConstructorArgs());
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodAnnotations(): AnnotationReaderInterface 
    {
        if ($this->annotation_class_str !== null)
        {
            $class = $this->annotation_class_str;
            return new $class($this->controller_str, $this->method_str, "method");
        }
        
        return new AnnotationReader($this->controller_str, $this->method_str, "method");
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodArgs(): array 
    {
        return $this->method_args;
    }

    /**
     * {@inheritDoc}
     */
    public function getConstructorArgs() : array
    {
        if ($this->constructor_args === null)
        {
            return [$this->getRequest(), $this->getResponse(), $this->getContainer()];
        }
        
        return $this->constructor_args;
    }

    /**
     * {@inheritDoc}
     */
    public function setMethodArgs(array $args) : ControllerReflectionInterface
    {
        $this->method_args = $args;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setConstructorArgs(?array $args) : ControllerReflectionInterface
    {
        $this->constructor_args = $args;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerAnnotations(): AnnotationReaderInterface 
    {
        if ($this->annotation_class_str !== null)
        {
            $class = $this->annotation_class_str;
            return new $class($this->controller_str);
        }
        
        return new AnnotationReader($this->controller_str);
    }

    /**
     * {@inheritDoc}
     */
    public function getClassReflection(): \ReflectionClass 
    {
        return new \ReflectionClass($this->controller_str);
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodReflection(): \ReflectionMethod 
    {
        return new \ReflectionMethod($this->controller_str, $this->method_str);
    }

    /**
     * {@inheritDoc}
     */
    public function setAnnotationReaderClass(?string $className): ControllerReflectionInterface
    {
        $this->annotation_class_str = $className;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function validateMethodType(): ControllerReflectionInterface
    {
        $method = $this->getMethodAnnotations()->get("method") ?? "";
        $method = (array) $method;
        $methods = array_map('strtoupper', $method);

        $current_method = $this->getRequest()->getMethod() ?? "";
        $current_method = strtoupper($current_method);

        foreach($methods as $m)
        {
            if ($m === "ANY" || $m === $current_method)
            {
                return $this;
            }
        }

        throw new MethodNotAllowedException($this->request, $this->response, $methods);
    }

    /**
     * {@inheritDoc}
     */
    public function hookEvent(ControllerEventInterface $event) : ControllerReflectionInterface
    {
        $this->events[] = $event;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container): ControllerReflectionInterface
    {
        $this->container = $container;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequest(ServerRequestInterface $request): ControllerReflectionInterface
    {
        $this->request = $request;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setResponse(ResponseInterface $response): ControllerReflectionInterface
    {
        $this->response = $response;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearEvents(): ControllerReflectionInterface
    {
        $this->events = [];
    }

}
