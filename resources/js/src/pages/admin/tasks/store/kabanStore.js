import { defineStore } from "pinia";

export const useTaskStore = defineStore("task", {
    state: () => ({
        taskInput: {
            name: "",
            memberIds: [],
            projectId: null,
            description: "",
            due_date: null,
            priority: 0,
        },
        selectedTask: null,
        edit: false,
        currentTaskId: 0,
        taskCommentsVisible: {},
    }),
    actions: {
        setSelectedTask(task) {
            this.selectedTask = task;
        },
        clearSelectedTask() {
            this.selectedTask = null;
        },
        resetTaskInput() {
            this.taskInput = {
                name: "",
                memberIds: [],
                projectId: null,
                description: "",
                due_date: null,
                priority: 0,
            };
        },
        toggleComments(taskId) {
            this.taskCommentsVisible[taskId] = !this.taskCommentsVisible[taskId];
        },
    },
});

export const taskStore = useTaskStore();
