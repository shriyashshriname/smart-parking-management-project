<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';

// Data
$total = $conn->query("SELECT COUNT(*) as total FROM slots")->fetch_assoc()['total'];
$available = $conn->query("SELECT COUNT(*) as available FROM slots WHERE status='available'")->fetch_assoc()['available'];
$occupied = $conn->query("SELECT COUNT(*) as occupied FROM slots WHERE status='occupied'")->fetch_assoc()['occupied'];

$slots = $conn->query("SELECT * FROM slots");
$vehicles = $conn->query("SELECT * FROM vehicles WHERE exit_time IS NULL");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128/examples/js/controls/OrbitControls.js"></script>

<style>
  :root {
    --bg-color: #09090b;
    --panel-bg: #18181b;
    --border-color: #27272a;
    --text-main: #f4f4f5;
    --text-muted: #a1a1aa;
    --primary-green: #22c55e;
    --primary-red: #ef4444;
    --primary-blue: #3b82f6;
    --primary-gold: #eab308;
    --sidebar-width: 260px;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Inter', sans-serif;
    background-color: var(--bg-color);
    color: var(--text-main);
    display: flex;
    min-height: 100vh;
  }

  /* Sidebar */
  .sidebar {
    width: var(--sidebar-width);
    background: var(--panel-bg);
    border-right: 1px solid var(--border-color);
    position: fixed;
    height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 24px;
    z-index: 100;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 12px;
    font-family: 'Instrument Serif', serif;
    font-size: 24px;
    color: var(--text-main);
    margin-bottom: 40px;
  }

  .nav-menu {
    list-style: none;
    flex: 1;
  }

  .nav-menu li {
    margin-bottom: 8px;
  }

  .nav-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-muted);
    text-decoration: none;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.2s;
  }

  .nav-menu a:hover, .nav-menu a.active {
    background: rgba(255, 255, 255, 0.05);
    color: var(--text-main);
  }

  .nav-menu a.active i {
    color: var(--primary-green);
  }

  .logout-btn {
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-muted);
    text-decoration: none;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
  }

  .logout-btn:hover {
    background: rgba(239, 68, 68, 0.1);
    color: var(--primary-red);
  }

  /* Main Content */
  .content {
    flex: 1;
    margin-left: var(--sidebar-width);
    padding: 32px 48px;
    max-width: 1400px;
  }

  /* Header */
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }

  .search-bar {
    display: flex;
    align-items: center;
    background: var(--panel-bg);
    border: 1px solid var(--border-color);
    padding: 10px 16px;
    border-radius: 8px;
    width: 300px;
  }

  .search-bar i {
    color: var(--text-muted);
    margin-right: 10px;
  }

  .search-bar input {
    background: transparent;
    border: none;
    outline: none;
    color: var(--text-main);
    width: 100%;
    font-size: 14px;
  }

  .user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #3f3f46;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
  }

  /* Welcome Banner */
  .welcome-banner {
    position: relative;
    padding: 40px;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 32px;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  }

  .welcome-banner-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('banner_bg.png') center/cover no-repeat;
    opacity: 0.7;
    z-index: 0;
  }

  .welcome-banner-content {
    position: relative;
    z-index: 1;
  }

  .welcome-banner h2 {
    font-family: 'Instrument Serif', serif;
    font-size: 40px;
    font-weight: 400;
    margin-bottom: 8px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
  }

  .welcome-banner p {
    color: #e4e4e7;
    font-size: 16px;
    text-shadow: 0 1px 5px rgba(0,0,0,0.5);
  }

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: var(--panel-bg);
    border: 1px solid var(--border-color);
    padding: 24px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
  }

  .stat-info h3 {
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
  }

  .stat-info h2 {
    font-size: 32px;
    font-weight: 600;
  }

  .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
  }

  .icon-total { background: rgba(59, 130, 246, 0.1); color: var(--primary-blue); }
  .icon-avail { background: rgba(34, 197, 94, 0.1); color: var(--primary-green); }
  .icon-occ { background: rgba(239, 68, 68, 0.1); color: var(--primary-red); }

  /* Section Wrappers */
  .grid-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 32px;
  }

  .panel {
    background: var(--panel-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 24px;
  }

  .panel h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* Types Grid */
  .types-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  }

  .type-card {
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--border-color);
    padding: 16px;
    text-align: center;
    border-radius: 12px;
    transition: background 0.2s;
  }
  .type-card:hover { background: rgba(255,255,255,0.05); }
  .type-card i { font-size: 20px; margin-bottom: 8px; color: var(--text-muted); }
  .type-card div { font-size: 13px; font-weight: 500; }

  /* Parking Grid Container */
  .parking-box {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 12px;
    max-height: 350px;
    overflow-y: auto;
    padding-right: 5px;
  }

  /* Scrollbar */
  .parking-box::-webkit-scrollbar { width: 6px; }
  .parking-box::-webkit-scrollbar-track { background: var(--bg-color); border-radius: 4px; }
  .parking-box::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; }

  .slot-card {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    padding: 12px;
    border-radius: 10px;
    text-align: center;
    transition: all 0.3s ease;
  }

  .slot-card:hover { transform: scale(1.05); z-index: 10; position: relative; }

  .slot-card .icon { font-size: 18px; margin-bottom: 6px; }
  .slot-id { font-size: 13px; font-weight: 600; margin-bottom: 4px; }
  .badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-weight: 600; display: inline-block; }

  /* Slot Status Colors */
  .available { border-color: var(--primary-green); box-shadow: 0 0 10px rgba(34,197,94,0.1); }
  .available .icon { color: var(--primary-green); }
  .occupied { border-color: var(--primary-red); background: rgba(239,68,68,0.05); }
  .occupied .icon { color: var(--primary-red); }

  /* Animations */
  .pulse-green { animation: pulseGreen 0.8s ease; }
  @keyframes pulseGreen { 0% { box-shadow: 0 0 0px var(--primary-green); } 50% { box-shadow: 0 0 20px var(--primary-green); } 100% { box-shadow: 0 0 10px rgba(34,197,94,0.1); } }
  .flash-red { animation: flashRed 0.8s ease; }
  @keyframes flashRed { 0% { background: var(--primary-red); } 50% { background: #7f1d1d; } 100% { background: rgba(239,68,68,0.05); } }

  /* Badges */
  .vip .badge { background: rgba(234, 179, 8, 0.2); color: var(--primary-gold); }
  .ev .badge { background: rgba(34, 197, 94, 0.2); color: var(--primary-green); }
  .suv .badge { background: rgba(59, 130, 246, 0.2); color: var(--primary-blue); }
  .normal .badge { background: rgba(161, 161, 170, 0.2); color: var(--text-muted); }

  /* Table */
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th { text-align: left; padding: 12px; font-size: 13px; color: var(--text-muted); font-weight: 500; border-bottom: 1px solid var(--border-color); }
  td { padding: 12px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.03); }
  tr:hover td { background: rgba(255,255,255,0.02); }

  /* Legend */
  .legend { display: flex; gap: 16px; margin-top: 20px; font-size: 13px; color: var(--text-muted); flex-wrap: wrap;}
  .legend-item { display: flex; align-items: center; gap: 6px; }
  .dot { width: 10px; height: 10px; border-radius: 50%; }
  .dot.green { background: var(--primary-green); box-shadow: 0 0 5px var(--primary-green); }
  .dot.red { background: var(--primary-red); box-shadow: 0 0 5px var(--primary-red); }
  .dot.gold { background: var(--primary-gold); }
  .dot.blue { background: var(--primary-blue); }

  /* 3D Map */
  #threeContainer {
    width: 100%;
    height: 350px;
    border-radius: 12px;
    overflow: hidden;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .grid-layout { grid-template-columns: 1fr; }
    .parking-box { grid-template-columns: repeat(6, 1fr); }
  }
