/**
 * Dashboard Premium Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    // Add staggered animation delay to cards
    const cards = document.querySelectorAll('.nav-btn');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${(index + 1) * 0.1}s`;
        card.classList.add('animate-fade-in');
    });

    // Smooth hover effects for slots
    const slots = document.querySelectorAll('.slot');
    slots.forEach(slot => {
        slot.addEventListener('mouseenter', () => {
            slot.style.transition = 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });
    });
});

// Update specific elements with transitions
function updateSlotUI(slotId, status) {
    const el = document.getElementById('slot-' + slotId);
    if (!el) return;
    
    // Unify classes: busy (occupied), reserved, free (available)
    let statusClass = 'free';
    if (status === 'occupied' || status === 'busy') statusClass = 'busy';
    else if (status === 'reserved' || status === 'warning') statusClass = 'reserved';
    
    const newClass = 'slot ' + statusClass;
    
    if (el.className !== newClass) {
        el.style.transform = 'scale(1.2)';
        setTimeout(() => {
            el.className = newClass;
            el.style.transform = 'scale(1)';
        }, 200);
    }
}
