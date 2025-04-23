<?php
namespace GX4\Control;

use Adianti\Registry\TSession;

/**
 * Define a constante CONEXAO caso ainda não esteja definida.
 *
 * A constante CONEXAO representa o nome da conexão com o banco de dados
 * e é inicializada com o valor presente na sessão do usuário.
 * Isso permite que cada usuário utilize uma conexão específica durante sua sessão.
 */
if (!defined('CONEXAO')) {
    define('CONEXAO', TSession::getValue('conexao'));
}

/**
 * Classe GX4\Control\TPage
 *
 * Estende a classe \Adianti\Control\TPage e atribui dinamicamente
 * o banco de dados a ser utilizado com base na constante CONEXAO.
 *
 * Essa padronização é útil em aplicações multi-banco, permitindo
 * que cada página utilize automaticamente a conexão da sessão atual.
 *
 * @package GX4\Control
 */
class TPage extends \Adianti\Control\TPage
{
    /**
     * Nome do banco de dados ativo, definido dinamicamente pela constante CONEXAO.
     * Utilizado como conexão padrão nas classes que herdam de TPage.
     *
     * @var string
     */
    protected static $database = CONEXAO;
}