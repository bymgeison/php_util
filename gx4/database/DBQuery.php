<?php

namespace GX4\Database;

use GX4\Database\TRecord;

class DBQuery extends TRecord
{

    protected $sql;

    public function setSqlQuery($sql)
    {
        $this->sql = $sql;
    }

    public function getEntity()
    {
        return $this->sql;
    }

    static function getDeletedAtColumn()
    {
        return false;
    }

    public function store()
    {
        return false;
    }

    public function load($id)
    {
        return false;
    }

    public function delete($id = null)
    {
        return false;
    }
}