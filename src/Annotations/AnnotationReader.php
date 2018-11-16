<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher
 */

namespace Pollus\Slim\Annotations;

use DocBlockReader\Reader;

/**
 * Esta classe realiza a leitura de anotações de um controller e implementa a
 * interface {@see AnnotationReaderInterface}
 * 
 * Esta implementação utiliza a classe {@see Reader} para realizar a leitura.
 */
class AnnotationReader implements AnnotationReaderInterface
{
    /**
     * @var Reader
     */
    protected $reader;
    
    /**
     * {@inheritDoc}
     */
    public function __construct(string $className, ?string $name, ?string $type)
    {
        $this->reader = new Reader($className, $name, $type);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        return $this->reader->getParameter($key);
    }
}
