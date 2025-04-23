# 🔌 Exemplo: Conexão Dinâmica com GX4\Control\TWindow

Este exemplo mostra como a classe `GX4\Control\TWindow` estende a `\Adianti\Control\TWindow` para configurar automaticamente a conexão com o banco de dados com base na sessão.

---

## 💡 Objetivo

Assim como a `GX4\Control\TPage`, esta classe permite que janelas (windows) utilizem dinamicamente o banco de dados da sessão atual, útil em cenários multi-cliente ou multi-banco.

---

## 🧱 Classe Personalizada

```php
<?php
namespace GX4\Control;

use Adianti\Registry\TSession;

/**
 * Define a constante CONEXAO com base na sessão atual, se ainda não estiver definida.
 */
if (!defined('CONEXAO')) {
    define('CONEXAO', TSession::getValue('conexao'));
}

/**
 * Classe TWindow estendida da TWindow original do Adianti.
 * Define dinamicamente o banco de dados da sessão na propriedade estática $database.
 */
class TWindow extends \Adianti\Control\TWindow
{
    protected static $database = CONEXAO;
}
