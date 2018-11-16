# Pollus - Slim Dispatcher
O pacote `pollus/slim-dispatcher` é um dispatcher de controllers para a Slim Framework 3 que possibilita a implementação de rotas genéricas. 

**Instalação:**
> composer require pollus/slim-dispatcher

## Exemplos

```php
require __DIR__ . '/../vendor/autoload.php';

use Pollus\Slim\Dispatcher\SlimDispatcher;
use Slim\Container;
use Slim\App;

$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true
]];

$container = new Container($config);
$app = new App($config);
$dispatcher= new SlimDispatcher("\\App\\Controllers", $container);

// Rota genérica
$app->any('/{controller}/{method}[/{params:.*}]', function ($request, $response, $args) use ($dispatcher)
{
    return $dispatcher
        ->prepare($request, $response, $args)
        ->validateMethodType()
        ->run();
});

// Run app
$app->run();
```
Supondo que uma requisição GET fosse enviada para `/produtos/listar/novos/asc`, o controller **ProdutosController** seria instanciado e o método **listar()** seria chamado com os argumentos **"novos"** e **"asc"**. A classe abaixo demonstra como seria a implementação do Controller.

```php
namespace App\Controllers;

use Pollus\Slim\Controller\Controller;

class ProdutosController extends Controller
{
    /**
     * Você pode determinar quais requisições são suportadas
     * para este método. Caso uma requisição não seja suportada
     * uma excessão MethodNotAllowedException será lançada,
     * fazendo com que a Slim Framework resposta com um erro 
     * HTTP (405) contendo os métodos permitidos.
     * 
     * @method GET
     * @method POST
     */
    public function listar(string $categoria, string $ordem)
    {
        // [...]
    }
}
```
Também é possível especificar um controller ou método manualmente. O exemplo abaixo irá instanciar o controller **HomeController** e chamar o método **index()**
```php
/**
 * Index
 */
$app->any('/', function ($request, $response, $args) use ($dispatcher)
{
    return $mvc
        ->setController("home")
        ->setMethod("index")
        ->prepare($request, $response, $args)
        ->validateMethodType()
        ->run();
});
```
Recursos
--
- Permite a conversão de URI para nome de classes e métodos.
- Permite injetar sua própria implementação da interface **ControllerReflectionInterface**
- Realiza a leitura de anotações para validar o tipo da requisição
- Permite adicionar validações personalizadas baseadas em anotações (esta funcionalidade não é um middleware)
- Integrada com as exceções **SlimNotFoundException** e **SlimMethodNotAllowedException** da Slim Framework 3
- Expõe uma interface padrão para os controllers, que os permite armazenar as variáveis $request, $response e $container no próprio objeto.


## MIT License

Copyright (c) 2018 Renan Cavalieri

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

----
**Aviso**: Esta documentação está incompleta! Ainda faltam uma lista dos métodos disponíveis, mais exemplos e uma documentação em inglês! 
