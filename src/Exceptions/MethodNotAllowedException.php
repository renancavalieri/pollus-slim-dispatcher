<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Exceptions;

use Slim\Exception\MethodNotAllowedException as SlimMethodNotAllowedException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Esta exception é lançada sempre que o cliente tenta realizar uma ação
 * com uma requisição que não foi permitida ou especificada em um método do controller
 * solicitado.
 * 
 * Esta exception é lançada dentro pela classe {@see ControllerReflection}
 */
class MethodNotAllowedException extends SlimMethodNotAllowedException 
{
    public function __construct(ServerRequestInterface $request, ResponseInterface $response, array $allowedMethods)
    {
        parent::__construct($request, $response, $allowedMethods);
    }
}