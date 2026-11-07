<?php

declare(strict_types=1);

namespace Unit\Application\Responder;

use App\Application\Responder\JsonResponder;
use App\Domain\Responder\Pagination\CursorPage;
use App\Domain\Responder\Problem\AbstractProblemDetails;
use App\Domain\Responder\Problem\ProblemDetailsInterface;
use App\Domain\Responder\Problem\ProblemType;
use App\Tests\Support\Trait\ApplicationTestTrait;
use Generator;
use JsonSerializable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Selective\TestTrait\Traits\ArrayTestTrait;
use Selective\TestTrait\Traits\HttpJsonTestTrait;
use Selective\TestTrait\Traits\HttpTestTrait;
use SineMacula\Http\Enums\HttpStatus;

#[CoversClass(JsonResponder::class)]
class JsonResponderTest extends TestCase
{
    use HttpTestTrait;
    use HttpJsonTestTrait;
    use ArrayTestTrait;
    use ApplicationTestTrait;

    private JsonResponder $sut;
    private ResponseInterface $response;

    protected function setUp(): void
    {
        $this->setUpApp();
        $this->response = $this->createResponse();
        $this->sut = new JsonResponder();
    }

    public static function provideResponseData(): Generator
    {
        yield 'empty array' => [[]];
        yield 'non-empty array' => [['foo' => 'bar']];
        yield 'array with null value' => [['foo' => null]];
        yield 'multidimensional array' => [['foo' => ['bar', 'baz' => ['bat']]]];
        yield 'serialized object' => [
            [
                'foo' => new class {
                    public function getValue(): string
                    {
                        return 'bar';
                    }
                },
            ],
            [
                'foo' => [],
            ],
        ];
        yield 'json serialised object' => [
            [
                'foo' => new class implements JsonSerializable {
                    public function getValue(): string
                    {
                        return 'bar';
                    }

                    public function jsonSerialize(): string
                    {
                        return $this->getValue();
                    }
                },
            ],
            [
                'foo' => 'bar',
            ],
        ];
    }

    public static function provideHeaders(): Generator
    {
        yield 'empty array' => [[], []];
        yield 'non-empty array' => [['foo' => 'bar'], ['foo' => ['bar']]];
        yield 'multiple headers' => [['foo' => 'bar', 'baz' => 'qux'], ['foo' => ['bar'], 'baz' => ['qux']]];
        yield 'multi-value header' => [['foo' => ['bar', 'baz']], ['foo' => ['bar', 'baz']]];
    }

    public static function provideStatus(): Generator
    {
        yield 'status in 100s' => [HttpStatus::CONTINUE];
        yield 'status in 200s' => [HttpStatus::RESET_CONTENT];
        yield 'status in 300s' => [HttpStatus::SEE_OTHER];
        yield 'status in 400s' => [HttpStatus::PAYMENT_REQUIRED];
        yield 'status in 500s' => [HttpStatus::BAD_GATEWAY];
    }

    public static function provideProblemDetails(): Generator
    {
        yield 'empty problem' => [
            new readonly class (HttpStatus::BAD_REQUEST) extends AbstractProblemDetails {
            },
            [
                'type' => 'about:blank',
                'status' => HttpStatus::BAD_REQUEST->value,
            ],
        ];
        yield 'specific problem' => [
            new readonly class (
                status: HttpStatus::NOT_FOUND,
                type: ProblemType::RESOURCE_NOT_FOUND,
                baseUri: 'https://example.net'
            ) extends AbstractProblemDetails {
            },
            [
                'type' => ProblemType::RESOURCE_NOT_FOUND->uri('https://example.net'),
                'status' => HttpStatus::NOT_FOUND->value,
            ],
        ];
        yield 'problem with title' => [
            new readonly class (
                status: HttpStatus::BAD_REQUEST,
                title: 'You do not have enough credit.'
            ) extends AbstractProblemDetails {
            },
            [
                'type' => 'about:blank',
                'status' => HttpStatus::BAD_REQUEST->value,
                'title' => 'You do not have enough credit.',
            ],
        ];
        yield 'problem with detail' => [
            new readonly class (
                status: HttpStatus::BAD_REQUEST,
                detail: 'Your current balance is 30, but that costs 50.'
            ) extends AbstractProblemDetails {
            },
            [
                'type' => 'about:blank',
                'status' => HttpStatus::BAD_REQUEST->value,
                'detail' => 'Your current balance is 30, but that costs 50.',
            ],
        ];
        yield 'problem with instance' => [
            new readonly class (
                status: HttpStatus::BAD_REQUEST,
                instance: '/account/123'
            ) extends AbstractProblemDetails {
            },
            [
                'type' => 'about:blank',
                'status' => HttpStatus::BAD_REQUEST->value,
                'instance' => '/account/123',
            ],
        ];
        yield 'problem with extensions' => [
            new readonly class (status: HttpStatus::BAD_REQUEST) extends AbstractProblemDetails {
                protected function extensions(): array
                {
                    return [
                        'balance' => 30,
                        'cost' => 50,
                    ];
                }
            },
            [
                'balance' => 30,
                'cost' => 50,
                'type' => 'about:blank',
                'status' => HttpStatus::BAD_REQUEST->value,
            ],
        ];
        yield 'complete problem' => [
            new readonly class (
                status: HttpStatus::UNPROCESSABLE_CONTENT,
                type: ProblemType::VALIDATION_ERROR,
                title: 'Your request is not valid',
                detail: 'Some details in the body of your request were invalid',
                instance: '/favourite-colour',
                baseUri: 'https://about.me.com'
            ) extends AbstractProblemDetails {
                protected function extensions(): array
                {
                    return [
                        'balance' => 30,
                        'cost' => 50,
                    ];
                }
            },
            [
                'balance' => 30,
                'cost' => 50,
                'type' => 'https://about.me.com/problem/validation-error',
                'status' => HttpStatus::UNPROCESSABLE_CONTENT->value,
                'title' => 'Your request is not valid',
                'detail' => 'Some details in the body of your request were invalid',
                'instance' => '/favourite-colour',
            ],
        ];
    }

