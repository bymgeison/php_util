# 🔌 Exemplo: Conexão Dinâmica com GX4\Control\TPage

Este exemplo demonstra como a classe `GX4\Control\TPage` estende a `\Adianti\Control\TPage` para configurar dinamicamente a conexão com o banco de dados com base na sessão atual.

---

## 💡 Objetivo

Permitir que a aplicação utilize diferentes conexões de banco de dados para cada sessão, sem precisar alterar manualmente a configuração global do sistema.

---

## 🧱 Classe Personalizada

```php
<?php
namespace GX4\Control;

use Adianti\Registry\TSession;

/**
 * Define a constante CONEXAO baseada na sessão atual, se ainda não estiver definida.
 */
if (!defined('CONEXAO')) {
    define('CONEXAO', TSession::getValue('conexao'));
}

/**
 * Classe TPage estendida do Adianti, que usa a constante CONEXAO
 * como valor da variável estática $database.
 */
class TPage extends \Adianti\Control\TPage
{
    protected static $database = CONEXAO;
}
