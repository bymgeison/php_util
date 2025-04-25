<?php

namespace GX4\Util;

use Exception;

class TJasper
{
    public string $jasperUrl;
    public string $reportPath;
    public string $type;
    public string $user;
    public string $password;
    public int $status_code;
    public array $parameters = [];

    public function __construct(
        string $jasperUrl,
        string $reportPath,
        string $type,
        string $user,
        string $password,
        array $parameters
    ) {
        $this->jasperUrl  = $jasperUrl;
        $this->reportPath = $reportPath;
        $this->type       = $type;
        $this->user       = $user;
        $this->password   = $password;
        $this->parameters = $parameters;
    }

    private function getQueryString(): string
    {
        $queryString = '';

        foreach ($this->parameters as $key => $val) {
            $queryString .= ($queryString === '' ? '?' : '&') . $key . '=' . $val;
        }

        return $queryString;
    }

    public function execute(): string
    {
        $url = $this->jasperUrl . '/rest_v2/reports/' . $this->reportPath . '.' . $this->type . $this->getQueryString();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->user}:{$this->password}");

        $result            = curl_exec($ch);
        $this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($this->status_code !== 200) {
            $xml                     = simplexml_load_string(strval($result));
            $exception               = new Exception("Erro {$this->status_code}. {$xml->errorCode}: {$xml->message}.");
            $exception->errorCode    = (string) $xml->errorCode;
            $exception->errorMessage = (string) $xml->message;

            throw $exception;
        }

        return $result;
    }
}
