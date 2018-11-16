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
use Pollus\Slim\Dispatcher\ControllerReflectionInterface;

/**
 * Esta interface define o método Execute() para que suas implementações possam 
 * ser chamadas dentro da classe {@see ControllerReflection}, antes da chamada
 * do próprio Controller durante o método Run();
 * 
 * Um ControllerEvent não é um middleware!
 */
interface ControllerEventInterface 
{
    /**
     * Executa o evento
     * 
     * Caso uma {@see ResponseInterface} seja retornada, a execução dos eventos 
     * e do próprio controller será interrompida.
     * 
     * @param ControllerReflectionInterface $controller_reflection
     * 
     * @return ResponseInterface|null
     */
    function execute(ControllerReflectionInterface $controller_reflection) : ?ResponseInterface;
}
