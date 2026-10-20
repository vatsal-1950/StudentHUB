<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 13 | Asynchronous AJAX CRUD</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .toast-msg {
            display: none;
            max-width: 600px;
            margin: 10px auto;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <button id="hamburgerBtn" class="hamburger-btn" aria-label="toggle menu">☰ Menu</button>
    <nav class="ttt">
        <a href="home.html" class="m">Home</a>|
        <a href="about.html" class="m1">About</a>|
        <a href="ajax_crud.php" class="m2">AJAX CRUD</a>|
        <a href="login.html" class="m9">Login</a>|
        <a href="dashboard.html" class="m3">Dashboard</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="contact.html" class="m6">Contact</a>|
        <a href="faq.html" class="m7">FAQ</a>|
        <a href="feedback.html" class="m8">Feedback</a>
    </nav>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<main>
<section>
    <h2 align="center">Practical 13: Asynchronous Fetch-Based CRUD</h2>
    <p align="center" style="margin-top: 5px;">Consuming PHP JSON API without Page Reload</p>
    <br>

    <div id="toast" class="toast-msg"></div>

    <form id="studentForm" style="max-width: 650px;">
        <h3 id="formTitle" style="color: blue; margin-bottom: 10px;">Add New Student via API</h3>
        <input type="hidden" id="studentId" value="">

        <label for="ajax_name">Full Name</label>
        <input type="text" id="ajax_name" required>

        <label for="ajax_email">Email Address</label>
        <input type="email" id="ajax_email" required>

        <label for="ajax_mobile">Mobile Number</label>
        <input type="tel" id="ajax_mobile" required>

        <label for="ajax_course">Course</label>
        <select id="ajax_course" required>
            <option value="B.Tech Computer Engineering">B.Tech Computer Engineering</option>
            <option value="B.Tech Information Technology">B.Tech Information Technology</option>
            <option value="B.Tech Civil Engineering">B.Tech Civil Engineering</option>
            <option value="B.Tech Mechanical Engineering">B.Tech Mechanical Engineering</option>
        </select>

        <label for="ajax_year">Year</label>
        <select id="ajax_year" required>
            <option value="First Year">First Year</option>
            <option value="Second Year">Second Year</option>
            <option value="Third Year">Third Year</option>
            <option value="Fourth Year">Fourth Year</option>
        </select>

        <label for="ajax_gender">Gender</label>
        <select id="ajax_gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <br><br>
        <button type="submit" id="submitBtn">Submit via API</button>
        <button type="button" id="resetBtn" style="background-color: gray; display: none;">Cancel Edit</button>
    </form>

    <div style="max-width: 800px; margin: 20px auto; text-align: center;">
        <input type="text" id="apiSearch" placeholder="Debounced live search..." style="width: 70%; padding: 8px;">
    </div>

    <div class="table-container">
        <p id="tableLoading" style="text-align: center; font-weight: bold;">Loading students from JSON API...</p>
        <table class="record-table" id="crudTable" style="display: none;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="crudTbody"></tbody>
        </table>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script>
let debounceTimer = null;
const toast = document.getElementById("toast");
const crudTable = document.getElementById("crudTable");
const crudTbody = document.getElementById("crudTbody");
const tableLoading = document.getElementById("tableLoading");
const studentForm = document.getElementById("studentForm");
const studentId = document.getElementById("studentId");
const formTitle = document.getElementById("formTitle");
const submitBtn = document.getElementById("submitBtn");
const resetBtn = document.getElementById("resetBtn");
const apiSearch = document.getElementById("apiSearch");

function showToast(message, isSuccess) {
    toast.textContent = message;
    toast.className = isSuccess ? "toast-msg alert-success" : "toast-msg alert-danger";
    toast.style.display = "block";
    setTimeout(() => { toast.style.display = "none"; }, 4000);
}

function loadStudents(query = "") {
    tableLoading.style.display = "block";
    crudTable.style.display = "none";

    fetch("api.php?action=list&search=" + encodeURIComponent(query))
        .then(response => response.json())
        .then(res => {
            tableLoading.style.display = "none";
            crudTbody.innerHTML = "";
            if (res.status === "success" && res.data.length > 0) {
                crudTable.style.display = "table";
                res.data.forEach(s => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${s.id}</td>
                        <td>${s.name}</td>
                        <td>${s.email}</td>
                        <td>${s.mobile}</td>
                        <td>${s.course}</td>
                        <td>${s.year}</td>
                        <td>
                            <button onclick='editRow(${JSON.stringify(s)})' style="padding: 4px 8px; margin: 2px;">Edit</button>
                            <button onclick='deleteRow(${s.id})' style="padding: 4px 8px; margin: 2px; background: red;">Delete</button>
                        </td>
                    `;
                    crudTbody.appendChild(tr);
                });
            } else {
                tableLoading.textContent = "No student records found.";
                tableLoading.style.display = "block";
            }
        })
        .catch(err => {
            tableLoading.textContent = "Error fetching data from API: " + err.message;
            tableLoading.style.display = "block";
        });
}

studentForm.addEventListener("submit", function(e) {
    e.preventDefault();
    const isEdit = studentId.value !== "";
    const payload = {
        action: isEdit ? "update" : "create",
        id: studentId.value,
        name: document.getElementById("ajax_name").value,
        email: document.getElementById("ajax_email").value,
        mobile: document.getElementById("ajax_mobile").value,
        course: document.getElementById("ajax_course").value,
        year: document.getElementById("ajax_year").value,
        gender: document.getElementById("ajax_gender").value
    };

    fetch("api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === "success") {
            showToast(data.message, true);
            resetForm();
            loadStudents();
        } else {
            showToast(data.message, false);
        }
    })
    .catch(err => {
        showToast("Request failed: " + err.message, false);
    });
});

function editRow(student) {
    studentId.value = student.id;
    document.getElementById("ajax_name").value = student.name;
    document.getElementById("ajax_email").value = student.email;
    document.getElementById("ajax_mobile").value = student.mobile;
    document.getElementById("ajax_course").value = student.course;
    document.getElementById("ajax_year").value = student.year;
    document.getElementById("ajax_gender").value = student.gender;

    formTitle.textContent = "Edit Student (#" + student.id + ") via API";
    submitBtn.textContent = "Update Student";
    resetBtn.style.display = "inline-block";
    window.scrollTo({ top: 100, behavior: "smooth" });
}

function deleteRow(id) {
    if (!confirm("Delete student #" + id + " via API?")) return;

    fetch("api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "delete", id: id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === "success") {
            showToast(data.message, true);
            loadStudents();
        } else {
            showToast(data.message, false);
        }
    })
    .catch(err => showToast("Delete failed: " + err.message, false));
}

function resetForm() {
    studentId.value = "";
    studentForm.reset();
    formTitle.textContent = "Add New Student via API";
    submitBtn.textContent = "Submit via API";
    resetBtn.style.display = "none";
}

resetBtn.addEventListener("click", resetForm);

apiSearch.addEventListener("input", function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        loadStudents(apiSearch.value.trim());
    }, 300);
});

document.addEventListener("DOMContentLoaded", () => loadStudents());
</script>
<script src="script.js"></script>
</body>
</html>
