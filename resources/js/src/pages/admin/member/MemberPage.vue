<script setup>
import { onMounted } from "vue";
import MemberTable from "./components/MemberTable.vue";
import { useGetMembers } from "./actions/GetMember.js"; // Removed MemberType
import { useRouter } from "vue-router";
import { memberStore } from "./store/memberStore.js"; // Removed MemberInputType
import { getUserData } from "../../../helper/getUserData"; // Helper function to get the logged-in user data

// import Header from "../components/Header.vue";

const user = getUserData();
const userRole = user?.user?.role || "member";

const { getMembers, loading, memberData } = useGetMembers();
async function showListOfMembers() {
    await getMembers();
}

const router = useRouter();
function editMember(member) {
    memberStore.memberInput = member;
    memberStore.edit = true;
    router.push("/create-members");
}

onMounted(async () => {
    await showListOfMembers();
    memberStore.edit = false;
    memberStore.memberInput = {};
});
</script>

<template>
    <div class="page-container">
        <div class="content-wrapper">
            <div class="header-section">
                <div class="title-wrapper">
                    <h2 class="page-title">Members Overview</h2>
                    <p class="subtitle">Manage team members and their access</p>
                </div>
                <RouterLink
                    v-if="userRole === 'user'"
                    to="/create-members"
                    class="create-btn"
                >
                    <i class="bi bi-person-plus"></i> Create Member
                </RouterLink>
            </div>

            <div class="card dashboard-card">
                <MemberTable
                    @editMember="editMember"
                    :loading="loading"
                    @getMember="getMembers"
                    :members="memberData"
                >
                    <template #pagination>
                        <Bootstrap5Pagination
                            v-if="memberData?.data?.last_page > 1"
                            :data="memberData?.data"
                            @pagination-change-page="getMembers"
                            class="pagination-wrapper"
                        />
                    </template>
                </MemberTable>
            </div>
        </div>
    </div>
</template>

<style scoped>
.page-container {
    padding: 2rem;
    background-color: #f8f9fa;
    min-height: 100vh;
}

.content-wrapper {
    max-width: 1400px;
    margin: 0 auto;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.title-wrapper {
    .page-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .subtitle {
        color: #6c757d;
        margin: 0.5rem 0 0 0;
    }
}

.create-btn {
    background-color: #0d6efd;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background-color 0.2s;

    &:hover {
        background-color: #0b5ed7;
    }

    i {
        font-size: 1.1rem;
    }
}

.dashboard-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    border: none;
}

.pagination-wrapper {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}
</style>
