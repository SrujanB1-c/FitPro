document.addEventListener('DOMContentLoaded', function () {
    // Inject the logout modal HTML into the DOM
    if (!document.getElementById('logoutConfirmModal')) {
        const modalHtml = `
            <div id="logoutConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
                <div style="background: var(--bg-white, #fff); padding: 2rem; border-radius: 8px; max-width: 400px; width: 90%; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
                    <h3 style="margin-bottom: 1rem; color: var(--text-dark, #333);">Confirm Logout</h3>
                    <p style="margin-bottom: 1.5rem; color: var(--text-body, #666);">Are you sure you want to log out of your account?</p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button id="closeLogoutModal" class="btn" style="background: var(--bg-subtle, #f0f0f0); color: var(--text-main, #333); flex: 1; border: 1px solid var(--border-color, #ddd); cursor: pointer; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 500;">Cancel</button>
                        <button id="confirmLogoutBtn" class="btn" style="flex: 1; border: none; background-color: #dc3545; color: white; cursor: pointer; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 500;">Yes, Logout</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    const logoutModal = document.getElementById('logoutConfirmModal');
    const closeLogoutModal = document.getElementById('closeLogoutModal');
    const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
    let logoutUrl = 'profile.php?action=logout';

    // Intercept all logout links
    const logoutLinks = document.querySelectorAll('a[href*="action=logout"]');
    
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            logoutUrl = this.getAttribute('href') || logoutUrl;
            logoutModal.style.display = 'flex';
        });
    });

    if (closeLogoutModal) {
        closeLogoutModal.addEventListener('click', function() {
            logoutModal.style.display = 'none';
        });
    }

    if (confirmLogoutBtn) {
        confirmLogoutBtn.addEventListener('click', function() {
            window.location.href = logoutUrl;
        });
    }

    // Close modal if user clicks outside of it
    window.addEventListener('click', function(event) {
        if (event.target == logoutModal) {
            logoutModal.style.display = 'none';
        }
    });
});
