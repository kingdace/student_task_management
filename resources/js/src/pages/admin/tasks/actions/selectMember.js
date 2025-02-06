import { ref } from "vue";
import { showError } from "../../../../helper/toast-notification";
import { taskStore } from "../store/kabanStore.js";

export function useSelectMember() {
    const selectedMembers = ref([]);

    function selectMember(member) {
        const exist = selectedMembers.value.filter(
            (memberItem) => memberItem.id === member.id
        );

        if (exist.length === 0) {
            selectedMembers.value.push({
                id: member.id,
                first_name: member.first_name,
                middle_name: member.middle_name,
                last_name: member.last_name,
                email: member.email,
            });
            taskStore.taskInput.memberIds.push(member.id);
        } else {
            showError("You have already selected this member !");
        }
    }

    function unSelectMember(memberId) {
        const filteredMembers = selectedMembers.value.filter(
            (memberItem) => memberItem.id !== memberId
        );
        const filteredMemberIds = taskStore.taskInput.memberIds.filter(
            (item) => item !== memberId
        );
        taskStore.taskInput.memberIds = filteredMemberIds;
        selectedMembers.value = filteredMembers;
    }

    return { selectedMembers, selectMember, unSelectMember };
}
