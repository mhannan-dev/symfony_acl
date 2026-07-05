import { createRequire } from 'module';
const require = createRequire(import.meta.url);
try {
  console.log("Resolved:", require.resolve('@tailwindcss/vite'));
} catch (e) {
  console.log("Error:", e.message);
}
