<template>
    <div class="d-flex">
        <!-- Sidebar / Header -->

        <Header />
        <!-- Main Content -->
        <div class="main-content container mt-5">
            <!-- User Information Header -->
            <div class="user-header text-center mb-4">
                <h2>Edit User Information</h2>
            </div>
            <div class="user-info-box p-4 rounded shadow-sm bg-white">
                <div class="profile-info d-flex align-items-center mb-4">
                    <img
                        src="/public/images/profile.jpg"
                        alt="User Photo"
                        class="profile-pic me-3"
                    />
                    <div class="profile-details">
                        <h3>{{ formData.fullName }}</h3>
                        <p>{{ formData.email }}</p>
                    </div>
                </div>
                <div class="user-details">
                    <form @submit.prevent="saveChanges">
                        <div class="mb-3">
                            <label
                                for="fullName"
                                class="form-label text-primary"
                                >Full Name</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="fullName"
                                v-model="formData.fullName"
                            />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-primary"
                                >Email Account</label
                            >
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                v-model="formData.email"
                            />
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label text-primary"
                                >Mobile Number</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="mobile"
                                v-model="formData.mobile"
                            />
                        </div>
                        <div class="mb-3">
                            <label
                                for="location"
                                class="form-label text-primary"
                                >Location</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="location"
                                v-model="formData.location"
                            />
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import Sidebar from "@/layouts/Sidebar.vue";
// import Header from "@/layouts/Header.vue";
import Header from "../components/Header.vue";
import "@css/view_user_edit.css";
export default {
    components: {
        Header,
    },
    data() {
        return {
            formData: {
                fullName: "Null",
                email: "Null",
                mobile: "Null",
                location: "Null",
            },
        };
    },
    methods: {
        saveChanges() {
            // Handle the save logic here (e.g., API call)
            alert("Changes saved successfully!");
            // Optionally update UI display
            document.querySelector(".user-email").textContent =
                this.formData.email;
            document.getElementById("display-name").textContent =
                this.formData.fullName;
        },
    },
};
</script>
