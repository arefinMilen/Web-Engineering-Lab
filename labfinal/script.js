// ============================
// script.js - Campus Fest 2025
// ============================

// Auto-refresh functionality for real-time updates
let autoRefreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    setupFormValidation();
    setupAutoRefresh();
    setupConfirmationDialogs();
    setupAlertAutoHide();
    setupButtonLoadingStates();
    setupTableInteractions();
    setupStatusAnimations();
}

// =======================
// FORM VALIDATION
// =======================

function setupFormValidation() {
    const registerForm = document.querySelector('form[method="POST"]');
    
    if (registerForm && window.location.pathname.includes('register.php')) {
        const studentIdInput = document.querySelector('input[name="student_id"]');
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const fullNameInput = document.querySelector('input[name="full_name"]');
        
        // Student ID validation (xxx-xx-xxxx format)
        if (studentIdInput) {
            studentIdInput.addEventListener('input', function() {
                validateStudentId(this);
            });
            
            studentIdInput.addEventListener('blur', function() {
                validateStudentId(this);
            });
        }
        
        // Email validation (@greenfield.edu domain)
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                validateEmail(this);
            });
            
            emailInput.addEventListener('blur', function() {
                validateEmail(this);
            });
        }
        
        // Password strength validation
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                validatePassword(this);
            });
            
            passwordInput.addEventListener('blur', function() {
                validatePassword(this);
            });
        }
        
        // Full name validation
        if (fullNameInput) {
            fullNameInput.addEventListener('input', function() {
                validateFullName(this);
            });
        }
        
        // Form submission validation
        registerForm.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                showAlert('Please fix all validation errors before submitting.', 'error');
            }
        });
    }
}

function validateStudentId(input) {
    const value = input.value.trim();
    const regex = /^\d{3}-\d{2}-\d{4}$/;
    
    if (!value) {
        setFieldState(input, 'neutral');
        return true;
    }
    
    if (!regex.test(value)) {
        setFieldState(input, 'error', 'Format should be: 123-45-6789');
        return false;
    } else {
        setFieldState(input, 'success', 'Valid Student ID format');
        return true;
    }
}

function validateEmail(input) {
    const value = input.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!value) {
        setFieldState(input, 'neutral');
        return true;
    }
    
    if (!emailRegex.test(value)) {
        setFieldState(input, 'error', 'Please enter a valid email address');
        return false;
    } else if (!value.endsWith('@greenfield.edu')) {
        setFieldState(input, 'error', 'Must use @greenfield.edu email domain');
        return false;
    } else {
        setFieldState(input, 'success', 'Valid email address');
        return true;
    }
}

function validatePassword(input) {
    const value = input.value;
    const hasLetter = /[a-zA-Z]/.test(value);
    const hasNumber = /\d/.test(value);
    const hasSpecial = /[@$!%*?&]/.test(value);
    const minLength = value.length >= 9;
    
    if (!value) {
        setFieldState(input, 'neutral');
        return true;
    }
    
    let message = '';
    let isValid = true;
    
    if (!minLength) {
        message = 'Password must be at least 9 characters long';
        isValid = false;
    } else if (!hasLetter) {
        message = 'Password must contain at least one letter';
        isValid = false;
    } else if (!hasNumber) {
        message = 'Password must contain at least one number';
        isValid = false;
    } else if (!hasSpecial) {
        message = 'Password must contain at least one special character (@$!%*?&)';
        isValid = false;
    } else {
        message = 'Strong password!';
    }
    
    setFieldState(input, isValid ? 'success' : 'error', message);
    updatePasswordStrengthIndicator(input, value);
    return isValid;
}

function validateFullName(input) {
    const value = input.value.trim();
    
    if (!value) {
        setFieldState(input, 'neutral');
        return true;
    }
    
    if (value.length < 2) {
        setFieldState(input, 'error', 'Name must be at least 2 characters long');
        return false;
    } else if (!/^[a-zA-Z\s]+$/.test(value)) {
        setFieldState(input, 'error', 'Name can only contain letters and spaces');
        return false;
    } else {
        setFieldState(input, 'success', 'Valid name');
        return true;
    }
}

function validateForm() {
    const studentIdInput = document.querySelector('input[name="student_id"]');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const fullNameInput = document.querySelector('input[name="full_name"]');
    
    let isValid = true;
    
    if (studentIdInput) isValid &= validateStudentId(studentIdInput);
    if (emailInput) isValid &= validateEmail(emailInput);
    if (passwordInput) isValid &= validatePassword(passwordInput);
    if (fullNameInput) isValid &= validateFullName(fullNameInput);
    
    return isValid;
}

