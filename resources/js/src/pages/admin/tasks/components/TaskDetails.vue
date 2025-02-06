<template>
    <div class="task-details card">
        <div class="card-body">
            <!-- Task Header -->
            <div
                class="task-header d-flex justify-content-between align-items-start mb-4"
            >
                <h4 class="task-title mb-0">{{ task.name }}</h4>
                <span :class="['badge', getPriorityBadgeClass(task.priority)]">
                    <i class="fas fa-flag me-1"></i>
                    {{ priorityText }}
                </span>
            </div>

            <!-- Description -->
            <div class="description mb-4">
                <h5 class="section-title">Description</h5>
                <p class="description-text">
                    {{ task.description || "No description provided" }}
                </p>
            </div>

            <!-- Progress Section -->
            <div class="progress-section mb-4">
                <h5 class="section-title">Progress</h5>
                <div class="progress mb-2" style="height: 10px">
                    <div
                        class="progress-bar"
                        :style="{ width: `${localProgress}%` }"
                        :class="getProgressClass(localProgress)"
                    >
                        {{ localProgress }}%
                    </div>
                </div>
                <div class="progress-buttons">
                    <button
                        v-for="value in [0, 25, 50, 75, 100]"
                        :key="value"
                        class="btn btn-sm"
                        :class="
                            localProgress === value
                                ? 'btn-primary'
                                : 'btn-outline-primary'
                        "
                        @click="updateProgress(value)"
                    >
                        {{ value }}%
                    </button>
                </div>
            </div>

            <!-- Due Date -->
            <div class="due-date mb-4">
                <h5 class="section-title">Due Date</h5>
                <p class="due-date-text">
                    <i class="far fa-calendar-alt me-2"></i>
                    {{ formatDate(task.due_date) }}
                </p>
            </div>

            <!-- Attachments -->
            <div class="attachments mb-4">
                <h5 class="section-title">Attachments</h5>
                <div class="attachment-upload card mb-3">
                    <div class="card-body">
                        <div class="form-check form-check-inline mb-3">
                            <input
                                class="form-check-input"
                                type="radio"
                                v-model="uploadType"
                                value="file"
                                id="fileUpload"
                            />
                            <label class="form-check-label" for="fileUpload"
                                >Upload File</label
                            >
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                v-model="uploadType"
                                value="url"
                                id="urlUpload"
                            />
                            <label class="form-check-label" for="urlUpload"
                                >Add URL</label
                            >
                        </div>

                        <!-- File Upload -->
                        <div v-if="uploadType === 'file'">
                            <input
                                type="file"
                                @change="handleFileSelect"
                                accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain"
                                class="form-control"
                            />
                        </div>

                        <!-- URL Input -->
                        <div v-if="uploadType === 'url'">
                            <input
                                type="url"
                                v-model="urlInput"
                                class="form-control"
                                placeholder="Enter URL"
                            />
                        </div>

                        <textarea
                            v-if="selectedFile || urlInput"
                            v-model="attachmentComment"
                            class="form-control mt-2"
                            placeholder="Add a comment about this file/URL (optional)"
                            rows="2"
                        ></textarea>
                        <button
                            v-if="selectedFile || urlInput"
                            @click="uploadAttachment"
                            class="btn btn-primary mt-2"
                            :disabled="uploading"
                        >
                            {{ uploading ? "Uploading..." : "Upload" }}
                        </button>
                    </div>
                </div>
                <div class="attachment-list">
                    <div
                        v-for="attachment in task.attachments"
                        :key="attachment.id"
                        class="attachment-item card mb-2"
                    >
                        <div class="card-body">
                            <div class="attachment-content">
                                <div class="attachment-info">
                                    <template
                                        v-if="
                                            attachment.file_type.startsWith(
                                                'image/'
                                            )
                                        "
                                    >
                                        <img
                                            :src="attachment.file_url"
                                            class="attachment-image"
                                            alt="Attachment"
                                            @click="viewAttachment(attachment)"
                                        />
                                    </template>
                                    <template v-else>
                                        <i
                                            :class="
                                                getFileIcon(
                                                    attachment.file_type
                                                )
                                            "
                                        ></i>
                                    </template>
                                    <span class="file-name">{{
                                        attachment.file_name
                                    }}</span>
                                </div>
                                <div class="attachment-meta">
                                    <small class="text-muted">
                                        Uploaded by:
                                        {{ getUploaderIdentity(attachment) }} on
                                        {{ formatDate(attachment.created_at) }}
                                    </small>
                                </div>
                            </div>
                            <div
                                v-if="attachment.comment"
                                class="attachment-comment mt-2"
                            >
                                {{ attachment.comment }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { showError, successMsg } from "../../../../helper/toast-notification";

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["refresh"]);
const selectedFile = ref(null);
const attachmentComment = ref("");
const uploading = ref(false);
const localProgress = ref(props.task.progress);
const uploadType = ref("file");
const urlInput = ref("");

