<script setup>
import { useVuelidate } from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import { ref, onMounted, onBeforeUnmount } from "vue";
import { taskStore } from "../store/kabanStore.js";
import { useSelectMember } from "../actions/selectMember.js";
import { useCreateTask } from "../actions/CreateTask.js";
import { showError, successMsg } from "../../../../helper/toast-notification";
import { myDebounce } from "../../../../helper/util";
import { useGetProjectDetail } from "../actions/project_details/getProjectDetail.js";
import { makeHttpReq } from "../../../../helper/makeHttpReq";

defineProps(["members"]);

const emit = defineEmits(["closeModal", "refreshKabanBoard", "getMembers"]);

const rules = {
    name: { required },
    due_date: { required },
};
const v$ = useVuelidate(rules, taskStore.taskInput);
const query = ref("");

const { selectMember, unSelectMember, selectedMembers } = useSelectMember();
const { loading, createTask } = useCreateTask();

const modalRef = ref(null);
const previousActiveElement = ref(null);

// Add new refs for document upload
const selectedDocument = ref(null);
const documentComment = ref("");
const linkUrl = ref("");
const uploadType = ref("document"); // 'document' or 'link'
const uploading = ref(false);

async function submitTask() {
    const result = await v$.value.$validate();
    if (!result) return;

    if (taskStore.taskInput.memberIds.length === 0) {
        showError("please select a member!");
        return;
    }

    try {
        // First create the task
        const taskResponse = await createTask();

        if (taskResponse) {
            // Get the task ID from the response
            const taskId = taskResponse.id;

            // If there's a document or link to upload
            if (selectedDocument.value || linkUrl.value) {
                uploading.value = true;
                try {
                    const formData = new FormData();

                    if (selectedDocument.value) {
                        formData.append("file", selectedDocument.value);
                        formData.append("comment", documentComment.value);
                    } else if (linkUrl.value) {
                        formData.append("link_url", linkUrl.value);
                        formData.append("comment", documentComment.value);
                    }

                    await makeHttpReq(
                        `tasks/${taskId}/documents`,
                        "POST",
                        formData,
                        true
                    );

                    successMsg("Task and documents created successfully!");
                } catch (error) {
                    showError(
                        "Failed to upload attachment: " +
                            (error.message || "Unknown error")
                    );
                }
            }

            // Reset form fields
            selectedDocument.value = null;
            documentComment.value = "";
            linkUrl.value = "";
            emit("refreshKabanBoard");
        }
    } catch (error) {
        showError(error.message || "Failed to create task");
    } finally {
        uploading.value = false;
    }
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const allowedTypes = [
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "text/plain",
        ];
        if (!allowedTypes.includes(file.type)) {
            showError(
                "Invalid file type. Please upload PDF, DOC, DOCX, XLS, XLSX, or TXT files."
            );
            event.target.value = "";
            return;
        }
        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            showError("File size must be less than 10MB");
            event.target.value = "";
            return;
        }
        selectedDocument.value = file;
    }
}

const searchMember = myDebounce(async function () {
    emit("getMembers", 1, query.value);
}, 200);

onMounted(() => {
    if (!taskStore.taskInput.name) {
        taskStore.taskInput.name = "";
    }
    previousActiveElement.value = document.activeElement;
    if (modalRef.value) {
        modalRef.value.focus();
    }
});

onBeforeUnmount(() => {
    if (previousActiveElement.value) {
        previousActiveElement.value.focus();
    }
});
</script>

