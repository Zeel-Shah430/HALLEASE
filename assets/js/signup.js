function validateForm() {
    const password = document.querySelector('input[name="password"]').value;

    if (password.length < 5) {
        alert("Password must be at least 5 characters long.");
        return false;
    }

    return true;
}
