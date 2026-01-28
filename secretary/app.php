<?php
session_start();

//if (!isset($_SESSION['secretary_logged_in']) || $_SESSION['secretary_logged_in'] !== true) {
  //  header('Location: index.php');
    //exit;
//}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Secretary Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8fafc;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #1d4ed8;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    header h1 {
      font-size: 22px;
      margin: 0;
    }

    main {
      padding: 20px;
      max-width: 1000px;
      margin: 0 auto;
    }

    #calendar {
      background: white;
      padding: 10px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    #statusModal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.4);
    }

    #statusModal .modal-content {
      background: white;
      padding: 20px;
      margin: 10% auto;
      width: 90%;
      max-width: 400px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.25);
    }

    #statusModal .modal-content h3 {
      margin-top: 0;
    }

    #statusModal .modal-content p {
      margin: 5px 0;
    }

    #statusModal select,
    #statusModal button {
      width: 100%;
      padding: 8px;
      margin-top: 10px;
      font-size: 16px;
    }

    .logout-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 10px 16px;
      background-color: #1d4ed8;
      border: none;
      color: white;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      transition: background-color 0.3s ease, transform 0.2s ease;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    .logout-btn:hover {
      background-color: #1743c1;
      transform: translateY(-1px);
    }

    .logout-btn svg {
      width: 18px;
      height: 18px;
      fill: white;
    }

   
    @media (max-width: 900px) {
      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }

      main {
        padding: 10px;
      }
    }

    @media (max-width: 600px) {
      header h1 {
        font-size: 18px;
      }

      .logout-btn {
        font-size: 13px;
        padding: 8px 12px;
      }

      #calendar {
        padding: 5px;
      }

      .modal-content {
        margin: 20% auto !important;
      }
    }
  </style>
</head>
<body>

<header>
  <h1>Secretary Dashboard</h1>
  <div style="display: flex; gap: 10px;">
    <a href="change_password.php" class="logout-btn" title="Change Password"> Change Password
    </a>
    <a href="logout_sec.php" class="logout-btn" title="Logout">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 19v-14H4v14h16zM4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/>
      </svg>
      Logout
    </a>
  </div>
</header>


<main>
  <h2>📅 Appointment Calendar</h2>
  <div id="calendar"></div>
</main>

<div id="statusModal">
  <div class="modal-content">
    <h3>Appointment Details</h3>
    <p><strong>Doctor:</strong> <span id="modalDoctor"></span></p>
    <p><strong>Patient:</strong> <span id="modalPatient"></span></p>
    <p><strong>Date:</strong> <span id="modalDate"></span></p>
    <p><strong>start Time:</strong> <span id="modalTime"></span></p>
    <p><strong>End Time:</strong> <span id="modalTime"></span></p>
    
    <form id="statusForm">
      <input type="hidden" id="appointmentId" name="id" />
      <label for="statusSelect"><strong>Status:</strong></label>
      <select id="statusSelect" name="status" required>
        <option value="scheduled">Scheduled</option>
        <option value="visited">Visited</option>
        <option value="missed">Missed</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <button type="submit">Save</button>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    const appointmentId = document.getElementById('appointmentId');
    const statusSelect = document.getElementById('statusSelect');
    const modalDoctor = document.getElementById('modalDoctor');
    const modalPatient = document.getElementById('modalPatient');
    const modalDate = document.getElementById('modalDate');
    const modalTime = document.getElementById('modalTime');

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: 'fetch_events.php',
      eventClick: function(info) {
        const event = info.event;
        appointmentId.value = event.id;
        statusSelect.value = event.extendedProps.status || 'scheduled';

        modalDoctor.textContent = event.extendedProps.doctor_name;
        modalPatient.textContent = event.extendedProps.patient_name;

        const startDate = new Date(event.start);
        modalDate.textContent = startDate.toLocaleDateString();
        modalTime.textContent = startDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

        modal.style.display = 'block';
      }
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      fetch('update_status1.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: id=${appointmentId.value}&status=${statusSelect.value}
      })
      .then(response => response.text())
      .then(data => {
        modal.style.display = 'none';
        calendar.refetchEvents();
        alert("Status updated successfully!");
      })
      .catch(error => {
        alert("Error updating status.");
      });
    });

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    };

    calendar.render();
  });
</script>

</body>
</html>