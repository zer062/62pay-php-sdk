<?php

declare(strict_types=1);

namespace Sixtytwopay\Tests\Inputs\Invoice;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sixtytwopay\Inputs\Invoice\InvoiceCreditCardInput;

final class InvoiceCreditCardInputTest extends TestCase
{
    public function testPayloadIncludesIpv4ClientIpUnchanged(): void
    {
        $input = InvoiceCreditCardInput::fromArray($this->cardData(['client_ip' => '203.0.113.10']));

        $this->assertSame('203.0.113.10', $input->toPayload()['client_ip']);
    }

    public function testPayloadIncludesIpv6ClientIpUnchanged(): void
    {
        $input = InvoiceCreditCardInput::fromArray($this->cardData(['client_ip' => '2001:db8::10']));

        $this->assertSame('2001:db8::10', $input->toPayload()['client_ip']);
    }

    public function testPayloadKeepsExistingFields(): void
    {
        $payload = InvoiceCreditCardInput::fromArray($this->cardData(['installments' => 3]))->toPayload();

        $this->assertSame(
            [
                'holder_name' => 'Maria Oliveira',
                'number' => '4111111111111111',
                'card_expiry_date' => '12/30',
                'ccv' => '123',
                'installments' => 3,
                'billing_name' => 'Maria Oliveira',
                'billing_email' => 'maria@example.com',
                'billing_document_number' => '00000000000',
                'billing_postal_code' => '01311000',
                'billing_street' => 'Rua Exemplo',
                'billing_address_number' => '100',
                'billing_address_complement' => 'Sala 1',
                'billing_neighborhood' => 'Centro',
                'billing_city' => 'Sao Paulo',
                'billing_state' => 'SP',
                'billing_phone' => '11900000000',
                'client_ip' => '203.0.113.10',
            ],
            $payload,
        );
    }

    public function testPayloadHasNoClearSaleSessionFields(): void
    {
        $payload = InvoiceCreditCardInput::fromArray($this->cardData())->toPayload();

        $this->assertArrayNotHasKey('session_id', $payload);
        $this->assertArrayNotHasKey('app_key', $payload);
    }

    public function testMissingClientIpIsRejected(): void
    {
        $data = $this->cardData();
        unset($data['client_ip']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('client_ip is required');

        InvoiceCreditCardInput::fromArray($data);
    }

    public function testEmptyClientIpIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('client_ip is required');

        InvoiceCreditCardInput::fromArray($this->cardData(['client_ip' => '']));
    }

    public function testMalformedClientIpIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('client_ip must be a valid IPv4 or IPv6 address');

        InvoiceCreditCardInput::fromArray($this->cardData(['client_ip' => '999.1.1.1']));
    }

    public function testForwardedHeaderListIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        InvoiceCreditCardInput::fromArray($this->cardData(['client_ip' => '203.0.113.10, 10.0.0.1']));
    }

    private function cardData(array $overrides = []): array
    {
        return array_replace(
            [
                'holder_name' => 'Maria Oliveira',
                'number' => '4111111111111111',
                'card_expiry_date' => '12/30',
                'ccv' => '123',
                'billing_name' => 'Maria Oliveira',
                'billing_email' => 'maria@example.com',
                'billing_document_number' => '00000000000',
                'billing_postal_code' => '01311000',
                'billing_street' => 'Rua Exemplo',
                'billing_address_number' => '100',
                'billing_address_complement' => 'Sala 1',
                'billing_neighborhood' => 'Centro',
                'billing_city' => 'Sao Paulo',
                'billing_state' => 'sp',
                'billing_phone' => '11900000000',
                'client_ip' => '203.0.113.10',
            ],
            $overrides,
        );
    }
}
