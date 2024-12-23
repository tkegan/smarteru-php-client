<?php

/**
 * Contains CBS\SmarterU\tests\Client\GetUserGroupsClientTest.
 *
 * @copyright   $year$ Core Business Solutions
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\CBS\SmarterU;

use CBS\SmarterU\DataTypes\ErrorCode;
use CBS\SmarterU\DataTypes\Group;
use CBS\SmarterU\DataTypes\Permission;
use CBS\SmarterU\Exceptions\MissingValueException;
use CBS\SmarterU\Exceptions\SmarterUException;
use CBS\SmarterU\Queries\GetUserQuery;
use CBS\SmarterU\Client;
use DateTime;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

/**
 * Tests CBS\SmarterU\Client::getUserGroups().
 */
class GetUserGroupsClientTest extends TestCase {
    /**
     * Test that getUserGroups() passes the correct input into the SmarterU API
     * when all required information is present and the query uses the ID as
     * the user identifier.
     */
    public function testGetUserGroupsProducesCorrectInputForUserID(): void {
        $accountApi = 'account';
        $userApi = 'user';
        $id = '1';
        $client = new Client($accountApi, $userApi);

        $createdDate = '2022-07-29';
        $modifiedDate = '2022-07-30';

        /**
         * The response needs a body because getUserGroups() will try to
         * process the body once the response has been received, however
         * this test is about making sure the request made by getUserGroups()
         * is correct. The processing of the response will be tested further
         * down.
         */
        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <UserGroups>
                    <Group>
                        <Name>My Group</Name>
                        <Identifier>My Group</Identifier>
                        <IsHomeGroup>1</IsHomeGroup>

                        <Permissions>
                            <Permission>MANAGE_USERS</Permission>
                        </Permissions>
                    </Group>
                </UserGroups>
            </Info>
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
        $client->readGroupsForUserById($id);

        // Make sure there is only 1 request, then translate it to XML.
        self::assertCount(1, $container);
        $request = $container[0]['request'];
        $decodedBody = urldecode((string) $request->getBody());
        $expectedBody = 'Package=' . $client->getXMLGenerator()->getUser(
            $accountApi,
            $userApi,
            (new GetUserQuery())
                ->setId($id)
                ->setMethod('getUserGroups')
        );
        self::assertEquals($decodedBody, $expectedBody);
    }

    /**
     * Test that getUserGroups() passes the correct input into the SmarterU API
     * when all required information is present and the query uses the ID as
     * the user identifier.
     */
    public function testGetUserGroupsProducesCorrectInputForEmailAddress(): void {
        $accountApi = 'account';
        $userApi = 'user';
        $email = 'test@test.com';
        $client = new Client($accountApi, $userApi);

        $query = (new GetUserQuery())
            ->setEmail($email);

        $createdDate = '2022-07-29';
        $modifiedDate = '2022-07-30';

        /**
         * The response needs a body because getUserGroups() will try to
         * process the body once the response has been received, however
         * this test is about making sure the request made by getUserGroups()
         * is correct. The processing of the response will be tested further
         * down.
         */
        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <UserGroups>
                    <Group>
                        <Name>My Group</Name>
                        <Identifier>My Group</Identifier>
                        <IsHomeGroup>1</IsHomeGroup>

                        <Permissions>
                            <Permission>MANAGE_USERS</Permission>
                        </Permissions>
                    </Group>
                </UserGroups>
            </Info>
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
        $client->readGroupsForUserByEmail($email);

        // Make sure there is only 1 request, then translate it to XML.
        self::assertCount(1, $container);
        $request = $container[0]['request'];
        $decodedBody = urldecode((string) $request->getBody());
        $expectedBody = 'Package=' . $client->getXMLGenerator()->getUser(
            $accountApi,
            $userApi,
            (new GetUserQuery())
                ->setEmail($email)
                ->setMethod('getUserGroups')
        );
        self::assertEquals($decodedBody, $expectedBody);
    }

