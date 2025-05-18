<?php
    include 'connection.php';
    
    if (!isset($_SESSION['gmail'])) {
        header("Location: index.php");
        exit();
    }

    ?>
    <!--MAIN FORM-->

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Intern Management System</title>
        <link rel="stylesheet" href="./styles.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Playfair Display"> 
        <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script defer src="facerecognition.js"></script>
        <script defer src="script.js"></script>
        
    </head>
    <body>
    <div class="dashBoardcontainer">
        <!--navigation bar-->
        <div id= "navigation" class="nav-bar">
            <span id="close-nav" class="close-nav" onclick="closeNav()">&times;</span>
            <img src="uploads/cdm.png" id="cdm-logo" name="cdm-logo" alt="cdm-logo">
            <p>Intern Management System</p>
            <ul class="nav-list">
                <li onclick="showSection('home')"><i class="fa-solid fa-house"></i> Home</li>
                <li onclick="showSection('interns')"><i class="fas fa-users"></i> Interns</li>
                <li onclick="showSection('apply')"><i class="fas fa-file-alt"></i> Intern Application</li>
                <li><a href="InternAttPortal.php" target= "_blank" style="text-decoration:none; color: black;"><i class="fa-solid fa-arrow-right-to-bracket"></i> Attendance Login</a></li>
                <li style="margin-top:50px; background-color:#A8CD89;"><a href= "AdminChangePassword.php" style="text-decoration:none; color: black;"><i class="fa-solid fa-key"></i>Change Password</a></li>
                <li id="logoutAdminModal" onclick="openLogoutModal()" style="margin-top:10px; background-color:#A8CD89;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</li>
            </ul>
        </div> 
        <!-- Logout Confirmation Modal -->
        <div id="adminLogoutModal" class="modal-container" style="display: none; z-index:1000;">
            <div class="modal-content">
                <h3 style="color:red;">Confirm Logout</h3>
                <p style="font-weight:bold;">Are you sure you want to log out?</p>
                <button onclick="window.location.href='logout.php'" style="background-color: red; margin:5px; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Confirm</button>
                <button id="cancelLogout" onclick="closeLogoutModal()" style="background-color: gray; margin:5px; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
            </div>
        </div>
        <!-- Sections --> 
        <div class="content">
        <span id="open-nav" class = "open-nav" onclick="openNav()"> <i class="fa-solid fa-bars" style = "position:fixed; left:1.5em;"></i></span>
        <div id="home" class="section" style="display:none;"  >
                <div class="dashboard-header">
                    <h1>Dashboard</h1>
                    <div class="notification-wrapper">
                        <div class="notification-badge" id="notification-badge" style="display: none;"></div> 
                        <img src="uploads/bell.png" width="25" height="25" class="dashboard-img" id="notification-bell" onclick="toggleNotifications()">
                        <div id="notification-container" class="notification-box" style="display: none;">
                            <h3>Notifications</h3>
                            <ul id="notification-list"></ul> <!-- list of notification -->
                        </div>
                    </div>
                </div>

                <!-- dashboard cards -->
                <div class="dashboard-cards">
                    <div id="ics" class="dashboard-card"> 
                        <h2>Institute of Computer Studies</h2>
                        <h4>Number of Interns</h4>
                        <input type="text" id="ics-interns" style="outline:none;"class="info-box" value="0" readonly>
                    </div>
                    <div id="ioe" class="dashboard-card"> 
                        <h2>Institute of Education</h2>
                        <h4>Number of Interns</h4>
                        <input type="text" id="ioe-interns" style="outline:none;" class="info-box" value="0" readonly>
                    </div>
                    <div id="iob" class="dashboard-card">     
                        <h2>Institute of Business</h2>
                        <h4>Number of Interns</h4>
                        <input type="text" id="iob-interns" style="outline:none;" class="info-box" value="0" readonly>
                    </div>

                    <!--recent login-->
                    <div class="recent-log" id="recent-log">
                        <h2>Recent Logins</h2>
                        <table>
                            <tr>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Time in</th>
                                <th>Time out</th>
                            </tr>
                            <tbody id="recent-interns"></tbody> <!-- Data will be loaded here -->
                        </table>
                    </div>
                    
                </div>
            </div>
            
            <div id="interns" class="section">
                <h1>Intern List</h1>
                <form action="" onsubmit="return false;" method="GET">
                    <div class="search-container">
                        <div class="search-section">
                            <img src= "uploads/search-symbol.png">
                            <input type = "text" id="search" name="search" placeholder="Search" onkeyup="searchInterns()">     
                        </div> 
                        <div class="exportbtn"> 
                            <a href ="export.php" style="text-decoration:none; "><button type ="button" id = "exportButton" name="exportButton">Export to Excel</button></a>
                        </div> 
                    </div> 
                </form> 
                        <div class="interns-container" style="overflow-y:scroll; overflow-x: hidden;">
                            <table>
                                <thead class="table-head" style="position:sticky;" >
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Gmail</th>
                                        <th class="deptSort">Department 
                                            <select id="departmentFilter" onchange="filterByDepartment()">
                                                <option value="all">All Departments</option>
                                                <option value="Institute of Computer Studies (ICS)">(ICS)</option>
                                                <option value="Institute of Business (IOB)">(IOB)</option>
                                                <option value="Institute of Education (IOE)">(IOE)</option>
                                            </select>
                                        </th>
                                        <th>Date Started</th>
                                        <th>Required Hours</th>
                                        <th>Hours Remaining</th> 
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    <!-- Data will be loaded dynamically here -->
                                </tbody>
                            </table>
                        </div>
    
                        
                            <!-- Edit Intern Modal -->
                            <div id="editModal">
                                <h3>Edit Intern</h3>
                                <form id="editForm">
                                    <input type="hidden" id="editId" name="id">
                                    <label>First Name:</label>
                                    <input type="text" id="editFname" name="fname" required><br><br>
                                    <label>Last Name:</label>
                                    <input type="text" id="editLname" name="lname" required><br><br>
                                    <label>Email:</label>
                                    <input type="email" id="editGmail" name="gmail" required><br><br>
                                    <label>Department:</label>
                                    <input type="text" id="editDepartment" name="department" required><br><br>
                                    <label>Required Hours:</label>
                                    <input type="number" id="editHours" name="required_hours" required><br><br>
                                    <button type="submit">Update</button>
                                    <button type="button" onclick="closeEditModal()">Cancel</button>
                                </form>
                            </div>
                            <!-- Delete Intern Modal -->
                            <div id="deleteModal" class="modal" style="display: none;">
                                <div class="modal-content">
                                    <span class="close" onclick="closeModal()"></span>
                                    <h3 style="color:red; ">Confirm Delete</h3>
                                    <p style="font-weight:bold;">Are you sure you want to delete <span id="internName"></span> ?</p>
                                    <button id="confirmDelete">Delete</button>
                                    <button id="cancel" onclick="closeModal()">Cancel</button>
                                </div>
                            </div>
                            <!-- View Intern Modal -->
                            <div id="viewAttendanceModal" class="viewmodal" style="display: none;">
                                <div class="viewmodal-content">
                                    <span class="viewmodalClose" onclick="closeViewModal()">&times;</span>
                                    <h2 style = "margin:0 auto;">Attendance History</h2>
                                    <div class="dlBtn">
                                        <button id="downloadAttBtn">Download Attendance</button>
                                    </div>   
                                    <div id="attendanceTableWrapper">
                                        <table width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Department</th>
                                                    <th>Time In</th>
                                                    <th>Time Out</th>
                                                    <th>Date</th>
                                                    <th>Hours Completed</th>
                                                </tr>
                                            </thead>
                                            <tbody id="attendanceTableBody">
                                                <!-- Attendance records will be inserted here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
            </div>
            
        
            <div id="apply" class="section" style="display:none;">
                <h1>Intern Application Form</h1>
                <div class="applicationContainer">
                    <form id="member-form" action="save_intern.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="fname">First name: </label>
                            <input type="text" id="fname" name="fname" placeholder="Enter your first name"required>  

                            <label for="lname">Last name: </label>
                            <input type="text" id="lname" name="lname" placeholder="Enter your last name"required>

                            <label for="gmail">Gmail: </label>
                            <input type="text" id="gmail" name="gmail" placeholder="Enter your gmail" style="text-transform: none;" required>

                            <label for="phone-no">Phone Number: </label>                    
                            <input type="text" id="phone-no" name="phone-no" placeholder="Enter your phone number" required>

                            <label for="department">Department: </label>
                            <input list="departments" id="department" name="department" placeholder="Please choose your assigned department"required>

                            <datalist id="departments">
                            <option value="Institute of Computer Studies (ICS)">
                            <option value="Institute of Education (IOE)">
                            <option value="Institute of Business (IOB)">
                            </datalist>

                            <label for="school">School: </label>
                            <input type="text" id="school" name="school" placeholder="Enter your School" required>

                            <label for="date-started">Date Started: </label>
                            <input type="date" id="date-started" name="date-started" placeholder="Enter your started date" required>

                            <label for="required-hours">Required Hours: </label>                    
                            <input type="text" id="required-hours" name="required-hours" placeholder="Enter your required hours" required>
                            
                            <label for="resume">Resume: </label>
                            <input type="file" id="resume" name="resume" style="padding:8px;"required>
                            <button type="submit" style="margin-top:50px; width:50%; position:absolute; left:0; bottom:-9%;">Submit Application</button>
                            <button type="button" style="margin-top:50px; width:50%; position:absolute; right:0; bottom:-9%;">Cancel</button>
                        </div>
            
                            <div class="face-capture">
                                <h2>Face Recognition</h2>   
                                <video id="camera-stream" width="320" height="240" autoplay></video>
                            <div class="face-button">
                                <button type="button" id="capture-btn">Capture Face</button><br>
                                <button type="button" id="retry-btn">Retry</button>
                            </div>
                                <canvas id="capture-canvas" style="display:none;"></canvas>
                                <input type="hidden" id="face-descriptor" name="face-descriptor"> <!--input ng face base64 -->
                            </div> 

                            <div class="captured-face">
                                <h2>Captured Face</h2>
                                <img id="photo-preview" width="320" height="190">
                                <input type="hidden" id="face-image" name="face-image">
                            </div>
                    </form>
                </div>
            </div>
        
            <div id="status" class="section" style="display:none;">
                <h1>Intern Status</h1>
            </div>
        </div>
    </div>


    </body>

    <style>
    #open-nav {
        display:none;
    }
    #close-nav {
        display:none;
    }
        .modal-container {
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }
    .close {
        float: right;
        font-size: 20px;
        cursor: pointer;
    }

