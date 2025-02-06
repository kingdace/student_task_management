<script>
import { onMounted } from "vue";
import { useGetProject } from "./actions/getProject";
import ProjectTable from "./components/ProjectTable.vue";
import { useRouter } from "vue-router";
import { projectStore } from "./store/projectStore";
import { usePinnedProject } from "./actions/pinnedProject";
import { getUserData } from "../../../helper/getUserData";
import { makeHttpReq } from "../../../helper/makeHttpReq";
import { successMsg, showError } from "../../../helper/toast-notification";

export default {
    components: {
        ProjectTable,
    },
    setup() {
        const user = getUserData();
        const userRole = user?.user?.role || "member";

        // Fetch projects
        const { getProjects, loading, ProjectData } = useGetProject();
        const showListOfProjects = async () => {
            await getProjects();
        };

        // Router and edit functionality
        const router = useRouter();
        const editProject = (project) => {
            projectStore.projectInput = {
                id: project.id,
                title: project.title,
                startDate: project.startDate,
                endDate: project.endDate,
            };

            projectStore.edit = true;
            router.push("/create-projects");
        };

        // Delete functionality
        const deleteProject = async (projectId) => {
            if (confirm("Are you sure you want to delete this project?")) {
                try {
                    await makeHttpReq(`projects/${projectId}`, "DELETE");
                    successMsg("Project deleted successfully!");
                    await getProjects(); // Refresh the project list
                } catch (error) {
                    showError(
                        error.message ||
                            "An error occurred while deleting the project."
                    );
                }
            }
        };

        // Pin project functionality
        const { pinnedProject } = usePinnedProject();
        const pinnedProjectOnDashboard = async (projectId) => {
            await pinnedProject(projectId);
            router.push("/admin");
        };

        // Lifecycle hook
        onMounted(async () => {
            await showListOfProjects();
            projectStore.edit = false;
            projectStore.projectInput = {
                id: null,
                title: "",
                startDate: "",
                endDate: "",
            };
        });

        // Expose variables and functions to the template
        return {
            userRole,
            getProjects,
            loading,
            ProjectData,
            editProject,
            deleteProject,
            pinnedProjectOnDashboard,
        };
    },
};
</script>

<template>
    <div class="page-container">
        <div class="content-wrapper">
            <div class="header-section">
                <div class="title-wrapper">
                    <h2 class="page-title">Projects Overview</h2>
                    <p class="subtitle">Manage and track all your projects</p>
                </div>
                <RouterLink
                    v-if="userRole === 'user'"
                    to="/create-projects"
                    class="create-btn"
                >
                    <i class="bi bi-plus-lg"></i> Create Project
                </RouterLink>
            </div>

            <div class="card dashboard-card">
                <ProjectTable
                    @getProject="getProjects"
                    :loading="loading"
                    @editProject="editProject"
                    :projects="ProjectData"
                    @deleteProject="deleteProject"
                    @pinnedProject="pinnedProjectOnDashboard"
                >
                    <template #pagination>
                        <Bootstrap5Pagination
                            v-if="ProjectData?.data"
                            :data="ProjectData?.data"
                            @pagination-change-page="getProjects"
                            class="pagination-wrapper"
                        />
                    </template>
                </ProjectTable>
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
