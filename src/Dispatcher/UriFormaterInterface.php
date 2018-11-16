<?php declare(strict_types=1);

/**
 * Pollus - Slim Dispatcher
 * 
 * @copyright (c) 2018, Renan Cavalieri
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://github.com/renancavalieri/pollus-slim-dispatcher GitHub
 */

namespace Pollus\Slim\Dispatcher;

/**
 * Esta interface expõe os métodos UriToClassName() e UriToMethodName()
 * para serem utilizados no {@see SlimDispatcher}.
 * 
 * O objetivo desta interface é permitir que a estrutura de nomes de classes
 * e métodos possa ser personalizável.
 */
interface UriFormaterInterface 
{

    /**
     * Formata um identificador URI para um nome de classe.
     * 
     * Exemplo: pessoas-juridicas => PessoasJuridicasController
     * 
     * @param string $uri
     * @return string
     */
    public function uriToClassName(string $uri): string;

    /**
     * Formata um identificador URI para um nome de médoto
     * 
     * Exemplo: listar-pessoas => listarPessoas
     * 
     * @param string $uri
     * @return string
     */
    public function uriToMethodName(string $uri): string;
}
