document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 1. MOBILE SIDEBAR TOGGLE
    // ==========================================
    const mobileBtn = document.querySelector('.mobile-toggle');
    const overlay = document.querySelector('.overlay');
    const sidebar = document.querySelector('.sidebar');

    if (mobileBtn) {
        mobileBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
    }
    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // ==========================================
    // 2. DASHBOARD FILTER LOGIC
    // ==========================================
    const filterSelect = document.getElementById('filterSelect');
    
    if (filterSelect) {
        // Run immediately to set initial state
        toggleInputs();

        // Run whenever the user changes the dropdown
        filterSelect.addEventListener('change', toggleInputs);
    }

    function toggleInputs() {
        const val = filterSelect.value;
        
        // Hide all inputs first
        const ids = ['dateInput', 'weekInput', 'monthInput', 'rangeInput'];
        ids.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });

        // Show the correct one based on selection
        if (val === 'daily') {
            document.getElementById('dateInput').style.display = 'block';
        } 
        else if (val === 'weekly') {
            document.getElementById('weekInput').style.display = 'block';
        } 
        else if (val === 'monthly') {
            document.getElementById('monthInput').style.display = 'block';
        } 
        else if (val === 'custom') {
            document.getElementById('rangeInput').style.display = 'flex';
        }
    }

    // ==========================================
    // 3. CHART INITIALIZATION
    // ==========================================
    // We check if 'window.dashboardData' exists (passed from PHP)
    if (window.dashboardData) {
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: window.dashboardData.labels,
                    datasets: [{
                        label: 'Revenue (RM)',
                        data: window.dashboardData.sales,
                        backgroundColor: 'rgba(6, 78, 59, 0.7)',
                        borderColor: 'rgba(6, 78, 59, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }
});