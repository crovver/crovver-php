<?php

declare(strict_types=1);

namespace Crovver\Types;

class Invoice
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $invoiceNumber,
        public readonly string  $invoiceType,
        public readonly string  $status,
        public readonly float   $totalAmount,
        public readonly string  $currency,
        public readonly ?string $dueDate,
        public readonly ?string $paidAt,
        public readonly ?string $issuedAt,
        public readonly ?string $periodStart,
        public readonly ?string $periodEnd,
        public readonly ?string $paymentProvider,
        public readonly ?string $planName,
        public readonly ?string $hostedInvoiceUrl,
        public readonly ?string $invoicePdf,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id:               $data['id']               ?? '',
            invoiceNumber:    $data['invoiceNumber']    ?? '',
            invoiceType:      $data['invoiceType']      ?? '',
            status:           $data['status']           ?? '',
            totalAmount:      (float) ($data['totalAmount'] ?? 0),
            currency:         $data['currency']         ?? 'USD',
            dueDate:          $data['dueDate']          ?? null,
            paidAt:           $data['paidAt']           ?? null,
            issuedAt:         $data['issuedAt']         ?? null,
            periodStart:      $data['periodStart']      ?? null,
            periodEnd:        $data['periodEnd']        ?? null,
            paymentProvider:  $data['paymentProvider']  ?? null,
            planName:         $data['planName']         ?? null,
            hostedInvoiceUrl: $data['hostedInvoiceUrl'] ?? null,
            invoicePdf:       $data['invoicePdf']       ?? null,
        );
    }
}
