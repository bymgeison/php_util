<?php

namespace GX4\Trait;

use stdClass;

trait FileSaveTrait
{
    public function saveFile($object, $data, $input_name, $target_path)
    {
        $dados_file = json_decode(urldecode($data->$input_name));

        if(is_null($dados_file))
        {
            $dados_file = new stdClass();
            $dados_file->fileName = $data->$input_name;
        }

        if (isset($dados_file->fileName))
        {
            $oldObject = new (get_class($object))($object->{$object::PRIMARYKEY});

            $pk = $object::PRIMARYKEY;

            $target_path.= '/' . $object->$pk;
            $target_path = str_replace('//', '/', $target_path);

            $source_file = $dados_file->fileName;
            $target_file = strpos($dados_file->fileName, $target_path) === FALSE ? $target_path . '/' . $dados_file->fileName : $dados_file->fileName;
            $target_file = str_replace('tmp/', '', $target_file);

            $objectNew             = new stdClass();
            $objectNew->folderPath = $target_path;
            $objectNew->pathTmp    = "tmp/{$source_file}";
            $objectNew->path       = $target_file;

            if (!empty($dados_file->delFile))
            {
                $delete_file = $target_path . '/' . urldecode($dados_file->delFile);
                $target_file = str_replace('tmp/', '', $delete_file);
                $objectNew->delFile = $target_file;
            }

            if (!is_null($oldObject->$input_name) && $oldObject->$input_name != $objectNew->path)
            {
                $objectNew->delFile = $oldObject->$input_name;
            }

            return $objectNew;
        }
    }
}
