import { makeHttpReq } from "../../../../helper/makeHttpReq";
import { successMsg } from "../../../../helper/toast-notification";
import { showErrorResponse } from "../../../../helper/util";
import { taskStore } from "../store/kabanStore.js";

export function useDragTask(fn, slug) {
    async function fromNotStartedToPending(taskId, projectId) {
        const notStartedTask = document.querySelector(
            ".notStartedTask_" + taskId
        );
        const pendingColumn = document.querySelector(".pending_task");
        let isDragged = false;

        pendingColumn.addEventListener("dragstart", function () {
            console.log("dragstart");
        });

        pendingColumn.addEventListener("dragover", function (event) {
            if (!isDragged) {
                event.preventDefault();
                pendingColumn.className += " hovered";
                isDragged = true;
            }
        });

        pendingColumn.addEventListener("dragleave", function () {
            console.log("dragleave");
            isDragged = false;
            pendingColumn.classList.remove("hovered");
        });

        pendingColumn.addEventListener("drop", async function (event) {
            event.preventDefault();
            pendingColumn.append(notStartedTask);
            pendingColumn.classList.remove("hovered");
            isDragged = false;

            taskStore.currentTaskId = taskId;
            if (!pendingColumn.getAttribute("data-listeners-added")) {
                pendingColumn.setAttribute("data-listeners-added", "true");

                setTimeout(async () => {
                    await Promise.all([
                        changeTaskStatus(
                            taskStore.currentTaskId,
                            projectId,
                            "tasks/not_started_to_pending"
                        ),
                        fn(slug),
                    ]);
                    pendingColumn.removeAttribute("data-listeners-added");
                }, 200);
            }
        });
    }

    async function fromPendingToCompleted(taskId, projectId) {
        const pendingTask = document.querySelector(".pendingTask_" + taskId);
        const completedColumn = document.querySelector(".completed_task");
        let isDragged = false;

        completedColumn.addEventListener("dragstart", function () {
            console.log("dragstart");
        });

        completedColumn.addEventListener("dragover", function (event) {
            if (!isDragged) {
                event.preventDefault();
                completedColumn.className += " hovered";
                isDragged = true;
            }
        });

        completedColumn.addEventListener("dragleave", function () {
            console.log("dragleave");
            isDragged = false;
            completedColumn.classList.remove("hovered");
        });

        completedColumn.addEventListener("drop", async function (event) {
            event.preventDefault();
            completedColumn.append(pendingTask);
            completedColumn.classList.remove("hovered");
            isDragged = false;

            taskStore.currentTaskId = taskId;
            if (!completedColumn.getAttribute("data-listeners-added")) {
                completedColumn.setAttribute("data-listeners-added", "true");

                setTimeout(async () => {
                    await Promise.all([
                        changeTaskStatus(
                            taskStore.currentTaskId,
                            projectId,
                            "tasks/pending_to_completed"
                        ),
                        fn(slug),
                    ]);
                    completedColumn.removeAttribute("data-listeners-added");
                }, 200);
            }
        });
    }

    function fromCompletedToPending(taskId, projectId) {
        const completedTask = document.querySelector(
            ".completedTask_" + taskId
        );
        const pendingColumn = document.querySelector(".pending_task");
        let isDragged = false;

        pendingColumn.addEventListener("dragstart", function () {
            console.log("dragstart");
        });

        pendingColumn.addEventListener("dragover", function (event) {
            if (!isDragged) {
                event.preventDefault();
                pendingColumn.className += " hovered";
                isDragged = true;
            }
        });

        pendingColumn.addEventListener("dragleave", function () {
            console.log("dragleave");
            isDragged = false;
            pendingColumn.classList.remove("hovered");
        });

        pendingColumn.addEventListener("drop", async function (event) {
            event.preventDefault();
            pendingColumn.append(completedTask);
            pendingColumn.classList.remove("hovered");
            isDragged = false;

            taskStore.currentTaskId = taskId;
            if (!pendingColumn.getAttribute("data-listeners-added")) {
                pendingColumn.setAttribute("data-listeners-added", "true");

                setTimeout(async () => {
                    await Promise.all([
                        changeTaskStatus(
                            taskStore.currentTaskId,
                            projectId,
                            "tasks/completed_to_pending"
                        ),
                        fn(slug),
                    ]);
                    pendingColumn.removeAttribute("data-listeners-added");
                }, 200);
            }
        });
    }

    return {
        fromNotStartedToPending,
        fromPendingToCompleted,
        fromCompletedToPending,
    };
}

export async function changeTaskStatus(taskId, projectId, endPoint) {
    try {
        const data = await makeHttpReq(endPoint, "POST", {
            taskId: taskId,
            projectId: projectId,
        });

        successMsg(data.message);
    } catch (error) {
        showErrorResponse(error);
    }
}