watch(
    () => props.task.progress,
    (newProgress) => {
        localProgress.value = newProgress;
    }
);

function getPriorityBadgeClass(priority) {
    return {
        "badge bg-success": priority === 0,
        "badge bg-warning": priority === 1,
        "badge bg-danger": priority === 2,
    };
}

const priorityText = computed(() => {
    const priorities = ["Low", "Medium", "High"];
    return priorities[props.task.priority];
});

function getProgressClass(value) {
    if (value >= 75) return "bg-success";
    if (value >= 50) return "bg-info";
    if (value >= 25) return "bg-warning";
    return "bg-danger";
}

async function updateProgress(value) {
    localProgress.value = value;

    try {
        await makeHttpReq(`tasks/${props.task.id}/progress`, "PUT", {
            progress: value,
            projectId: props.task.projectId,
        });
        successMsg("Progress updated successfully");
        emit("refresh");
    } catch (error) {
        localProgress.value = props.task.progress;
        showError("Failed to update progress");
    }
}

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString();
}

function getUploaderIdentity(attachment) {
    if (attachment.member?.first_name) {
        return `${attachment.member.first_name}`;
    }
    if (attachment.user) {
        return "User";
    }
    return "Unknown";
}

async function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        const allowedTypes = [
            "image/",
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "text/plain",
        ];

        if (!allowedTypes.some((type) => file.type.startsWith(type))) {
            showError(
                "Please select a valid file type (image, PDF, DOC, DOCX, XLS, XLSX, or TXT)"
            );
            event.target.value = "";
            return;
        }
        selectedFile.value = file;
    }
}

async function uploadAttachment() {
    if (!selectedFile.value && !urlInput.value) return;

    uploading.value = true;
    try {
        const formData = new FormData();

        if (uploadType.value === "file" && selectedFile.value) {
            formData.append("file", selectedFile.value);
        } else if (uploadType.value === "url" && urlInput.value) {
            formData.append("url", urlInput.value);
        }

        if (attachmentComment.value) {
            formData.append("comment", attachmentComment.value);
        }

        await makeHttpReq(
            `tasks/${props.task.id}/attachments`,
            "POST",
            formData,
            true
        );

        selectedFile.value = null;
        urlInput.value = "";
        attachmentComment.value = "";
        emit("refresh");
        successMsg("Attachment uploaded successfully");
    } catch (error) {
        showError(error.message || "Failed to upload attachment");
    } finally {
        uploading.value = false;
    }
}

function viewAttachment(attachment) {
    if (attachment.file_type.startsWith("image/")) {
        window.open(attachment.file_url, "_blank");
    }
}

function getFileIcon(fileType) {
    const iconMap = {
        "application/pdf": "fas fa-file-pdf",
        "application/msword": "fas fa-file-word",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
            "fas fa-file-word",
        "application/vnd.ms-excel": "fas fa-file-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
            "fas fa-file-excel",
        "text/plain": "fas fa-file-alt",
    };
    return iconMap[fileType] || "fas fa-file";
}
</script>

<style scoped>
.task-details {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border: none;
}

.task-title {
    font-weight: 600;
    color: #2c3e50;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.description-text {
    color: #6c757d;
    line-height: 1.6;
}

.progress {
    background-color: #e9ecef;
    border-radius: 8px;
}

.progress-buttons {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.progress-buttons .btn {
    min-width: 60px;
    transition: all 0.2s;
}

.progress-buttons .btn:hover {
    transform: translateY(-1px);
}

.due-date-text {
    color: #6c757d;
}

.attachment-upload {
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
    transition: border-color 0.2s;
}

.attachment-upload:hover {
    border-color: #007bff;
}

.attachment-item {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s;
}

.attachment-item:hover {
    transform: translateY(-2px);
}

.attachment-image {
    max-width: 150px;
    max-height: 150px;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.attachment-image:hover {
    transform: scale(1.05);
}

.file-name {
    font-weight: 500;
    margin-left: 0.5rem;
}

.attachment-comment {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 6px;
    color: #6c757d;
}

.attachment-meta {
    margin-top: 0.5rem;
}
</style>
