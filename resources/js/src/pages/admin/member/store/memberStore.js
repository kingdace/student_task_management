import { defineStore } from "pinia";
import { reactive } from "vue";

export const useMemberStore = defineStore("member", {
    state: () => ({
        memberInput: reactive({
            first_name: "",
            middle_name: "",
            last_name: "",
            email: "",
            password: "",
        }),
        edit: false,
    }),
});

export const memberStore = useMemberStore();
