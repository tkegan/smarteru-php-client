<?php

/**
 * Contains Tests\CBS\SmarterU\UpdateGroupClientTest.php
 *
 * @author      Will Santanen <will.santanen@thecoresolution.com>
 * @copyright   $year$ Core Business Solutions
 * @license     MIT
 * @version     $version$
 * @since       2022/08/11
 */

declare(strict_types=1);

namespace Tests\CBS\SmarterU\Client;

use CBS\SmarterU\DataTypes\Group;
use CBS\SmarterU\DataTypes\GroupPermissions;
use CBS\SmarterU\DataTypes\LearningModule;
use CBS\SmarterU\DataTypes\SubscriptionVariant;
use CBS\SmarterU\DataTypes\Tag;
use CBS\SmarterU\DataTypes\Permission;
use CBS\SmarterU\Exceptions\MissingValueException;
use CBS\SmarterU\Exceptions\SmarterUException;
use CBS\SmarterU\Queries\GetUserGroupsQuery;
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
 * Tests CBS\SmarterU\Client::updateGroup().
 */
class UpdateGroupClientTest extends TestCase {
    /**
     * A Group to pass into the Client for testing.
     */
    protected Group $group;

    /**
     * Set up the Group for testing.
     */
    public function setUp(): void {
        $name = 'My Group';
        $groupId = '12';
        $createdDate = new DateTime('2022/08/02');
        $modifiedDate = new DateTime();
        $description = 'This is a group created for testing.';
        $homeGroupMessage = 'Home Group';
        $email1 = 'phpunit@test.com';
        $email2 = 'test@phpunit.com';
        $notificationEmails = [$email1, $email2];
        $userHelpOverrideDefault = false;
        $userHelpEnabled = true;
        $helpEmail1 = 'phpunit2@test.com';
        $helpEmail2 = 'test2@phpunit.com';
        $userHelpEmail = [$helpEmail1, $helpEmail2];
        $userHelpText = 'Help Message';
        $tag1 = (new Tag())
            ->setTagId('1')
            ->setTagValues('Tag1 values');
        $tag2 = (new Tag())
            ->setTagId('2')
            ->setTagValues('Tag2 values');
        $tags = [$tag1, $tag2];
        $userLimitEnabled = true;
        $userLimitAmount = 50;
        $status = 'Active';
        $permission1 = (new Permission())
            ->setCode('MANAGE_USERS');
        $permission2 = (new Permission())
            ->setCode('MANAGE_COURSES');
        $user1 = (new GroupPermissions())
            ->setEmployeeId('2')
            ->setHomeGroup(true)
            ->setAction('Add')
            ->setPermissions([$permission1, $permission2]);
        $user2 = (new GroupPermissions())
            ->setEmployeeId('3')
            ->setHomeGroup(false)
            ->setAction('Add')
            ->setPermissions([]);
        $users = [$user1, $user2];
        $module1 = (new LearningModule())
            ->setId('4')
            ->setAction('Add')
            ->setAllowSelfEnroll(true)
            ->setAutoEnroll(false);
        $module2 = (new LearningModule())
            ->setId('5')
            ->setAction('Remove')
            ->setAllowSelfEnroll(false)
            ->setAutoEnroll(true);
        $learningModules = [$module1, $module2];
        $variant1 = (new SubscriptionVariant())
            ->setId('6')
            ->setAction('Add')
            ->setRequiresCredits(true);
        $variant2 = (new SubscriptionVariant())
            ->setId('7')
            ->setAction('Remove')
            ->setRequiresCredits(false);
        $subscriptionVariants = [$variant1, $variant2];
        $dashboardSetId = '8';

        $this->group = (new Group())
            ->setName($name)
            ->setGroupId($groupId)
            ->setCreatedDate($createdDate)
            ->setModifiedDate($modifiedDate)
            ->setDescription($description)
            ->setHomeGroupMessage($homeGroupMessage)
            ->setNotificationEmails($notificationEmails)
            ->setUserHelpOverrideDefault($userHelpOverrideDefault)
            ->setUserHelpEnabled($userHelpEnabled)
            ->setUserHelpEmail($userHelpEmail)
            ->setUserHelpText($userHelpText)
            ->setTags($tags)
            ->setUserLimitEnabled($userLimitEnabled)
            ->setUserLimitAmount($userLimitAmount)
            ->setStatus($status)
            ->setUsers($users)
            ->setLearningModules($learningModules)
            ->setSubscriptionVariants($subscriptionVariants)
            ->setDashboardSetId($dashboardSetId);
    }

