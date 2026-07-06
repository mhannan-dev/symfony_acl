# ACL / RBAC Architecture

This document describes the Django-style Role-Based Access Control (RBAC) system implemented in this Symfony application.

## Core Tables

**1. `groups`**
- Stores user groups/roles
- Fields: `id`, `name` (unique)
- Acts as roles in the RBAC system

**2. `permissions`**
- Stores granular permissions
- Fields: `id`, `name`, `content_type_id`, `codename`
- Links to content types for model-level permissions
- Unique constraint: `content_type_id` + `codename`

**3. `content_types`**
- Django-style content type framework
- Fields: `id`, `app_label`, `model`
- Enables permissions like "add_user", "change_post", "delete_comment"
- Auto-populated via `app:sync-permissions` console command

## Pivot Tables

**4. `group_permissions`**
- Many-to-many: Groups ↔ Permissions
- Fields: `id`, `group_id`, `permission_id`
- Unique constraint prevents duplicate assignments

**5. `user_groups`**
- Many-to-many: Users ↔ Groups
- Fields: `id`, `user_id`, `group_id`
- Assigns users to roles

**6. `user_permissions`**
- Direct user-to-permission assignments
- Fields: `id`, `user_id`, `permission_id`
- Allows user-specific permission overrides

## Audit Trail

**7. `activity_logs`**
- Tracks user actions
- Fields: `action_time`, `object_id`, `object_repr`, `action_flag`, `change_message`, `user_id`, `content_type_id`
- Records CRUD operations with timestamps

## ACL Mechanism Flow

1. **User** → assigned to **Groups** (via `user_groups`)
2. **Groups** → assigned **Permissions** (via `group_permissions`)
3. **Users** → can have direct **Permissions** (via `user_permissions`)
4. **Permissions** → linked to **Content Types** for model-specific actions
5. All actions logged in **Activity Logs**

This provides hierarchical permission management with both role-based and user-specific permissions, plus comprehensive audit logging.

## Key Console Commands

```bash
# Auto-detect Doctrine entities and create content types + default permissions
php bin/console app:sync-permissions

# Load sample data from fixtures
php bin/console doctrine:fixtures:load
```

## Permission Resolution Order

When checking if a user has a permission:

1. Check `user_permissions` (direct assignment — highest priority)
2. Check `user_groups` → `group_permissions` (role-based — lower priority)
3. Super-admin users (all members of "Admin" group) implicitly have all permissions
