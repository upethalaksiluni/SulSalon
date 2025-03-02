document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.nav-item');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Deactivate all buttons and hide all content
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Activate current button and content
            this.classList.add('active');
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Profile image change functionality
    const changePhotoBtn = document.getElementById('change-photo-btn');
    const profileImageInput = document.getElementById('profile-image-input');
    const profileImage = document.querySelector('.profile-image img');
    
    changePhotoBtn.addEventListener('click', function() {
        profileImageInput.click();
    });
    
    profileImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                profileImage.src = e.target.result;
            };
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Cancel button functionality
    const cancelBtn = document.getElementById('cancel-btn');
    
    cancelBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
            location.reload();
        }
    });
    
    // Password validation
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (newPasswordInput && confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== newPasswordInput.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        newPasswordInput.addEventListener('input', function() {
            // Check password requirements
            const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/;
            if (!passwordRegex.test(this.value)) {
                this.setCustomValidity('Password must meet all requirements');
            } else {
                this.setCustomValidity('');
                // Also update confirm password validation
                if (confirmPasswordInput.value) {
                    if (confirmPasswordInput.value !== this.value) {
                        confirmPasswordInput.setCustomValidity('Passwords do not match');
                    } else {
                        confirmPasswordInput.setCustomValidity('');
                    }
                }
            }
        });
    }
    
    // Form submission validation
    const profileForm = document.getElementById('profile-form');
    
    profileForm.addEventListener('submit', function(e) {
        // Check if it's a password change submission
        if (e.submitter && e.submitter.name === 'change_password') {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (!currentPassword && !newPassword && !confirmPassword) {
                // If all password fields are empty, prevent submission
                e.preventDefault();
                return;
            }
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                alert('All password fields must be filled out to change password');
                e.preventDefault();
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                e.preventDefault();
                return;
            }
            
            // Check password requirements
            const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/;
            if (!passwordRegex.test(newPassword)) {
                alert('Password must be at least 8 characters and include uppercase letter, number, and special character');
                e.preventDefault();
                return;
            }
        }
        
        // For profile update, validate required fields
        if (e.submitter && e.submitter.name === 'update_profile') {
            const requiredFields = ['fullname', 'gender', 'birthdate', 'phone', 'email'];
            let missingFields = [];
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input || !input.value.trim()) {
                    missingFields.push(field.replace('_', ' '));
                }
            });
            
            if (missingFields.length > 0) {
                alert('Please fill in all required fields: ' + missingFields.join(', '));
                e.preventDefault();
                return;
            }
            
            // Validate email format
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                alert('Please enter a valid email address');
                e.preventDefault();
                return;
            }
        }
    });
    
    // Auto-scroll to message if present
    const message = document.querySelector('.message');
    if (message) {
        message.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Auto-dismiss success messages after 5 seconds
        if (message.classList.contains('success')) {
            setTimeout(function() {
                message.style.opacity = '1';
                let fadeEffect = setInterval(function() {
                    if (message.style.opacity > 0) {
                        message.style.opacity -= 0.1;
                    } else {
                        clearInterval(fadeEffect);
                        message.style.display = 'none';
                    }
                }, 50);
            }, 5000);
        }
    }
    
    // Add form dirty check to prevent accidental navigation
    let formIsDirty = false;
    const formInputs = profileForm.querySelectorAll('input, select, textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            formIsDirty = true;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formIsDirty) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    // Reset form dirty check on successful submission
    if (document.querySelector('.message.success')) {
        formIsDirty = false;
    }
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Remove non-digit characters
            let value = this.value.replace(/\D/g, '');
            
            // Format as (XXX) XXX-XXXX
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
                } else {
                    value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
                }
            }
            
            this.value = value;
        });
    }
});