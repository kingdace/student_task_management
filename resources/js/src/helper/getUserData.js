// Function to get logged-in user data (User or Member)
export function getUserData() {
    try {
        const userData =
            localStorage.getItem("userData") ||
            localStorage.getItem("memberData");
        if (userData) {
            try {
                const connectedUser = JSON.parse(userData);
                return connectedUser;
            } catch (error) {
                console.error("Error parsing userData:", error.message);
            }
        }
    } catch (error) {
        console.error(error.message);
    }
    return undefined; // Return undefined if no valid data
}
