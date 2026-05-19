// Modern Calendar Module Script
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('mod-events-calendar');
    if (!container) return;
    
    var popup = document.getElementById('event-popup');
    var activeCell = null;
    var hideTimeout = null;
    var enterTimeout = null;
    var isMobile = window.innerWidth <= 768;
    
    function clearAllTimeouts() {
        if (hideTimeout) {
            clearTimeout(hideTimeout);
            hideTimeout = null;
        }
        if (enterTimeout) {
            clearTimeout(enterTimeout);
            enterTimeout = null;
        }
    }
    
    function showPopupDesktop(cell, events, formattedDate) {
        if (!events || events.length === 0) return;
        
        var html = '<div class="popup-date">📅 ' + formattedDate + '</div>';
        
        events.forEach(function(event) {
            html += '<div class="popup-event">';
            html += '<div class="popup-event-title">📌 ' + escapeHtml(event.title) + '</div>';
            if (event.url && event.url.trim() !== '') {
                html += '<div class="popup-event-link">🔗 <a href="' + escapeHtml(event.url) + '" target="_blank">Перейти к событию</a></div>';
            }
            html += '</div>';
        });
        
        popup.innerHTML = html;
        popup.classList.remove('mobile');
        popup.classList.add('active');
        popup.style.pointerEvents = 'auto';
        
        var cellRect = cell.getBoundingClientRect();
        var containerRect = container.getBoundingClientRect();
        var popupHeight = popup.offsetHeight;
        var popupWidth = popup.offsetWidth;
        
        var top = cellRect.top - containerRect.top - popupHeight - 10;
        var left = cellRect.left - containerRect.left + (cellRect.width / 2) - (popupWidth / 2);
        
        if (top < 0) {
            top = cellRect.bottom - containerRect.top + 10;
        }
        if (left < 0) {
            left = 10;
        }
        if (left + popupWidth > containerRect.width) {
            left = containerRect.width - popupWidth - 10;
        }
        
        popup.style.top = top + 'px';
        popup.style.left = left + 'px';
        popup.style.bottom = 'auto';
        popup.style.right = 'auto';
    }
    
    function showPopupMobile(cell, events, formattedDate) {
        if (!events || events.length === 0) return;
        
        var html = '<div class="popup-date">📅 ' + formattedDate + '</div>';
        
        events.forEach(function(event) {
            html += '<div class="popup-event">';
            html += '<div class="popup-event-title">📌 ' + escapeHtml(event.title) + '</div>';
            if (event.url && event.url.trim() !== '') {
                html += '<div class="popup-event-link">🔗 <a href="' + escapeHtml(event.url) + '" target="_blank">Перейти к событию</a></div>';
            }
            html += '</div>';
        });
        
        popup.innerHTML = html;
        popup.classList.add('mobile');
        popup.classList.add('active');
        popup.style.top = 'auto';
        popup.style.bottom = '20px';
        popup.style.left = '50%';
        popup.style.transform = 'translateX(-50%)';
        popup.style.right = 'auto';
    }
    
    function hidePopup() {
        popup.classList.remove('active');
        popup.classList.remove('mobile');
        activeCell = null;
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function updateMobileStatus() {
        isMobile = window.innerWidth <= 768;
    }
    
    window.addEventListener('resize', function() {
        updateMobileStatus();
        hidePopup();
    });
    
    if (popup) {
        popup.addEventListener('mouseenter', function() {
            clearAllTimeouts();
        });
        
        popup.addEventListener('mouseleave', function() {
            hideTimeout = setTimeout(function() {
                hidePopup();
            }, 100);
        });
    }
    
    var cells = container.querySelectorAll('.has-event');
    
    cells.forEach(function(cell) {
        cell.addEventListener('mouseenter', function(e) {
            if (isMobile) return;
            clearAllTimeouts();
            
            enterTimeout = setTimeout(function() {
                var events = cell.getAttribute('data-events');
                var formattedDate = cell.getAttribute('data-formatted-date');
                
                if (events) {
                    try {
                        events = JSON.parse(events);
                        if (events.length > 0) {
                            showPopupDesktop(cell, events, formattedDate);
                            activeCell = cell;
                        }
                    } catch(e) {}
                }
            }, 150);
        });
        
        cell.addEventListener('mouseleave', function(e) {
            if (isMobile) return;
            clearAllTimeouts();
            
            var relatedTarget = e.relatedTarget;
            if (relatedTarget && popup && (popup.contains(relatedTarget) || relatedTarget === popup)) {
                return;
            }
            
            hideTimeout = setTimeout(function() {
                if (popup && popup.matches(':hover')) {
                    return;
                }
                hidePopup();
            }, 200);
        });
        
        cell.addEventListener('click', function(e) {
            e.stopPropagation();
            var events = this.getAttribute('data-events');
            var formattedDate = this.getAttribute('data-formatted-date');
            
            if (events) {
                try {
                    events = JSON.parse(events);
                    
                    if (popup.classList.contains('active') && activeCell === this) {
                        hidePopup();
                    } else {
                        if (isMobile) {
                            showPopupMobile(this, events, formattedDate);
                        } else {
                            showPopupDesktop(this, events, formattedDate);
                        }
                        activeCell = this;
                    }
                } catch(e) {}
            }
        });
    });
    
    document.addEventListener('click', function(e) {
        if (isMobile) {
            if (!e.target.closest('.has-event') && !e.target.closest('.event-popup')) {
                hidePopup();
            }
        }
    });
    
    window.addEventListener('scroll', function() {
        if (popup && popup.classList.contains('active')) {
            hidePopup();
        }
    });
});