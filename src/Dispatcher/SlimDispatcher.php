<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Dispatcher;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Pollus\Slim\Dispatcher\ControllerReflectionInterface;
use Pollus\Slim\Exceptions\DispatcherException;

/**
 * O SlimDispatcher é uma classe que prepara a solicitação dentro de uma rota
 * da Slim Framework para ser encaminhada para um controller, seguindo uma
 * implementação simples do padrão arquitetural Slim.
 */
class SlimDispatcher
{
    
    /**
     * Armazena o namespace dos controllers que serão instanciados.
     * 
     * Esta variável é populada no construtor e no método SetNamespace(),
     * portanto nunca será NULL
     * 
     * @var string
     */
    protected $namespace;
    
    /**
     * Armazena o nome da classe que implementa a interface {@see ControllerReflectionInterface}
     * 
     * Esta variável é populada através do método SetControllerReflectionClass()
     * 
     * @var string|null
     */
    protected $controller_reflection_class = null;
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * Armazena os valores informados pelo método SetController();
     * 
     * Quando esta variável estiver populada, o Controller informado em Prepare() 
     * será ignorado.
     * 
     * @var string|null
     */
    protected $controller = null;
    
    /**
     * Armazena os valores informados pelo método SetMethod();
     * 
     * Quando esta variável estiver populada, o método informado em Prepare() 
     * será ignorado.
     * 
     * @var string|null
     */
    protected $method = null;
    
    /**
     * Armazena os valores informados pelo método SetArgs();
     * 
     * Quando esta variável estiver populada, os argumentos informados no método
     * Prepare() serão ignorados.
     * 
     * @var array|null
     */
    protected $args = null;
    
    /**
     * @var UriFormaterInterface|null;
     */
    protected $formater;
    

    /**
     * Inicia um novo SlimDispatcher
     * 
     * @param string $namespace
     * @param ContainerInterface $container
     */
    public function __construct(string $namespace, ContainerInterface $container)
    {
        $this->namespace = $namespace;
        $this->container = $container;
    }
    
    /**
     * Prepara o controller e retorna uma implementação da interface 
     * {@see ControllerReflectionInterface}.
     * 
     * A implementação padrão é a classe {@see ControllerReflection}, porém é 
     * possível definir uma nova implementação utilizando o método SetControllerReflectionClass(),
     * 
     * Este método espera que 3 chaves sejam informadas no parâmetro $args, sendo:
     * 
     * - controller: (string) Nome do controller que será instanciado
     * - method: (string) Nome do método que será executado no controller
     * - params: (array) Argumentos que serão fornecidos para o método especificado
     * 
     * Caso o "controller" ou "method" sejam omitidos, uma {@see DispatcherException}
     * será lançada. Caso a chave "params" não exista ou não contenha nenhum valor,
     * o método será preparado na implementação da {@see ControllerReflectionInterface}
     * para ser chamado sem qualquer passagem de argumentos.
     * 
     * Também é possível utilizar os métodos SetController(), SetMethod() e SetArgs()
     * para especificar manualmente qual controller, método e argumentos serão 
     * utilizados para construir o Controller. Neste caso não seria necessário
     * informar estes valores dentro do parâmetro $args.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ControllerReflectionInterface
     */
    function prepare(ServerRequestInterface $request, ResponseInterface $response, array $args = []) : ControllerReflectionInterface
    {
        if ($this->controller !== null) { $controller = $this->controller; }
        else { $controller = $args["controller"] ?? null; }
        
        if ($this->method !== null) { $method = $this->method; }
        else { $method = $args["method"] ?? null; }
        
        $params = $args["params"] ?? null;
        
        if ($controller === null)
        {
            throw new DispatcherException("O controller não foi informado na solicitação");
        }
        
        if ($method === null)
        {
            throw new DispatcherException("O método não foi informado na solicitação");
        }
        
        unset($args["controller"]);
        unset($args["method"]);
        
        $controller = $this->getUriFormater()->uriToClassName($controller);
        $method_str = $this->getUriFormater()->uriToMethodName($method);
        
        $controller_str = rtrim($this->namespace, "\\") . "\\" . $controller;
        
        $controller_reflection = $this->getControllerReflectionInstance($controller_str, $method_str, $request, $response, $this->container);
        
        if ($params === null) { $params = []; }
        else { $params = explode("/", $params); }
        
        $controller_reflection->setMethodArgs($params);
        
        
        return $controller_reflection;
    }

