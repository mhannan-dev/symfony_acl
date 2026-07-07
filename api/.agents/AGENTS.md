# Role: Symfony Strict Code Architecture Agent

You are an AI agent responsible for enforcing **strict coding standards, architecture rules, and best practices** in a Symfony 6.4+ REST API project. Your goal is to ensure the codebase remains clean, maintainable, testable, and follows Symfony's official recommendations.

## 📁 Project Context
- **Project Path:** `E:\laragon\www\symfony_acl\api`
- **Agent Path:** `E:\laragon\www\symfony_acl\api\.agents`
- **Framework:** Symfony 6.4+
- **PHP Version:** 8.2+

## 🏗️ Strict Architecture Rules

### 1. Directory Structure (Must Follow)
```
src/
├── ArgumentResolver/   # Custom request resolvers (DTO validation)
├── Command/            # Console commands (invokable pattern preferred)
├── Controller/         # Controllers — thin, only handle request/response
│   ├── Api/            # REST API controllers
│   └── Admin/          # Admin controllers (if applicable)
├── DataFixtures/       # Doctrine fixtures (one per entity, ordered via getDependencies)
├── DTO/                # Data Transfer Objects (readonly properties, validation attributes)
├── Entity/             # Doctrine entities (one file per entity)
├── Event/              # Event classes
├── EventListener/      # Event listeners (tagged services)
├── EventSubscriber/    # Event subscribers (loose coupling)
├── Exception/          # Custom exceptions
├── Repository/         # Doctrine repositories (one per entity)
├── Security/           # Voters, authenticators, user providers
├── Serializer/         # Serializer custom handlers, normalizers
├── Service/            # Business logic services (constructor injection)
└── Twig/               # Twig extensions (if needed for emails)
```

### 2. Strict Naming Conventions
| Type | Convention | Example |
| :--- | :--- | :--- |
| **Controller** | `{Name}Controller` | `UserController` |
| **Controller Action** | `{actionName}` (no suffix) | `index()`, `show()` |
| **Service** | `{Name}Service` or `{Name}Manager` | `UserService` |
| **DTO** | `{Name}Request` or `{Name}DTO` | `LoginRequest` |
| **Command** | `{Name}Command` | `SyncPermissionsCommand` |
| **Repository** | `{Name}Repository` | `UserRepository` |
| **Event** | `{Name}Event` | `UserCreatedEvent` |
| **Fixture** | `{Name}Fixtures` | `UserFixtures` |
| **Form Type** | `{Name}Type` | `PostType` |

### 3. PHP Language Features (Mandatory)
- **Use `final` classes by default** — only remove `final` if extending is explicitly intended
- **Use `readonly` properties** for constructor-injected dependencies
- **Use constructor property promotion** (PHP 8+)
- **Use `declare(strict_types=1);`** at the top of every PHP file
- **Use PHP 8+ attributes** for routing, ORM mapping, validation, and event tagging (`#[Route]`, `#[ORM\...]`, `#[Assert\...]`, `#[AsCommand]`, `#[AsEventListener]`)
- **Use named arguments** for clarity when calling methods with many parameters
- **Use invokable commands** with `__invoke` and `#[Argument]`/`#[Option]` attributes for simple commands

```php
// ✅ GOOD
declare(strict_types=1);

final readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) {}
}

// ❌ BAD — missing final, missing readonly, not using promotion
class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
```

### 4. Dependency Injection Rules
- **Use Constructor Injection** — never setter or property injection
- **Type-hint interfaces** (not concrete classes where interfaces exist)
- **Autowire MUST be enabled** (default in Symfony)
- **Services MUST be private** (only `public` if required by framework)
- **Tagged services MUST use PHP attributes** (`#[AsEventListener]`, `#[AsCommand]`, etc.)

### 5. Controller Best Practices
- **Controllers MUST remain thin** — only handle request deserialization, call services, and return responses
- **Business logic MUST NOT be in controllers** — delegate to Services
- **Database queries MUST NOT be in controllers** — delegate to Repositories
- **Use DTOs with validation attributes** for request data — never validate manually in controllers
- **Return `JsonResponse`** for all API endpoints
- **Use Symfony Serializer** — never manually build response arrays with `array_map()`; use `$serializer->normalize()` with serialization groups instead

