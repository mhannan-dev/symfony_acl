# ACL Audit & Comparison: `symfony_acl` vs `dcms`

> **Audit Date:** 2026-07-07
> **symfony_acl:** `E:\laragon\www\symfony_acl\api\` (Symfony 7.4)
> **dcms:** `E:\laragon\www\dcms\` (Laravel 11.x)

---

## 1. High-Level Comparison

| Aspect | symfony_acl | dcms |
|---|---|---|
| **Framework** | Symfony 7.4 | Laravel 11.x |
| **ORM** | Doctrine ORM + DBAL | Laravel Eloquent |
| **Frontend** | Nuxt.js (separate frontend) | Inertia.js + Vue (monolith) |
| **Auth** | `form_login` (session-based) | Laravel auth (session/sanctum) |
| **Multi-tenancy** | None | `spatie/laravel-multitenancy` |
| **Permission naming** | `{action}_{model}` (e.g. `add_user`) | `{module}.{model}.{action}` (e.g. `saas.tenants.manage`) |
| **Authorization mechanism** | Symfony Voter (`PermissionVoter`) | Trait (`HasPermissions`) + Middleware (`EnsureUserHasPermission`) |
| **Permission source** | Direct (`user_permissions`) + Group (`group_permissions`) | Direct (`permission_user`) + Role (`permission_role`) |
| **Caching** | None (raw SQL per request) | `Cache::remember` (3600s per user) |
| **Super Admin bypass** | Not explicit (assigned all perms via Group) | `isSuperAdmin()` bypasses all checks |
| **Soft Deletes** | No | Yes (roles, permissions) |
| **API versioning** | `/api/v1/` prefixes | No versioning |
| **Legacy role column** | No | Yes (`users.role`: `super_admin`, `tenant_admin`, `staff`) |

---

## 2. Database Schema Comparison

### symfony_acl (8 tables)
```
users
content_types
permissions
groups
user_groups          (user <-> group M:N)
group_permissions     (group <-> permission M:N)
user_permissions      (user <-> permission M:N)
activity_logs
reset_password_request
```

### dcms (5 core ACL tables + extras)
```
users                  (includes legacy `role` column, `tenant_id`)
content_types
permissions            (includes `slug`, `group_name`, `codename`)
roles                  (equivalent of symfony_acl `groups`)
permission_role        (role <-> permission M:N)
role_user              (user <-> role M:N)
permission_user        (user <-> permission M:N)
audit_logs
staff_invitations
tenants
plans
...
```

### Key Schema Differences

| Feature | symfony_acl | dcms |
|---|---|---|
| **User-Group/Role pivot** | `user_groups` (composite) | `role_user` (composite) |
| **Group/Role-Permission pivot** | `group_permissions` (composite) | `permission_role` (composite) |
| **Direct User-Permission pivot** | `user_permissions` (composite) | `permission_user` (composite) |
| **Permission unique constraint** | `(content_type_id, codename)` | `content_type_id` + `codename` via unique index |
| **Permission slug field** | No (uses `codename` directly) | Yes (`slug` column, dot-notation) |
| **Permission group_name** | No | Yes (for UI grouping, e.g. "SaaS", "Tenant") |
| **Soft deletes on roles/perms** | No | Yes |
| **Tenant isolation** | No | Yes (`tenant_id` FK + global scope) |

---

## 3. Authorization Flow Comparison

### symfony_acl Flow
```
Request
  └─ security.yaml access_control (ROLE_USER check)
       └─ #[IsGranted('view_user')] attribute on controller
            └─ PermissionVoter::supports()  ── matches pattern?
                 └─ PermissionVoter::voteOnAttribute()
                      └─ PermissionCheckService::hasPermission()
                           ├─ Check user_permissions (direct)
                           └─ Check user_groups → group_permissions (group)
```

### dcms Flow
```
Request
  └─ EnsureUserHasPermission middleware (route-level)
       └─ $user->hasPermissionTo($permission)
            └─ HasPermissions trait
                 ├─ isSuperAdmin()? → return true (bypass)
                 └─ getAllPermissions() (cached)
                      ├─ Check permission_user (direct)
                      └─ Check role_user → permission_role (role-based)
