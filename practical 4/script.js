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
