<template>
    <div class="kanban-column">
        <!-- Column Header -->
        <div class="card card-header" style="background-color: #28a745">
            <div class="d-flex align-items-center justify-content-center">
                <i class="fas fa-check-circle me-2 text-white"></i>
                <span class="header-text text-white">Completed</span>
            </div>
        </div>

        <!-- Scrollable Task List -->
        <div class="task-list">
            <div
                v-for="task in projectData?.data?.tasks"
                :key="task.id"
                v-show="task.status === TaskStatus.COMPLETED"
                draggable="true"
                @drag="
                    emit(
                        'fromCompletedToPending',
                        task.id,
                        projectData?.data?.id
                    )
                "
                :class="['task-card', `completedTask_${task.id}`]"
                @click.stop="openTaskDetails(task)"
            >
                <!-- Task Content -->
                <div class="task-header">
                    <h6 class="task-title">{{ task.name }}</h6>
                    <span
                        :class="['badge', getPriorityBadgeClass(task.priority)]"
                    >
                        <i class="fas fa-flag me-1"></i>
                        {{ getPriorityLabel(task.priority) }}
                    </span>
                </div>

                <p class="task-description">{{ task.description }}</p>

                <div class="progress-wrapper">
                    <div class="progress">
                        <div
                            class="progress-bar"
                            :style="{ width: task.progress + '%' }"
                            :class="getProgressBarClass(task.progress)"
                        ></div>
                    </div>
                    <small class="progress-text"
                        >{{ task.progress }}% Complete</small
                    >
                </div>

                <div class="task-meta">
                    <div class="due-date">
                        <i class="far fa-calendar-alt"></i>
                        <span>{{ formatDate(task.due_date) }}</span>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div
                    class="attachments-section"
                    v-if="task.attachments?.length"
                >
                    <div class="section-label">
                        <i class="fas fa-paperclip"></i>
                        <span>Member Attachments</span>
                    </div>
                    <div class="attachment-list">
                        <div
                            v-for="attachment in task.attachments"
                            :key="attachment.id"
                            class="attachment-item"
                            @click.stop="viewAttachment(attachment)"
                        >
                            <div class="attachment-preview">
                                <template v-if="attachment.file_type === 'url'">
                                    <i class="fas fa-link"></i>
                                    <a
                                        :href="attachment.file_path"
                                        target="_blank"
                                        class="url-link"
                                        @click.stop
                                    >
                                        Open Link
                                    </a>
                                </template>
                                <template
                                    v-else-if="
                                        attachment.file_type.startsWith(
                                            'image/'
                                        )
                                    "
                                >
                                    <img
                                        :src="attachment.file_url"
                                        :alt="attachment.file_name"
                                        @click.stop="viewAttachment(attachment)"
                                    />
                                </template>
                                <template v-else>
                                    <i
                                        :class="
                                            getFileIcon(attachment.file_type)
                                        "
                                        @click.stop="viewAttachment(attachment)"
                                        style="cursor: pointer"
                                    ></i>
                                </template>
                            </div>
                            <div class="attachment-info">
                                <span class="member-name">
                                    {{
                                        attachment.member
                                            ? (attachment.member.first_name ||
                                                  "") +
                                              " " +
                                              (
                                                  attachment.member.last_name ||
                                                  ""
                                              ).trim()
                                            : "Group Leader"
                                    }}
                                </span>
                                <span v-if="attachment.comment" class="comment">
                                    {{ attachment.comment }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="documents-section" v-if="task.documents?.length">
                    <div class="section-label">
                        <i class="fas fa-file-alt"></i>
                        <span>Group Leader Attachments</span>
                    </div>
                    <div class="document-list">
                        <div
                            v-for="doc in task.documents"
                            :key="doc.id"
                            class="document-item"
                        >
                            <div class="document-content">
                                <div class="document-main">
                                    <template v-if="doc.file_type === 'link'">
                                        <i class="fas fa-link"></i>
                                        <a :href="doc.link_url" target="_blank">
                                            {{ doc.link_url }}
                                        </a>
                                    </template>
                                    <template v-else>
                                        <i
                                            :class="
                                                getDocumentIcon(
                                                    doc.document_type
                                                )
                                            "
                                        ></i>
                                        <a :href="doc.file_url" target="_blank">
                                            {{ doc.file_name }}
                                        </a>
                                    </template>
                                </div>
                                <!-- Add this section to display the comment -->
                                <div
                                    v-if="doc.comment"
                                    class="document-comment"
                                >
                                    {{ doc.comment }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <TaskComments :taskId="task.id" v-if="task.showComments" />
                <button
                    class="btn btn-link btn-sm text-muted"
                    @click="task.showComments = !task.showComments"
                >
                    <i class="fas fa-comments"></i>
                    {{ task.showComments ? "Hide Comments" : "Show Comments" }}
                </button>
                <!-- Assignees -->
                <div class="assignees">
                    <div
                        v-for="(member, index) in task.task_members"
                        :key="member.id"
                        :class="'member-avatar member_' + index"
                    >
                        <img
                            :src="
                                member?.members?.profile_photo ||
                                '/images/profile6.jpg'
                            "
                            :alt="member?.members?.first_name"
                            class="member-image"
                            :title="member?.members?.first_name"
                        />
                    </div>
                    <span class="assignee-names">
                        {{
                            task.task_members
                                .map((m) =>
                                    `${m.members?.first_name || ""} ${
                                        m.members?.last_name || ""
                                    }`.trim()
                                )
                                .join(", ")
                        }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { getChar } from "../../../../helper/util";
import { TaskStatus } from "../actions/project_details/getProjectDetail";
import { taskStore } from "../store/kabanStore";
import { ref } from "vue";
import TaskComments from "./TaskComments.vue";

const props = defineProps({
    projectData: Object,
});

const emit = defineEmits([
    "fromCompletedToPending",
    "openTaskDetails",
    "refreshKabanBoard",
]);

function getPriorityBadgeClass(priority) {
    return {
        "badge bg-success": priority === 0,
        "badge bg-warning": priority === 1,
        "badge bg-danger": priority === 2,
    };
}

function getPriorityLabel(priority) {
    return ["Low", "Medium", "High"][priority];
}

function getProgressBarClass(progress) {
    return {
        "bg-danger": progress < 30,
        "bg-warning": progress >= 30 && progress < 70,
        "bg-success": progress >= 70,
    };
}

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

function openTaskDetails(task) {
    taskStore.setSelectedTask(task);
    emit("openTaskDetails", task);
}

function viewAttachment(attachment) {
    if (attachment.file_type === "url") {
        window.open(attachment.file_path, "_blank");
    } else if (attachment.file_type.startsWith("image/")) {
        window.open(attachment.file_url, "_blank");
    } else {
        // For non-image files, trigger download or open in new tab
        window.open(attachment.file_url, "_blank");
    }
}

function getDocumentIcon(docType) {
    const iconMap = {
        pdf: "fas fa-file-pdf",
        doc: "fas fa-file-word",
        docx: "fas fa-file-word",
        xls: "fas fa-file-excel",
        xlsx: "fas fa-file-excel",
        txt: "fas fa-file-alt",
    };
    return iconMap[docType] || "fas fa-file";
}

function getFileIcon(fileType) {
    const iconMap = {
        "image/png": "fas fa-file-image",
        "image/jpeg": "fas fa-file-image",
        "image/gif": "fas fa-file-image",
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
.kanban-column {
    display: flex;
    flex-direction: column;
    height: 100%;
    background-color: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    padding: 1rem;
    margin: 0;
    border: none;
    border-radius: 12px 12px 0 0 !important;
}

.header-text {
    font-size: 1.1rem;
    font-weight: 600;
}

.task-list {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.task-list::-webkit-scrollbar {
    width: 6px;
}

.task-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.task-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.task-card {
    background: white;
    border-radius: 8px;
    padding: 1.25rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.task-title {
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
}

.task-description {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.progress-wrapper {
    margin-bottom: 1rem;
}

.progress {
    height: 8px;
    margin-bottom: 0.5rem;
}

.progress-text {
    display: block;
    text-align: right;
    color: #6c757d;
}

.task-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.due-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.attachments-section,
.documents-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 1rem;
}

.section-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.75rem;
}

.attachment-list,
.document-list {
    display: flex;
    flex-wrap: nowrap;
    gap: 0.75rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.attachment-item {
    flex: 0 0 auto;
    width: 120px;
    background: white;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.attachment-preview {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.attachment-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.attachment-preview i {
    font-size: 1.5rem;
    color: #6c757d;
}

.attachment-info {
    padding: 0.5rem;
    font-size: 0.75rem;
}

.member-name {
    display: block;
    font-weight: 600;
    color: #495057;
}

.comment {
    display: block;
    color: #6c757d;
    margin-top: 0.25rem;
}

.document-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: white;
    border-radius: 6px;
    font-size: 0.875rem;
    white-space: nowrap;
}

.document-item a {
    color: #007bff;
    text-decoration: none;
}

.assignees {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.member-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    overflow: hidden;
    background: #28a745; /* Green for Completed */
}

.member-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.assignee-names {
    font-size: 0.875rem;
    color: #6c757d;
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal;
    overflow: visible;
}
.hovered {
    border: 2px dashed rgb(157, 156, 156);
    border-radius: 8px;
}

.url-link {
    color: #007bff;
    text-decoration: none;
    margin-left: 0.5rem;
    font-size: 0.875rem;
}

.url-link:hover {
    text-decoration: underline;
    color: #0056b3;
}
.document-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.document-main {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.document-comment {
    font-size: 0.75rem;
    color: #6c757d;
    margin-left: 1.5rem;
    font-style: italic;
}
</style>
