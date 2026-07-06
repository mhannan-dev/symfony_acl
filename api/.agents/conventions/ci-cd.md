# CI/CD Standards

## GitHub Workflow Structure

### Repository Configuration
```
.github/
└── workflows/
    ├── lint.yaml       # Code style, static analysis, validation
    └── tests.yaml      # PHPUnit test suite
```

---

## 1. Lint Workflow (`.github/workflows/lint.yaml`)

Runs on every push and pull request. Validates code quality, style, and static analysis.

```yaml
name: Lint

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  php-cs-fixer:
    name: PHP-CS-Fixer
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          tools: cs2pr
      - run: composer install --no-progress
      - run: vendor/bin/php-cs-fixer check --diff --using-cache=no

  phpstan:
    name: PHPStan
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - run: composer install --no-progress
      - run: vendor/bin/phpstan analyse --no-progress

  lint:
    name: Linters
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - run: composer install --no-progress
      - run: composer validate --no-check-publish --strict
      - run: php bin/console lint:yaml config --parse-tags --no-debug
      - run: php bin/console lint:container --no-debug
      - run: php bin/console doctrine:schema:validate --skip-sync --no-debug
      - run: composer audit
```

---

## 2. Test Workflow (`.github/workflows/tests.yaml`)

Runs PHPUnit tests across PHP versions.

```yaml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  phpunit:
    name: PHPUnit (PHP ${{ matrix.php }})
    strategy:
      matrix:
        php: [8.2, 8.3, 8.4]
      fail-fast: false
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: pdo_sqlite
      - run: composer install --no-progress
      - run: vendor/bin/phpunit
```

---

## 3. Composer Scripts

Add the following scripts to `composer.json` for local use:

```json
{
    "scripts": {
        "lint": [
            "@php-cs-fixer",
            "@phpstan",
            "@lint-yaml",
            "@lint-container"
        ],
        "test": "phpunit",
        "php-cs-fixer": "php-cs-fixer check --diff",
        "php-cs-fixer-fix": "php-cs-fixer fix",
        "phpstan": "phpstan analyse",
        "lint-yaml": "lint:yaml config --parse-tags",
        "lint-container": "lint:container"
    }
}
```

---

## 4. Local Development Setup

### `.editorconfig` (project root)
```ini
root = true

[*]
charset = utf-8
end_of_line = lf
indent_size = 4
indent_style = space
insert_final_newline = true
trim_trailing_whitespace = true

[*.{yml,yaml}]
indent_size = 2

[*.md]
trim_trailing_whitespace = false
```

### `.gitignore` additions
```
# Code quality
/.php-cs-fixer.cache
/phpstan-baseline.neon
/.phpunit
/.phpunit.result.cache
```

---

## 5. Required Composer Dev Dependencies

```json
{
    "require-dev": {
        "dama/doctrine-test-bundle": "^8.0",
        "doctrine/doctrine-fixtures-bundle": "^4.1",
        "friendsofphp/php-cs-fixer": "^3.92",
        "phpstan/phpstan": "^2.0",
        "phpstan/phpstan-doctrine": "^2.0",
        "phpstan/phpstan-symfony": "^2.0",
        "phpunit/phpunit": "^11.5",
        "symfony/browser-kit": "^6.4",
        "symfony/css-selector": "^6.4",
        "symfony/maker-bundle": "^1.36",
        "symfony/stopwatch": "^6.4"
    }
}
```

---

## 6. Acceptance Criteria for Merging

A PR must pass all of the following before merging:
1. ✅ PHP-CS-Fixer — no style violations
2. ✅ PHPStan — level 6, no errors
3. ✅ composer validate — valid composer.json
4. ✅ YAML lint — no syntax errors
5. ✅ Container lint — no service configuration errors
6. ✅ Doctrine schema validation — entities match database
7. ✅ composer audit — no known vulnerabilities
8. ✅ PHPUnit — all tests pass (no deprecations, notices, or warnings)