    /**
     * Test that Client::updateGroup() produces the expected input for the API
     * when all required information and all optional information is present.
     */
    public function testUpdateGroupProducesExpectedInputWhenAllInfoIsPresent() {
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $name = $this->group->getName();
        $groupId = $this->group->getGroupId();

        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <Group>$name</Group>
                <GroupID>$groupId</GroupID>
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
        $client->updateGroup($this->group);

        // Make sure there is only 1 request, then translate it to XML.
        self::assertCount(1, $container);
        $request = $container[0]['request'];
        $decodedBody = urldecode((string) $request->getBody());
        $expectedBody = 'Package=' . $client->getXMLGenerator()->updateGroup(
            $accountApi,
            $userApi,
            $this->group
        );
        self::assertEquals($decodedBody, $expectedBody);
    }

    /**
     * Test that Client::updateGroup() produces the expected input for the API
     * when all required information but no optional information is present.
     */
    public function testUpdateGroupProducesExpectedInputWithOnlyRequiredInfo() {
        $accountApi = 'account';
        $userApi = 'user';
        $method = 'method';
        $id = '1';
        $newId = '2';
        $users = $this->group->getUsers();
        $learningModules = $this->group->getLearningModules();
        $subscriptionVariants = $this->group->getSubscriptionVariants();

        $group = (new Group())
            ->setOldGroupId($id)
            ->setGroupId($newId)
            ->setUsers($users)
            ->setLearningModules($learningModules)
            ->setSubscriptionVariants($subscriptionVariants);

        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <Group>$newId</Group>
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
        $client->updateGroup($group);

        // XML translation clears out the old name/ID values so that
        // any future updateGroup queries on the same Group object do not
        // mistakenly use the old data to identify the Group after the name
        // and/or ID have already been updated by a previous query.
        // They must be set again to produce the expected output below.
        $group->setOldGroupId($id);

        // Make sure there is only 1 request, then translate it to XML.
        self::assertCount(1, $container);
        $request = $container[0]['request'];
        $decodedBody = urldecode((string) $request->getBody());
        $expectedBody = 'Package=' . $client->getXMLGenerator()->updateGroup(
            $accountApi,
            $userApi,
            $group
        );
        self::assertEquals($decodedBody, $expectedBody);
    }

    /**
     * Test that updateGroup() throws an exception when the request results
     * in an HTTP error.
     */
    public function testUpdateGroupThrowsExceptionWhenHTTPErrorOccurs() {
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
        $client->updateGroup($this->group);
    }

    /**
     * Test that updateGroup() throws an exception when the SmarterU API
     * returns a fatal error.
     */
    public function testUpdateGroupThrowsExceptionWhenFatalErrorReturned() {
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $xmlString = <<<XML
        <SmarterU>
            <Result>Failed</Result>
            <Errors>
                <Error>
                    <ErrorID>Error1</ErrorID>
                    <ErrorMessage>Testing</ErrorMessage>
                </Error>
                <Error>
                    <ErrorID>Error2</ErrorID>
                    <ErrorMessage>123</ErrorMessage>
                </Error>
            </Errors>
        </SmarterU>
        XML;

        $xml = simplexml_load_string($xmlString);
        $body = $xml->asXML();

        $response = new Response(200, [], $body);

        $container = [];
        $history = Middleware::history($container);

        $mock = (new MockHandler([$response]));

        $handlerStack = HandlerStack::create($mock);

        $handlerStack->push($history);

        $httpClient = new HttpClient(['handler' => $handlerStack]);

        $client->setHttpClient($httpClient);

        self::expectException(SmarterUException::class);
        self::expectExceptionMessage('Error1: Testing, Error2: 123');
        $client->updateGroup($this->group);
    }

    /**
     * Test that updateGroup() returns the expected output when the SmarterU
     * API returns no errors.
     */
    public function testUpdateGroupProducesCorrectOutput() {
        $accountApi = 'account';
        $userApi = 'user';
        $client = new Client($accountApi, $userApi);

        $name = $this->group->getName();
        $groupId = $this->group->getGroupId();

        $xmlString = <<<XML
        <SmarterU>
            <Result>Success</Result>
            <Info>
                <Group>$name</Group>
                <GroupID>$groupId</GroupID>
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
        $result = $client->updateGroup($this->group);
        self::assertInstanceOf(Group::class, $result);
        self::assertEquals($this->group->getName(), $result->getName());
        self::assertEquals($this->group->getGroupId(), $result->getGroupId());
    }
}
