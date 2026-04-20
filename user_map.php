<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - Maps</title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

<style>
  :root {
    --bg-color: #09090b; --panel-bg: #18181b; --border-color: #27272a;
    --text-main: #f4f4f5; --text-muted: #a1a1aa; --primary-green: #22c55e;
    --primary-red: #ef4444; --primary-blue: #3b82f6; --primary-gold: #eab308;
    --sidebar-width: 260px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

  /* Sidebar */
  .sidebar {
    width: var(--sidebar-width); background: var(--panel-bg); border-right: 1px solid var(--border-color);
    position: fixed; height: 100vh; display: flex; flex-direction: column; padding: 24px; z-index: 1000;
  }
  .brand { display: flex; align-items: center; gap: 12px; font-family: 'Instrument Serif', serif; font-size: 24px; margin-bottom: 40px; }
  .nav-menu { list-style: none; flex: 1; }
  .nav-menu a {
    display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none;
    padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; transition: 0.2s;
  }
  .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.05); color: var(--text-main); }
  .nav-menu a.active i { color: var(--primary-green); }
  .logout-btn { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; transition: 0.2s; margin-top: auto;}
  .logout-btn:hover { background: rgba(239,68,68,0.1); color: var(--primary-red); }

  /* Content */
  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; max-width: 1400px; display: flex; flex-direction: column; }
  
  .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;}
  .page-header h1 { font-family: 'Instrument Serif', serif; font-size: 36px; font-weight: 400; margin-bottom: 5px; }
  .page-header p { color: var(--text-muted); }

  /* Tabs */
  .tabs { display: flex; gap: 15px; background: var(--panel-bg); padding: 5px; border-radius: 12px; border: 1px solid var(--border-color); width: fit-content; }
  .tab-btn { background: transparent; color: var(--text-muted); border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.3s; }
  .tab-btn.active { background: var(--border-color); color: var(--text-main); }
  .tab-btn:hover:not(.active) { color: var(--text-main); }

  /* Tab Contents */
  .tab-content { display: none; flex: 1; min-height: 600px; }
  .tab-content.active { display: flex; flex-direction: column; }

  /* Indoor Map Panel */
  .map-panel { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 30px; flex: 1; display: flex; flex-direction: column; }
  .parking-box { display: grid; grid-template-columns: repeat(10, 1fr); gap: 15px; max-height: 500px; overflow-y: auto; padding-right: 10px; margin-bottom: 20px;}
  .parking-box::-webkit-scrollbar { width: 8px; }
  .parking-box::-webkit-scrollbar-track { background: var(--bg-color); border-radius: 4px; }
  .parking-box::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; }

  /* Indoor Slot Styles */
  .slot-card { background: var(--bg-color); border: 1px solid var(--border-color); padding: 15px 10px; border-radius: 12px; text-align: center; transition: all 0.3s ease; position: relative; }
  .slot-card .icon { font-size: 20px; margin-bottom: 8px; }
  .slot-id { font-size: 14px; font-weight: 600; margin-bottom: 5px; color: var(--text-main); }
  .plate { font-size: 11px; padding: 3px 6px; border-radius: 4px; background: rgba(255,255,255,0.1); display: inline-block; font-family: monospace; letter-spacing: 1px;}
  .available { border-color: var(--primary-green); }
  .available .icon { color: var(--primary-green); }
  .occupied { border-color: #3f3f46; background: rgba(255,255,255,0.02); opacity: 0.6; }
  .occupied .icon { color: #52525b; }
  .occupied .slot-id { color: #71717a; }
  .mine { border-color: var(--primary-blue); background: rgba(59,130,246,0.1); box-shadow: 0 0 15px rgba(59,130,246,0.3); opacity: 1; transform: scale(1.05); z-index: 10; animation: pulseBlue 2s infinite; }
  .mine .icon { color: #60a5fa; }
  .mine .plate { background: var(--primary-blue); color: white; font-weight: bold;}
  
  @keyframes pulseBlue {
    0% { box-shadow: 0 0 0px rgba(59,130,246,0.4); }
    50% { box-shadow: 0 0 20px rgba(59,130,246,0.6); }
    100% { box-shadow: 0 0 0px rgba(59,130,246,0.4); }
  }

  .legend { display: flex; gap: 20px; font-size: 14px; color: var(--text-muted); justify-content: center; padding-top: 20px; border-top: 1px solid var(--border-color); margin-top: auto;}
  .legend-item { display: flex; align-items: center; gap: 8px; }
  .dot { width: 12px; height: 12px; border-radius: 50%; }
  .dot.green { background: var(--primary-green); }
  .dot.grey { background: #52525b; }
  .dot.blue { background: var(--primary-blue); box-shadow: 0 0 8px var(--primary-blue); }

  /* City Map Styling */
  #cityMap { flex: 1; border-radius: 16px; border: 1px solid var(--border-color); background: #1a1a1a; min-height: 500px; z-index: 1;}
  
  /* Leaflet Dark Mode Customizations */
  .leaflet-popup-content-wrapper, .leaflet-popup-tip { background: var(--panel-bg); color: var(--text-main); border: 1px solid var(--border-color); }
  .leaflet-popup-content { margin: 15px; font-family: 'Inter', sans-serif;}
  .leaflet-container a.leaflet-popup-close-button { color: var(--text-muted); }
  
  .hub-title { font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--primary-gold); font-family: 'Instrument Serif', serif;}
  .hub-slots { font-size: 13px; color: var(--text-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 5px;}
  .hub-btn { display: block; background: var(--primary-green); color: #000; text-align: center; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px; transition: 0.2s;}
  .hub-btn:hover { background: #1da851; color: #000; }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <div class="page-header">
    <div>
      <h1>Interactive Maps</h1>
      <p>Find your parked car indoors, or locate nearby Smart Parking Hubs in the city.</p>
    </div>
    
    <div class="tabs">
      <button class="tab-btn active" onclick="switchTab('nearby')"><i class="fa fa-map"></i> Nearby Hubs</button>
      <button class="tab-btn" onclick="switchTab('indoor')"><i class="fa fa-building"></i> Indoor Floor Plan</button>
    </div>
  </div>

  <!-- NEARBY CITY MAP TAB -->
  <div id="tab-nearby" class="tab-content active">
    <div id="cityMap"></div>
  </div>

  <!-- INDOOR MAP TAB -->
  <div id="tab-indoor" class="tab-content">
    <div class="map-panel">
      <div class="parking-box" id="parkingBox">
        <!-- Slots will be loaded via JS -->
      </div>
      
      <div class="legend">
        <div class="legend-item"><span class="dot green"></span> Available</div>
        <div class="legend-item"><span class="dot grey"></span> Occupied (Others)</div>
        <div class="legend-item"><span class="dot blue"></span> Your Vehicle</div>
      </div>
    </div>
  </div>

</main>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
  // ---- Tab Logic ----
  function switchTab(tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    event.currentTarget.classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');

    // Leaflet needs this when container becomes visible
    if(tabId === 'nearby' && map) {
      setTimeout(() => { map.invalidateSize(); }, 100);
    }
  }

  // ---- Nearby Map (Leaflet) Logic ----
  // Initialize map centered on Pune, Maharashtra
  const map = L.map('cityMap').setView([18.5204, 73.8567], 12);

  // Use CartoDB Dark Matter tiles for premium dark aesthetic
  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    subdomains: 'abcd',
    maxZoom: 20
  }).addTo(map);

  // Custom marker icon (Golden Pin)
  const parkingIcon = L.divIcon({
    className: 'custom-pin',
    html: `<div style="background: var(--primary-gold); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 10px rgba(234,179,8,0.5); border: 2px solid #000;"><i class="fa fa-parking" style="color:#000; font-size:14px;"></i></div>`,
    iconSize: [30, 30],
    iconAnchor: [15, 15],
    popupAnchor: [0, -15]
  });

  // Pune Smart Parking Hubs Data
  const hubs = [
    { name: "Hinjewadi Tech Park Hub", lat: 18.5913, lng: 73.7389, slots: 45, ev: 12 },
    { name: "Shivaji Nagar Central Plaza", lat: 18.5314, lng: 73.8446, slots: 12, ev: 2 },
    { name: "Viman Nagar Airport Hub", lat: 18.5679, lng: 73.9143, slots: 104, ev: 30 },
    { name: "Magarpatta Cybercity", lat: 18.5157, lng: 73.9272, slots: 8, ev: 0 },
    { name: "Koregaon Park VIP Hub", lat: 18.5362, lng: 73.8939, slots: 25, ev: 5 }
  ];

  // Drop markers
  hubs.forEach(hub => {
    const popupContent = `
      <div class="hub-title">${hub.name}</div>
      <div class="hub-slots"><i class="fa fa-car" style="color:var(--text-muted)"></i> ${hub.slots} Standard Slots</div>
      <div class="hub-slots"><i class="fa fa-bolt" style="color:var(--primary-green)"></i> ${hub.ev} EV Chargers Available</div>
      <a href="book.php" class="hub-btn">Book Slot Here</a>
    `;

    L.marker([hub.lat, hub.lng], {icon: parkingIcon})
      .bindPopup(popupContent)
      .addTo(map);
  });


  // ---- Indoor Floor Plan Logic ----
  function loadIndoorMap(){
    // Only fetch if indoor tab is active to save resources
    if(!document.getElementById('tab-indoor').classList.contains('active')) return;

    fetch("get_user_map_data.php")
      .then(res => res.json())
      .then(data => {
        let html = "";
        data.forEach(slot => {
          let cardClass = slot.status;
          let iconClass = slot.status === 'available' ? 'fa-square-parking' : 'fa-car';
          let displayData = "";

          if(slot.status === 'occupied') {
            if(slot.is_mine) {
              cardClass = "mine";
              displayData = `<div class="plate">${slot.vehicle_no}</div>`;
            } else {
              displayData = `<div class="plate" style="color:transparent; background:rgba(255,255,255,0.05);">XXX</div>`;
            }
          } else {
              displayData = `<div class="plate" style="visibility:hidden;">XXX</div>`;
          }

          html += `
          <div class="slot-card ${cardClass}">
            <div class="icon"><i class="fa-solid ${iconClass}"></i></div>
            <div class="slot-id">Slot ${slot.slot_id}</div>
            ${displayData}
          </div>
          `;
        });
        document.getElementById("parkingBox").innerHTML = html;
      }).catch(e => console.log("Fetch error: ", e));
  }

  loadIndoorMap();
  setInterval(loadIndoorMap, 3000);

</script>
</body>
</html>
