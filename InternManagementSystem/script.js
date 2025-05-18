//cancel button sa index.php
function cancelForm() {
 
    document.getElementById("gmail").value = "";
    document.getElementById("password").value = "";
    document.getElementById("confirm-password").value = "";

   
    window.location.href = "index.php";
}
//cancel button sa intern_registerPortal.php
function cancelIntern() {
 
    document.getElementById("gmail").value = "";
    document.getElementById("password").value = "";
    document.getElementById("confirm-password").value = "";

   
    window.location.href = "InternLogin.php";
}
//for show password
function togglePassword(inputId, checkboxId) {
    var passwordField = document.getElementById(inputId);
    var checkbox = document.getElementById(checkboxId);
    passwordField.type = checkbox.checked ? "text" : "password";
}
//for changing content 
function showSection(sectionId) {
   
    let sections = document.querySelectorAll('.section');
    sections.forEach(section => section.style.display = 'none');

    document.getElementById(sectionId).style.display = 'block';
}
//for logout
function logout() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = 'logout.php'; // Redirect to logout.php
    }
}

//for fetching recent login
function fetchRecentInterns() {
    fetch('fetch_recentInterns.php')
    .then(response => response.text())
    .then(data => {
        document.getElementById('recent-interns').innerHTML = data;
    })
    .catch(error => console.error('Error fetching interns:', error));
}
fetchRecentInterns();
setInterval(fetchRecentInterns, 1000);

//for fetching number of interns per department
function updateInternCount() {
    fetch('fetch_internCount.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('ics-interns').value = data["Institute of Computer Studies (ICS)"];
        document.getElementById('ioe-interns').value = data["Institute of Education (IOE)"];
        document.getElementById('iob-interns').value = data["Institute of Business (IOB)"];
    })
    .catch(error => console.error('Error fetching intern count:', error));
}
//call when reload
window.onload = updateInternCount;
setInterval(updateInternCount, 2000);



//for fetching all the collection of intern 
function fetchInterns() {
    fetch('fetchAllinternTable.php')
        .then(response => response.text())
        .then(data => {
            document.querySelector(".interns-container tbody").innerHTML = data;
        })
        .catch(error => console.error("Error fetching interns:", error));
}
// Load interns when the page loads
fetchInterns();
setInterval(fetchInterns, 15000);



document.addEventListener("DOMContentLoaded", function () {
    fetchNotifications();
});

// Toggle Notification Box
function toggleNotifications() {
    var notificationBox = document.getElementById("notification-container");
    
    if (notificationBox.style.display === "none" || notificationBox.style.display === "") {
        notificationBox.style.display = "block"; // Show notifications
    } else {
        notificationBox.style.display = "none"; // Hide notifications
    }
}


// Fetch Notifications from the Database
function fetchNotifications() {
    fetch('fetch_notifications.php')
    .then(response => response.json())
    .then(data => {
        let notificationList = document.getElementById("notification-list");
        let notificationBadge = document.getElementById("notification-badge");

        notificationList.innerHTML = ""; // Clear previous notifications

        if (data.length === 0) {
            notificationList.innerHTML = "<li>No new notifications</li>";
            notificationBadge.style.display = "none"; // Hide badge if no notifications
        } else {
            notificationBadge.style.display = "block"; // Show badge if there are notifications
            notificationBadge.textContent = data.length; // Show number of notifications

            data.forEach(notification => {
                let listItem = document.createElement("li");
                listItem.textContent = notification.message;
                notificationList.appendChild(listItem);
            });
        }
    })
    .catch(error => console.error("Error fetching notifications:", error));
}
setInterval( fetchNotifications, 10000);

// Show modal and populate with intern data
function editIntern(id) {
    fetch("get_intern.php?id=" + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById("editId").value = data.id;
            document.getElementById("editFname").value = data.fname;
            document.getElementById("editLname").value = data.lname;
            document.getElementById("editGmail").value = data.gmail;
            document.getElementById("editDepartment").value = data.department;
            document.getElementById("editHours").value = data.required_hours;
            document.getElementById("editModal").style.display = "block";
        })
        .catch(error => console.error("Error fetching intern details:", error));
}

// Close modal
function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

// Handle form submission
document.getElementById("editForm").addEventListener("submit", function (e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch("update_intern.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            location.reload(); // Refresh table after update
        }
    })
    .catch(error => console.error("Error updating intern:", error));
});

