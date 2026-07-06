# Testing Standards

## Framework & Tools
- **PHPUnit** (via `phpunit/phpunit` ^11)
- **DAMA DoctrineTestBundle** (`dama/doctrine-test-bundle`) for automatic database transaction rollback
- **Symfony WebTestCase** for functional tests
- **Symfony Panther** (optional) for browser-level tests

## Test Structure
```
tests/
├── bootstrap.php               # Test bootstrap (load .env.test, set umask)
├── Controller/                  # Functional tests for controllers
│   ├── Api/
│   │   ├── UserControllerTest.php
│   │   ├── GroupControllerTest.php
│   │   ├── PermissionControllerTest.php
│   │   ├── AuthControllerTest.php
│   │   └── ActivityLogControllerTest.php
├── Command/                     # Console command tests
│   ├── AbstractCommandTestCase.php
│   └── SyncPermissionsCommandTest.php
├── Service/                     # Unit tests for services
├── Security/                    # Voter tests
└── Utils/                       # Utility tests
```

## Test Patterns

### Functional API Tests (WebTestCase)
```php
declare(strict_types=1);

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserControllerTest extends WebTestCase
{
    public function testListUsers(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/users');

        self::assertResponseIsSuccessful();
        self::assertJson($client->getResponse()->getContent());
    }

    public function testCreateUserRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/users/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]));

        self::assertResponseStatusCodeSame(401);
    }
}
```

### Authenticated Requests
```php
public function testCreateUserAsAdmin(): void
{
    $client = static::createClient();
    // Login as admin first
    $client->request('POST', '/api/v1/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
        'email' => 'admin@yopmail.com',
        'password' => 'Test@1234',
    ]));

    $client->request('POST', '/api/v1/users/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'Password1',
    ]));

    self::assertResponseStatusCodeSame(200);
}
```

### Smoke Tests
Always include a smoke test that verifies all public and authenticated routes respond correctly:
```php
public function testPublicUrlsAreSuccessful(): void
{
    $client = static::createClient();
    $client->request('GET', '/api/v1/login');
    self::assertResponseIsSuccessful();
}

public function testSecureUrlsRedirectToLogin(): void
{
    $client = static::createClient();
    $client->request('GET', '/api/v1/users');
    self::assertResponseStatusCodeSame(401);
}
```

### Data Providers
```php
public static function invalidUserDataProvider(): array
{
    return [
        'missing name' => [['email' => 'test@test.com', 'password' => 'Pass1234']],
        'invalid email' => [['name' => 'Test', 'email' => 'not-an-email', 'password' => 'Pass1234']],
        'short password' => [['name' => 'Test', 'email' => 'test@test.com', 'password' => 'short']],
    ];
}

/** @dataProvider invalidUserDataProvider */
public function testCreateUserValidation(array $data): void
{
    $client = static::createClient();
    $client->request('POST', '/api/v1/users/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
    self::assertResponseStatusCodeSame(422);
}
```

### Command Tests
```php
final class SyncPermissionsCommandTest extends WebTestCase
{
    public function testExecute(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:sync-permissions');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
    }
}
```

## PHPUnit Configuration (`phpunit.dist.xml`)
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true"
         failOnDeprecation="true"
         failOnNotice="true"
         failOnWarning="true"
>
    <php>
        <env name="APP_ENV" value="test"/>
    </php>

    <testsuites>
        <testsuite name="Project Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

## DAMA DoctrineTestBundle Configuration
```yaml
# config/packages/dama_doctrine_test_bundle.yaml
dama_doctrine_test:
    enable_static_connection: true
    enable_static_meta_data_cache: true
    enable_static_query_cache: true
```

## Test Bootstrap (`tests/bootstrap.php`)
```php
<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}
```
