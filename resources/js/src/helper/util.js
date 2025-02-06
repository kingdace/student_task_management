import { showError } from "./toast-notification";

export function showErrorResponse(error) {
    if (Array.isArray(error)) {
        for (const message of error) {
            showError(message);
        }
    } else if (error.message) {
        showError(error.message);
    } else {
        showError("An unexpected error occurred");
    }
}

export function myDebounce(func, delay) {
    let timer;
    return function () {
        clearTimeout(timer);
        timer = setTimeout(() => func(), delay);
    };
}

export function openModal(element) {
    return new Promise((resolve) => {
        var modal = document.getElementById(element);
        if (modal) {
            setTimeout(function () {
                modal.classList.add("fade", "show");
                modal.style.display = "block";
                modal.classList.add("in");
            }, 500);

            document.body.classList.add("modal-open");

            var modalBackdrop = document.createElement("div");
            modalBackdrop.className = "modal-backdrop fade show";
            document.body.appendChild(modalBackdrop);
        }
        resolve(modal);
    });
}

export function closeModal(element) {
    var modal = document.getElementById(element);
    var modalBackdrop = document.querySelector(".modal-backdrop");

    if (modal) {
        modal.classList.remove("in", "show", "fade");
        modal.style.display = "";
        document.body.classList.remove("modal-open");

        if (modalBackdrop) {
            document.body.removeChild(modalBackdrop);
        }
    }
}

export function getChar(str) {
    if (typeof str !== "undefined") {
        const index = 1;
        if (index >= 0 && index < str.length) {
            return str.charAt(index).toLocaleUpperCase();
        } else {
            return "";
        }
    }
}

export function formatDateTime(date) {
    if (!date) return "";
    return new Date(date).toLocaleString();
}

export function getTaskStatusText(status) {
    const statusMap = {
        0: "Not Started",
        1: "Pending",
        2: "Completed",
    };
    return statusMap[status] || "Unknown";
}

export function getPriorityText(priority) {
    const priorities = ["Low", "Medium", "High"];
    return priorities[priority] || "Unknown";
}

export function getPriorityClass(priority) {
    return {
        "text-success": priority === 0,
        "text-warning": priority === 1,
        "text-danger": priority === 2,
    };
}