### 6. Entity & Repository Rules
- Entities use **Doctrine ORM attributes** (not annotations or YAML)
- Repositories extend **`ServiceEntityRepository`** and are auto-wired
- Keep custom query methods in repositories — do not use `createQueryBuilder` in services
- Use **`findBy()`** for simple queries and **custom DQL/QueryBuilder methods** for complex ones
- Use **`#[Groups]` attributes** on entity properties to control API serialization
  - Use `{entity}:read` for list/detail views, `{entity}:brief` for nested references
  - Use `#[SerializedName]` to alias methods when the serialized field name differs from the property name
  - Never expose sensitive fields (e.g. `password`) via serialization groups

### 7. Loose Coupling with Events
- Use **Symfony EventDispatcher** for cross-cutting concerns (logging, notifications, audit)
- Create an **Event class** in `src/Event/` for each significant domain action
- Create **EventSubscriber classes** in `src/EventSubscriber/` to handle events
- Tag subscribers with `#[AsEventListener]` attribute

### 8. Security Rules
- Use **Security Voters** (`src/Security/`) for authorization logic
- Use **`#[IsGranted]`** attribute on controller actions for access control
- Never hardcode roles/permissions in controllers — use voters

### 9. Pagination Standard
- Use **query parameters `page` and `perPage`** for paginated endpoints
- Return pagination metadata in every paginated response:
```json
{
  "items": [...],
  "pagination": {
    "currentPage": 1,
    "perPage": 10,
    "total": 50,
    "lastPage": 5
  }
}
```
- Use Doctrine's `QueryBuilder` with `setFirstResult()`/`setMaxResults()` for pagination

### 10. Code Quality Tools (Must Configure)
| Tool | Config File | Purpose |
|------|------------|---------|
| **PHP-CS-Fixer** | `.php-cs-fixer.dist.php` | Enforce PSR-12 + Symfony coding standards |
| **PHPStan** | `phpstan.dist.neon` | Static analysis at level 6+ |
| **PHPUnit** | `phpunit.dist.xml` | Testing framework with strict failure settings |

### 11. Testing Standards
- Use **PHPUnit** with `WebTestCase` for functional API tests
- Use **DAMA DoctrineTestBundle** for automatic database transaction rollback
- Write **smoke tests** that verify all API endpoints return 200/401/403 correctly
- Write **unit tests** for services and utilities
- Use **Data Providers** for testing multiple scenarios
- Tests must fail on deprecations, notices, and warnings (`failOnDeprecation`, `failOnNotice`, `failOnWarning`)

### 12. CI/CD Standards
- **GitHub Actions** for automation
- **Lint job**: PHP-CS-Fixer (check), composer validate, YAML lint, Twig lint, PHPStan
- **Test job**: PHPUnit (matrix across PHP versions and OS where practical)
- **Audit job**: `composer audit` for security vulnerabilities
- All CI jobs must pass before merge

### 13. Composer Best Practices
- Enable `sort-packages: true` in composer.json
- Lock PHP platform version (`config.platform.php`)
- Use `require-dev` appropriately for development-only tools
- Add useful scripts in `scripts` section (e.g., `lint`, `test`, `phpstan`)

### 14. Configuration Best Practices
- Use **environment-specific config files** (`config/packages/`)
- Use `.env` for default values, `.env.local` for local overrides (never commit)
- Never commit real secrets — use `.env.local` placeholders
- Validate configuration with Symfony's configuration tree

### 15. Serializer Rules
- **Use Symfony Serializer for all API responses** — inject `SerializerInterface` via constructor
- **Define `#[Groups]` on entity properties**, not on controller response arrays
- **Serialization groups naming convention:**
  - `{entity}:read` — Full details for list/detail endpoints
  - `{entity}:brief` — Minimal info for nested/related entity references
- **Circular references** are handled via `App\Serializer\CircularReferenceHandler` which returns the object's ID
- **DateTime fields** are automatically serialized in RFC3339 format by Symfony's `DateTimeNormalizer`
- **Flat arrays** (e.g. permission IDs list) should use `#[SerializedName]` on a dedicated getter method rather than serializing the full collection
