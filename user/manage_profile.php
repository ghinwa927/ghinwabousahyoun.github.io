<?php
session_start();
// require 'config.php'; // Assuming config.php handles database connection or other setup
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile - <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="manage_profile.css">
</head>
<body>
    <header class="main-header">
        <h1>Hello, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>!</h1>
        <a href="user-dashboard.php" id="homeIcon" class="home-icon" title="Go to Dashboard"><i class="fas fa-home"></i></a>
    </header>

    <div class="container-flex">
        <div class="sidebar">
            <h3>Profile Sections</h3>
            <nav class="sidebar-nav">
                <a href="#" id="personalInfoBtn"><i class="fas fa-user-circle"></i>Personal Info</a>
                <a href="#" id="medicalInfoBtn"><i class="fas fa-briefcase-medical"></i>Medical Info</a>
                <a href="#" id="passwordBtn"><i class="fas fa-key"></i>Update Password</a>
            </nav>
        </div>
        <div class="content-area" id="contentArea">
            </div>
    </div>

    <script>
    $(document).ready(function() {
        // Function to set the active state for sidebar links
        function setActiveLink(clickedLink) {
            $('.sidebar-nav a').removeClass('active'); // Remove active class from all links
            if (clickedLink) {
                $(clickedLink).addClass('active'); // Add active class to the clicked link
            }
        }

        // Function to load default content
        function loadDefaultContent() {
            $('#contentArea').html(`
                <div class="content-placeholder">
                    <i class="fas fa-info-circle"></i>
                    <p>Select a section from the sidebar to view or manage your information.</p>
                </div>
            `);
            setActiveLink(null); // No link is active by default
        }

        // Load content and handle back button
        function loadContent(url, clickedLink) {
            $('#contentArea').fadeOut(200, function() {
                $(this).load(url, function(response, status, xhr) {
                    if (status == "error") {
                        $(this).html(`<div class="content-placeholder"><i class="fas fa-exclamation-triangle"></i><p>Sorry, there was an error loading the content: ${xhr.status} ${xhr.statusText}</p></div>`);
                    } else {
                        // Re-attach back button functionality if a #backBtn exists in loaded content
                        // It's better if loaded content has its own script or uses event delegation
                        $('#backBtn').off('click').on('click', function(e) {
                            e.preventDefault();
                            loadDefaultContent();
                        });
                    }
                    $(this).fadeIn(200);
                });
            });
            setActiveLink(clickedLink);
        }

        $('#personalInfoBtn').click(function(e) {
            e.preventDefault(); // Prevent default anchor behavior
            loadContent('fetch_personal_info.php', this);
        });

        $('#medicalInfoBtn').click(function(e) {
            e.preventDefault();
            loadContent('fetch_medical_info.php', this);
        });

        $('#passwordBtn').click(function(e) {
            e.preventDefault();
            loadContent('changepassword.php', this);
        });

        // Load default content initially
        loadDefaultContent();
    });
    </script>
</body>
</html>
