<template>
    <div class="d-flex">
        <!-- Sidebar / Header -->
        <Header />
        <!-- Main Content -->
        <div class="main-content container mt-5">
            <!-- User Information Header -->
            <div class="user-header text-center mb-4">
                <h2>User Information</h2>
            </div>
            <div class="user-info-box p-4 rounded shadow-sm bg-white">
                <div class="profile-info d-flex align-items-center mb-4">
                    <img
                        src="/public/images/profile.jpg"
                        alt="User Photo"
                        class="profile-pic me-3"
                    />
                    <div class="profile-details">
                        <h3>{{ userName }}</h3>
                        <p>{{ userEmail }}</p>
                    </div>
                </div>
                <div class="user-details">
                    <div class="mb-3">
                        <h3 class="text-primary">Full Name</h3>
                        <p>{{ userName }}</p>
                    </div>
                    <div class="mb-3">
                        <h3 class="text-primary">Email Account</h3>
                        <p>{{ userEmail }}</p>
                    </div>
                    <div class="mb-3">
                        <h3 class="text-primary">Mobile Number</h3>
                        <p>{{ userMobile }}</p>
                    </div>
                    <div class="mb-3">
                        <h3 class="text-primary">Location</h3>
                        <p>{{ userLocation }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import Sidebar from "@/layouts/Sidebar.vue";
// import Header from "@/layouts/Header.vue";
import Header from "../components/Header.vue";
import "@css/view_user.css";

export default {
    components: {
        Header,
    },

    data() {
        return {
            userName: "Null",
            userEmail: "Null",
            userMobile: "Null",
            userLocation: "Null",
        };
    },
};
</script>
