<script setup>
import { onMounted, ref, onBeforeUnmount } from "vue";
import { useRoute } from "vue-router";
// import BreadCrumb from "./components/BreadCrumb.vue";
import { useGetProjectDetail } from "./actions/project_details/getProjectDetail";
import ProjectDetail from "./components/ProjectData.vue";
import ProjectProgress from "./components/ProjectProgress.vue";
import NotStartedColumn from "./components/NotStartedColumn.vue";
import PendingColumn from "./components/PendingColumn.vue";
import CompletedColumn from "./components/CompletedColumn.vue";
import AddTaskModal from "./components/AddTaskModal.vue";
import TaskDetailsModal from "./components/TaskDetailsModal.vue";
import { closeModal, openModal } from "../../../helper/util";
import { useGetMembers } from "../member/actions/GetMember.js";
import { taskStore } from "./store/kabanStore.js";
import { useDragTask } from "./actions/dragTask.js";
import { Modal } from "bootstrap/dist/js/bootstrap.esm.js";

const route = useRoute();
const { ProjectData, getProjectDetail } = useGetProjectDetail();
const { getMembers, loading, memberData } = useGetMembers();
const slug = route.query?.query;
const selectedTask = ref(null);
let taskModal = null;

async function openTaskModal() {
    openModal("taskModal").then(() => {
        taskStore.taskInput.projectId = ProjectData.value?.data.id;
        taskStore.taskInput.memberIds = [];
    });
}

function closeTaskModal() {
    closeModal("taskModal");
}

function openTaskDetails(task) {
    if (taskModal) {
        taskModal.dispose();
        taskModal = null;
    }

    selectedTask.value = task;

    const modalEl = document.getElementById("taskDetailsModal");
    if (modalEl) {
        taskModal = new Modal(modalEl);
        taskModal.show();
    }
}

function closeTaskDetails() {
    if (taskModal) {
        taskModal.hide();
        taskModal.dispose();
        taskModal = null;
        selectedTask.value = null;

        // Clean up Bootstrap modal artifacts
        document.body.classList.remove("modal-open");
        const backdrop = document.querySelector(".modal-backdrop");
        if (backdrop) {
            backdrop.remove();
        }

        // Restore scrolling
        document.body.style.overflow = "";
        document.body.style.paddingRight = "";
    }
}

const {
    fromNotStartedToPending,
    fromPendingToCompleted,
    fromCompletedToPending,
} = useDragTask(getProjectDetail, slug);

async function refreshKabanBoard() {
    await getProjectDetail(slug);
}

onMounted(async () => {
    await getProjectDetail(slug);
    getMembers(1, "");

    const modalEl = document.getElementById("taskDetailsModal");
    if (modalEl) {
        modalEl.addEventListener("hidden.bs.modal", closeTaskDetails);
    }
});

onBeforeUnmount(() => {
    if (taskModal) {
        taskModal.dispose();
        taskModal = null;
    }
    selectedTask.value = null;

    // Clean up Bootstrap modal artifacts
    document.body.classList.remove("modal-open");
    const backdrop = document.querySelector(".modal-backdrop");
    if (backdrop) {
        backdrop.remove();
    }

    // Restore scrolling
    document.body.style.overflow = "";
    document.body.style.paddingRight = "";
});
</script>

<template>
    <div class="task-page">
        <AddTaskModal
            @refreshKabanBoard="refreshKabanBoard"
            @getMembers="getMembers"
            :members="memberData"
            @closeModal="closeTaskModal"
        />
        <div class="content-wrapper">
            <ProjectDetail :ProjectData="ProjectData" />
            <ProjectProgress :ProjectData="ProjectData" />

            <div class="kanban-board">
                <div class="kanban-columns">
                    <NotStartedColumn
                        class="not_started_task"
                        @fromNotStartedToPending="fromNotStartedToPending"
                        :projectData="ProjectData"
                        @openTaskModal="openTaskModal"
                        @openTaskDetails="openTaskDetails"
                        @refreshKabanBoard="refreshKabanBoard"
                    />
                    <PendingColumn
                        class="pending_task"
                        @fromPendingToCompleted="fromPendingToCompleted"
                        @fromCompletedToPending="fromCompletedToPending"
                        :projectData="ProjectData"
                        @openTaskDetails="openTaskDetails"
                        @refreshKabanBoard="refreshKabanBoard"
                    />
                    <CompletedColumn
                        class="completed_task"
                        @fromCompletedToPending="fromCompletedToPending"
                        :projectData="ProjectData"
                        @openTaskDetails="openTaskDetails"
                        @refreshKabanBoard="refreshKabanBoard"
                    />
                </div>
            </div>
        </div>

        <Teleport to="body">
            <TaskDetailsModal
                v-if="selectedTask"
                :task="selectedTask"
                @closeModal="closeTaskDetails"
                @refresh="refreshKabanBoard"
            />
        </Teleport>
    </div>
</template>

<style scoped>
.task-page {
    min-height: 100vh;
    padding: 1.5rem;
}

.content-wrapper {
    display: flex;
    flex-direction: column;
}

.kanban-board {
    flex: 1;
}

.kanban-columns {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    padding-bottom: 1rem;
}

.kanban-columns > * {
    flex: 1;
    min-width: 300px;
    max-width: 500px;
}

.hovered {
    border: 2px dashed rgb(157, 156, 156);
    border-radius: 8px;
}

.assignees {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.assignees button {
    border-radius: 50%;
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    border: 1px solid #dee2e6;
    background-color: #fff;
    color: #6c757d;
}

.assignees .member_1 {
    margin-left: -8px;
}

.assignees .member_2 {
    margin-left: -8px;
}
</style>
