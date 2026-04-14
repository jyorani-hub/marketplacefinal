// Simple client-side form validation
document.addEventListener('DOMContentLoaded', function () {

    // Register form
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirm').value;

            if (name.length < 2) {
                e.preventDefault();
                alert('Name must be at least 2 characters.');
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
            if (pass.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters.');
                return;
            }
            if (pass !== confirm) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }
        });
    }

    // Login form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const email = document.getElementById('email').value.trim();
            const pass = document.getElementById('password').value;
            if (!email || !pass) {
                e.preventDefault();
                alert('Please fill in both email and password.');
            }
        });
    }

    // Sell / product form
    const productForm = document.getElementById('product-form');
    if (productForm) {
        productForm.addEventListener('submit', function (e) {
            const name = document.getElementById('name').value.trim();
            const price = parseFloat(document.getElementById('price').value);
            const stock = parseInt(document.getElementById('stock').value, 10);

            if (name.length < 3) {
                e.preventDefault();
                alert('Product name must be at least 3 characters.');
                return;
            }
            if (isNaN(price) || price <= 0) {
                e.preventDefault();
                alert('Please enter a valid price greater than 0.');
                return;
            }
            if (isNaN(stock) || stock < 1) {
                e.preventDefault();
                alert('Stock must be at least 1.');
                return;
            }
        });
    }

    // Confirm delete actions
    document.querySelectorAll('.confirm-delete').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });
});