.dlBtn{
    display:flex;
    padding:5px;
    justify-content:left;
    margin-bottom:5px;
}   
.dlBtn button{
    background-color:#3D8D7A;
    color: white;
    padding:8px;
    border: 1 solid;
    border-radius: 10px;
}
.dlBtn button:hover{
    background-color:#50A18D
}
 .search-container{
    display:flex;
    justify-content:space-between;
    padding:5px;
    margin-top:50px;
    margin-bottom:-50px;
 }
 .search-section{
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
 }
 .search-section img {
        width: 16px;
        height: 16px;
    }
 .search-section label{
        font-weight:bold;
 }
 .deptSort select{
        border-radius:5px;
 }
.exportbtn button {
        background-color: #3D8D7A;
        color: white;
        border: none;
        padding:  15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: 0.3s;
        margin-right:15px;
}

.exportbtn button:hover {
        background-color: #50A18D;
}
    .search-section input {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        width: 200px;
    }

.viewmodal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .viewmodal-content {
        background-color: white;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 10px;
        text-align: center;
    }
    .viewmodalClose {
        float: right;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
    }
   


    .modal {
        position: fixed;
        top: 0; 
        left: 0;
        width: 100%; 
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex; 
        justify-content: center; 
        align-items: center;
    }
    .modal button{
        padding:10px;
        margin:12px;
        border:none;
        width:30%;
        border-radius:5px;
    }
    .modal #confirmDelete{  
        background: red;
        color:white;
    }
    .modal #cancel{  
        border: 1px solid #0C0C0C;
        background: white;
    }
    .modal #confirmDelete:hover {  
        background: #c9302c;
        
    }
    .modal #cancel:hover{  
        background:#FFFDEC;
    }
    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .close {
        position: absolute;
        top: 10px; right: 15px;
        font-size: 20px;
        cursor: pointer;
    }

    /* Modal Styling */
    #editModal {
        display: none; /* Initially hidden */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        width: 400px;
        max-width: 90%;
        z-index: 1000;
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Modal Header */
    #editModal h3 {
        text-align: center;
        margin-bottom: 15px;
        font-size: 1.5rem;
        color: #333;
    }

    /* Modal Form Inputs */
    #editModal label {
        display: block;
        font-weight: bold;
        margin-top:2px;
        color: #555;
    }

    #editModal input {
        width: 94%;
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
        transition: border 0.3s ease-in-out;
    }

    #editModal input:focus {
        border-color: #3D8D7A;
        outline: none;
        box-shadow: 0 0 5px rgba(61, 141, 122, 0.5);
    }

    /* Modal Buttons */
    #editModal button {
        width: 100%;
        padding: 10px;
        margin-top: 15px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease-in-out;
    }

    #editModal button[type="submit"] {
        background: #3D8D7A;
        color: white;
    }

    #editModal button[type="submit"]:hover {
        background: #2a6d5a;
    }

    #editModal button[type="button"] {
        background: #d9534f;
        color: white;
    }

    #editModal button[type="button"]:hover {
        background: #c9302c;
    }


    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translate(-50%, -55%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }

    /* Notification Badge (Red Dot) */
    .notification-badge {
        display:flex;
        position: absolute;
        top: 62px;
        right: 73px;
        background: red;
        color: white;
        font-size: 12px;
        border-radius: 50%;
        width: 16px;
        height: 17px;
        text-align: center;
        line-height: 15px;
        display: none;
        z-index: 9999;
    }


    .notification-box {
        position: absolute;
        top: 95px;  
        right: 90px;
        background: white;
        width: 250px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        padding: 10px;
        background-color: #e8f5e9;
        z-index: 1000; 
        display: none; 
    }

    .notification-box h3 {
        font-size: 16px;
        margin-top: 10px;
        margin-bottom: 10px;
        margin-left:10px;
    }

    .notification-box ul {
        padding: 15px;
        margin: 0;
        margin-left:15px;
        font-weight:bold;
    }

    .notification-box li {
        padding:5px;
        font-size: 14px;
        width: 100%;
    }

    .notification-box li:last-child {
        border-bottom: none;
    }

    #home img{
        display:flex;
        margin: 0 auto;
        position:relative;
        margin-right:35px;
        bottom:55px;
        cursor: pointer;
        transition: transform 0.3s ease-in-out;
    }
        
    .interns-container {
        margin-top: 50px;
        padding:20px;
        background: #e8f5e9; 
        border-radius: 10px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        overflow-x: auto;
        text-align:center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    th, td {
        border: 1px solid #3D8D7A;
        padding: 10px;
        text-align: center;
        position:sticky;
    }

    th {
        background-color: #3D8D7A;
        color: white;
        position:sticky;
    }
    tbody {
        font-weight:bold;
        
    }

    body, html {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Playfair Display', serif;
    }

    .dashBoardcontainer {
        display: flex;
        height: 100vh;
        width: 100%;
    }

    .nav-bar {
        background-color: #3D8D7A;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        width: 250px;
        position: fixed;
        left: 0;
        height: 100vh;
        box-shadow: 2px 2px 4px #3D8D7A;

    }
    .nav-bar img {
        width: 8em;
        height: 8em;
        margin-bottom: 10px;
    }

    .nav-bar p {
        color: #d3f5d5;
        font-size: 1.3em;
        font-weight:bold;
        text-align: center;
        margin-bottom: 20px;
        margin-top:5px;
    }

    .nav-list {
        list-style: none;
        padding: 0;
        width: 100%;
    }
    .nav-list i {
        margin-right: 5px; 
    }


    .nav-list li {
        background-color: #e8f5e9;
        color: black;
        text-align:left;
        padding: 15px;
        margin: 5px 0;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        margin:12px;
    }

    .nav-list li:hover {
        background-color: #b9f6ca;
        color: #005c4b;
        transform: scale(1.03);
        transition: all 0.2s ease;
    }

    .nav-toggle {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 2em;
        cursor: pointer;
        margin-top: 10px;
    }

    .content {
        display:block;
        margin-left: 16.875em;
        padding: 20px;
        width: 100%;
        height:auto;
        overflow-y: hidden;
    }

    .dashboard-cards {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        padding-top: 10px;
    }

    .dashboard-card {
        background: #e8f5e9;
        padding: 15px;
        border-radius: 10px;
        width: 300px;
        text-align: center;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        margin:30px;
    }
    .dashboard-card h2 {
        font-size:20px;
        margin: 0 auto;
    }
    .dashboard-card h4{
        display: block; 
        margin: 25px;
    }
    .info-box {
        width: 10%;
        border:none;
        text-align: center;
        font-size: 20px;
        background-color: #e8f5e9;
        font-weight: bold;
    }
    .section{
        height:96%;
        flex-grow: 1;
        margin-left: 15px;
        padding: 20px;
        overflow-y: hidden;
        border: none; 
        border-radius: 5px;
        box-shadow: 1px 2px 15px #3D8D7A;
    }
    .section h1 {
        text-align:left;
        margin-left:30px;
    }
    
.recent-log{
   margin-top:20px;
   padding:15px;
   background: #e8f5e9;
   margin:auto;
   border-radius: 10px;
   text-align: center;
   box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.2);
   width: 100%;
}
.recent-log h2{
    color: #3D8D7A;
    margin-bottom:15px;
    font-size:22px;
}
.recent-log table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: #e8f5e9;
    margin:auto;
    border-radius: 10px;
    text-align: center;
    box-sizing: border-box;
    box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.2);
}
.recent-log th, .recent-log td {
    border: 1px solid #3D8D7A;
    padding: 12px;
    text-align: center;
    font-size: 16px;
}
.form-group {
    display: flex;
    flex-direction: column;
    align-items:center; 
    width: 400px; 
    margin-left: 12em;
    position:absolute;
    top:9em;
}