function setFieldState(input, state, message = '') {
    // Remove existing validation classes
    input.classList.remove('field-success', 'field-error', 'field-neutral');
    
    // Add appropriate class
    input.classList.add(`field-${state}`);
    
    // Update border color
    switch (state) {
        case 'success':
            input.style.borderColor = '#28a745';
            break;
        case 'error':
            input.style.borderColor = '#dc3545';
            break;
        default:
            input.style.borderColor = '#e1e5e9';
    }
    
    // Show/hide validation message
    if (message) {
        showValidationMessage(input, message, state);
    } else {
        hideValidationMessage(input);
    }
}

function showValidationMessage(element, message, type = 'error') {
    hideValidationMessage(element);
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `validation-message validation-${type}`;
    messageDiv.style.cssText = `
        color: ${type === 'success' ? '#28a745' : '#dc3545'};
        font-size: 0.8rem;
        margin-top: 0.25rem;
        padding: 0.25rem 0;
        transition: all 0.3s ease;
    `;
    messageDiv.textContent = message;
    
    element.parentNode.appendChild(messageDiv);
    
    // Animate in
    setTimeout(() => {
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translateY(0)';
    }, 10);
}

function hideValidationMessage(element) {
    const existingMessage = element.parentNode.querySelector('.validation-message');
    if (existingMessage) {
        existingMessage.style.opacity = '0';
        existingMessage.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            if (existingMessage.parentNode) {
                existingMessage.parentNode.removeChild(existingMessage);
            }
        }, 300);
    }
}

function updatePasswordStrengthIndicator(input, password) {
    let strengthIndicator = input.parentNode.querySelector('.password-strength');
    
    if (!strengthIndicator) {
        strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength';
        strengthIndicator.style.cssText = `
            margin-top: 0.5rem;
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            overflow: hidden;
            transition: all 0.3s ease;
        `;
        
        const strengthBar = document.createElement('div');
        strengthBar.className = 'strength-bar';
        strengthBar.style.cssText = `
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        `;
        
        strengthIndicator.appendChild(strengthBar);
        input.parentNode.appendChild(strengthIndicator);
    }
    
    const strengthBar = strengthIndicator.querySelector('.strength-bar');
    const strength = calculatePasswordStrength(password);
    
    strengthBar.style.width = `${strength.percentage}%`;
    strengthBar.style.background = strength.color;
}

function calculatePasswordStrength(password) {
    let score = 0;
    let percentage = 0;
    let color = '#dc3545';
    
    if (password.length >= 9) score += 25;
    if (/[a-z]/.test(password)) score += 25;
    if (/[A-Z]/.test(password)) score += 25;
    if (/\d/.test(password)) score += 25;
    if (/[@$!%*?&]/.test(password)) score += 25;
    if (password.length >= 12) score += 25;
    
    percentage = Math.min(score, 100);
    
    if (percentage >= 80) color = '#28a745';
    else if (percentage >= 60) color = '#ffc107';
    else if (percentage >= 40) color = '#fd7e14';
    
    return { percentage, color };
}

// =======================
// AUTO REFRESH
// =======================

function setupAutoRefresh() {
    // Only auto-refresh on student and admin pages
    if (window.location.pathname.includes('student.php') || 
        window.location.pathname.includes('admin.php')) {
        
        autoRefreshInterval = setInterval(function() {
            // Only refresh if user is not actively interacting with forms
            if (!isUserInteracting()) {
                refreshPageData();
            }
        }, 30000); // 30 seconds
    }
}

function isUserInteracting() {
    const activeElement = document.activeElement;
    return (activeElement && 
           (activeElement.tagName === 'INPUT' || 
            activeElement.tagName === 'SELECT' || 
            activeElement.tagName === 'TEXTAREA'));
}

function refreshPageData() {
    // Show subtle refresh indicator
    showRefreshIndicator();
    
    // Soft reload to preserve scroll position
    window.location.reload();
}

function showRefreshIndicator() {
    const indicator = document.createElement('div');
    indicator.className = 'refresh-indicator';
    indicator.innerHTML = '<span class="loading"></span> Updating...';
    indicator.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0, 115, 230, 0.9);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(indicator);
    
    setTimeout(() => {
        if (indicator.parentNode) {
            indicator.parentNode.removeChild(indicator);
        }
    }, 2000);
}

// =======================
// CONFIRMATION DIALOGS
// =======================

