<style>
    .input-password-toggle {
        position: absolute;
        right: 0;
        top: 0;
        cursor: pointer;
        padding: 10px 15px;
        z-index: 9;
    }

    input[data-bb-password]:valid, input[data-bb-password].is-valid {
        background-image: unset;
    }

    body[dir="rtl"] .input-password-toggle {
        right: unset;
        left: 0;
    }
</style>

<script>
    window.addEventListener('load', function () {
        document.querySelectorAll('[data-bb-toggle-password]').forEach(button => {
            button.addEventListener('click', () => {
                const passwordField = button.parentElement.querySelector('[data-bb-password]');

                if (passwordField.getAttribute('type') === 'password') {
                    passwordField.setAttribute('type', 'text');
                    button.innerHTML = `<?php echo BaseHelper::renderIcon('ti ti-eye-off'); ?>`;
                } else {
                    passwordField.setAttribute('type', 'password');
                    button.innerHTML = `<?php echo BaseHelper::renderIcon('ti ti-eye'); ?>`;
                }
            });
        });
    });
</script>
<?php /**PATH D:\xampp\htdocs\inspira\platform/core/base/resources/views/forms/fields/password-toggle-script.blade.php ENDPATH**/ ?>