.form-group label{
    display:flex;
    color:black;
    font-weight: bold;
    padding:2.5px;
    text-align:left;
    width: 100%;
}
.form-group input,
.form-group button {
    width: 100%;
    padding: 7px;
    font-size: 16px;
    margin-bottom: 2px;
    border: 1px solid #3D8D7A;
    border-radius: 5px;
    text-transform: Capitalize;
}
.form-group button {
    background-color: #3D8D7A;
    color: white;
    font-weight: bold;
    cursor: pointer;
    display:block;
    margin:-9px;
}

.form-group button:hover {
    background-color: #2b6e5c;
}
.face-capture {
    display: flex;
    flex-direction: column;
    align-items: center;
    position:relative; 
    left:55em;
    top:1px;
    background: #e8f5e9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    margin: 30px;
    text-align: center;
}

.face-capture h2 {
    color: #3D8D7A;
    font-size: 22px;
    margin-bottom: 15px;
}

.face-capture video {
    width: 100%;
    max-width: 320px;
    height: auto;
    border: 2px solid #3D8D7A;
    border-radius: 8px;
    margin-bottom: 10px;
}
.captured-face img{
    width: 100%;
    max-width: 320px;
    height: auto;
    border: 2px solid #3D8D7A;
    border-radius: 8px;
    margin-bottom: 10px;
}
.face-button{
    align-items:center;
    color: white;
    border-radius:5px;
    display:flex;
    padding:10px;
}
.face-button button{
    background-color: #3D8D7A;
    margin-right:15px;
    margin-left:15px;
    width:120px;
    padding:10px;
    border:none;
    border-radius:5px;
    color: white;
    font-weight: bold;
}

