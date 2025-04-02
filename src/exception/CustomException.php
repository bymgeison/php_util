<?php

namespace GX4\Exception;
class CustomException extends \PDOException
{
    protected $detailedMessage;

    public function __construct(\Exception $exception = null)
    {
        // Extrair a mensagem específica do erro PostgreSQL
        $this->detailedMessage = $this->extractDetailedMessage($exception->getMessage());
        parent::__construct($this->detailedMessage,(int) $exception->getCode(), $exception);
    }

    private function extractDetailedMessage($message)
    {
        $matches = [];
        if (preg_match('/ERROR:\s*(.*?)(?:\s*CONTEXT:|$)/', $message, $matches)) {
            return $matches[1];
        }
        return $message; // Fallback caso a regex não funcione
    }

    public function getDetailedMessage()
    {
        return $this->detailedMessage;
    }
}
