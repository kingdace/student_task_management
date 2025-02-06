<script setup>
import { ref } from "vue";
import { myDebounce } from "../../../../helper/util";
import { getUserData } from "../../../../helper/getUserData";

const user = getUserData();
const userRole = user?.user?.role || "member";

defineProps(["projects", "loading"]);

const emit = defineEmits([
    "pinnedProject",
    "editProject",
    "viewProjectDetail",
    "getProject",
]);

const query = ref("");
const search = myDebounce(async function () {
    await emit("getProject", 1, query.value);
}, 2000);

function getProgressClass(progress) {
    if (progress < 30) return "low";
    if (progress < 70) return "medium";
    return "high";
}
</script>

<template>
    <div class="table-container">
        <div class="search-section">
            <div class="search-wrapper">
                <i class="bi bi-search search-icon"></i>
                <BaseInput
                    @keydown="search"
                    v-model="query"
                    placeholder="Search projects..."
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
                        <th>Title</th>
                        <th>Progress</th>
                        <th v-if="userRole === 'user'">Actions</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="project in projects?.data?.data"
                        :key="project.id"
                    >
                        <td class="id-column">#{{ project.id }}</td>
                        <td class="title-column">{{ project.title }}</td>
                        <td>
                            <div class="progress-wrapper">
                                <div class="progress">
                                    <div
                                        class="progress-bar"
                                        :style="{
                                            width:
                                                project?.task_progress
                                                    ?.progress + '%',
                                        }"
                                        :class="
                                            getProgressClass(
                                                project?.task_progress?.progress
                                            )
                                        "
                                    ></div>
                                </div>
                                <span class="progress-text">
                                    {{ project?.task_progress?.progress }}%
                                </span>
                            </div>
                        </td>
                        <td v-if="userRole === 'user'" class="actions-column">
                            <button
                                @click="emit('editProject', project)"
                                class="action-btn edit-btn"
                                title="Edit project"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button
                                @click="emit('deleteProject', project.id)"
                                class="action-btn delete-btn"
                                title="Delete project"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        <td>
                            <button
                                @click="emit('pinnedProject', project.id)"
                                class="pin-btn"
                                :class="{ 'is-pinned': project.isPinned }"
                            >
                                <i class="bi bi-pin"></i>
                                {{ project.isPinned ? "Pinned" : "Pin" }}
                            </button>
                        </td>
                        <td>
                            <RouterLink
                                class="view-btn"
                                :to="'/kaban?query=' + project.slug"
                            >
                                View Details
                                <i class="bi bi-arrow-right"></i>
                            </RouterLink>
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

.progress-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 100px;

    .progress {
        flex: 1;
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        width: 100px;
    }

    .progress-bar {
        height: 100%;
        transition: width 0.3s ease;

        &.low {
            background: #ef4444;
        }
        &.medium {
            background: #f59e0b;
        }
        &.high {
            background: #10b981;
        }
    }

    .progress-text {
        min-width: 45px;
        font-size: 0.875rem;
        color: #64748b;
    }
}

.action-btn {
    padding: 0.5rem;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    margin-right: 0.5rem;

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

.pin-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: rgb(224, 222, 239);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;

    &.is-pinned {
        background: #f0fdf4;
        border-color: #86efac;
        color: #16a34a;
    }
    &:hover {
        background: rgb(197, 191, 244);
    }
}

.view-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #d0e055;
    border-radius: 6px;
    color: #0f172a;
    text-decoration: none;
    &.is-pinned {
        background: #f0fdf4;
        border-color: #86efac;
        color: #16a34a;
    }
    &:hover {
        background: #e1ff01;
    }
}
</style>
