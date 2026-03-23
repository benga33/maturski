document.addEventListener("DOMContentLoaded", function () {

    function setupPasswordStrength() {

        const passwordInput = document.getElementById("register-password");
        const strengthFill = document.getElementById("strength-fill");
        const strengthText = document.getElementById("strength-text");

        if (!passwordInput) return;

        passwordInput.addEventListener("input", function () {

            const password = passwordInput.value;
            let score = 0;

            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password) && /[a-z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            if (score === 0) {
                strengthFill.style.width = "0%";
                strengthText.innerText = "";
            }

            if (score === 1) {
                strengthFill.style.width = "25%";
                strengthFill.style.background = "red";
                strengthText.innerText = "Weak";
            }

            if (score === 2) {
                strengthFill.style.width = "50%";
                strengthFill.style.background = "orange";
                strengthText.innerText = "Moderate";
            }

            if (score === 3) {
                strengthFill.style.width = "75%";
                strengthFill.style.background = "#d4c600";
                strengthText.innerText = "Good";
            }

            if (score === 4) {
                strengthFill.style.width = "100%";
                strengthFill.style.background = "green";
                strengthText.innerText = "Strong";
            }

        });

    }

    setupPasswordStrength();

    window.showForm = function (formId) {

        const forms = document.querySelectorAll(".form-box");

        forms.forEach(function (form) {
            form.classList.remove("active");
        });

        const selectedForm = document.getElementById(formId);

        if (selectedForm) {
            selectedForm.classList.add("active");
        }

        if (formId === "register-form") {
            setupPasswordStrength();
        }

    };

});