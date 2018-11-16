<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Exceptions;

/**
 * Esta Exception é lançada sempre que o {@see SlimDispatcher} não possui 
 * informações suficientes para instanciar uma implementação da interface 
 * {@see ControllerInterface} ou chamar seu método durante a execução 
 * do Prepare();
 */
class DispatcherException extends \Exception {}
