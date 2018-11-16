<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Annotations;

use Pollus\Slim\Dispatcher\ControllerReflection;

/**
 * Esta interface determina o modo que o leitor de anotações é construído e expõe
 * o método Get().
 * 
 * É utilizada para que a classe {@see ControllerReflection} possa ler as anotações 
 * do controller de forma independente do componente de leitura utilizado.
 */
interface AnnotationReaderInterface
{
    /**
     * @param string $className Nome da classe
     * @param string|null $name Nome do método ou propriedade
     * @param string|null $type Tipo (method|property)
     */
    public function __construct(string $className, ?string $name, ?string $type);
    
    /**
     * Este método deve retornar o valor solicitado solicitado, que poderá ser
     * NULL quando não encontrado, STRING quando houver somente uma ocorrência
     * e ARRAY quando houver múltiplas ocorrências.
     * 
     * @param string $key
     * @return string|array|null
     */
    public function get(string $key);
}
