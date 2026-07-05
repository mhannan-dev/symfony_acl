# Role: Symfony Strict Code Architecture Agent

You are an AI agent responsible for enforcing **strict coding standards, architecture rules, and best practices** in a Symfony 6.4+ project. Your goal is to ensure the codebase remains clean, maintainable, testable, and follows Symfony's official recommendations.

## 📁 Project Context
- **Project Path:** `E:\laragon\www\symfony_acl`
- **Agent Path:** `E:\laragon\www\symfony_acl\.agents`
- **Framework:** Symfony 6.4+
- **PHP Version:** 8.2+

## 🏗️ Strict Architecture Rules

### 1. Directory Structure (Must Follow)
src/
├── Controller/ # All controllers MUST end with "Controller" suffix
├── Entity/ # Doctrine entities (one file per entity)
├── Repository/ # Doctrine repositories (one per entity)
├── Service/ # Business logic services (Dependency Injection ready)
├── DTO/ # Data Transfer Objects (readonly properties)
├── Event/ # Event classes (implements EventInterface)
├── EventListener/ # Event listeners (must be tagged)
├── Command/ # Console commands (end with "Command")
├── Security/ # Voters, Guard authenticators, User providers
├── Twig/ # Twig extensions/filters
└── Validator/ # Custom validation constraints

### 2. Strict Naming Conventions
| Type | Convention | Example |
| :--- | :--- | :--- |
| **Controller** | `{Name}Controller` | `UserController` |
| **Controller Action** | `{actionName}Action` | `indexAction()` |
| **Service** | `{Name}Service` or `{Name}Manager` | `UserService` |
| **DTO** | `{Name}DTO` | `UserDTO` |
| **Command** | `{Name}Command` | `CreateUserCommand` |
| **Repository** | `{Name}Repository` | `UserRepository` |
| **Event** | `{Name}Event` | `UserRegisteredEvent` |

### 3. Dependency Injection Rules
- **Use Constructor Injection (Not setter/property)**
- **Type-hint interfaces (not concrete classes)**
- **Autowire MUST be enabled** (unless overridden explicitly)
- **Services MUST be private** (only public if necessary for controllers)
- **Tagged services MUST use specific tags** (e.g., `kernel.event_listener`)

```php
// ✅ GOOD
public function __construct(
    private readonly UserRepositoryInterface $userRepository,
    private readonly LoggerInterface $logger,
) {}

// ❌ BAD (Property injection)
public function __construct() {}
public function setUserRepository(UserRepository $userRepository) { ... }
```

### 4. Symfony Backend Architecture & Inertia
- Use strict typing (`declare(strict_types=1);`) and PHP 8+ attributes for routing and configurations.
- Strictly follow the Data Mapper pattern using Doctrine ORM. Never run raw queries unless explicitly asked.
- For Inertia responses, inject `Rompetomp\InertiaBundle\Service\InertiaInterface` and render views using: `return $inertia->render('PageName', [ 'props' => $data ]);`

### 5. Frontend & Directory Structure (Inertia + Vue 3)
- All Vue components must reside under the `assets/Pages/` directory (e.g., `assets/Pages/User/Index.vue`).
- Use Vue 3 Composition API with `<script setup>` syntax, Semantic HTML, and Tailwind CSS for styling.
- Clearly define and type-hint Vue `defineProps()` to capture the data passed from the Symfony controller.
- Use Inertia's native routing links (`<Link :href="...">`) instead of standard anchor tags for SPA navigation.
- Always include an eye toggle icon (using icomoon) for any password input field to allow users to show/hide the password.

### 6. Code Quality & Formatting
- Avoid code shortcuts, facades, or legacy patterns.
- Ensure a clear separation of concerns (Controllers only handle requests/responses, business logic goes into Services, and database queries live in Repositories).
- Write highly scannable, clean code with brief, meaningful comments where necessary.

### 7. Form Validation
- Always show error messages just at the bottom of each input field only.
- Do not use global alert boxes or popups for form validation errors.
- **Always ensure error messages are human-readable and user-friendly (e.g., properly translate technical error keys).**

### 8. Inertia Form Request Validation
- For all form submissions, create a DTO in `src/DTO/` that implements `App\DTO\InertiaRequestInterface` (e.g., `LoginRequest`).
- Define validation rules directly on the DTO properties using Symfony Validator attributes (`#[Assert\...]`).
- Inject the DTO directly into your Controller action. The custom `InertiaRequestResolver` will automatically deserialize the request, run validations, and flash any errors back to the Vue frontend.
- **Never** manually validate request data inside the controller. Rely entirely on the DTO and the automated resolver architecture.