<template>
    <Teleport to="body">
        <div
            ref="modalRef"
            class="modal fade"
            id="taskModal"
            tabindex="-1"
            role="dialog"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="submitTask">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">Add Task</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                @click="emit('closeModal')"
                            ></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <!-- Selected Members -->
                            <div class="selected-members mb-4">
                                <span
                                    v-for="member in selectedMembers"
                                    @click="unSelectMember(member.id)"
                                    :key="member.id"
                                    class="member-tag"
                                >
                                    {{ member.first_name }}
                                    {{ member.middle_name || "" }}
                                    {{ member.last_name }}
                                    <i class="remove-icon">×</i>
                                </span>
                            </div>

                            <!-- Task Input -->
                            <div class="form-group mb-3">
                                <Error
                                    :errors="v$.name.$errors"
                                    label="Task Name"
                                >
                                    <BaseInput
                                        placeholder="Enter task name"
                                        :model-value="
                                            taskStore.taskInput.name || ''
                                        "
                                        @update:model-value="
                                            (value) =>
                                                (taskStore.taskInput.name =
                                                    value)
                                        "
                                        class="form-control"
                                    />
                                </Error>
                            </div>

                            <!-- Description -->
                            <div class="form-group mb-3">
                                <label class="form-label">Description</label>
                                <textarea
                                    class="form-control"
                                    v-model="taskStore.taskInput.description"
                                    rows="3"
                                    placeholder="Enter task description"
                                ></textarea>
                            </div>

                            <!-- Due Date -->
                            <div class="form-group mb-3">
                                <label class="form-label">Due Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    v-model="taskStore.taskInput.due_date"
                                />
                            </div>

                            <!-- Priority -->
                            <div class="form-group mb-3">
                                <label class="form-label">Priority</label>
                                <select
                                    class="form-select"
                                    v-model="taskStore.taskInput.priority"
                                >
                                    <option :value="0">Low</option>
                                    <option :value="1">Medium</option>
                                    <option :value="2">High</option>
                                </select>
                            </div>

                            <!-- Member Search -->
                            <div class="form-group mb-4">
                                <label class="form-label">Search Members</label>
                                <BaseInput
                                    type="text"
                                    v-model="query"
                                    @keydown="searchMember"
                                    placeholder="Search by name..."
                                    class="form-control"
                                />
                            </div>

                            <!-- Members Table -->
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%">ID</th>
                                            <th style="width: 60%">Name</th>
                                            <th
                                                style="width: 25%"
                                                class="text-end"
                                            >
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="member in members?.data
                                                ?.data"
                                            :key="member.id"
                                        >
                                            <td class="align-middle">
                                                #{{ member.id }}
                                            </td>
                                            <td class="align-middle">
                                                {{ member.first_name }}
                                                {{ member.middle_name || "" }}
                                                {{ member.last_name }}
                                            </td>
                                            <td class="text-end">
                                                <button
                                                    @click="
                                                        selectMember(member)
                                                    "
                                                    type="button"
                                                    class="btn btn-light btn-sm"
                                                >
                                                    <i class="plus-icon">+</i>
                                                    Add
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Document Upload Section -->
                            <div class="document-upload-section mt-3">
                                <h6>Add Document or Link</h6>
                                <div class="btn-group mb-2">
                                    <button
                                        type="button"
                                        @click.stop="uploadType = 'document'"
                                        :class="[
                                            'btn',
                                            uploadType === 'document'
                                                ? 'btn-primary'
                                                : 'btn-outline-primary',
                                        ]"
                                    >
                                        Upload Document
                                    </button>
                                    <button
                                        type="button"
                                        @click.stop="uploadType = 'link'"
                                        :class="[
                                            'btn',
                                            uploadType === 'link'
                                                ? 'btn-primary'
                                                : 'btn-outline-primary',
                                        ]"
                                    >
                                        Add Link
                                    </button>
                                </div>

                                <div v-if="uploadType === 'document'">
                                    <input
                                        type="file"
                                        class="form-control mb-2"
                                        @change="handleFileSelect"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                                    />
                                    <div
                                        v-if="selectedDocument"
                                        class="selected-file"
                                    >
                                        Selected: {{ selectedDocument.name }}
                                    </div>
                                </div>

                                <div v-else class="link-input">
                                    <input
                                        type="url"
                                        class="form-control mb-2"
                                        v-model="linkUrl"
                                        placeholder="Enter URL"
                                    />
                                </div>

                                <textarea
                                    v-if="selectedDocument || linkUrl"
                                    class="form-control"
                                    v-model="documentComment"
                                    rows="2"
                                    placeholder="Add a comment about this attachment"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Add Modal Footer -->
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                @click="emit('closeModal')"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                @click="submitTask"
                                :disabled="loading || uploading"
                            >
                                {{
                                    loading || uploading
                                        ? "Adding..."
                                        : "Add Task"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
:deep(.task-input-container) {
    /* Assuming the Error component uses a class 'error-label' for the label */
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.25rem;
}

.modal-dialog {
    max-width: 300px;
    margin: 1.75rem auto;
}

.modal-content {
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #dee2e6;
}

.modal-header h5 {
    font-size: 1rem;
    margin: 0;
}

.modal-body {
    padding: 1rem;
}

.form-group {
    margin-bottom: 0.75rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.25rem;
}

.selected-members {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 0.75rem;
}

.member-tag {
    padding: 4px 12px;
    font-size: 0.875rem;
    border-radius: 5px;
    background-color: #e9ecef;
    border: 1px solid #dee2e6;
    color: #495057;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
}

.member-tag:hover {
    background-color: #e2e6ea;
}

.remove-icon {
    margin-left: 6px;
    font-style: normal;
    color: #dc3545;
    font-weight: bold;
    font-size: 1.1rem;
    line-height: 1;
}

.member-tag:hover .remove-icon {
    color: #c82333;
}

.table-responsive {
    margin-top: 0.5rem;
    max-height: 200px;
    overflow-y: auto;
    font-size: 0.813rem;
}

.table th {
    padding: 0.5rem;
    font-size: 0.813rem;
}

.table td {
    padding: 0.5rem;
}

.btn-light {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.form-control {
    font-size: 0.875rem;
    padding: 0.375rem 0.5rem;
}

.btn-primary {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.task-input-container {
    margin-bottom: 0;
}

.table {
    margin-bottom: 0;
}

.btn-light {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 0.25rem 0.75rem;
}

.btn-light:hover {
    background-color: #e9ecef;
}

.plus-icon {
    font-style: normal;
    margin-right: 4px;
}

.table-responsive {
    margin-top: 1rem;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
}

.document-upload,
.link-input {
    margin-top: 1rem;
}

.selected-file {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.btn-group {
    gap: 0.5rem;
}

.document-upload-section {
    margin-top: 1rem;
    border-top: 1px solid #dee2e6;
    padding-top: 0.5rem;
}

.document-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.document-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    background: #f8f9fa;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}

.document-link {
    color: #007bff;
    text-decoration: none;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.document-link:hover {
    text-decoration: underline;
}
</style>
