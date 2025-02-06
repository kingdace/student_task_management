<script setup>
import { useVuelidate } from "@vuelidate/core";
import { required, email } from "@vuelidate/validators";
import { loginInput, useLoginUser } from "./actions/login.js";
import { useRouter } from "vue-router"; // Import useRouter to navigate

// Import CSS file directly
import "@css/login.css";

// Validation rules for email and password
const rules = {
    email: { required, email },
    password: { required },
};

const v$ = useVuelidate(rules, loginInput);
const { loading, login } = useLoginUser();

const router = useRouter(); // Use router to navigate programmatically

async function submitLogin() {
    const result = await v$.value.$validate();

    if (!result) return;

    await login();
    v$.value.$reset();
}

function goBack() {
    router.push("/home"); // Redirect to the home page
}
</script>

<template>
    <main class="login-container">
        <!-- Back button at the top left corner -->
        <button class="back-button" @click="goBack">← Back</button>

        <section class="login-card shadow-sm">
            <div class="card-body">
                <header class="logo-container">
                    <img
                        src="/public/images/logo.jpg"
                        alt="Logo"
                        class="logo"
                    />
                </header>
                <h2 class="login-title text-center">Login to your account</h2>

                <!-- Form starts here -->
                <form @submit.prevent="submitLogin">
                    <!-- Email input -->
                    <div class="form-group">
                        <Error label="E-mail" :errors="v$.email.$errors">
                            <BaseInput
                                v-model="loginInput.email"
                                class="form-control"
                                placeholder="Enter your email"
                            />
                        </Error>
                    </div>

                    <!-- Password input -->
                    <div class="form-group">
                        <Error label="Password" :errors="v$.password.$errors">
                            <BaseInput
                                v-model="loginInput.password"
                                type="password"
                                class="form-control"
                                placeholder="Enter your password"
                            />
                        </Error>
                    </div>

                    <!-- Submit button -->
                    <div class="form-group">
                        <BaseBtn
                            label="Login now"
                            :loading="loading"
                            class="btn btn-primary btn-block"
                        />
                    </div>

                    <!-- Link to register -->
                    <div class="form-group already-account text-center">
                        <p>
                            Don’t have an account?
                            <RouterLink to="/register" class="login-link"
                                >Sign Up</RouterLink
                            >
                        </p>
                    </div>
                </form>
            </div>
        </section>
    </main>
</template>