</style>
</head>

<body>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="content">
  
  <!-- Header -->
  <header class="header">
    <div class="search-bar">
      <i class="fa fa-search"></i>
      <input type="text" placeholder="Search for vehicle, slot ID...">
    </div>
    
    <div class="user-profile">
      <div class="avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
      <span><?php echo $_SESSION['name']; ?></span>
    </div>
  </header>

  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <div class="welcome-banner-bg"></div>
    <div class="welcome-banner-content">
      <h2>Welcome back, <?php echo explode(' ', trim($_SESSION['name']))[0]; ?>!</h2>
      <p>Here is the live status of your parking facilities today.</p>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info">
        <h3>Total Capacity</h3>
        <h2><?php echo $total; ?></h2>
      </div>
      <div class="stat-icon icon-total"><i class="fa fa-layer-group"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>Available Slots</h3>
        <h2 style="color: var(--primary-green);"><?php echo $available; ?></h2>
      </div>
      <div class="stat-icon icon-avail"><i class="fa fa-check-circle"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>Occupied Slots</h3>
        <h2 style="color: var(--primary-red);"><?php echo $occupied; ?></h2>
      </div>
      <div class="stat-icon icon-occ"><i class="fa fa-car"></i></div>
    </div>
  </div>

  <!-- Grid Layout -->
  <div class="grid-layout">
    
    <!-- Left Column -->
    <div class="col-left">
      <!-- 2D Interactive Map -->
      <div class="panel" style="margin-bottom: 24px;">
        <h3><i class="fa fa-table-cells-large"></i> Live Floor Plan</h3>
        <div class="parking-box" id="parkingBox"></div>
        
        <div class="legend">
          <div class="legend-item"><span class="dot green"></span> Available</div>
          <div class="legend-item"><span class="dot red"></span> Occupied</div>
          <div class="legend-item" style="margin-left: 10px;"><span class="dot" style="background:#a1a1aa"></span> Normal</div>
          <div class="legend-item"><span class="dot gold"></span> VIP</div>
          <div class="legend-item"><span class="dot green"></span> EV</div>
          <div class="legend-item"><span class="dot blue"></span> SUV</div>
        </div>
      </div>

      <!-- Chart -->
      <div class="panel">
        <h3><i class="fa fa-chart-area"></i> Occupancy Forecast</h3>
        <canvas id="chart" height="100"></canvas>
      </div>
    </div>

    <!-- Right Column -->
    <div class="col-right">
      
      <!-- Slot Types Breakdown -->
      <div class="panel" style="margin-bottom: 24px;">
        <h3><i class="fa fa-tags"></i> Slot Zones</h3>
        <div class="types-grid">
          <div class="type-card"><i class="fa fa-car"></i><br><div>Normal</div></div>
          <div class="type-card"><i class="fa fa-crown" style="color:var(--primary-gold)"></i><br><div>VIP</div></div>
          <div class="type-card"><i class="fa fa-bolt" style="color:var(--primary-green)"></i><br><div>EV</div></div>
          <div class="type-card"><i class="fa fa-truck" style="color:var(--primary-blue)"></i><br><div>SUV</div></div>
        </div>
      </div>

      <!-- 3D Map -->
      <div class="panel" style="margin-bottom: 24px;">
        <h3><i class="fa fa-cube"></i> 3D Visualization</h3>
        <div id="threeContainer"></div>
      </div>

      <!-- Recent Vehicles -->
      <div class="panel">
        <h3><i class="fa fa-clock-rotate-left"></i> Recent Entries</h3>
        <table>
          <tr>
            <th>License Plate</th>
            <th>Slot ID</th>
            <th>Entry Time</th>
          </tr>
          <?php while($v = $vehicles->fetch_assoc()){ ?>
          <tr>
            <td style="font-family: monospace; font-size:15px; font-weight:bold; letter-spacing:1px;"><?php echo $v['vehicle_no']; ?></td>
            <td><span class="badge" style="background:rgba(255,255,255,0.1); color:var(--text-main);"><?php echo $v['slot_id']; ?></span></td>
            <td style="color:var(--text-muted); font-size:13px;"><?php echo date('H:i', strtotime($v['entry_time'])); ?></td>
          </tr>
          <?php } ?>
        </table>
      </div>

    </div>

  </div>

