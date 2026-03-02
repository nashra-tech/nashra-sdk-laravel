<?php

namespace Nashra\Sdk\Exceptions;

class ValidationException extends NashraException
{
    /** @var array<string, string[]> */
    private array $errors;

    /**
     * @param  array<string, string[]>  $errors
     */
    public function __construct(string $message = 'The given data was invalid.', array $errors = [], ?array $response = null)
    {
        parent::__construct($message, 422, 'VALIDATION_ERROR', $response);
        $this->errors = $errors;
    }

    /**
     * @return array<string, string[]>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
