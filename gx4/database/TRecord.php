<?php
namespace GX4\Database;

/*
* Classe base TRecord estendida
*
* Esta classe personaliza o comportamento da TRecord do Adianti Framework para atender
* necessidades comuns dos projetos Golfran, especialmente no uso com bancos Firebird.
*
* Funcionalidades implementadas:
*
* - Remoção de espaços em branco no final de campos tipo CHAR (Firebird) após o carregamento
* - Método `default_values()` reservado para futura aplicação de valores padrão
*
* @package GX4\Database
*/

class TRecord extends \Adianti\Database\TRecord
{
    /*
    * Define valores padrão para o registro.
    *
    * Este método pode ser sobrescrito nas classes filhas para aplicar
    * valores padrão em novos registros ou após o carregamento.
    */
    public function default_values()
    {
        // Este método pode ser implementado nas classes filhas
        // para aplicar valores padrão ao registro.
    }

    /*
    * Evento executado automaticamente após o carregamento de um registro único
    *
    * Remove espaços em branco à direita dos valores do tipo string (especialmente úteis para Firebird)
    *
    * @param object $object Objeto carregado do banco de dados
    */
    public function onAfterLoad($object)
    {
        foreach ($object as $key => $value)
        {
            if(! is_null($value))
            {
                $object->$key = trim($value);
            }
        }
    }

    /*
    * Evento executado automaticamente após o carregamento de uma coleção de registros
    *
    * Remove espaços em branco à direita dos valores do tipo string para cada item da coleção
    *
    * @param object $object Objeto da coleção carregado do banco
    */
    public function onAfterLoadCollection($object)
    {
        foreach ($object as $key => $value)
        {
            if(! is_null($value))
            {
                $object->$key = trim($value);
            }
        }
    }
}