function setupConfirmationDialogs() {
    document.addEventListener('click', function(e) {
        // Update tickets confirmation
        if (e.target.textContent === 'Update' && e.target.type === 'submit') {
            const eventName = e.target.closest('tr').querySelector('td:first-child strong').textContent;
            if (!confirm(`Update ticket count for "${eventName}"?\n\nThis may promote students from the waiting list if more tickets become available.`)) {
                e.preventDefault();
            }
        }
        
        // Approve waitlist confirmation
        if (e.target.textContent === 'Approve' && e.target.type === 'submit') {
            const row = e.target.closest('tr');
            const eventName = row.querySelector('td:first-child strong').textContent;
            const studentName = row.querySelector('td:nth-child(2)').textContent;
            
            if (!confirm(`Approve "${studentName}" for "${eventName}"?\n\nThis will confirm their booking and reduce available tickets by 1.`)) {
                e.preventDefault();
            }
        }
        
        // Book ticket confirmation
        if (e.target.textContent === 'Book Now' && e.target.type === 'submit') {
            const eventName = e.target.closest('tr').querySelector('td:first-child strong').textContent;
            if (!confirm(`Book ticket for "${eventName}"?\n\nThis action cannot be undone.`)) {
                e.preventDefault();
            }
        }
        
        // Join waitlist confirmation
        if (e.target.textContent === 'Join Waitlist' && e.target.type === 'submit') {
            const eventName = e.target.closest('tr').querySelector('td:first-child strong').textContent;
            if (!confirm(`Join waiting list for "${eventName}"?\n\nYou'll be notified if tickets become available.`)) {
                e.preventDefault();
            }
        }
    });
}

// =======================
// ALERT MANAGEMENT
// =======================

function setupAlertAutoHide() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert, index) {
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.className = 'alert-close';
        closeBtn.style.cssText = `
            float: right;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            margin-left: 1rem;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        `;
        
        closeBtn.addEventListener('click', function() {
            hideAlert(alert);
        });
        
        alert.appendChild(closeBtn);
        
        // Auto-hide after delay (staggered for multiple alerts)
        setTimeout(function() {
            if (alert.parentNode) {
                hideAlert(alert);
            }
        }, 5000 + (index * 1000));
    });
}

function hideAlert(alert) {
    alert.style.transition = 'all 0.3s ease';
    alert.style.opacity = '0';
    alert.style.transform = 'translateX(100%)';
    alert.style.marginBottom = '0';
    alert.style.padding = '0';
    alert.style.height = '0';
    
    setTimeout(function() {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 300);
}

function showAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alert, container.firstChild);
        setupAlertAutoHide();
    }
}

// =======================
// BUTTON LOADING STATES
// =======================

function setupButtonLoadingStates() {
    document.addEventListener('submit', function(e) {
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            setButtonLoading(submitBtn);
            
            // Re-enable after timeout if form doesn't redirect
            setTimeout(function() {
                resetButtonLoading(submitBtn);
            }, 5000);
        }
    });
}

function setButtonLoading(button) {
    button.disabled = true;
    button.dataset.originalText = button.innerHTML;
    button.innerHTML = '<span class="loading"></span> Processing...';
    button.style.opacity = '0.8';
    button.style.cursor = 'not-allowed';
}

function resetButtonLoading(button) {
    if (button && button.dataset.originalText) {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText;
        button.style.opacity = '1';
        button.style.cursor = 'pointer';
        delete button.dataset.originalText;
    }
}

// =======================
// TABLE INTERACTIONS
// =======================

function setupTableInteractions() {
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(0, 115, 230, 0.05)';
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'all 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = 'scale(1)';
        });
    });
    
    // Add sorting functionality to tables
    setupTableSorting();
}

function setupTableSorting() {
    const tables = document.querySelectorAll('table');
    tables.forEach(function(table) {
        const headers = table.querySelectorAll('th');
        headers.forEach(function(header, index) {
            if (header.textContent.trim() && 
                !header.textContent.includes('Action') && 
                !header.textContent.includes('Status')) {
                
                header.style.cursor = 'pointer';
                header.style.userSelect = 'none';
                header.title = 'Click to sort';
                
                header.addEventListener('click', function() {
                    sortTable(table, index);
                });
            }
        });
    });
}

