# Symfony ACL Boilerplate

A comprehensive Role-Based Access Control (RBAC) Management System boilerplate. 
This project follows a modern **decoupled architecture**, consisting of a Symfony REST API backend and a Nuxt (Vue 3) frontend.

## 💼 Business Impact & Value

While this project is built on modern, scalable technology, its primary goal is to solve critical business challenges and accelerate enterprise software development:

- **Reduced Operational Risk & Liability:** Prevents unauthorized access to sensitive business data through a battle-tested, granular permission schema.
- **Enterprise-Ready Compliance:** Built-in activity tracking and strict access controls provide the necessary foundation for meeting compliance requirements (SOC2, GDPR, HIPAA).
- **Scalable Organizational Growth:** Group-based permission management allows administrative overhead to remain low even as employee and user counts scale massively.
- **Faster Time-to-Market:** Serves as a ready-to-deploy foundation for B2B SaaS products and internal tools, saving weeks of expensive engineering time and allowing the team to focus on core product features.
- **Seamless User Experience:** A premium, intuitive dashboard ensures that non-technical managers and administrators can easily manage complex organizational hierarchies.

---

## 📂 Architecture & Directory Structure

The repository is split into two independent applications:

```mermaid
flowchart LR
    Client([Web Client]) <-->|UI Interaction| Frontend[Nuxt 3 Frontend]
    Frontend <-->|REST API / JSON| Backend[Symfony 6.4 API Backend]
    Backend <-->|Doctrine ORM| Database[(Database)]
```

- **[`/api`](./api/)**: A strict Symfony 6.4+ RESTful API utilizing the Data Mapper pattern, request DTOs, and JWT/Session authentication.
- **[`/frontend`](./frontend/)**: A standalone Nuxt (Vue 3) application providing the UI for the dashboard and permission management.

---

## 🧩 Coding Pattern

The backend follows a clean architecture approach utilizing Request/Response DTOs and the Data Mapper pattern:

```mermaid
flowchart TD
    Req([HTTP Request]) --> Ctrl[API Controller]
    Ctrl -->|Maps & Validates| DTO[Request DTO]
    Ctrl --> Svc[Application Service]
    Svc -->|Reads| DTO
    Svc <--> Repo[Doctrine Repository]
    Repo <-->|Maps| Ent[Doctrine Entity]
    Repo <--> DB[(Database)]
    Svc -->|Returns| ResDTO[Response DTO]
    Ctrl -->|Serializes| Res([HTTP JSON Response])
    ResDTO -.-> Res
```

---

## 🚀 Getting Started

To run the full stack locally, you need to start both the API and the Frontend development servers.

### 1. Running the API (Backend)
```bash
cd api
composer install

# Setup your .env database credentials, then run:
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

# Start the Symfony server (or use Laragon/Docker)
symfony server:start
# OR
php -S 127.0.0.1:8000 -t public
```

### 2. Running the Frontend (UI)
```bash
cd frontend
npm install;

# Start the Nuxt development server
npm run dev
```

---

## 🗄️ Entity Relationship Diagram (ERD)

The ACL (Access Control List) system uses a robust Django-style permissions database schema.

```mermaid
erDiagram
    User {
        int id PK
        string name
        string email
        string password
    }
    Group {
        int id PK
        string name
    }
    Permission {
        int id PK
        string name
        string codename
        int contentType_id FK
    }
    ContentType {
        int id PK
        string appLabel
        string model
    }
    UserGroup {
        int id PK
        int user_id FK
        int group_id FK
    }
    UserPermission {
        int id PK
        int user_id FK
        int permission_id FK
    }
    GroupPermission {
        int id PK
        int group_id FK
        int permission_id FK
    }
    ActivityLog {
        int id PK
        datetime actionTime
        string objectId
        string objectRepr
        int actionFlag
        string changeMessage
        int user_id FK
        int contentType_id FK
    }

    User ||--o{ UserGroup : "belongs to"
    Group ||--o{ UserGroup : "contains"
    
    User ||--o{ UserPermission : "has direct"
    Permission ||--o{ UserPermission : "assigned to"
    
    Group ||--o{ GroupPermission : "has"
    Permission ||--o{ GroupPermission : "assigned to"

    ContentType ||--o{ Permission : "groups"
    
    User ||--o{ ActivityLog : "performs"
    ContentType ||--o{ ActivityLog : "tracks"
```


