Act as a Senior UI/UX Developer specializing in Tailwind CSS, Vue 3, and Symfony architectures. I am building a Role-Based Access Control (RBAC) Management System and need you to generate a UI component utilizing a strict, professional "Corporate Tech" color palette.

Here are the design rules and color map you MUST follow using Tailwind CSS classes:
1. Shell/Layout: Sidebar and primary nav must use a dominant dark authority color (bg-slate-900 with text-slate-100). The main background should be high-contrast light (bg-slate-50).
2. Action States: Primary buttons, active links, and focus items must use Royal Blue (bg-blue-600 hover:bg-blue-700 text-white).
3. Role Hierarchy Badges:
   - ROLE_SUPER_ADMIN: bg-indigo-50 text-indigo-700 border-indigo-200
   - ROLE_MANAGER / ROLE_EDITOR: bg-cyan-50 text-cyan-700 border-cyan-200
   - ROLE_USER: bg-slate-100 text-slate-700 border-slate-200
4. Semantic Status Indicators (Permission Matrix):
   - Access Granted / Allowed: bg-emerald-50 text-emerald-700 border-emerald-200 (include a soft bg-emerald-500 indicator dot)
   - Access Denied / Revoked: bg-rose-50 text-rose-700 border-rose-200 (include a soft bg-rose-500 indicator dot)
   - Inherited / Conditional: bg-amber-50 text-amber-700 border-amber-200 (include a soft bg-amber-500 indicator dot)

Task:
Generate a clean, responsive Dashboard Permission Matrix / User Management Table component. It should display a list of users, their assigned roles using the proper badges, and a grid/table showing features and whether their access is "Allowed", "Denied", or "Inherited". Ensure text contrast complies with WCAG AA (4.5:1 ratio). Write the template using clean syntax suitable for a Vue 3 component or Tailwind-ready layout.
