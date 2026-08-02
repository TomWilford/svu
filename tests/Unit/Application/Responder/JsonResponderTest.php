<?php

declare(strict_types=1);

namespace Unit\Application\Responder;

use App\Application\Responder\JsonResponder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Selective\TestTrait\Traits\HttpJsonTestTrait;

#[CoversClass(JsonResponder::class)]
class JsonResponderTest extends TestCase
{
    use HttpJsonTestTrait;

    private JsonResponder $sut;
    private ResponseInterface $response;

    protected function setUp(): void
    {
        $this->response = new Psr17Factory()->createResponse();
        $this->sut = new JsonResponder();
    }

    public function test_respond_sets_application_json_header(): void
    {
        $result = $this->sut->respond($this->response, []);

        $this->assertJsonContentType($result);
    }

    public function test_respond_encodes_empty_array(): void
    {
        $result = $this->sut->respond($this->response, []);

        $this->assertJsonStringEqualsJsonString('[]', (string) $result->getBody());
    }

    public function test_respond_encodes_multidimensional_array()
    {
        $result = $this->sut->respond($this->response, [
            'foo' => 'bar',
            'baz' => [
                'bat' => 'biz'
            ]
        ]);

        $this->assertJsonStringEqualsJsonString('{"foo":"bar","baz":{"bat":"biz"}}', (string) $result->getBody());
    }
}
