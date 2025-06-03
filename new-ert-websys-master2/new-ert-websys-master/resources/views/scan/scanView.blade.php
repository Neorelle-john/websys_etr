<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daily Attendance Scanner</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f0f2f5;
      color: #333;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 40px 20px;
      text-align: center;
    }

    .header img {
      max-height: 100px;
      margin-bottom: 20px;
    }

    .header h1 {
      font-size: 2rem;
      color: #003366;
      margin-bottom: 10px;
    }

    .header h2 {
      font-size: 1.3rem;
      color: #0056b3;
      margin-bottom: 30px;
    }

    button {
      background-color: #0056b3;
      color: #fff;
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      width: 100%;
      max-width: 200px;
      margin: 20px 0;
    }

    button:hover {
      background-color: #003f88;
    }

    #reader {
      display: none;
      margin: 30px auto;
      padding: 20px;
      max-width: 600px;
      width: 100%;
      background: #fff;
      border-radius: 12px;
      border: 2px dashed #ccc;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    #employee-info {
      display: none;
      margin-top: 30px;
      text-align: left;
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      font-size: 1rem;
    }

    #employee-info h2 {
      text-align: center;
      color: #003366;
      margin-bottom: 20px;
      font-size: 1.5rem;
    }

    img.photo {
      display: block;
      margin: 0 auto 20px;
      max-width: 150px;
      border-radius: 10px;
      border: 2px solid #ccc;
    }

    #employee-info strong {
      display: inline-block;
      width: 180px;
      color: #0056b3;
      font-weight: 600;
      margin-bottom: 8px;
    }

    #employee-info span {
      font-weight: 400;
      color: #555;
    }

    hr {
      border: none;
      border-top: 1px solid #ddd;
      margin: 20px 0;
    }

    #error-message {
      color: #d9534f;
      font-weight: 600;
      margin-top: 20px;
    }

    @media (max-width: 600px) {
      .container {
        padding: 20px;
      }

      button {
        width: 100%;
      }

      #employee-info strong {
        width: 150px;
      }

      .header h1 {
        font-size: 1.5rem;
      }

      #employee-info {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="{{ asset('images/psulogo.png') }}" alt="PSU Logo">
      <h1>Pangasinan State University</h1>
      <h2>Urdaneta City Campus - Daily Attendance Log</h2>
    </div>

    <button onclick="startScanner()">Start QR Scan</button>
    <div id="error-message"></div>

    <div id="reader"></div>

    <div id="employee-info">
      <h2>Employee Attendance</h2>
      <img id="empPhoto" class="photo" src="" alt="Employee Photo">
      <div>
        <strong>Name:</strong> <span id="empName"></span>
      </div>
      <div>
        <strong>ID Number:</strong> <span id="empID"></span>
      </div>
      <div>
        <strong>Classification:</strong> <span id="empClass"></span>
      </div>
      <div>
        <strong>College:</strong> <span id="empCollege"></span>
      </div>
      <hr>
      <?php 
        $date = new DateTime("now", new DateTimeZone('Asia/Manila'));
        $currentDate = $date->format('F j, Y');
        echo "<strong>Date:</strong> " . $currentDate . "<br>";
      ?>
      <div>
        <strong>AM Time In:</strong> <span id="amIn"></span>
      </div>
      <div>
        <strong>AM Time Out:</strong> <span id="amOut"></span>
      </div>
      <div>
        <strong>PM Time In:</strong> <span id="pmIn"></span>
      </div>
      <div>
        <strong>PM Time Out:</strong> <span id="pmOut"></span>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/html5-qrcode"></script>
  <script>
    let html5QrCode = null;
    let scanning = false;

    function startScanner() {
      if (scanning) return;

      scanning = true;
      document.querySelector("button").style.display = "none";
      document.getElementById("error-message").textContent = "";
      document.getElementById("reader").style.display = "block";

      html5QrCode = new Html5Qrcode("reader");

      html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 300 },
        (decodedText) => {
          html5QrCode.stop().then(() => {
            document.getElementById("reader").style.display = "none";
          });

          try {
            const data = JSON.parse(decodedText);
            sendToServer(data.id_number);
          } catch (e) {
            document.getElementById("error-message").textContent = "Invalid QR code content.";
            resetUIAfterScan();
          }
        },
        (errorMessage) => {
          console.warn(errorMessage);
        }
      ).catch(err => {
        document.getElementById("error-message").textContent = "Camera initialization failed.";
        resetUIAfterScan();
      });
    }

    function sendToServer(idNumber) {
      fetch('/save-attendance', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id_number: idNumber })
      })
      .then(response => response.json())
      .then(data => {
        if (data.employee) {
          document.getElementById('empPhoto').src = data.employee.picture_path || '';
          document.getElementById('empName').textContent = data.employee.name || '—';
          document.getElementById('empID').textContent = data.employee.id_number || '—';
          document.getElementById('empClass').textContent = data.employee.classification || '—';
          document.getElementById('empCollege').textContent = data.employee.college || '—';

          document.getElementById('amIn').textContent = data.attendance.am_time_in || '—';
          document.getElementById('amOut').textContent = data.attendance.am_time_out || '—';
          document.getElementById('pmIn').textContent = data.attendance.pm_time_in || '—';
          document.getElementById('pmOut').textContent = data.attendance.pm_time_out || '—';

          document.getElementById('employee-info').style.display = 'block';

          setTimeout(() => {
            resetUIAfterScan();
          }, 8000);
        } else {
          document.getElementById("error-message").textContent = data.message || "Employee not found.";
          resetUIAfterScan();
        }
      });
    }

    function resetUIAfterScan() {
      scanning = false;
      if (html5QrCode) {
        html5QrCode.clear();
      }
      document.getElementById("employee-info").style.display = "none";
      document.querySelector("button").style.display = "inline-block";
      document.getElementById("reader").style.display = "none";
    }
  </script>
</body>
</html>
