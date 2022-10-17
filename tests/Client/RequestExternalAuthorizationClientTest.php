<?php

/**
 * Contains CBS\SmarterU\Tests\Client\RequestExternalAuthorizationClientTest.php.
 *
 * @author      Will Santanen
 * @copyright   $year$ Core Business Solutions
 * @license     MIT
 * @version     $version$
 * @since       2022/10/11
 */

declare(strict_types=1);

namespace Tests\CBS\SmarterU\Client;

use CBS\SmarterU\DataTypes\ExternalAuthorization;
use CBS\SmarterU\Exceptions\SmarterUException;
use CBS\SmarterU\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

/**
 * Tests Client::requestExternalAuthorization().
 */
class RequestExternalAuthorizationClientTest extends TestCase {
    /**
     * Test that Client::requestExternalAuthorizationByEmail() produces the
     * correct input to the SmarterU API and returns the correct value.
     */
    public function testRequestExternalAuthorizationByEmailProducesCorrectOutput() {
        $authKey = 'authKey';
        $requestKey = 'requestKey';
        $redirectPath = 'redirectPath';

        $email = 'test@test.com';
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <AuthKey>$authKey</AuthKey>
                <RequestKey>$requestKey</RequestKey>
                <RedirectPath>$redirectPath</RedirectPath>
            </Info>
            <Errors>
            </Errors>
        </SmarterU>
        XML;

        // Set up the container to capture the request.
        $response = new Response(200, [], $xmlString);
        $container = [];
        $history = Middleware::history($container);
        $mock = (new MockHandler([$response]));
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $client->setHttpClient($httpClient);

        // Make the request.
        $response = $client->requestExternalAuthorizationByEmail($email);

        // Make sure there is only 1 request, then translate it to XML.
        self::assertCount(1, $container);
        $request = $container[0]['request'];
        $decodedBody = urldecode((string) $request->getBody());
        $expectedBody = 'Package=' . $client->getXMLGenerator()->requestExternalAuthorization(
            $accountApi,
            $userApi,
            ['Email' => $email]
        );
        self::assertEquals($decodedBody, $expectedBody);

        self::assertInstanceOf(ExternalAuthorization::class, $response);
        self::assertEquals($response->getAuthKey(), $authKey);
        self::assertEquals($response->getRequestKey(), $requestKey);
        self::assertEquals($response->getRedirectPath(), $redirectPath);
    }

    /**
     * Test that Client::requestExternalAuthorizationByEmail() throws the
     * expected exception when an HTTP error occurs and prevents the request
     * from being made.
     */
    public function testRequestExternalAuthorizationByEmailThrowsExceptionWhenHttpErrorOccurs() {
        $email = 'test@test.com';
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $response = new Response(404);

        $container = [];
        $history = Middleware::history($container);

        $mock = (new MockHandler([$response]));

        $handlerStack = HandlerStack::create($mock);

        $handlerStack->push($history);

        $httpClient = new HttpClient(['handler' => $handlerStack]);

        $client->setHttpClient($httpClient);

        self::expectException(ClientException::class);
        self::expectExceptionMessage('Client error: ');
        $client->requestExternalAuthorizationByEmail($email);
    }

    /**
     * Test that Client::requestExternalAuthorizationByEmail() throws the
     * expected exception when the SmarterU API returns a fatal error.
     */
    public function testRequestExternalAuthorizationByEmailThrowsExceptionWhenFatalErrorReturned() {
        $email = 'test@test.com';
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $xmlString = <<<XML
        <SmarterU>
            <Result>Failed</Result>
            <Info>
            </Info>
            <Errors>
                <Error>
                    <ErrorID>1</ErrorID>
                    <ErrorMessage>An error has occurred.</ErrorMessage>
                </Error>
            </Errors>
        </SmarterU>
        XML;

        // Set up the container to capture the request.
        $response = new Response(200, [], $xmlString);
        $container = [];
        $history = Middleware::history($container);
        $mock = (new MockHandler([$response]));
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $client->setHttpClient($httpClient);

        // Make the request.
        self::expectException(SmarterUException::class);
        self::expectExceptionMessage(
            'SmarterU rejected the request due to the following error(s): 1: An error has occurred'
        );
        $client->requestExternalAuthorizationByEmail($email);
    }

