<?php

namespace GX4\Database;

class TSqlSelect extends \Adianti\Database\TSqlSelect
{

    public function getOracleInstruction( $prepared )
    {
        if (preg_match('/builder_db_query_temp/', $this->entity))
        {
            $this->entity = self::removeDoubleQuotesFromAliases($this->entity);
        }

        // obtém a cláusula WHERE do objeto criteria.
        if ($this->criteria)
        {
            $expression = $this->criteria->dump( $prepared );

            // obtém as propriedades do critério
            $group    = $this->criteria->getProperty('group');
            $order    = $this->criteria->getProperty('order');
            $limit    = (int) $this->criteria->getProperty('limit');
            $offset   = (int) $this->criteria->getProperty('offset');
            $direction= in_array($this->criteria->getProperty('direction'), array('asc', 'desc')) ? $this->criteria->getProperty('direction') : '';
        }
        $columns = implode(',', $this->columns);

        $basicsql  = 'SELECT ';
        $basicsql .= $columns;
        $basicsql .= ' FROM ' . $this->entity;

        if (!empty($expression))
        {
            $basicsql .= ' WHERE ' . $expression;
        }

        if (isset($group) AND !empty($group))
        {
            $basicsql .= ' GROUP BY ' . (is_array($group) ? implode(',', $group) : $group);
        }
        if (isset($order) AND !empty($order))
        {
            $basicsql .= ' ORDER BY ' . $order . ' ' . $direction;
        }

        if ((isset($limit) OR isset($offset)) AND ($limit>0 OR $offset>0))
        {
            $this->sql = "SELECT {$columns} ";
            $this->sql.= "  FROM (";
            $this->sql.= "       SELECT rownum \"__ROWNUMBER__\", A.{$columns} FROM ({$basicsql}) A";

            if ($limit >0 )
            {
                $total = $offset + $limit;
                $this->sql .= " WHERE rownum <= {$total} ";
            }
            $this->sql.= ")";
            if ($offset > 0)
            {
                $this->sql .= " WHERE \"__ROWNUMBER__\" > {$offset} ";
            }
        }
        else
        {
            $this->sql = $basicsql;
        }

        return $this->sql;
    }

    public static function  removeDoubleQuotesFromAliases($sql) {
        // Expressão regular para encontrar alias com aspas duplas
        $pattern = '/\bas\s+"([^"]+)"/i';
        // Substituição para remover as aspas duplas
        $replacement = 'as $1';
        // Aplicar a expressão regular
        return preg_replace($pattern, $replacement, $sql);
    }
}