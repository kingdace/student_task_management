import { reactive, ref } from "vue";
import { makeHttpReq } from "../../../helper/makeHttpReq";
import { successMsg } from "../../../helper/toast-notification";
import { showErrorResponse } from "../../../helper/util";

// Define loginInput without TypeScript types
export const loginInput = reactive({
    email: "",
    password: "",
});

export function useLoginUser() {
    const loading = ref(false);

    async function login() {
        try {
            loading.value = true;
            const data = await makeHttpReq("login", "POST", loginInput);
            loading.value = false;

            loginInput.email = "";
            loginInput.password = "";

            successMsg(data.message);

            if (data.isLoggedIn) {
                localStorage.setItem("userData", JSON.stringify(data));
                window.location.href = "/app/admin";
            }
        } catch (error) {
            loading.value = false;
            showErrorResponse(error);
        }
    }

    return { login, loading };
}
