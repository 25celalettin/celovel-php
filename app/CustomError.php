<?php

class CustomError {
    private $statusCode = 500;
    private $errorMessage = 'Custom Error';

    public function __construct($statusCode = null, $errorMessage = null) {
        $statusCode != null ? $this->statusCode = $statusCode : null;
        $errorMessage != null ? $this->errorMessage = $errorMessage : null;

        http_response_code($this->statusCode);

        die($this->errorMessage . '<br> Status Code: ' . $this->statusCode);
    }
}