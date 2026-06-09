<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Define dropdown swipe-to-delete function
        window.initializeDropdownSwipeToDelete = function(card) {
            let startX = 0;
            let diffX = 0;
            let isDragging = false;
            const id = card.getAttribute('data-id');
            const wrapper = card.closest('.dropdown-notif-wrapper');
            if (!wrapper) return;
            
            const underlay = document.getElementById(`dd-underlay-${id}`);
            const underlayContent = document.getElementById(`dd-underlay-content-${id}`);
            const threshold = 140;
            
            card.addEventListener('dragstart', (e) => e.preventDefault());
            
            function handleStart(clientX) {
                isDragging = true;
                startX = clientX;
                card.style.transition = 'none';
                card.classList.add('dragging');
                if (underlay) underlay.style.transition = 'none';
                if (underlayContent) underlayContent.style.transition = 'none';
            }
            
            function handleMove(clientX) {
                if (!isDragging) return;
                diffX = clientX - startX;
                diffX = Math.max(0, diffX);
                
                card.style.transform = `translateX(${diffX}px)`;
                const progress = Math.min(diffX / threshold, 1);
                
                if (underlay) {
                    if (diffX >= threshold) {
                        underlay.style.backgroundColor = 'rgba(239, 68, 68, 0.95)';
                        if (underlayContent) {
                            underlayContent.style.opacity = '1';
                            underlayContent.style.transform = 'translateX(0)';
                        }
                    } else {
                        underlay.style.backgroundColor = `rgba(254, 202, 202, ${0.2 + progress * 0.6})`;
                        if (underlayContent) {
                            underlayContent.style.opacity = progress;
                            underlayContent.style.transform = `translateX(${-16 + progress * 16}px)`;
                        }
                    }
                }
            }
            
            function handleEnd() {
                if (!isDragging) return;
                isDragging = false;
                card.classList.remove('dragging');
                
                card.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s';
                if (underlay) underlay.style.transition = 'background-color 0.4s, opacity 0.4s';
                if (underlayContent) underlayContent.style.transition = 'all 0.4s';
                
                if (diffX >= threshold) {
                    card.style.transform = 'translateX(100%)';
                    card.style.opacity = '0';
                    if (underlay) underlay.style.backgroundColor = '#ef4444';
                    
                    setTimeout(() => {
                        wrapper.style.height = `${wrapper.offsetHeight}px`;
                        wrapper.offsetHeight;
                        wrapper.style.height = '0';
                        wrapper.style.marginBottom = '0';
                        wrapper.style.padding = '0';
                        wrapper.style.opacity = '0';
                        
                        // AJAX request to delete
                        fetch(`/notifications/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                _method: 'DELETE'
                            })
                        }).then(response => {
                            if (response.ok) {
                                setTimeout(() => {
                                    wrapper.remove();
                                    
                                    // Update count badge
                                    const badgeEl = document.querySelector('#nav-notif-btn span');
                                    if (badgeEl) {
                                        let currentCount = parseInt(badgeEl.textContent.trim()) || 0;
                                        if (currentCount > 1) {
                                            badgeEl.textContent = currentCount - 1;
                                        } else {
                                            badgeEl.remove();
                                        }
                                    }
                                    
                                    // If empty, show placeholder
                                    const remaining = document.querySelectorAll('.dropdown-notif-wrapper');
                                    if (remaining.length === 0) {
                                        const container = document.querySelector('#notifMenu .max-h-72');
                                        if (container) {
                                            container.innerHTML = `<div class="px-4 py-8 text-center text-xs text-gray-400">Tidak ada notifikasi.</div>`;
                                        }
                                    }
                                }, 400);
                            } else {
                                card.style.transform = 'translateX(0px)';
                                card.style.opacity = '1';
                                if (underlay) underlay.style.backgroundColor = 'rgba(254, 202, 202, 0.2)';
                            }
                        });
                    }, 300);
                } else {
                    card.style.transform = 'translateX(0px)';
                    if (underlay) underlay.style.backgroundColor = 'rgba(254, 202, 202, 0.2)';
                    if (underlayContent) {
                        underlayContent.style.opacity = '0';
                        underlayContent.style.transform = 'translateX(-16px)';
                    }
                }
                diffX = 0;
            }
            
            let didMove = false;
            
            card.addEventListener('mousedown', (e) => {
                if (e.target.closest('.dd-delete-btn') || e.target.closest('form')) return;
                didMove = false;
                handleStart(e.clientX);
            });
            
            window.addEventListener('mousemove', (e) => {
                if (isDragging) {
                    handleMove(e.clientX);
                    if (Math.abs(diffX) > 8) {
                        didMove = true;
                    }
                }
            });
            
            window.addEventListener('mouseup', () => {
                handleEnd();
            });
            
            card.addEventListener('touchstart', (e) => {
                if (e.target.closest('.dd-delete-btn') || e.target.closest('form')) return;
                didMove = false;
                handleStart(e.touches[0].clientX);
            }, { passive: true });
            
            card.addEventListener('touchmove', (e) => {
                if (isDragging) {
                    handleMove(e.touches[0].clientX);
                    if (Math.abs(diffX) > 8) {
                        didMove = true;
                    }
                }
            }, { passive: true });
            
            card.addEventListener('touchend', () => {
                handleEnd();
            });

            // Programmatic click handler
            card.addEventListener('click', (e) => {
                if (e.target.closest('.dd-delete-btn') || e.target.closest('form')) return;
                if (didMove) return;
                
                const form = document.getElementById(`read-form-${id}`);
                if (form) {
                    form.submit();
                }
            });
        };

        // Initialize all existing dropdown items
        document.querySelectorAll('.dropdown-notif-card').forEach(card => {
            window.initializeDropdownSwipeToDelete(card);
        });
    });
</script>
