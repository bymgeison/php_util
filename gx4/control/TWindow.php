<?php
namespace GX4\Control;

use Adianti\Registry\TSession;

/**
 * Define a constante CONEXAO caso ainda não esteja definida.
 *
 * A constante CONEXAO representa o nome da conexão com o banco de dados
 * e é definida com base no valor armazenado na sessão atual do usuário.
 * Isso permite que diferentes usuários utilizem conexões distintas,
 * conforme o contexto de sua sessão.
 */
if (!defined('CONEXAO')) {
    define('CONEXAO', TSession::getValue('conexao'));
}

/**
 * Classe GX4\Control\TWindow
 *
 * Estende a classe \Adianti\Control\TWindow e define automaticamente
 * o banco de dados a ser utilizado com base na constante CONEXAO.
 *
 * Essa abordagem padroniza e facilita o uso de múltiplas conexões
 * nos sistemas desenvolvidos com o Adianti Framework.
 *
 * @package GX4\Control
 */
class TWindow extends \Adianti\Control\TWindow
{
    /**
     * Nome do banco de dados ativo, definido pela constante CONEXAO.
     * Este valor será usado pelas classes que herdam de TWindow para
     * realizar operações no banco.
     *
     * @var string
     */
    protected static $database = CONEXAO;
}