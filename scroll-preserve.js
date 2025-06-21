// scroll-preserve.js
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have a saved scroll position
    const savedScroll = sessionStorage.getItem('scrollPosition');
    if (savedScroll) {
        // Restore the scroll position
        window.scrollTo(0, savedScroll);
        // Clear the saved position
        sessionStorage.removeItem('scrollPosition');
    }

    // Save scroll position before page unload/refresh
    window.addEventListener('beforeunload', function() {
        sessionStorage.setItem('scrollPosition', window.scrollY);
    });
});