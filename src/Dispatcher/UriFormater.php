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
 * Esta implementação formata os nomes das classes e dos métodos com ucfirst, e
 * para classes, concatena a string "Controller" no final.
 * 
 * Exemplo de classe:
 *      produtos => ProdutosController
 *      produtos-estoque => ProdutosEstoqueController
 *      meusprodutos => MeusprodutosController
 * 
 * Exemplos de métodos
 * 
 *      visualizar => Visualizar
 *      visualizar-itens => VisualizarItens
 *      visualizaritens => Visualizaritens
 */
class UriFormater implements UriFormaterInterface
{
    /**
     * {@inheritDoc}
     * 
     * Exemplo de classe:
     *      produtos => ProdutosController
     *      produtos-estoque => ProdutosEstoqueController
     *      meusprodutos => MeusprodutosController
     */
    public function uriToClassName(string $uri): string 
    {
        $string_parts = explode("-", $uri);
        $string = array_map('ucfirst', $string_parts);
        $string = implode("", $string) . "Controller"; 
        return preg_replace("/[^a-zA-Z0-9\-]/", "", $string);
    }

    /**
     * {@inheritDoc}
     * 
     * Exemplos de métodos
     * 
     *      visualizar => Visualizar
     *      visualizar-itens => VisualizarItens
     *      visualizaritens => Visualizaritens
     */
    public function uriToMethodName(string $uri): string 
    {
        $string_parts = explode("-", $uri);
        $string = array_map('ucfirst', $string_parts);
        $string[0] = strtolower($string[0]);
        $string = implode("", $string); 
        return preg_replace("/[^a-zA-Z0-9\-]/", "", $string);
    }
   

}
