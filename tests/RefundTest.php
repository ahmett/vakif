<?php

namespace Payconn\Vakif\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Payconn\Common\HttpClient;
use Payconn\Vakif\Model\Refund;
use Payconn\Vakif\Token;
use PHPUnit\Framework\TestCase;

class RefundTest extends TestCase
{
    public function testFailure()
    {
        $response = new Response(200, [], '<?xml version="1.0" encoding="UTF-8"?>
        <VposResponse>
           <ResultCode>0001</ResultCode>
           <ResultDetail>İŞLEM BAŞARISIZ</ResultDetail>
        </VposResponse>');
        $mock = new MockHandler([
            $response,
        ]);
        $handler = HandlerStack::create($mock);
        $client = new HttpClient(['handler' => $handler]);

        // purchase
        $token = new Token('123', '345', '12345');
        $refund = new Refund();
        $refund->setTestMode(true);
        $refund->setAmount(100);
        $refund->setOrderId('GVP'.time());
        $response = (new \Payconn\Vakif($token, $client))->refund($refund);
        $this->assertFalse($response->isSuccessful());
    }

    public function testSuccessful()
    {
        $response = new Response(200, [], '<?xml version="1.0" encoding="UTF-8"?>
        <VposResponse>
           <ResultCode>0000</ResultCode>
           <ResultDetail>İŞLEM BAŞARILI</ResultDetail>
        </VposResponse>');
        $mock = new MockHandler([
            $response,
        ]);
        $handler = HandlerStack::create($mock);
        $client = new HttpClient(['handler' => $handler]);

        // refund
        $token = new Token('123', '345', '12345');
        $refund = new Refund();
        $refund->setTestMode(true);
        $refund->setAmount(100);
        $refund->setOrderId('GVP'.time());
        $response = (new \Payconn\Vakif($token, $client))->refund($refund);
        $this->assertTrue($response->isSuccessful());
    }
}
