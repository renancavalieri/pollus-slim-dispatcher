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
 * Esta interface define o modo do qual as implementações dos controllers devem 
 * ser construídas e é essencial para a classe {@see ControllerReflection}.
 */
interface ControllerInterface
{
    /**
     * Este método deve popular as propriedades do controller com os argumentos
     * informados.
     * 
     * @param ServerRequestInterface $request Objeto da requisição
     * @param ResponseInterface $response Objeto de resposta
     * @param ContainerInterface $container Container de objetos
     */
    function __construct(ServerRequestInterface $request, ResponseInterface $response, ContainerInterface $container);
}