    /**
     * Define a classe que irá construir o controller.
     * 
     * A classe especificada deverá implementar a interface {@see ControllerReflectionInterface}
     * 
     * Classe padrão: {@see ControllerReflection}
     * 
     * @param string $class_name
     */
    public function setControllerReflectionClass(?string $class_name) : SlimDispatcher
    {
        $this->controller_reflection_class = $class_name;
        return $this;
    }

    /**
     * Retorna o {@see ContainerInterface}
     * 
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface 
    {
        return $this->container;
    }

    /**
     * Define o objeto que irá gerenciar o container
     * 
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container) : SlimDispatcher
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Define manualmente o controller solicitado.
     * 
     * Ao informar qualquer valor diferente de NULL, o {@see SlimDispatcher}
     * irá ignorar o controller especificado em Prepare()
     * 
     * @param string|null $controller
     * @return $this
     */
    public function setController(?string $controller): SlimDispatcher 
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Define manualmente o método solicitado.
     * 
     * Ao informar qualquer valor diferente de NULL, o {@see SlimDispatcher}
     * irá ignorar o método especificado em Prepare()
     * 
     * @param string|null $method
     * @return \Pollus\Slim\Dispatcher\SlimDispatcher
     */
    public function setMethod(?string $method): SlimDispatcher 
    {
        $this->method = $method;
        return $this;
    }
    
    /**
     * Define manualmente os argumentos para o método solicitado.
     * 
     * Ao informar qualquer valor diferente de NULL, o {@see SlimDispatcher}
     * irá ignorar os argumentos especificado em Prepare()
     * 
     * @param array $args
     * @return \Pollus\Slim\Dispatcher\SlimDispatcher
     */
    public function setArgs(array $args): SlimDispatcher 
    {
        $this->args = $args;
        return $this;
    }

    /**
     * Define o namespace dos controllers
     * 
     * @param string $namespace
     * @return \Pollus\Slim\Dispatcher\SlimDispatcher
     */
    public function setNamespace(string $namespace): SlimDispatcher 
    {
        $this->namespace = $namespace;
        return $this;
    }
    
    /**
     * Define o objeto de formatação de identificadores de URL
     * 
     * @param UriFormaterInterface $formater
     */
    public function setUriFormater(UriFormaterInterface $formater)
    {
        $this->formater = $formater;
    }
    
    /**
     * Retorna o objeto de conversão de identificadores de URL.
     * 
     * Caso objeto tenha sido definido pelo método, a classe {@see UriFormater}
     * será instanciada.
     * 
     * @return UriFormaterInterface
     */
    protected function getUriFormater() : UriFormaterInterface
    {
        if ($this->formater === null)
        {
            $this->formater = new UriFormater();
        }
        
        return $this->formater;
    }
    
    /**
     * Retorna uma instância da implementação da interface {@see ControllerReflectionInterface}
     * 
     * Caso nenhuma classe tenha sido especificada anteriormente, a classe
     * {@see ControllerReflection} será utilizada.
     * 
     * @param type $controller_str
     * @return \Pollus\Slim\Dispatcher\ControllerReflectionInterface
     */
    protected function getControllerReflectionInstance($controller_str, $method_str, ServerRequestInterface $request, ResponseInterface $response, ContainerInterface $container) : ControllerReflectionInterface
    {
        if ($this->controller_reflection_class === null)
        {
            $reflection = new \ReflectionClass(ControllerReflection::class);
        }
        else
        {
            $reflection = new \ReflectionClass($this->controller_reflection_class);
        }
        
        return $reflection->newInstance($controller_str, $method_str, $request, $response, $container);
    }
}