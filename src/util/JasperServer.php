<?php

namespace GX4\Util;

use Exception;

class JasperServer {
    private $jasperUrl;
    private $reportPath;
    private $type = 'pdf';
    private $user;
    private $password;
    private $status_code;
    private $parameters = [];

    function __construct($jasperUrl, $user, $password, $reportPath = null) {
        $this->jasperUrl = $jasperUrl;
        $this->reportPath = $reportPath;
        $this->user = $user;
        $this->password = $password;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
    public function setReportPath($reportPath)
    {
        $this->reportPath = $reportPath;
    }

    public function addParameters(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    public function __get($property)
    {
        return $this->parameters[$property];
    }
    public function __set($property, $value)
    {
        $this->parameters[$property] = $value;
    }

    private function getQueryString() {
        $queryString = "";
        foreach ($this->parameters as $key => $val) {
            if ($queryString == "") {
                $queryString .= '?';
            } else {
                $queryString .= '&';
            }

            $queryString .= $key . "=" . $val;
        }

        return $queryString;
    }

    public function execute() {
        $url = $this->jasperUrl . '/rest_v2/reports/' . $this->reportPath . '.' . $this->type . $this->getQueryString();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90000); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->password");
        $result=curl_exec ($ch);
        $this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close ($ch);

        if ($this->status_code != 200) {
            $xml = simplexml_load_string(strval($result));
            $exception = new Exception("Erro $this->status_code. $xml->errorCode: $xml->message.");
            $exception->errorCode = $xml->errorCode;
            $exception->errorMessage = $xml->message;

            throw $exception;
        }

        return $result;
    }

    public function PDF($folder, $file)
    {
        try
        {
            file_put_contents("{$folder}/{$file}.{$this->type}", $this->execute());

            return ["path" => "{$folder}/{$file}.{$this->type}", "file" => "{$file}.{$this->type}"];
        }
        catch(Exception $e)
        {
            throw new Exception('Erro na geração do arquivos Jasper' . $e->getMessage());
        }
    }
    public function viewPDFAdianti($folder, $file)
    {
        try
        {
            $file = $this->PDF($folder, $file);

            \Adianti\Control\TPage::openFile($file['path']);
        }
        catch(Exception $e)
        {
            throw new Exception('Erro na geração do arquivos Jasper' . $e->getMessage());
        }
    }
}