function deleteIntern(fname, lname) {
    if (!confirm(`Are you sure you want to delete ${fname} ${lname}?`)) return;

    fetch("delete_intern.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `fname=${encodeURIComponent(fname)}&lname=${encodeURIComponent(lname)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Intern deleted successfully.");
            location.reload(); // Refresh the page
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

let deleteFname = "";
let deleteLname = "";

function showModal(fname, lname) {
    deleteFname = fname;
    deleteLname = lname;
    document.getElementById("internName").innerText = `${fname} ${lname}`;
    document.getElementById("deleteModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("deleteModal").style.display = "none";
}

document.getElementById("confirmDelete").addEventListener("click", function() {
    fetch("delete_intern.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `fname=${encodeURIComponent(deleteFname)}&lname=${encodeURIComponent(deleteLname)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Intern deleted successfully.");
            location.reload();
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));

    closeModal();
});


// Close the modal if the user clicks outside the modal content
window.onclick = function(event) {
    let modal = document.getElementById("viewAttendanceModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
};

window.onclick = function(event) {
    let modal = document.getElementById("adminLogoutModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function viewButton(internId, internName) {
    // Fetch attendance records via AJAX
    fetch("fetch_attendance.php?intern_id=" + internId)
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("attendanceTableBody");
            tableBody.innerHTML = ""; // Clear existing records
            
            if (data.length > 0) {
                data.forEach(record => {
                    let row =
                    `<tr>
                        <td>${record.name}</td>
                        <td>${record.department}</td>
                        <td>${record.time_in}</td>
                        <td>${record.time_out}</td>
                        <td>${record.formatted_date}</td>
                        <td>${record.hours_completed}</td>
                    </tr>`;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = "<tr><td colspan='6'>No attendance records found.</td></tr>";
            }

            document.getElementById("viewAttendanceModal").style.display = "block";
        })
        .catch(error => console.error("Error fetching attendance:", error));
}



function closeViewModal() {
    document.getElementById("viewAttendanceModal").style.display = "none";
}

function searchInterns() {
    let searchQuery = document.getElementById("search").value.trim();

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "search.php?query=" + encodeURIComponent(searchQuery), true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("table-body").innerHTML = xhr.responseText;
        }
    };

    xhr.send();
}

// Load all interns when the page loads
window.onload = function () {
    searchInterns();
};

// download button for admin side
document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ script.js loaded successfully");

    // Select the download button
    const downloadButton = document.getElementById("downloadAttBtn");

    if (!downloadButton) {
        console.error("❌ Download button not found!");
        return;
    }

    console.log("✅ Download button found, adding event listener...");

    downloadButton.addEventListener("click", function () {
        console.log("✅ Download button clicked, capturing table...");

        const tableWrapper = document.getElementById("attendanceTableWrapper");

        if (!tableWrapper) {
            console.error("❌ Table wrapper not found!");
            return;
        }

        html2canvas(tableWrapper, { scale: 2 }).then((canvas) => {
            console.log("✅ Canvas created successfully");

            const imgData = canvas.toDataURL("image/png");
            const link = document.createElement("a");
            link.href = imgData;
            link.download = "Attendance_History.png";

            // Append link, trigger click, then remove
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            console.log("✅ Download triggered");
        }).catch((err) => {
            console.error("❌ Error capturing canvas:", err);
        });
    });
});

function filterByDepartment() {
    let selectedDept = document.getElementById("departmentFilter").value;
    let table = document.querySelector("#table-body");
    let rows = Array.from(table.rows);

    rows.forEach(row => {
        let deptCell = row.cells[3].textContent.trim();
        if (selectedDept === "all" || deptCell === selectedDept) {
            row.style.display = "";
        } else {    
            row.style.display = "none";
        }
    });
}

function openLogoutModal() {
    document.getElementById("adminLogoutModal").style.display = "flex";
}

function closeLogoutModal() {
    document.getElementById("adminLogoutModal").style.display = "none";
}

function openNav() {
    document.getElementById("navigation").classList.add("active");
    document.getElementById("open-nav").style.display = "none";
}

function closeNav() {
    document.getElementById("navigation").classList.remove("active");
    document.getElementById("open-nav").style.display = "flex";
}
