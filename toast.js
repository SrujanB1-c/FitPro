/**
 * Global Toast Notification System for FitPro
 */
document.addEventListener('DOMContentLoaded', () => {
    // Check if container exists, if not create it
    if (!document.querySelector('.toast-container')) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
});

function showToast(message, type = 'info', duration = 3000) {
    const container = document.querySelector('.toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Choose icon based on type
    let icon = '🔔';
    if (type === 'success') icon = '✅';
    if (type === 'error') icon = '❌';

    toast.innerHTML = `
        <span class="toast-icon">${icon}</span>
        <span class="toast-message">${message}</span>
    `;

    container.appendChild(toast);

    // Force reflow and show
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Hide and remove
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, duration);
}
