import { defineStore } from "pinia";

const useProjectStore = defineStore("project", {
    state: () => ({
        projectInput: {
            id: null,
            title: "",
            startDate: "",
            endDate: "",
        },
        edit: false,
    }),
});

export const projectStore = useProjectStore();