    /**
     * Test that Client::requestExternalAuthorizationByEmployeeId() produces
     * the correct input to the SmarterU API and returns the correct value.
     */
    public function testRequestExternalAuthorizationByEmployeeIdProducesCorrectOutput() {
        $authKey = 'authKey';
        $requestKey = 'requestKey';
        $redirectPath = 'redirectPath';

        $employeeId = '12';
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <AuthKey>$authKey</AuthKey>
                <RequestKey>$requestKey</RequestKey>
                <RedirectPath>$redirectPath</RedirectPath>
            </Info>
            <Errors>
            </Errors>
        </SmarterU>
        XML;

        // Set up the container to capture the request.
        $response = new Response(200, [], $xmlString);
        $container = [];
        $history = Middleware::history($container);
        $mock = (new MockHandler([$response]));
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $client->setHttpClient($httpClient);

        // Make the request.
        $response = $client->requestExternalAuthorizationByEmployeeId($employeeId);

        // Make sure there is only 1 request, then translate it to XML.
        self::assertCount(1, $container);
        $request = $container[0]['request'];
        $decodedBody = urldecode((string) $request->getBody());
        $expectedBody = 'Package=' . $client->getXMLGenerator()->requestExternalAuthorization(
            $accountApi,
            $userApi,
            ['EmployeeID' => $employeeId]
        );
        self::assertEquals($decodedBody, $expectedBody);

        self::assertInstanceOf(ExternalAuthorization::class, $response);
        self::assertEquals($response->getAuthKey(), $authKey);
        self::assertEquals($response->getRequestKey(), $requestKey);
        self::assertEquals($response->getRedirectPath(), $redirectPath);
    }

    /**
     * Test that Client::requestExternalAuthorizationByEmployeeId() throws the
     * expected exception when an HTTP error occurs and prevents the request
     * from being made.
     */
    public function testRequestExternalAuthorizationByEmployeeIdThrowsExceptionWhenHttpErrorOccurs() {
        $employeeId = '12';
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $response = new Response(404);

        $container = [];
        $history = Middleware::history($container);

        $mock = (new MockHandler([$response]));

        $handlerStack = HandlerStack::create($mock);

        $handlerStack->push($history);

        $httpClient = new HttpClient(['handler' => $handlerStack]);

        $client->setHttpClient($httpClient);

        self::expectException(ClientException::class);
        self::expectExceptionMessage('Client error: ');
        $client->requestExternalAuthorizationByEmployeeId($employeeId);
    }

    /**
     * Test that Client::requestExternalAuthorizationByEmployeeId() throws the
     * expected exception when the SmarterU API returns a fatal error.
     */
    public function testRequestExternalAuthorizationByEmployeeIdThrowsExceptionWhenFatalErrorReturned() {
        $employeeId = '12';
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $xmlString = <<<XML
        <SmarterU>
            <Result>Failed</Result>
            <Info>
            </Info>
            <Errors>
                <Error>
                    <ErrorID>1</ErrorID>
                    <ErrorMessage>An error has occurred.</ErrorMessage>
                </Error>
            </Errors>
        </SmarterU>
        XML;

        // Set up the container to capture the request.
        $response = new Response(200, [], $xmlString);
        $container = [];
        $history = Middleware::history($container);
        $mock = (new MockHandler([$response]));
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $client->setHttpClient($httpClient);

        // Make the request.
        self::expectException(SmarterUException::class);
        self::expectExceptionMessage(
            'SmarterU rejected the request due to the following error(s): 1: An error has occurred'
        );
        $client->requestExternalAuthorizationByEmployeeId($employeeId);
    }
}
