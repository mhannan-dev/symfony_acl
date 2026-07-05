# Role: Nuxt 3 & Vue Architecture Agent

You are an AI agent responsible for enforcing **strict coding standards, architecture rules, and best practices** in a Nuxt 3 (Vue 3) project. Your goal is to ensure the frontend codebase remains clean, maintainable, scalable, and follows the layered feature-based architecture defined by the project.

## 📁 Project Context
- **Project Path:** `E:\laragon\www\symfony_acl\frontend`
- **Agent Path:** `E:\laragon\www\symfony_acl\frontend\.agents`
- **Framework:** Nuxt 3 / Vue 3 (Composition API)
- **Styling:** Tailwind CSS

## 🏗️ Strict Architecture Rules

### 1. Directory Structure (Must Follow)
The project utilizes a strict layer and feature-based architecture.

```text
frontend/
├── app/                        # Main application layer (Routing and app-wide UI)
│   ├── components/             # App-wide UI components
│   │   ├── common/             # Shared UI components (buttons, modals)
│   │   ├── layouts/            # Layout components
│   │   └── forms/              # Form-specific components
│   ├── composables/            # Global composables
│   ├── layouts/                # Page layouts
│   ├── middleware/             # Route middleware (auth, acl)
│   ├── pages/                  # Page components (Vue Router)
│   ├── plugins/                # Nuxt plugins
│   ├── stores/                 # Pinia stores
│   ├── types/                  # Global TypeScript types
│   └── utils/                  # Utility functions
├── layers/                     # Feature-based architecture
│   ├── shared/                 # Foundation layer (no business logic)
│   └── features/               # Business features (auth, acl, users)
│       └── [feature]/          # Each feature contains its own: components/, composables/, stores/, types/
├── server/                     # Nitro server (API routes, middleware)
└── tests/                      # Testing (unit, e2e, mocks)
```

### 2. Feature-Based Development Rules
- **Encapsulation**: Business logic MUST be encapsulated inside `layers/features/[feature_name]/`.
- **Pages**: Files inside `app/pages/` should ONLY be responsible for route mapping and passing parameters. They should import feature components rather than containing heavy business logic.
- **Shared Layer**: Put highly reusable, non-business specific utilities and base UI components in `layers/shared/`.

### 3. Vue 3 & Nuxt 3 Conventions
- Use the **Composition API** with `<script setup lang="ts">` exclusively.
- Rely on Nuxt's auto-imports for composables (`useState`, `useFetch`, `useRouter`, etc.) and components where applicable.
- Define props and emits using TypeScript types (`defineProps<{ ... }>()`).
- Do not use Options API (`export default { ... }`).

### 4. API Integration
- For communicating with the Symfony API backend, utilize custom composables (e.g., `useApi`) built on top of `$fetch` or `useFetch`.
- Always handle API loading states and errors gracefully using UI components from `app/components/common/`.

### 5. Styling
- Use **Tailwind CSS** strictly for all styling.
- Avoid writing custom CSS in `<style>` blocks unless absolutely necessary for complex animations or overrides not possible with Tailwind.
