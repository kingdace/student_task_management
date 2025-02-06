<script setup>
import { registerInput, useRegisterUser } from "./actions/register.js";
import { useVuelidate } from "@vuelidate/core";
import { required, email } from "@vuelidate/validators";
import { useRouter } from "vue-router"; // Import useRouter to navigate
import { showError } from "./../../helper/toast-notification"; // Add this import

// Import CSS file directly
import "@css/register.css";

const rules = {
    email: { required, email },
    password: { required },
    confirmPassword: {
        required,
        sameAsPassword: (value) => {
            if (!value) return true; // Let required handle empty value
            return value === registerInput.password || "Passwords must match";
        },
    },
};

const v$ = useVuelidate(rules, registerInput);
const { loading, register } = useRegisterUser();

const router = useRouter(); // Use router to navigate programmatically

async function submitRegister() {
    const result = await v$.value.$validate();
    if (!result) {
        // Check specifically for password mismatch
        if (registerInput.password !== registerInput.confirmPassword) {
            showError("Passwords do not match");
        }
        return;
    }
    await register();
    v$.value.$reset();
}

function goBack() {
    router.push("/home"); // Redirect to the home page
}
</script>

<template>
    <main class="register-container">
        <!-- Back button at the top left corner -->
        <button class="back-button" @click="goBack">← Back</button>

        <section class="register-card shadow-sm">
            <div class="card-body">
                <header class="logo-container">
                    <img
                        src="/public/images/logo.jpg"
                        alt="Logo"
                        class="logo"
                    />
                </header>
                <h2 class="register-title text-center">Create your account</h2>
                <form @submit.prevent="submitRegister">
                    <!-- Email input -->
                    <div class="form-group">
                        <label for="email" class="form-label"></label>
                        <Error label="E-mail" :errors="v$.email.$errors">
                            <BaseInput
                                v-model="registerInput.email"
                                placeholder="Enter your email"
                                class="form-control"
                                type="email"
                            />
                        </Error>
                        <div class="invalid-feedback" v-if="v$.email.$error">
                            Please provide a valid email.
                        </div>
                    </div>

                    <!-- Password input -->
                    <div class="form-group">
                        <label for="password" class="form-label"></label>
                        <Error label="Password" :errors="v$.password.$errors">
                            <BaseInput
                                v-model="registerInput.password"
                                placeholder="Create a password"
                                class="form-control"
                                type="password"
                            />
                        </Error>
                    </div>

                    <!-- Non-Functional Confirm Password input -->
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label"></label>
                        <Error
                            label="Confirm Password"
                            :errors="v$.confirmPassword.$errors"
                        >
                            <BaseInput
                                v-model="registerInput.confirmPassword"
                                placeholder="Confirm your password"
                                class="form-control"
                                type="password"
                            />
                        </Error>
                        <div
                            class="invalid-feedback"
                            v-if="v$.confirmPassword.$error"
                        >
                            {{
                                v$.confirmPassword.$errors[0].$message ||
                                "Passwords must match"
                            }}
                        </div>
                    </div>

                    <!-- Register Button -->
                    <div class="form-group">
                        <BaseBtn
                            label="Register now"
                            :loading="loading"
                            class="btn btn-primary btn-block"
                        />
                    </div>

                    <!-- Already have an account? -->
                    <div class="form-group already-account text-center">
                        <p>
                            Already have an account?
                            <RouterLink to="/login" class="login-link"
                                >Login</RouterLink
                            >
                        </p>
                    </div>
                </form>
            </div>
        </section>
    </main>
</template>
