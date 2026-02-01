function checkSignup() {
    let roles = document.getElementsByName("role");
    let selectedRole = "";

    for (let i = 0; i < roles.length; i++) {
        if (roles[i].checked) {
            selectedRole = roles[i].value;
        }
    }

    if (selectedRole === "user") {
        window.location.href = "register.php";
    } else if (selectedRole === "admin" || selectedRole === "hallowner") {
        alert("Admins and Hall Owners cannot sign up. Please contact the system administrator.");
    } else {
        alert("Please select a role before signing up.");
    }
}
