<script setup>
import { useForm, Head } from '@inertiajs/vue3'

const form = useForm({
    password: '',
})

const submit = () => {
    form.post(window.location.pathname, {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Reset Password" />
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Reset your password
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Please enter your new password below.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-200 dark:border-gray-700">
                <form class="space-y-6" @submit.prevent="submit">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            New password
                        </label>
                        <div class="mt-1">
                            <input id="password" v-model="form.password" name="password" type="password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                :class="{ 'border-red-500': form.errors.password }" />
                        </div>
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600" id="password-error">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
