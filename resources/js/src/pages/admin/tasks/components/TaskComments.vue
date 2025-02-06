<template>
    <div class="comments-section">
        <div class="comments-header">
            <i class="fas fa-comments me-2"></i>
            <span>Comments</span>
        </div>

        <div class="comments-list" ref="commentsList">
            <div
                v-for="comment in comments"
                :key="comment.id"
                class="comment-item"
            >
                <div class="comment-author">
                    {{ getCommentAuthor(comment) }}
                </div>
                <div class="comment-content">
                    {{ comment.comment }}
                </div>
                <div class="comment-meta">
                    {{ formatDateTime(comment.created_at) }}
                </div>
            </div>
        </div>

        <div class="comment-input">
            <textarea
                v-model="newComment"
                placeholder="Add a comment..."
                class="form-control"
                rows="2"
            ></textarea>
            <button
                class="btn btn-primary btn-sm mt-2"
                @click="submitComment"
                :disabled="!newComment.trim()"
            >
                Add Comment
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { formatDateTime } from "../../../../helper/util";
import { successMsg, showError } from "../../../../helper/toast-notification";

const props = defineProps({
    taskId: {
        type: Number,
        required: true,
    },
});

const comments = ref([]);
const newComment = ref("");
const commentsList = ref(null);

const getComments = async () => {
    try {
        const response = await makeHttpReq(
            `tasks/${props.taskId}/comments`,
            "GET"
        );
        comments.value = response;
        scrollToBottom();
    } catch (error) {
        showError("Failed to load comments");
    }
};

const submitComment = async () => {
    if (!newComment.value.trim()) return;

    try {
        const response = await makeHttpReq(
            `tasks/${props.taskId}/comments`,
            "POST",
            {
                comment: newComment.value,
            }
        );
        comments.value.push(response.comment);
        newComment.value = "";
        successMsg("Comment added successfully");
        scrollToBottom();
    } catch (error) {
        showError("Failed to add comment");
    }
};

const getCommentAuthor = (comment) => {
    if (comment.member) {
        return `${comment.member.first_name} ${comment.member.last_name}`;
    }
    return "Group Leader";
};

const scrollToBottom = () => {
    setTimeout(() => {
        if (commentsList.value) {
            commentsList.value.scrollTop = commentsList.value.scrollHeight;
        }
    }, 100);
};

onMounted(() => {
    getComments();
});
</script>

<style scoped>
.comments-section {
    margin-top: 1rem;
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
}

.comments-header {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #495057;
}

.comments-list {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 1rem;
}

.comment-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
}

.comment-author {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
}

.comment-content {
    color: #212529;
    margin-bottom: 0.5rem;
}

.comment-meta {
    font-size: 0.75rem;
    color: #6c757d;
}

.comment-input textarea {
    resize: none;
    border-radius: 8px;
}
</style>
