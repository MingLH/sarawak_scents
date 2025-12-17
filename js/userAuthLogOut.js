document.addEventListener("DOMContentLoaded", function() {
    
    // --- Manual Logout Button ---
    const logoutBtn = document.getElementById("profileLogoutBtn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", function(e) {
            e.preventDefault();
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        });
    }

});