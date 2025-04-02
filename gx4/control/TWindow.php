<?php
namespace GX4\Control;

use Adianti\Registry\TSession;

if (!defined('CONEXAO')) {
    define('CONEXAO', TSession::getValue('conexao'));
}

class TWindow extends \Adianti\Control\TWindow
{
    protected static $database = CONEXAO;
}
