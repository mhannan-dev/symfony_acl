<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    tokenLifetime: {
        type: Number,
        default: 3600
    }
})
</script>

<template>
    <Head title="Check your email" />
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Check your email
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 px-6">
                If an account matching your email exists, then an email was just sent that contains a link that you can use to reset your password.
                This link will expire in {{ tokenLifetime / 3600 }} hour(s).
            </p>

            <div class="mt-8">
                <Link href="/login" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Return to login
                </Link>
            </div>
        </div>
    </div>
</template>
