<?php

declare(strict_types=1);

namespace Crovver;

class CrovverError extends \RuntimeException
{
    private ?int $statusCode;
    private ?string $errorCode;
    private bool $retryable;

    public function __construct(
        string $message,
        ?int $statusCode = null,
        ?string $errorCode = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->retryable = self::isRetryableStatus($statusCode);
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function isRetryable(): bool
    {
        return $this->retryable;
    }

    public static function isRetryableStatus(?int $status): bool
    {
        if ($status === null) {
            return true; // Network errors are retryable
        }

        return in_array($status, [408, 429, 500, 502, 503, 504], true)
            || $status >= 500;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name'       => 'CrovverError',
            'message'    => $this->getMessage(),
            'statusCode' => $this->statusCode,
            'code'       => $this->errorCode,
            'retryable'  => $this->retryable,
        ];
    }
}
