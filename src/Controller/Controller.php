<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Implementação abstrata da interface {@see ControllerInterface} para servir
 * de base para os controllers
 * 
 * Esta implementação popula as variáveis protegidas do Controller
 */
abstract class Controller implements ControllerInterface
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var ResponseInterface
     */
    protected $response;
    
    /**
     * Inicia um novo controller e popula as variáveis protegidas
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param ContainerInterface $container
     */
    function __construct(ServerRequestInterface $request, ResponseInterface $response, ContainerInterface $container)
    {
       $this->request = $request;
       $this->response = $response;
       $this->container = $container;
    }
}
