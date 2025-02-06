import { reactive, ref } from "vue";
import { makeHttpReq } from "../../../helper/makeHttpReq";
import { showError, successMsg } from "../../../helper/toast-notification";

// No need to define types in JavaScript, so we simply remove them.
export const registerInput = reactive({
    email: "",
    password: "",
    confirmPassword: "", // Initialize confirmPassword
});

export function useRegisterUser() {
    const loading = ref(false);

    async function register() {
        try {
            // Add password confirmation check
            if (registerInput.password !== registerInput.confirmPassword) {
                showError("Passwords do not match");
                return;
            }

            loading.value = true;
            const data = await makeHttpReq(
                "register",
                "POST",
                {
                    email: registerInput.email,
                    password: registerInput.password,
                } // Only send necessary data
            );
            loading.value = false;
            registerInput.email = "";
            registerInput.password = "";
            registerInput.confirmPassword = "";
            successMsg(data.message);
        } catch (error) {
            loading.value = false;
            for (const message of error) {
                showError(message);
            }
        }
    }

    return { register, loading };
}
