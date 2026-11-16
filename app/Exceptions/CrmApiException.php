<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A failed call to the CRM portal API. Status 0 means the CRM could not be reached at all.
 */
class CrmApiException extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $errors
     */
    public function __construct(
        string $message,
        public readonly int $status = 0,
        public readonly ?string $errorCode = null,
        public readonly array $errors = [],
    ) {
        parent::__construct($message, $status);
    }

    public function isUnreachable(): bool
    {
        return $this->status === 0 || $this->status >= 500 || $this->errorCode === 'PORTAL_DISABLED';
    }

    public function isUnauthenticated(): bool
    {
        return $this->status === 401 && ! in_array($this->errorCode, ['INVALID_CREDENTIALS', 'INVALID_SERVICE_KEY'], true);
    }

    public function is(string $errorCode): bool
    {
        return $this->errorCode === $errorCode;
    }

    /**
     * CRM validation errors flattened to field => first message.
     *
     * @return array<string, string>
     */
    public function fieldErrors(): array
    {
        $out = [];
        foreach ($this->errors as $field => $messages) {
            if (is_array($messages) && ! array_is_list($messages)) {
                continue;
            }
            $out[(string) $field] = is_array($messages) ? (string) ($messages[0] ?? '') : (string) $messages;
        }

        return $out;
    }
}
