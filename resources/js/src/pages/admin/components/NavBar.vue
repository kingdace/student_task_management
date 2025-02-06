<script lang="ts" setup>
import { ref } from "vue";
import { RouterLink } from "vue-router";
import { APP } from "../../../App/APP";
import { getUserData } from "./../../../helper/getUserData"; // Helper function to get the logged-in user data

// Fetch user data and role
const user = getUserData();
const userRole = user?.user?.role || "member";

// Define the complete navigation list
const navigation = ref([
    {
        name: "Dashboard",
        link: "/admin",
        icon: "bi bi-wrench-adjustable",
    },
    {
        name: "Projects",
        link: "/projects",
        icon: "bi bi-activity",
    },
    {
        name: "Members",
        link: "/members",
        icon: "bi bi-people",
    },
]);

// Filter navigation to hide "Members" for users with specific roles
const filteredNavigation = ref(
    navigation.value.filter(
        (item) => !(userRole === "member" && item.name === "Members")
    )
);

const emit = defineEmits<{
    (e: "logout"): Promise<void>;
}>();

defineProps<{
    loggedInUserEmail: string | undefined;
}>();
</script>

<template>
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
        <div class="position-sticky pt--2">
            <div class="text-center mb-4">
                <img
                    :src="`${APP.baseURL}/others/logo.png`"
                    class="sidebar-logo"
                    alt="StudTask_LOGO"
                />
                <div class="user-email">
                    <i class="fas fa-user"></i>{{ loggedInUserEmail }}
                </div>
            </div>
            <div class="menu-header">
                <span>Menu</span>
            </div>

            <ul class="nav flex-column gap-2 px-2">
                <li
                    class="nav-item"
                    v-for="nav in filteredNavigation"
                    :key="nav.name"
                >
                    <RouterLink class="nav-link" :to="nav.link" exact>
                        <i :class="nav.icon"></i>
                        <span>{{ nav.name }}</span>
                    </RouterLink>
                </li>

                <li class="nav-item logout" @click="emit('logout')">
                    <a class="nav-link">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<style scoped>
.sidebar {
    background: linear-gradient(180deg, #97a9c4 0%, #6680a3 100%);
    min-height: 100vh;
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-logo {
    height: 150px;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
}

.sidebar-logo:hover {
    transform: scale(1.05);
}

.user-email {
    color: #ffffff;
    font-size: 0.9rem;
    opacity: 0.9;
    margin-top: 8px;
}

.menu-header {
    padding: 0 1rem;
    margin: 1.5rem 0 1rem;
}

.menu-header span {
    color: #ffffff;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    opacity: 0.9;
}

.nav-item {
    margin-bottom: 5px;
}

.nav-link {
    color: #ffffff;
    padding: 0.8rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.nav-link i {
    font-size: 1.1rem;
}

.router-link-exact-active {
    background: rgba(255, 255, 255, 0.2) !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.logout {
    margin-top: 2rem;
    cursor: pointer;
}

.logout .nav-link {
    color: #ff4757;
    background: rgba(255, 71, 87, 0.1);
}

.logout .nav-link:hover {
    background: rgba(255, 71, 87, 0.2);
}

.user-email i {
    margin-right: 5px;
}

.user-email .fas.fa-user {
    font-size: 1rem;
}

/* .position-sticky {
    padding-top: 100px;
} */
</style>
