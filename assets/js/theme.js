/**
 * Smart Parking - Theme Management System
 * Handles Dark/Light mode with persistence
 */

(function() {
    // Initial Load - Apply theme immediately to avoid flickering
    const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', savedTheme);
    console.log('Initial theme applied:', savedTheme);
})();

/**
 * Toggle between dark and light themes
 * @param {Event} e - Optional event to prevent default
 */
function toggleTheme(e) {
    if (e) {
        if (typeof e.preventDefault === 'function') e.preventDefault();
        if (typeof e.stopPropagation === 'function') e.stopPropagation();
    }
    
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme') || 'light';
    const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    // Apply changes
    html.setAttribute('data-theme', nextTheme);
    localStorage.setItem('theme', nextTheme);
    
    console.log('Theme changed to:', nextTheme);
    
    // Update UI elements if necessary
    window.dispatchEvent(new CustomEvent('themeChanged', { detail: nextTheme }));
    
    return false;
}

// Export to window
window.toggleTheme = toggleTheme;
