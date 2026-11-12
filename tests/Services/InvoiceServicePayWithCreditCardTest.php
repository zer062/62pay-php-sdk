<?php

declare(strict_types=1);

namespace Sixtytwopay\Tests\Services;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Sixtytwopay\Client;
use Sixtytwopay\Inputs\Invoice\InvoiceCreditCardInput;
use Sixtytwopay\Services\InvoiceService;

final class InvoiceServicePayWithCreditCardTest extends TestCase
{
    private array $history = [];

    public function testClientIpIsSentInJsonBody(): void
    {
        $service = $this->serviceRespondingWith(new Response(200, [], json_encode([
            'data' => [
                'message' => 'Pagamento processado.',
                'status' => 'CONFIRMED',
            ],
        ])));

        $service->payWithCreditCard('019a0000-0000-7000-8000-000000000002', $this->input('2001:db8::10'));

        $this->assertCount(1, $this->history);

        $request = $this->history[0]['request'];
        $body = json_decode((string)$request->getBody(), true);

        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('invoices/019a0000-0000-7000-8000-000000000002/pay/credit-card', $request->getUri()->getPath());
        $this->assertSame('2001:db8::10', $body['client_ip']);
    }

    public function testApiValidationErrorForClientIpReachesIntegrator(): void
    {
        $service = $this->serviceRespondingWith(new Response(422, [], json_encode([
            'errors' => [
                [
                    'code' => 'validation_error',
                    'description' => 'O campo client ip deve ser um IP público.',
                    'meta' => [
                        'fields' => [
                            'client_ip' => ['O campo client ip deve ser um IP público.'],
                        ],
                    ],
                ],
            ],
        ])));

        $response = $service->payWithCreditCard('019a0000-0000-7000-8000-000000000002', $this->input('203.0.113.10'));

        $this->assertTrue($response->failed());
        $this->assertSame(422, $response->statusCode());
        $this->assertArrayHasKey('client_ip', $response->raw()['errors'][0]['meta']['fields']);
    }

    private function serviceRespondingWith(Response $response): InvoiceService
    {
        $stack = HandlerStack::create(new MockHandler([$response]));
        $stack->push(Middleware::history($this->history));

        return new InvoiceService(new Client('test-key', 'SANDBOX', ['handler' => $stack]));
    }

    private function input(string $clientIp): InvoiceCreditCardInput
    {
        return new InvoiceCreditCardInput(
            holderName: 'Maria Oliveira',
            number: '4111111111111111',
            cardExpiryDate: '12/30',
            ccv: '123',
            billingName: 'Maria Oliveira',
            billingEmail: 'maria@example.com',
            billingDocumentNumber: '00000000000',
            billingPostalCode: '01311000',
            billingStreet: 'Rua Exemplo',
            billingAddressNumber: '100',
            billingAddressComplement: 'Sala 1',
            billingNeighborhood: 'Centro',
            billingCity: 'Sao Paulo',
            billingState: 'SP',
            billingPhone: '11900000000',
            clientIp: $clientIp,
            installments: 1,
        );
    }
}