function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const isNumeric = rows.every(row => {
        const cell = row.cells[columnIndex];
        return cell && !isNaN(cell.textContent.trim());
    });
    
    const sortedRows = rows.sort((a, b) => {
        const aText = a.cells[columnIndex].textContent.trim();
        const bText = b.cells[columnIndex].textContent.trim();
        
        if (isNumeric) {
            return parseFloat(aText) - parseFloat(bText);
        } else {
            return aText.localeCompare(bText);
        }
    });
    
    // Clear tbody and append sorted rows
    tbody.innerHTML = '';
    sortedRows.forEach(row => tbody.appendChild(row));
}

// =======================
// STATUS ANIMATIONS
// =======================

function setupStatusAnimations() {
    // Animate status indicators
    const statusIndicators = document.querySelectorAll('.status-available, .status-sold-out');
    statusIndicators.forEach(function(indicator) {
        if (indicator.classList.contains('status-sold-out')) {
            indicator.style.animation = 'pulse-red 2s infinite';
        } else {
            indicator.style.animation = 'pulse-green 2s infinite';
        }
    });
    
    // Add ticket count animations
    animateTicketCounts();
}

function animateTicketCounts() {
    const ticketCounts = document.querySelectorAll('td:contains("Available"), td:contains("tickets")');
    ticketCounts.forEach(function(cell) {
        const count = parseInt(cell.textContent);
        if (!isNaN(count)) {
            if (count === 0) {
                cell.style.color = '#dc3545';
                cell.style.fontWeight = 'bold';
            } else if (count < 5) {
                cell.style.color = '#ffc107';
                cell.style.fontWeight = 'bold';
            } else {
                cell.style.color = '#28a745';
            }
        }
    });
}

// =======================
// UTILITY FUNCTIONS
// =======================

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// =======================
// ACCESSIBILITY FEATURES
// =======================

function setupAccessibility() {
    // Add ARIA labels to form fields
    const formFields = document.querySelectorAll('input, select, textarea');
    formFields.forEach(function(field) {
        if (!field.getAttribute('aria-label') && !field.getAttribute('aria-labelledby')) {
            const label = field.parentNode.querySelector('label');
            if (label) {
                const labelId = 'label-' + Math.random().toString(36).substr(2, 9);
                label.id = labelId;
                field.setAttribute('aria-labelledby', labelId);
            }
        }
    });
    
    // Add keyboard navigation for tables
    const tables = document.querySelectorAll('table');
    tables.forEach(function(table) {
        table.setAttribute('role', 'table');
        table.setAttribute('tabindex', '0');
    });
    
    // Focus management for modals and alerts
    setupFocusManagement();
}

function setupFocusManagement() {
    // Focus first input in forms
    const firstInput = document.querySelector('form input:not([type="hidden"]):first-of-type');
    if (firstInput && !document.activeElement.tagName.match(/input|select|textarea/i)) {
        firstInput.focus();
    }
    
    // Trap focus in modal dialogs if any
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            handleTabKeyPress(e);
        }
        if (e.key === 'Escape') {
            handleEscapeKey(e);
        }
    });
}

function handleTabKeyPress(e) {
    const focusableElements = document.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const focusableArray = Array.from(focusableElements);
    const currentIndex = focusableArray.indexOf(document.activeElement);
    
    if (e.shiftKey) {
        // Shift + Tab (backward)
        if (currentIndex === 0) {
            e.preventDefault();
            focusableArray[focusableArray.length - 1].focus();
        }
    } else {
        // Tab (forward)
        if (currentIndex === focusableArray.length - 1) {
            e.preventDefault();
            focusableArray[0].focus();
        }
    }
}

function handleEscapeKey(e) {
    // Close any open alerts or modals
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        alerts.forEach(alert => hideAlert(alert));
    }
}

// =======================
// PERFORMANCE MONITORING
// =======================

function setupPerformanceMonitoring() {
    // Monitor page load time
    window.addEventListener('load', function() {
        const loadTime = performance.now();
        console.log(`Page loaded in ${Math.round(loadTime)}ms`);
        
        // Log slow loading pages
        if (loadTime > 3000) {
            console.warn('Slow page load detected:', window.location.pathname);
        }
    });
    
    // Monitor large DOM changes
    if ('MutationObserver' in window) {
        const observer = new MutationObserver(function(mutations) {
            const largeChanges = mutations.filter(m => m.addedNodes.length > 10);
            if (largeChanges.length > 0) {
                console.log('Large DOM changes detected:', largeChanges);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
}

// =======================
// INITIALIZATION
// =======================

// Setup accessibility and performance monitoring when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupAccessibility();
    setupPerformanceMonitoring();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});

// Export functions for testing (if in test environment)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateStudentId,
        validateEmail,
        validatePassword,
        calculatePasswordStrength
    };
}