    /**
     * Test that getUserGroups() passes the correct input into the SmarterU API
     * when all required information is present and the query uses the employee
     * ID as the user identifier.
     */
    public function testGetUserGroupsProducesCorrectInputForEmployeeID(): void {
        $accountApi = 'account';
        $userApi = 'user';
        $employeeId = '1';
        $client = new Client($accountApi, $userApi);

        $createdDate = '2022-07-29';
        $modifiedDate = '2022-07-30';

        /**
         * The response needs a body because getUserGroups() will try to
         * process the body once the response has been received, however this
         * test is about making sure the request made by getUserGroups() is
         * correct. The processing of the response will be tested further down.
         */
        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <UserGroups>
                    <Group>
                        <Name>My Group</Name>
                        <Identifier>My Group</Identifier>
                        <IsHomeGroup>1</IsHomeGroup>

                        <Permissions>
                            <Permission>MANAGE_USERS</Permission>
                        </Permissions>
                    </Group>
                </UserGroups>
            </Info>
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
        $client->readGroupsForUserByEmployeeId($employeeId);

        // Make sure there is only 1 request, then translate it to XML.
        self::assertCount(1, $container);
        $request = $container[0]['request'];
        $decodedBody = urldecode((string) $request->getBody());
        $expectedBody = 'Package=' . $client->getXMLGenerator()->getUser(
            $accountApi,
            $userApi,
            (new GetUserQuery())
                ->setEmployeeId($employeeId)
                ->setMethod('getUserGroups')
        );
        self::assertEquals($decodedBody, $expectedBody);
    }

    /**
     * Test that getUserGroups() throws an exception when the request results
     * in an HTTP error.
     */
    public function testGetUserThrowsExceptionWhenHTTPErrorOccurs(): void {
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
        $client->readGroupsForUserByEmail('test@test.com');
    }

    /**
     * Test that getUserGroups() throws an exception when the SmarterU API
     * returns a fatal error.
     */
    public function testGetUserThrowsExceptionWhenFatalErrorReturned(): void {
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $codes = ['UT:01', 'UT:02'];
        $messages = [
            'An error mocked for unit testing',
            'Another error mocked for unit testing'
        ];
        $body = <<<XML
        <SmarterU>
            <Result>Failed</Result>
            <Errors>
                <Error>
                    <ErrorID>$codes[0]</ErrorID>
                    <ErrorMessage>$messages[0]</ErrorMessage>
                </Error>
                <Error>
                    <ErrorID>$codes[1]</ErrorID>
                    <ErrorMessage>$messages[1]</ErrorMessage>
                </Error>
            </Errors>
        </SmarterU>
        XML;

        $response = new Response(200, [], $body);

        $container = [];
        $history = Middleware::history($container);

        $mock = (new MockHandler([$response]));

        $handlerStack = HandlerStack::create($mock);

        $handlerStack->push($history);

        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $logger->expects($this->once())->method('error')->with(
            $this->identicalTo('Failed to make request to SmarterU API. See context for request/response details.'),
            $this->identicalTo([
                'request' => "<?xml version=\"1.0\"?>\n<SmarterU>\n<AccountAPI>********</AccountAPI><UserAPI>********</UserAPI><Method>getUserGroups</Method><Parameters><User><ID>1</ID></User></Parameters></SmarterU>\n",
                'response' => $body
            ])
        );

        $client
            ->setHttpClient($httpClient)
            ->setLogger($logger);

        // Make the request. Because we want to inspect custom exception
        // properties we'll handle the try/catch/cache of the exception
        $exception = null;
        try {
            $client->readGroupsForUserById('1');
        } catch (SmarterUException $error) {
            $exception = $error;
        }

        self::assertInstanceOf(SmarterUException::class, $exception);
        self::assertEquals(Client::SMARTERU_EXCEPTION_MESSAGE, $exception->getMessage());

        $errorCodes = $error->getErrorCodes();
        self::assertIsArray($errorCodes);
        self::assertCount(2, $errorCodes);

        $errorCode = reset($errorCodes);
        self::assertInstanceOf(ErrorCode::class, $errorCode);
        self::assertContains($errorCode->getErrorCode(), $codes);
        self::assertContains($errorCode->getErrorMessage(), $messages);

        $errorCode = next($errorCodes);
        self::assertInstanceOf(ErrorCode::class, $errorCode);
        self::assertContains($errorCode->getErrorCode(), $codes);
        self::assertContains($errorCode->getErrorMessage(), $messages);
    }

