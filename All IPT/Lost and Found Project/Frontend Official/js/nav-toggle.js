document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('nav-toggle');
    const sidebar = document.getElementById('sb');
    const mainContent = document.getElementById('main-cont');
    
    navToggle.addEventListener('click', function() {
        sidebar.classList.toggle('sb-closed');
        mainContent.classList.toggle('expanded');
    });
}); 