</main>

<script>
// Chart Setup
Chart.defaults.color = '#a1a1aa';
Chart.defaults.font.family = 'Inter';

new Chart(document.getElementById("chart"), {
  type: 'line',
  data: {
    labels: ["00:00","02:00","04:00","06:00","08:00","10:00","12:00","14:00","16:00","18:00","20:00","22:00"],
    datasets: [{
      label: 'Predicted Occupancy %',
      data: [20,15,25,50,80,60,55,70,90,85,60,40],
      borderColor: "#22c55e",
      backgroundColor: "rgba(34, 197, 94, 0.1)",
      borderWidth: 2,
      fill: true,
      tension: 0.4,
      pointRadius: 3,
      pointBackgroundColor: "#22c55e"
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: "#27272a", drawBorder: false } },
      y: { grid: { color: "#27272a", drawBorder: false }, beginAtZero: true, max: 100 }
    }
  }
});

// 2D Grid Logic
let previousSlots = {};

function getType(slot_id){
  if(slot_id <= 5) return "vip";
  if(slot_id <= 15) return "normal";
  if(slot_id <= 23) return "ev";
  return "suv";
}

function getIcon(type){
  if(type=="vip") return "fa-crown";
  if(type=="ev") return "fa-bolt";
  if(type=="suv") return "fa-truck";
  return "fa-car";
}

