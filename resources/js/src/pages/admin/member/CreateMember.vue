<script setup>
import { useVuelidate } from "@vuelidate/core";
import { required, email } from "@vuelidate/validators";
import { useCreateOrUpdateMember } from "./actions/createMember.js";
import { useMemberStore } from "./store/memberStore.js";
import { storeToRefs } from "pinia";
import { ref, onMounted } from "vue";

const memberStore = useMemberStore();
const { memberInput, edit } = storeToRefs(memberStore);

if (!memberStore.edit) {
    memberStore.$reset();
}

const rules = {
    first_name: { required },
    middle_name: { required },
    last_name: { required },
    email: { required, email },
    password: { required },
};

const v$ = useVuelidate(rules, memberInput);
const { loading, createOrUpdate } = useCreateOrUpdateMember();

async function submitMember() {
    const result = await v$.value.$validate();
    if (!result) return;
    await createOrUpdate();
    v$.value.$reset();
}

onMounted(() => {
    return () => {
        if (!memberStore.edit) {
            memberStore.$reset();
        }
    };
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
                                memberStore.edit
                                    ? "Edit Member"
                                    : "Create Member"
                            }}
                        </h2>
                        <p class="form-subtitle">
                            {{
                                memberStore.edit
                                    ? "Update member information"
                                    : "Add a new member to your team"
                            }}
                        </p>
                    </div>
                    <RouterLink to="/members" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                        Back to Members
                    </RouterLink>
                </header>

                <form @submit.prevent="submitMember" class="form-content">
                    <div class="name-group">
                        <div class="form-group">
                            <label for="firstName" class="form-label"
                                >First Name</label
                            >
                            <Error label="" :errors="v$.first_name.$errors">
                                <BaseInput
                                    :model-value="memberInput.first_name || ''"
                                    @update:model-value="
                                        memberStore.memberInput.first_name =
                                            $event
                                    "
                                    id="firstName"
                                    required
                                    class="form-input"
                                    placeholder="Enter first name"
                                />
                            </Error>
                        </div>

                        <div class="form-group">
                            <label for="middleName" class="form-label"
                                >Middle Name</label
                            >
                            <BaseInput
                                :model-value="memberInput.middle_name || ''"
                                @update:model-value="
                                    memberStore.memberInput.middle_name = $event
                                "
                                id="middleName"
                                class="form-input"
                                placeholder="Enter middle name"
                            />
                        </div>

                        <div class="form-group">
                            <label for="lastName" class="form-label"
                                >Last Name</label
                            >
                            <Error label="" :errors="v$.last_name.$errors">
                                <BaseInput
                                    :model-value="memberInput.last_name || ''"
                                    @update:model-value="
                                        memberStore.memberInput.last_name =
                                            $event
                                    "
                                    id="lastName"
                                    required
                                    class="form-input"
                                    placeholder="Enter last name"
                                />
                            </Error>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label"
                            >Email Address</label
                        >
                        <Error label="" :errors="v$.email?.$errors">
                            <BaseInput
                                :model-value="memberInput.email || ''"
                                @update:model-value="
                                    memberStore.memberInput.email = $event
                                "
                                id="email"
                                type="email"
                                required
                                class="form-input"
                                placeholder="Enter email address"
                            />
                        </Error>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label"
                            >Password</label
                        >
                        <Error label="" :errors="v$.password.$errors">
                            <BaseInput
                                :model-value="memberInput.password || ''"
                                @update:model-value="
                                    memberStore.memberInput.password = $event
                                "
                                id="password"
                                type="password"
                                class="form-input"
                                placeholder="Enter password"
                            />
                        </Error>
                    </div>

                    <div class="form-actions">
                        <BaseBtn
                            :label="
                                memberStore.edit
                                    ? 'Update Member'
                                    : 'Create Member'
                            "
                            :loading="loading"
                            :class="
                                memberStore.edit ? 'btn-warning' : 'btn-primary'
                            "
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

.name-group {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    align-items: flex-start; /* Align items at the top */
}

.form-group {
    margin-bottom: 0; /* Remove extra margins */
    display: flex;
    flex-direction: column; /* Stack label and input correctly */
    justify-content: flex-start; /* Ensure consistency */
}

.form-label {
    margin-bottom: 0.25rem; /* Small space below the label */
    font-size: 0.95rem;
}

.form-input {
    height: 42px; /* Ensure all inputs have the same height */
    box-sizing: border-box; /* Ensure padding doesn't affect height */
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
