<script setup>
import { ref, onMounted } from "vue";
import { projectStore } from "./store/projectStore";
import { useCreateOrUpdateProject } from "./actions/createProject";

const rules = {
    title: (value) => value?.trim() !== "" || "Project title is required.",
    startDate: (value) => value?.trim() !== "" || "Start Date is required.",
    endDate: (value) => value?.trim() !== "" || "End Date is required.",
};

const errors = ref({});
function validate() {
    const projectInput = projectStore.projectInput;
    errors.value = Object.entries(rules).reduce((acc, [key, rule]) => {
        const isValid = rule(projectInput[key]);
        if (isValid !== true) acc[key] = isValid;
        return acc;
    }, {});
    return Object.keys(errors.value).length === 0;
}

const { loading, createOrUpdate } = useCreateOrUpdateProject();

async function submitProject() {
    if (!validate()) return;
    await createOrUpdate();
    Object.keys(errors.value).forEach((key) => (errors.value[key] = null));
}

onMounted(() => {
    // Reset form if not in edit mode
    if (!projectStore.edit) {
        projectStore.projectInput = {
            id: null,
            title: "",
            startDate: "",
            endDate: "",
        };
    }
});
</script>

<template>
    <div class="page-container">
        <div class="content-wrapper">
            <div class="form-card">
                <header class="form-header">
                    <div class="header-content">
                        <h2 class="form-title">
                            {{
                                projectStore.edit
                                    ? "Edit Project"
                                    : "Create Project"
                            }}
                        </h2>
                        <p class="form-subtitle">
                            {{
                                projectStore.edit
                                    ? "Update project details"
                                    : "Add a new project to your workspace"
                            }}
                        </p>
                    </div>
                    <RouterLink to="/projects" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                        Back to Projects
                    </RouterLink>
                </header>

                <form @submit.prevent="submitProject" class="form-content">
                    <div class="form-group">
                        <label for="projectTitle" class="form-label"
                            >Project Title</label
                        >
                        <BaseInput
                            v-model="projectStore.projectInput.title"
                            id="projectTitle"
                            required
                            class="form-input"
                            placeholder="Enter project title"
                        />
                        <p v-if="errors.title" class="error-message">
                            {{ errors.title }}
                        </p>
                    </div>

                    <div class="date-group">
                        <div class="form-group">
                            <label for="startDate" class="form-label"
                                >Start Date</label
                            >
                            <BaseInput
                                type="date"
                                v-model="projectStore.projectInput.startDate"
                                id="startDate"
                                required
                                class="form-input"
                            />
                            <p v-if="errors.startDate" class="error-message">
                                {{ errors.startDate }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="endDate" class="form-label"
                                >End Date</label
                            >
                            <BaseInput
                                type="date"
                                v-model="projectStore.projectInput.endDate"
                                id="endDate"
                                required
                                class="form-input"
                            />
                            <p v-if="errors.endDate" class="error-message">
                                {{ errors.endDate }}
                            </p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <BaseBtn
                            :class="
                                projectStore.edit
                                    ? 'btn-warning'
                                    : 'btn-primary'
                            "
                            :label="
                                projectStore.edit
                                    ? 'Update Project'
                                    : 'Create Project'
                            "
                            :loading="loading"
                            class="submit-btn"
                        />
                    </div>
                </form>
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
    max-width: 800px;
    margin: 0 auto;
}

.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    padding: 2rem;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.header-content {
    .form-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .form-subtitle {
        color: #64748b;
        margin: 0.5rem 0 0;
        font-size: 0.95rem;
    }
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #64748b;
    text-decoration: none;
    font-size: 0.95rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.2s;

    &:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
}

.form-content {
    max-width: 600px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 1.5rem;
}

.date-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 500;
    color: #334155;
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: all 0.2s;

    &:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
}

.error-message {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.form-actions {
    margin-top: 2rem;
}

.submit-btn {
    width: 100%;
    padding: 0.875rem;
    font-size: 1rem;
    font-weight: 500;
    border-radius: 6px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
    border: none;

    &:hover {
        background: #2563eb;
    }
}

.btn-warning {
    background: #f59e0b;
    color: white;
    border: none;

    &:hover {
        background: #d97706;
    }
}
</style>
