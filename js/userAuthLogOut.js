document.addEventListener("DOMContentLoaded", function() {
    const logoutBtn = document.getElementById("profileLogoutBtn");
    
    if (logoutBtn) {
        logoutBtn.addEventListener("click", function(e) {
            e.preventDefault(); // Stop the link from firing immediately
            
            if (confirm("Are you sure you want to log out?")) {
                // This grabs whatever is in the 'href' attribute of the <a> tag
                // In your admin page, it will correctly grab "../logout.php"
                window.location.href = logoutBtn.href; 
            }
        });
    }
});
