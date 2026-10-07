// Practical 4 - JavaScript DOM, Events and UI Interactivity

// Notification banner
const banner = document.getElementById("notificationBanner");
const closeBanner = document.getElementById("closeBanner");

if (closeBanner) {
    closeBanner.addEventListener("click", function () {
        banner.style.display = "none";
    });
}

// Hamburger menu
const hamburger = document.getElementById("hamburgerBtn");
const nav = document.querySelector(".ttt");

if (hamburger) {
    hamburger.addEventListener("click", function () {
        nav.classList.toggle("show-menu");
    });
}

// Light / Dark theme
const themeButton = document.getElementById("themeButton");
const savedTheme = localStorage.getItem("theme");

if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
}

if (themeButton) {
    themeButton.addEventListener("click", function () {
        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {
            localStorage.setItem("theme", "dark");
        } else {
            localStorage.setItem("theme", "light");
        }
    });
}

// FAQ collapsible questions
const faqQuestions = document.querySelectorAll(".faq-question");

faqQuestions.forEach(function (question) {
    question.addEventListener("click", function () {
        const answer = question.nextElementSibling;
        answer.classList.toggle("show-answer");

        if (answer.classList.contains("show-answer")) {
            question.textContent = "- " + question.textContent.substring(2);
        } else {
            question.textContent = "+ " + question.textContent.substring(2);
        }
    });
});

// Simple image/content slider
const slides = document.querySelectorAll(".slide");
const previousButton = document.getElementById("previousButton");
const nextButton = document.getElementById("nextButton");
let slideNumber = 0;

function showSlide(number) {
    slides.forEach(function (slide) {
        slide.style.display = "none";
    });

    if (slides.length > 0) {
        slides[number].style.display = "block";
    }
}

if (slides.length > 0) {
    showSlide(slideNumber);
}

if (nextButton) {
    nextButton.addEventListener("click", function () {
        slideNumber++;

        if (slideNumber >= slides.length) {
            slideNumber = 0;
        }

        showSlide(slideNumber);
    });
}

if (previousButton) {
    previousButton.addEventListener("click", function () {
        slideNumber--;

        if (slideNumber < 0) {
            slideNumber = slides.length - 1;
        }

        showSlide(slideNumber);
    });
}

// Modal popup
const modal = document.getElementById("noticeModal");
const openModal = document.getElementById("openModal");
const closeModal = document.getElementById("closeModal");

if (openModal) {
    openModal.addEventListener("click", function () {
        modal.style.display = "block";
    });
}

if (closeModal) {
    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
    });
}

window.addEventListener("click", function (event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});


// Practical 5 - Registration form validation
const registrationForm = document.getElementById("registrationForm");

if (registrationForm) {
    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const mobile = document.getElementById("mobile");
    const course = document.getElementById("course");
    const year = document.getElementById("year");
    const password = document.getElementById("password");
    const confirm = document.getElementById("confirm");
    const terms = document.getElementById("terms");
    const formMessage = document.getElementById("formMessage");
    const strengthMessage = document.getElementById("strengthMessage");

    function setError(id, message) {
        document.getElementById(id).textContent = message;
    }

    function clearErrors() {
        document.querySelectorAll(".error").forEach(function (item) {
            item.textContent = "";
        });
        formMessage.textContent = "";
    }

    function checkPasswordStrength(value) {
        let score = 0;
        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[a-z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        if (value.length === 0) strengthMessage.textContent = "";
        else if (score <= 2) strengthMessage.textContent = "Password strength: Weak";
        else if (score <= 4) strengthMessage.textContent = "Password strength: Medium";
        else strengthMessage.textContent = "Password strength: Strong";
    }

    password.addEventListener("input", function () {
        checkPasswordStrength(password.value);
    });

    registrationForm.addEventListener("submit", function (event) {
        event.preventDefault();
        clearErrors();
        let valid = true;

        const namePattern = /^[A-Za-z ]{2,50}$/;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const mobilePattern = /^[6-9][0-9]{9}$/;
        const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

        if (!namePattern.test(name.value.trim())) {
            setError("nameError", "Enter a valid name."); valid = false;
        }
        if (!emailPattern.test(email.value.trim())) {
            setError("emailError", "Enter a valid email address."); valid = false;
        }
        if (!mobilePattern.test(mobile.value.trim())) {
            setError("mobileError", "Enter a valid 10-digit mobile number."); valid = false;
        }
        if (course.selectedIndex === 0) {
            setError("courseError", "Please select a course."); valid = false;
        }
        if (year.value === "") {
            setError("yearError", "Please select a year."); valid = false;
        }
        if (!document.querySelector('input[name="gender"]:checked')) {
            setError("genderError", "Please select gender."); valid = false;
        }
        if (!passwordPattern.test(password.value)) {
            setError("passwordError", "Use at least 8 characters with letters and numbers."); valid = false;
        }
        if (password.value !== confirm.value) {
            setError("confirmError", "Passwords do not match."); valid = false;
        }
        if (!terms.checked) {
            setError("termsError", "Please accept the Terms & Conditions."); valid = false;
        }

        if (valid) {
            formMessage.textContent = "Registration form submitted successfully!";
            formMessage.className = "form-message success";
        } else {
            formMessage.textContent = "Please correct the errors shown above.";
            formMessage.className = "form-message error-message";
        }
    });
}
