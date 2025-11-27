<!DOCTYPE html>
<html lang="fi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kalenteri</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <!--header-->
  <header>
    <nav>
      <ul>
        <li><a href="#">Etusivu</a></li>
        <li><a href="#">Tietoa</a></li>
        <li><a href="#">Yhteystiedot</a></li>
      </ul>
    </nav>
  </header>

  <!--Formi-->
  <h2>Tapahtuma</h2>
  <form id="eventForm" action="save_event.php" method="post">
    <input type="text" name="nimi" placeholder="Tapahtuman nimi" required>
    <input type="datetime-local" name="aika" required>
    <textarea name="kuvaus" rows="4" placeholder="Anna kuvaus tai lisää muistiinpanoja"></textarea>
    <select name="kategoria" required>
      <option value="" disabled selected>Valitse kategoria</option>
      <option value="koulu">Koulu</option>
      <option value="työ">Työ</option>
      <option value="harrastus">Harrastus</option>
      <option value="juhla">Juhla</option>
      <option value="muu">Muu</option>
    </select>
    <button type="submit">Tallenna tapahtuma</button>
  </form>

 
  <h2>Muistutuskalenteri</h2>
  <!--kalenteri muutto-->
  <div style="text-align:center; margin-bottom:10px; margin: 0 25%;">
    <label for="month">Kuukausi:</label>
    <select id="month">
      <option value="0">Tammikuu</option>
      <option value="1">Helmikuu</option>
      <option value="2">Maaliskuu</option>
      <option value="3">Huhtikuu</option>
      <option value="4">Toukokuu</option>
      <option value="5">Kesäkuu</option>
      <option value="6">Heinäkuu</option>
      <option value="7">Elokuu</option>
      <option value="8">Syyskuu</option>
      <option value="9">Lokakuu</option>
      <option value="10" selected>Marraskuu</option>
      <option value="11">Joulukuu</option>
    </select>

    <label for="year">Vuosi:</label>
    <select id="year"></select>

    <button class="showBtn" id="showBtn">Näytä kalenteri</button>
  </div>

  <div class="calendar" id="calendar"></div>

  <!-- Popup -->
  <div class="overlay" id="overlay"></div>
  <div class="popup" id="popup">
    <div class="popup-content">
      <h3>Lisää muistutus</h3>
      <p id="selectedDate"></p>
      <input type="text" id="reminderInput" placeholder="Kirjoita muistutus">
      <div>
        <button onclick="saveReminder()">Tallenna</button>
        <button onclick="closePopup()">Peruuta</button>
      </div>
    </div>
  </div>

  <!-- FOOTTERI -->
  <footer>
    <p>&copy; 2025 Muistutuskalenteri.</p>
    <p>Suunnitellut: <strong>Lennu Kalle</strong></p>
    <p>
      <a href="#">Tietosuojaseloste</a>
      <a href="#">Käyttöehdot</a>
      <a href="#">Ota yhteyttä</a>
    </p>
  </footer>

  <!--kalenterin scripti-->
  <script>
    const calendar = document.getElementById("calendar");
    const yearSelect = document.getElementById("year");
    const currentYear = new Date().getFullYear();

    
    for (let y = currentYear - 5; y <= currentYear + 5; y++) {
      const opt = document.createElement("option");
      opt.value = y;
      opt.textContent = y;
      if (y === 2025) opt.selected = true;
      yearSelect.appendChild(opt);
    }

    let serverEvents = [];

    
    async function loadEvents() {
      const res = await fetch("get_events.php");
      if (!res.ok) {
        console.error("Tapahtumien haku epäonnistui:", res.status, await res.text());
        return;
      }
      serverEvents = await res.json();
    }

    function generateCalendar(year, month) {
      calendar.innerHTML = "";
      const daysInMonth = new Date(year, month + 1, 0).getDate();

      for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement("div");
        cell.className = "day";
        cell.innerHTML = `<strong>${day}.${month + 1}.${year}</strong><div id="day-${year}-${month}-${day}"></div>`;
        calendar.appendChild(cell);
      }

      
      serverEvents.forEach(event => {
        const dateStr = (event.aika || "").replace(' ', 'T'); // "YYYY-MM-DD HH:MM:SS" → "YYYY-MM-DDTHH:MM:SS"
        const eventDate = new Date(dateStr);
        if (isNaN(eventDate)) return;

        if (eventDate.getFullYear() === year && eventDate.getMonth() === month) {
          const day = eventDate.getDate();
          const container = document.getElementById(`day-${year}-${month}-${day}`);
          if (container) {
            const item = document.createElement("div");
            item.innerHTML = `
              <em>${event.nimi}</em><br>
              ${event.kuvaus ? event.kuvaus : ''}<br>
              <small>${event.kategoria}</small><br>
              <form action="delete_event.php" method="post" style="margin-top:6px;">
                <input type="hidden" name="id" value="${event.id}">
                <button type="submit">Poista</button>
              </form>
            `;
            container.appendChild(item);
          }
        }
      });
    }

    async function updateCalendar() {
      const selectedYear = parseInt(document.getElementById("year").value);
      const selectedMonth = parseInt(document.getElementById("month").value);
      await loadEvents();
      generateCalendar(selectedYear, selectedMonth);
    }

    
    document.getElementById("showBtn").addEventListener("click", (e) => {
      e.preventDefault();
      updateCalendar();
    });

    
    function openPopup() {
      document.getElementById('overlay').style.display = 'flex';
      document.getElementById('popup').style.display = 'flex';
    }
    function closePopup() {
      document.getElementById('overlay').style.display = 'none';
      document.getElementById('popup').style.display = 'none';
    }
    function saveReminder() { closePopup(); }

    
    updateCalendar();

    
    async function updateCalendar() {
  const selectedYear = parseInt(document.getElementById("year").value);
  const selectedMonth = parseInt(document.getElementById("month").value);
  await loadEvents();
  generateCalendar(selectedYear, selectedMonth);
  checkUpcomingEvents(); 
}

function showNotificationPopup(message) {
  const titleEl = document.querySelector('#popup .popup-content h3');
  const dateEl = document.getElementById('selectedDate');
  const inputEl = document.getElementById('reminderInput');
  const btnContainer = inputEl.nextElementSibling;

  
  titleEl.textContent = 'Muistutus';
  dateEl.textContent = message;
  inputEl.style.display = 'none';
  btnContainer.innerHTML = '<button id="notifOk">OK</button>';

  document.getElementById('notifOk').onclick = () => {
    
    titleEl.textContent = 'Lisää muistutus';
    dateEl.textContent = '';
    inputEl.value = '';
    inputEl.style.display = '';
    btnContainer.innerHTML = `
      <button onclick="saveReminder()">Tallenna</button>
      <button onclick="closePopup()">Peruuta</button>
    `;
    closePopup();
  };

  openPopup();
}


function checkUpcomingEvents() {
  const now = new Date();
  const next24h = new Date(now.getTime() + 24 * 60 * 60 * 1000);

  serverEvents.forEach(event => {
    const eventDate = new Date((event.aika || "").replace(' ', 'T'));
    if (eventDate > now && eventDate <= next24h) {
      const when = eventDate.toLocaleString();
      showNotificationPopup(`"${event.nimi}" on tulossa ${when}`);
    }
  });
}

  </script>
</body>
</html>