<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Exceptions;

use Slim\Exception\NotFoundException as SlimNotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

/**
 * Esta Exception será lançada sempre que a classe {@see ControllerReflection}
 * tentar instanciar um controller que não foi encontrado ou não possui o método
 * que foi informado em seu construtor.
 */
class NotFoundException extends SlimNotFoundException 
{
    public function __construct(ServerRequestInterface $request, ResponseInterface $response, ?string $message = null)
    {
        parent::__construct($request, $response);
        $this->message = $message;
    }
}
