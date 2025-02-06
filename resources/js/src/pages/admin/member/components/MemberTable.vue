<script setup>
import { ref } from "vue";
import { myDebounce } from "../../../../helper/util";
import { useToast } from "vue-toast-notification";
import { showErrorResponse } from "../../../../helper/util";

import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { getUserData } from "../../../../helper/getUserData"; // Helper function to get the logged-in user data

const user = getUserData();
const userRole = user?.user?.role || "member";

// Define props and emits
defineProps({
    members: {
        type: Object,
        required: true,
    },
    loading: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(["editMember", "getMember"]);

const query = ref("");
const search = myDebounce(async function () {
    await emit("getMember", 1, query.value);
}, 200);

const toast = useToast();

async function confirmDelete(memberId) {
    const confirmed = confirm("Are you sure you want to delete this member?");
    if (!confirmed) return;

    try {
        await makeHttpReq(`members/${memberId}`, "DELETE");
        toast.success("Member deleted successfully.");
        await emit("getMember", 1, query.value); // Refresh the member list
    } catch (error) {
        toast.error("Failed to delete member.");
    }
}
</script>

<template>
    <div class="table-container">
        <div class="search-section">
            <div class="search-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input
                    type="text"
                    @keydown="search"
                    v-model="query"
                    placeholder="Search members..."
                    class="search-input"
                />
            </div>
            <div v-if="loading" class="search-status">
                <div class="spinner"></div>
                <span>Searching...</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th v-if="userRole === 'user'" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="member in members?.data?.data" :key="member.id">
                        <td class="id-column">#{{ member.id }}</td>
                        <td>{{ member.first_name }}</td>
                        <td>{{ member.middle_name }}</td>
                        <td>{{ member.last_name }}</td>
                        <td class="email-column">
                            <span class="email-wrapper">
                                <i class="bi bi-envelope"></i>
                                {{ member.email }}
                            </span>
                        </td>
                        <td v-if="userRole === 'user'" class="action-column">
                            <button
                                @click="emit('editMember', member)"
                                class="action-btn edit-btn"
                                title="Edit member"
                            >
                                <i class="bi bi-pencil"></i>
                                Edit
                            </button>
                        </td>
                        <td v-if="userRole === 'user'" class="action-column">
                            <button
                                @click="confirmDelete(member.id)"
                                class="action-btn delete-btn"
                                title="Delete member"
                            >
                                <i class="bi bi-trash"></i>
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.table-container {
    width: 100%;
}

.search-section {
    margin-bottom: 2rem;
}

.search-wrapper {
    position: relative;
    max-width: 400px;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: border-color 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;

    th {
        background: #f8fafc;
        padding: 1rem;
        font-weight: 600;
        text-align: left;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }

    td {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
    }
}

.email-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #4b5563;

    i {
        color: #6b7280;
    }
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;

    &.edit-btn {
        background: #e0f2fe;
        color: #0284c7;

        &:hover {
            background: #bae6fd;
        }
    }

    &.delete-btn {
        background: #fee2e2;
        color: #ef4444;

        &:hover {
            background: #fecaca;
        }
    }
}

.action-column {
    padding: 0.75rem;
    text-align: center;
}

.search-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    color: #6b7280;

    .spinner {
        width: 1rem;
        height: 1rem;
        border: 2px solid #e2e8f0;
        border-top-color: #0d6efd;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