function loadSlots(){
  fetch("get_slots.php")
    .then(res => res.json())
    .then(data => {
      let html = "";
      data.forEach(slot => {
        let type = getType(slot.slot_id);
        let icon = getIcon(type);
        let animationClass = "";

        if(previousSlots[slot.slot_id]){
          if(previousSlots[slot.slot_id] !== slot.status){
            animationClass = slot.status === "available" ? "pulse-green" : "flash-red";
          }
        }
        previousSlots[slot.slot_id] = slot.status;

        html += `
        <div class="slot-card ${slot.status} ${type} ${animationClass}">
          <div class="icon"><i class="fa-solid ${icon}"></i></div>
          <div class="slot-id">${slot.slot_id}</div>
          <div class="badge">${type}</div>
        </div>
        `;
      });
      document.getElementById("parkingBox").innerHTML = html;
    }).catch(e => console.log("Fetch error: ", e));
}

loadSlots();
setInterval(loadSlots, 2000);

// 3D Map Logic
const container = document.getElementById("threeContainer");
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
renderer.setSize(container.clientWidth, container.clientHeight);
renderer.setClearColor(0x09090b);
container.appendChild(renderer.domElement);

camera.position.set(0, 15, 25);
camera.lookAt(0, 0, 0);

const controls = new THREE.OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.dampingFactor = 0.05;

const light = new THREE.DirectionalLight(0xffffff, 1);
light.position.set(10, 20, 10);
scene.add(light);
scene.add(new THREE.AmbientLight(0x404040));

let plane = new THREE.Mesh(
  new THREE.PlaneGeometry(100, 100),
  new THREE.MeshStandardMaterial({ color: 0x18181b, roughness: 0.8 })
);
plane.rotation.x = -Math.PI / 2;
scene.add(plane);

const grid = new THREE.GridHelper(50, 50, 0x27272a, 0x27272a);
scene.add(grid);

let slotMeshes = {};

function createSlots(data){
  Object.values(slotMeshes).forEach(mesh => scene.remove(mesh));
  slotMeshes = {};

  let perRow = 8;
  let spacing = 2;

  data.forEach((slot, index) => {
    let row = Math.floor(index / perRow);
    let col = index % perRow;

    let x = col * spacing - (perRow * spacing)/2 + (spacing/2);
    let z = row * spacing * -1 + (spacing/2);
    
    // Create base slot outline
    let base = new THREE.Mesh(
        new THREE.PlaneGeometry(1.8, 1.8),
        new THREE.MeshBasicMaterial({ color: 0x27272a, side: THREE.DoubleSide })
    );
    base.rotation.x = -Math.PI / 2;
    base.position.set(x, 0.01, z);
    scene.add(base);
    slotMeshes[`base_${index}`] = base;

    // Create car block if occupied
    let color = slot.status === "available" ? 0x22c55e : 0xef4444;
    
    if(slot.status === "occupied") {
        let cube = new THREE.Mesh(
            new THREE.BoxGeometry(1.2, 1, 1.6),
            new THREE.MeshStandardMaterial({ color: color, roughness: 0.3, metalness: 0.5 })
        );
        cube.position.set(x, 0.5, z);
        scene.add(cube);
        slotMeshes[`car_${index}`] = cube;
    } else {
       // Just a subtle green indicator on the ground
       let ind = new THREE.Mesh(
          new THREE.PlaneGeometry(1.6, 1.6),
          new THREE.MeshBasicMaterial({ color: 0x22c55e, transparent: true, opacity: 0.2 })
       );
       ind.rotation.x = -Math.PI / 2;
       ind.position.set(x, 0.02, z);
       scene.add(ind);
       slotMeshes[`ind_${index}`] = ind;
    }
  });
}

function load3DSlots(){
  fetch("get_slots.php")
    .then(res => res.json())
    .then(data => createSlots(data))
    .catch(e => console.log("Fetch error: ", e));
}
load3DSlots();
setInterval(load3DSlots, 3000);

function animate(){
  requestAnimationFrame(animate);
  controls.update();
  renderer.render(scene, camera);
}
animate();

window.addEventListener("resize", () => {
  camera.aspect = container.clientWidth / container.clientHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(container.clientWidth, container.clientHeight);
});

</script>
</body>
</html>