    /**
     * Test that getUserGroups() returns the expected output when the SmarterU API
     * returns a single Group with no errors.
     */
    public function testGetUserGroupsReturnsExpectedSingleGroup(): void {
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $name1 = 'My Group';
        $identifier1 = '1';
        $isHomeGroup1 = '0';
        $permission1 = 'MANAGE_USERS';
        $permission2 = 'MANAGE_GROUP_USERS';

        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <UserGroups>
                    <Group>
                        <Name>$name1</Name>
                        <Identifier>$identifier1</Identifier>
                        <IsHomeGroup>$isHomeGroup1</IsHomeGroup>
                        <Permissions>
                            <Permission>$permission1</Permission>
                            <Permission>$permission2</Permission>
                        </Permissions>
                    </Group>
                </UserGroups>
            </Info>
            <Errors>
            </Errors>
        </SmarterU>
        XML;

        $response = new Response(200, [], $xmlString);
        $container = [];
        $history = Middleware::history($container);
        $mock = (new MockHandler([$response]));
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $client->setHttpClient($httpClient);

        // Make the request.
        $result = $client->readGroupsForUserByEmployeeId('5');

        self::assertIsArray($result);
        self::assertCount(1, $result);
        self::assertInstanceOf(Group::class, $result[0]);
        self::assertEquals($name1, $result[0]->getName());
        self::assertEquals($identifier1, $result[0]->getGroupId());
        self::assertCount(2, $result[0]->getPermissions());
        self::assertContains(
            $permission1,
            $result[0]->getPermissions()
        );
        self::assertContains(
            $permission2,
            $result[0]->getPermissions()
        );
    }

    /**
     * Test that getUserGroups() returns the expected output when the SmarterU API
     * returns multiple Groups without any errors.
     */
    public function testGetUserGroupsReturnsExpectedMultipleGroups(): void {
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $name1 = 'My Group';
        $identifier1 = '1';
        $isHomeGroup1 = '0';
        $permission1 = 'MANAGE_USERS';
        $permission2 = 'MANAGE_GROUP_USERS';
        $name2 = 'Other Group';
        $identifier2 = '2';
        $isHomeGroup2 = '1';
        $name3 = 'Third Group';
        $identifier3 = '3';
        $isHomeGroup3 = '0';

        $createdDate = '2022-07-29';
        $modifiedDate = '2022-07-30';

        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <UserGroups>
                    <Group>
                        <Name>$name1</Name>
                        <Identifier>$identifier1</Identifier>
                        <IsHomeGroup>$isHomeGroup1</IsHomeGroup>
                        <Permissions>
                            <Permission>$permission1</Permission>
                            <Permission>$permission2</Permission>
                        </Permissions>
                    </Group>
                    <Group>
                        <Name>$name2</Name>
                        <Identifier>$identifier2</Identifier>
                        <IsHomeGroup>$isHomeGroup2</IsHomeGroup>
                        <Permissions>
                        </Permissions>
                    </Group>
                    <Group>
                        <Name>$name3</Name>
                        <Identifier>$identifier3</Identifier>
                        <IsHomeGroup>$isHomeGroup3</IsHomeGroup>
                        <Permissions>
                            <Permission>$permission1</Permission>
                        </Permissions>
                    </Group>
                </UserGroups>
            </Info>
            <Errors>
            </Errors>
        </SmarterU>
        XML;

        $response = new Response(200, [], $xmlString);
        $container = [];
        $history = Middleware::history($container);
        $mock = (new MockHandler([$response]));
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $httpClient = new HttpClient(['handler' => $handlerStack]);
        $client->setHttpClient($httpClient);

        // Make the request.
        $result = $client->readGroupsForUserById('1');

        self::assertIsArray($result);
        self::assertCount(3, $result);
        self::assertInstanceOf(Group::class, $result[0]);
        self::assertEquals($name1, $result[0]->getName());
        self::assertEquals($identifier1, $result[0]->getGroupId());
        self::assertCount(2, $result[0]->getPermissions());
        self::assertContains(
            $permission1,
            $result[0]->getPermissions()
        );
        self::assertContains(
            $permission2,
            $result[0]->getPermissions()
        );
        self::assertInstanceOf(Group::class, $result[1]);
        self::assertEquals($name2, $result[1]->getName());
        self::assertEquals($identifier2, $result[1]->getGroupId());
        self::assertCount(0, $result[1]->getPermissions());
        self::assertInstanceOf(Group::class, $result[2]);
        self::assertEquals($name3, $result[2]->getName());
        self::assertEquals($identifier3, $result[2]->getGroupId());
        self::assertCount(1, $result[2]->getPermissions());
        self::assertContains(
            $permission1,
            $result[2]->getPermissions()
        );
    }
}