    public function test_item_adds_json_content_type_header(): void
    {
        $response = $this->sut->item($this->response, []);

        $this->assertJsonContentType($response);
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed>|null $expected
     *
     * @return void
     */
    #[DataProvider('provideResponseData')]
    public function test_item_places_array_in_data_envelope(array $input, ?array $expected = null): void
    {
        $response = $this->sut->item($this->response, $input);

        $this->assertJsonValue(
            $expected ?? $input,
            'data',
            $response
        );
    }

    #[DataProvider('provideStatus')]
    public function test_item_can_change_status(HttpStatus $status): void
    {
        $response = $this->sut->item($this->response, [], $status);

        $this->assertSame($status->value, $response->getStatusCode());
    }

    /**
     * @param array<string, string|list<string>> $headers
     * @param array<string, string|list<string>> $expected
     *
     * @return void
     */
    #[DataProvider('provideHeaders')]
    public function test_item_can_add_headers(array $headers, array $expected): void
    {
        $response = $this->sut->item(response: $this->response, data: [], headers: $headers);

        $this->assertSame(['Content-Type' => ['application/json']] + $expected, $response->getHeaders());
    }

    public function test_collection_adds_json_content_type_header(): void
    {
        $response = $this->sut->collection(response: $this->response, data: [], page: new CursorPage(3));

        $this->assertJsonContentType($response);
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed>|null $expected
     *
     * @return void
     */
    #[DataProvider('provideResponseData')]
    public function test_collection_places_incoming_data_in_data_envelope(array $input, ?array $expected = null): void
    {
        $response = $this->sut->collection(response: $this->response, data: $input, page: new CursorPage(3));

        $this->assertJsonValue(
            $expected ?? $input,
            'data',
            $response
        );
    }

    #[DataProvider('provideStatus')]
    public function test_collection_can_change_status(HttpStatus $status): void
    {
        $response = $this->sut->collection(
            response: $this->response,
            data: [],
            page: new CursorPage(3),
            status: $status
        );

        $this->assertSame($status->value, $response->getStatusCode());
    }

    /**
     * @param array<string, string|list<string>> $headers
     * @param array<string, string|list<string>> $expected
     *
     * @return void
     */
    #[DataProvider('provideHeaders')]
    public function test_collection_can_add_headers(array $headers, array $expected): void
    {
        $response = $this->sut->collection(
            response: $this->response,
            data: [],
            page: new CursorPage(3),
            headers: $headers
        );

        $this->assertSame(['Content-Type' => ['application/json']] + $expected, $response->getHeaders());
    }

    /**
     * @param ProblemDetailsInterface $problemDetails
     * @param array<string, mixed> $expected
     *
     * @return void
     */
    #[DataProvider('provideProblemDetails')]
    public function test_problem_returns_data_in_correct_envelope(
        ProblemDetailsInterface $problemDetails,
        array $expected,
    ): void {
        $response = $this->sut->problem(
            response: $this->response,
            problemDetails: $problemDetails
        );

        $this->assertJsonData($expected, $response);
    }

    /**
     * @param array<string, string|list<string>> $headers
     * @param array<string, string|list<string>> $expected
     *
     * @return void
     */
    #[DataProvider('provideHeaders')]
    public function test_problem_can_add_headers(array $headers, array $expected): void
    {
        $problemDetails = new readonly class (HttpStatus::UNPROCESSABLE_CONTENT) extends AbstractProblemDetails {
        };
        $response = $this->sut->problem(
            response: $this->response,
            problemDetails: $problemDetails,
            headers: $headers
        );

        $this->assertSame(
            ['Content-Type' => ['application/problem+json']] + $expected,
            $response->getHeaders()
        );
    }

    #[DataProvider('provideStatus')]
    public function test_empty_can_change_status(HttpStatus $status): void
    {
        $response = $this->sut->empty($this->response, $status);

        $this->assertSame($status->value, $response->getStatusCode());
    }

    public function test_empty_adds_json_content_type_header(): void
    {
        $response = $this->sut->empty($this->response);

        $this->assertJsonContentType($response);
    }

    /**
     * @param array<string, string|list<string>> $headers
     * @param array<string, string|list<string>> $expected
     *
     * @return void
     */
    #[DataProvider('provideHeaders')]
    public function test_empty_can_add_headers(array $headers, array $expected): void
    {
        $response = $this->sut->empty(response: $this->response, headers: $headers);

        $this->assertSame(['Content-Type' => ['application/json']] + $expected, $response->getHeaders());
    }
}
