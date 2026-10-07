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


// Practical 6 - Fetch API, search, filter, sort and pagination
let allEvents = [];
let filteredEvents = [];
let currentPage = 1;
const recordsPerPage = 5;

const eventContainer = document.getElementById("eventContainer");
const searchInput = document.getElementById("searchInput");
const categoryFilter = document.getElementById("categoryFilter");
const sortSelect = document.getElementById("sortSelect");
const loadingMessage = document.getElementById("loadingMessage");
const errorMessage = document.getElementById("errorMessage");
const pagination = document.getElementById("pagination");

if (eventContainer) {
    fetch("events.json")
        .then(function (response) {
            if (!response.ok) throw new Error("Unable to load events.json");
            return response.json();
        })
        .then(function (data) {
            allEvents = data;
            loadingMessage.style.display = "none";
            applyFilters();
        })
        .catch(function (error) {
            loadingMessage.style.display = "none";
            errorMessage.textContent = "Error loading event data: " + error.message;
        });

    function applyFilters() {
        const searchText = searchInput.value.toLowerCase().trim();
        const category = categoryFilter.value;

        filteredEvents = allEvents.filter(function (event) {
            const matchesSearch = event.title.toLowerCase().includes(searchText) ||
                event.venue.toLowerCase().includes(searchText);
            const matchesCategory = category === "all" || event.category === category;
            return matchesSearch && matchesCategory;
        });

        const sortValue = sortSelect.value;
        filteredEvents.sort(function (a, b) {
            if (sortValue === "dateAsc") return a.date.localeCompare(b.date);
            if (sortValue === "dateDesc") return b.date.localeCompare(a.date);
            if (sortValue === "titleAsc") return a.title.localeCompare(b.title);
            return b.title.localeCompare(a.title);
        });

        currentPage = 1;
        renderEvents();
        renderPagination();
    }

    function renderEvents() {
        eventContainer.innerHTML = "";
        const start = (currentPage - 1) * recordsPerPage;
        const pageItems = filteredEvents.slice(start, start + recordsPerPage);

        if (pageItems.length === 0) {
            eventContainer.innerHTML = "<p>No events found.</p>";
            return;
        }

        pageItems.forEach(function (event) {
            const card = document.createElement("article");
            card.className = "card event-card";
            card.innerHTML = `<h3>${event.title}</h3>
                <p><strong>Date:</strong> ${event.date}</p>
                <p><strong>Venue:</strong> ${event.venue}</p>
                <p><strong>Category:</strong> ${event.category}</p>`;
            eventContainer.appendChild(card);
        });
    }

    function renderPagination() {
        pagination.innerHTML = "";
        const totalPages = Math.ceil(filteredEvents.length / recordsPerPage);
        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement("button");
            button.textContent = i;
            button.addEventListener("click", function () {
                currentPage = i;
                renderEvents();
                renderPagination();
            });
            pagination.appendChild(button);
        }
    }

    searchInput.addEventListener("input", applyFilters);
    categoryFilter.addEventListener("change", applyFilters);
    sortSelect.addEventListener("change", applyFilters);
}