.face-capture button:hover {
    background-color: #2b6e5c;
}

.face-capture canvas {
    display: none;
}
.captured-face {
    display: flex;
    flex-direction: column;
    align-items: center;
    position:relative; 
    left:55em;
    top:1px;
    background: #e8f5e9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    margin: 30px;
    text-align: center;
}
.captured-face image{

}

.captured-face h2{
    color: #3D8D7A;
    font-size: 22px;
    margin-bottom: 15px;
}
.caputured-face video{
    width: 100%;
    max-width: 320px;
    height: auto;
    border: 2px solid #3D8D7A;
    border-radius: 8px;
    margin-bottom: 10px;
}
@media screen and (max-width: 700px) {
    .nav-bar{
        position:fixed;
        height:100vh;
        width: min(13em, 100%);
        z-index: 10;
        left:-250px; 
        transition:left 0.5s ease;
    }
    .nav-bar.active {
        left: 0;
    }
    .nav-list li {
        padding: 10px;
        font-size: 13px;
        margin:12px;
    }
    .nav-bar p {
        font-size: 1.3em;
        margin-bottom:.3em;
        margin-top:6em;
    }
    .nav-bar img {
        position:fixed;
        margin-top:15px;
        width:8em;
        height:8em;
    }
    #close-nav{
        display:flex;
        font-size: 40px;
        font-weight:bold;
        position:relative;
        margin-right:-4.5em; 
        margin-top:-.4em;
        z-index: 1100;
    }
    #open-nav{
        display:block;
        position: fixed;
        top: 1.2em;
        left: 1.5em;
        font-size:24px;
    }
    .content {
        margin-left:-.8em;
        padding:1em;
        overflow:hidden;
        height:100dvh;
    }
    .section {
        box-sizing:border-box;
        overflow-y:scroll;
        border-radius: 15px;
    }
    .dashboard-card {
        padding: 20px;
        border-radius: 15px;
        min-width: 250px;
        margin:10px;
    }
    .dashboard-cards {
        display: flex;
        gap:0;
        padding-top:0;
    }
    .dashboard-card h2{
        font-size:18px;
    }
    .dashboard-card h4{
        font-size:14px;
        margin:12px;
    }
    .recent-log table {
        box-sizing:border-box;
        width: 100%;
    }
    .recent-log {
        border-radius: 15px;
        margin-top:30px;
        padding:5px;
    }
    .recent-log h2{
        margin-bottom:15px;
        font-size:18px;
    }
    .recent-log th, .recent-log td {
        font-size: 14px;
    }
    .section h1 {
        text-align:center;
        margin-left:0px;
        margin-bottom:-15px;
    }
    #home img {
        position: relative;
        margin-right:5px;
        bottom: 3.5em;
    }
    .notification-box{
        top: 50px;
        right:60px;
        width: 130px;
        border-radius: 10px;
        padding: 8px;
    }
    .notification-box h3 {
        font-size: 16px;
        margin-top: 10px;
        margin-bottom: 5px;
        margin-left: 10px;
    }
    .search-container{
        padding:2px;
        margin-top:30px;
        margin-bottom:-2.8em;
    }
    .search-section{
        padding: 2px;
    }
    .search-section img {
            width: 13px;
            height: 13px;
    }
    .search-section input {
        margin-left:-3px;
        padding: 4px;
        border-radius:3px;
        font-size: 12px;
        width: 110px;
    }
    .exportbtn button {
        padding: 4px;
        border-radius: 3px;
        font-size: 12px;
        margin-right: 2px;
    }
    .interns-container {
        margin-top: 50px;
        height:100%;
        max-height:40em;
        padding:10px;
    }   
    table, td, tbody, tr {
        display:block;
        width: auto;
        border-collapse: collapse;
        text-align: center;
        padding:8px;
        font-size:12px;
    }
    th{
        display:none;
    }
    
}
    </style>
    </html>
