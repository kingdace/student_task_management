<template>
    <header class="header d-flex justify-content-between align-items-center">
        <div class="user-profile dropdown">
            <div
                class="profile-icon"
                @click="toggleDropdown('userDropdown')"
                aria-expanded="false"
            >
                <!-- Use an img tag for the profile picture -->
                <img
                    src="/public/images/profile.jpg"
                    alt="User Profile"
                    class="profile-image"
                />
            </div>
            <div class="username">dgales@ssct.edu.ph</div>

            <ul class="dropdown-menu" :class="{ show: dropdowns.userDropdown }">
                <li>
                    <a
                        class="dropdown-item"
                        href="#"
                        @click="navigateTo('/view_user')"
                        >View User</a
                    >
                </li>
                <li>
                    <a
                        class="dropdown-item"
                        href="#"
                        @click="navigateTo('/view_user_edit')"
                        >Edit User</a
                    >
                </li>
            </ul>
        </div>
    </header>
</template>

<script>
import { ref } from "vue";

export default {
    setup() {
        const dropdowns = ref({
            userDropdown: false,
        });

        function toggleDropdown(dropdown) {
            dropdowns.value[dropdown] = !dropdowns.value[dropdown];
        }

        function navigateTo(path) {
            window.location.href = path;
        }

        return { dropdowns, toggleDropdown, navigateTo };
    },
};
</script>

<style scoped>
.header {
    position: absolute;
    top: 10px;
    left: 1000px;
    right: 10px; /* Full width */
    padding: 10px 20px; /* Padding for aesthetics */
    z-index: 1000; /* Ensure it is above other content */
}

.user-profile {
    display: flex;
    align-items: center;
}

.profile-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background-color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    cursor: pointer; /* Change cursor to pointer */
}

.profile-image {
    width: 100%; /* Fill the container */
    height: 100%;
    border-radius: 50%; /* Make the image round */
    object-fit: cover; /* Ensures the image covers the circle */
}

.dropdown-menu {
    position: absolute;
    top: 100%; /* Position below the icon */
    left: 0;
    margin-top: 5px; /* Add some spacing */
    display: none; /* Hide initially */
    border: 1px solid #ddd;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 10;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    padding: 10px 15px;
    color: #131313;
}

.username {
    font-size: 1.1rem;

    color: #000000;
}
</style>