```

---

## 4. Permission Naming Convention

| Aspect | symfony_acl | dcms |
|---|---|---|
| **Format** | `{action}_{model}` | `{module}.{model}.{action}` |
| **Examples** | `add_user`, `view_group`, `delete_permission` | `saas.tenants.manage`, `tenant.dashboard.view`, `diagnostic.billing.create` |
| **Actions** | `add`, `view`, `change`, `delete` | Free-form (context-specific) |
| **Scope/module** | Implicit via `content_type` | Explicit first segment in slug |
| **CRUD consistency** | Fixed 4 actions per content type | Flexible, defined in `config/acl.php` |

**Impact:** dcms namespacing prevents collision between modules (e.g. `diagnostic.dashboard.view` vs `tenant.dashboard.view`). symfony_acl relies on `content_types` table alone to scope permissions but has no module namespace in codenames.

---

## 5. Permission Sync / Auto-Discovery

Both projects have CLI commands that auto-discover entities and generate permissions:

| Feature | symfony_acl (`app:sync-permissions`) | dcms (`permissions:sync`) |
|---|---|---|
| **Discovery** | Doctrine entity metadata scan | File scan of `app/Models/` and `app/Modules/` |
| **Permission generation** | Fixed: `add_`, `view_`, `change_`, `delete_` | Configurable via `config/acl.php` (default: empty) |
| **ContentType creation** | Auto-creates from entities | Auto-creates from definitions |
| **Role/Group assignments** | Via fixtures only | Via `config/acl.assignments` |
| **`--dry-run`** | No | Yes |
| **Exclusion list** | Hardcoded (skips ActivityLog + MappedSuperclass) | Configurable in `config/acl.discovery.exclude` |

---

## 6. Multi-Tenancy

| Aspect | symfony_acl | dcms |
|---|---|---|
| **Multi-tenant** | No | Yes (via `spatie/laravel-multitenancy`) |
| **Tenant isolation** | N/A | Global scope filters by `tenant_id` on tenant-scoped models |
| **Super Admin** | N/A | Bypasses tenant scoping, sees all data |
| **Tenant Admin** | N/A | Manages users/roles within their tenant |
| **Staff/Users** | N/A | Scoped to tenant; roles assigned per-tenant |

---

## 7. Group/Role vs Direct Permission Assignment

Both support the same pattern: users get permissions via groups/roles AND/OR directly.

| Aspect | symfony_acl | dcms |
|---|---|---|
| **Group/Role → Permissions** | `groups` → `group_permissions` | `roles` → `permission_role` |
| **User → Group/Role** | `user_groups` | `role_user` |
| **User → Direct Permissions** | `user_permissions` | `permission_user` |
| **Combined check** | `PermissionCheckService` (union via SQL) | `HasPermissions::getAllPermissions()` (merge collections) |

---

## 8. Code Architecture & Patterns

| Aspect | symfony_acl | dcms |
|---|---|---|
| **Authorization class** | `PermissionVoter` (extending Symfony `Voter`) | `HasPermissions` trait on `User` model |
| **Middleware** | None (uses Symfony `access_control` + `#[IsGranted]`) | `EnsureUserHasPermission` middleware (route-level) |
| **Service layer** | `PermissionCheckService` (raw SQL via DBAL) | `PermissionSyncService`, `AclAssignmentService`, `AccessControlService` |
| **Controller protection** | PHP 8 `#[IsGranted]` attributes + `denyAccessUnlessGranted()` | Route middleware + `AuthorizesAclAccess` concern trait |
| **Action/UseCase pattern** | No | Yes (dedicated Action classes per use case) |
| **Audit logging** | `ActivityLog` entity (logged via service) | `AuditLog` model + `AuditLogMiddleware` |
| **UI management** | REST API consumed by Nuxt.js | Inertia.js + Vue (built-in admin panel) |
| **Validation** | Symfony forms/validation | Laravel Form Requests |

---

## 9. Strengths & Weaknesses

### symfony_acl Strengths
- **Clean Symfony integration** — uses standard Voter pattern, `#[IsGranted]` attributes, `security.yaml`
- **Simple, focused schema** — minimal tables, clear relationships
- **Underscore codename convention** — intuitive (`add_user`, `view_group`)
- **Restful API** — well-structured `/api/v1/` endpoints, frontend-agnostic
- **Auto sync command** — scans Doctrine entities and creates permissions effortlessly

### symfony_acl Weaknesses
- **No caching** — hits database on every `is_granted()` call (potential performance issue at scale)
- **No multi-tenancy** — single-tenant only
- **No super-admin bypass** — relies on group membership (all permissions must be explicitly assigned)
- **No soft deletes** — destructive deletions only
- **No permission grouping for UI** — no `group_name` field
- **Hardcoded CRUD actions** — only `add/view/change/delete` (not flexible for custom actions)

### dcms Strengths
- **Multi-tenant** — production-ready SaaS isolation via `spatie/laravel-multitenancy`
- **Caching** — permissions cached 1 hour per user (global cache for super-admin)
- **Super Admin bypass** — `isSuperAdmin()` skips all checks automatically
- **Flexible permission naming** — dot-notation with module scoping prevents collisions
- **Config-driven** — `config/acl.php` centralizes all entity/role/permission definitions
- **Soft deletes** — safe deactivation of roles/permissions
- **Rich admin UI** — built-in Access Control panel with role/permission/user management
- **Action/UseCase pattern** — clean separation of business logic
- **Audit logging** — middleware-based logging of non-GET requests
- **`--dry-run` flag** — preview sync changes before applying

### dcms Weaknesses
- **Framework coupling** — tightly coupled to Laravel (trait, Facades, Eloquent)
- **Legacy `role` column** — dual system (column + M:N roles) adds complexity
- **Middleware-only enforcement** — no Symfony-style `#[IsGranted]` or declarative attribute protection
- **General-codename permissions** — less structured CRUD convention (can lead to inconsistent naming)
- **Cache invalidation complexity** — must manually call `forgetCachedPermissions()` on changes
- **More complex schema** — more tables, more pivots

---

## 10. Summary

Both projects implement a **custom, database-driven RBAC system** modeled after Django's permission architecture with:

- ContentTypes (logical model groupings)
- CRUD/action-level permissions
- Group/Role-based permission inheritance
- Direct user permission overrides
- Activity/audit logging

**symfony_acl** is a cleaner, simpler implementation within the Symfony ecosystem — ideal for single-tenant apps that need straightforward, framework-native ACL.

**dcms** is a more enterprise-ready, production-hardened system with multi-tenancy, caching, soft-deletes, a built-in admin UI, and flexible permission definitions — better suited for SaaS platforms.

### Recommendation
If building a **new project**, use dcms's approach as a reference but adapt these improvements into symfony_acl:
1. Add permission caching (`Cache::remember`)
2. Add a `group_name` field to permissions for UI grouping
3. Implement dot-notation slug namespaceing (or keep underscore if simpler)
4. Add a super-admin bypass check
5. Consider multi-tenancy architecture if needed
6. Add a `--dry-run` flag to the sync command
7. Use soft-deletes on roles and permissions
