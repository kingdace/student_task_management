<template>
    <div class="home-container">
        <header class="header">
            <div class="logo">
                <img :src="logo" alt="Student Tasks Management Logo" />
            </div>
            <button class="sign-in" @click="goToLogin">Sign In</button>
        </header>
        <main class="main-content">
            <h1>STUDENT TASKS MANAGEMENT</h1>
            <p class="subtitle">Easily organize your projects and tasks</p>
            <p class="description">
                The Student Tasks Management System simplifies task tracking and
                collaboration. Assign tasks, track progress, and stay organized
                with your group—effortlessly.
            </p>
            <button class="cta-button" @click="goToRegister">
                Get Started
            </button>
        </main>
        <footer class="footer">
            <p>© 2024 Student Tasks Management. All rights reserved.</p>
        </footer>
    </div>
</template>

<script>
import logo from "/public/images/logo.jpg"; // Adjust this path if necessary
import "@css/home.css";

export default {
    data() {
        return {
            logo, // Use logo in the template
        };
    },
    methods: {
        goToLogin() {
            window.location.href = "/app/login";
        },
        goToRegister() {
            window.location.href = "/app/register";
        },
    },
    mounted() {
        // Add the homepage-body class to body when the component is mounted
        document.body.classList.add("homepage-body");
    },
    beforeDestroy() {
        // Remove the homepage-body class when leaving the homepage
        document.body.classList.remove("homepage-body");
    },
};
</script>
