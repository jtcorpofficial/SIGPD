<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['partida'])) {
  header('Location: menu.php'); exit;
}
$partida   = $_SESSION['partida'];
$jugadores = $partida['jugadores'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Draftosaurus · Tablero</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;800&display=swap" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/tablero.css" />
  <style>
    .overlay-msg{position:fixed;left:50%;top:1rem;transform:translateX(-50%);background:rgba(0,0,0,.75);color:#fff;padding:.6rem 1rem;border-radius:10px;box-shadow:0 10px 24px rgba(0,0,0,.2);z-index:9999;opacity:0;transition:opacity .2s ease}
    .overlay-msg.show{opacity:1}
    .mano-bloqueada{opacity:.5;filter:grayscale(40%)}
  </style>
</head>
<body class="d-flex flex-column min-vh-100 con-fondo">
  <header class="navbar navbar-expand-lg jungle-header sticky-top">
    <div class="container px-4 d-flex justify-content-between align-items-center">
      <a class="navbar-brand text-white fs-3" href="menu.php"> Draftosaurus</a>
      <a class="btn btn-safari fw-bold" href="menu.php">Salir</a>
    </div>
  </header>

  <main class="flex-grow-1 d-flex flex-column justify-content-center text-white px-4 py-4 mt-2 overflow-hidden">
    <section class="container-fluid">
      <div class="row g-4 align-items-start justify-content-center">

        <!-- PUNTUACIÓN -->
        <aside class="col-md-2 d-none d-md-block">
          <article class="caja-juego text-center">
            <h6 class="mb-2">Puntuación</h6>
            <ul class="list-unstyled mt-3" id="lista-puntuacion">
              <?php foreach ($jugadores as $i => $j): ?>
                <?php
                  $nombreCrudo = isset($j['nombre']) ? trim((string)$j['nombre']) : '';
                  $esBot = preg_match('/^bot\b/i', $nombreCrudo);
                  $nombreMostrado = $nombreCrudo === '' || $esBot ? ('Jugador '.($i+1)) : $nombreCrudo;
                ?>    
                <li data-jugador="<?php echo $i; ?>">
                  <?php echo htmlspecialchars($nombreMostrado, ENT_QUOTES, 'UTF-8'); ?>:
                  <span class="badge bg-warning" id="puntos-<?php echo $i; ?>"><?php echo (int)$j['puntos']; ?> pts</span>
                </li>
              <?php endforeach; ?>
            </ul>
          </article>
        </aside>

        <!-- TABLERO -->
        <section class="col-12 col-md-8 text-center contenedor-tablero">
          <div class="marco-tablero">
            <img src="img/tablero.png" alt="Tablero del juego" class="img-fluid rounded shadow-lg tablero-img" />
            <div class="zona-drop" data-recinto="bosque" data-lado="izquierda" data-terreno="bosque" style="left:8%; top:8%; width:33%; height:28%;"></div>
            <div class="zona-drop" data-recinto="prado" data-lado="izquierda" data-terreno="rocas"  style="left:8%; top:40%; width:35%; height:24%;"></div>
            <div class="zona-drop" data-recinto="amor" data-lado="derecha" data-terreno="bosque"  style="left:55%; top:8%; width:30%; height:26%;"></div>
            <div class="zona-drop" data-recinto="trio" data-lado="izquierda" data-terreno="rocas"  style="left:8%; top:68%; width:35%; height:24%;"></div>
            <div class="zona-drop" data-recinto="rey" data-lado="derecha" data-terreno="bosque"   style="left:55%; top:40%; width:30%; height:26%;"></div>
            <div class="zona-drop" data-recinto="isla" data-lado="derecha" data-terreno="rocas"   style="left:70%; top:70%; width:16%; height:22%;"></div>
            <div class="zona-drop" data-recinto="rio"  data-lado="centro" data-terreno="agua"  style="left:47.5%; top:10%; width:5%; height:80%;"></div>
          </div>
        </section>

        <!-- DADO Y MANO -->
        <aside class="col-md-2">
          <div class="caja-juego text-center mb-3">
            <h6 class="mb-2">Dado</h6>
            <div class="d-none d-md-block">
              <button id="dado-escritorio" class="btn btn-safari w-100">Tirar dado</button>
              <p class="mt-2 mb-0 small"><span id="texto-restriccion">Restricción: —</span></p>
            </div>
            <div class="d-md-none">
              <button id="dado-movil" class="btn btn-safari w-100">Tirar dado</button>
              <p class="mt-2 mb-0 small"><span id="texto-restriccion-movil">Restricción: —</span></p>
            </div>
          </div>
          <div class="caja-juego text-center bloque-dinos">
            <h6 class="mb-2">Dinosaurios disponibles</h6>
            <div id="mano-dinos" class="d-flex flex-wrap justify-content-center gap-2"></div>
          </div>
        </aside>

      </div>
    </section>
  </main>

  <!-- OVERLAY FINAL -->
  <div class="fin-backdrop" id="fin-partida-backdrop" style="display:none;">
    <div class="fin-card">
      <h2 id="fin-titulo" class="mb-1">Fin de la partida</h2>
      <p id="fin-sub" class="text-muted mb-3">¡Gracias por jugar!</p>
      <table class="table table-sm mb-3">
        <thead><tr><th>Jugador</th><th class="text-end">Puntos</th></tr></thead>
        <tbody id="fin-tbody"></tbody>
      </table>
      <div class="acciones">
        <button class="btn btn-prim" onclick="location.href='menu.php'">Volver al menú</button>
        <button class="btn btn-sec" onclick="location.reload()">Jugar de nuevo</button>
      </div>
    </div>
  </div>

  <footer class="text-white text-center py-3">
    <p class="m-0">Desarrollado por JT Corp © 2025</p>
  </footer>

<script>
/* ============================
   VARIABLES PRINCIPALES
============================ */
var JUGADORES = <?php echo json_encode($jugadores, JSON_UNESCAPED_UNICODE); ?>;
for (var i = 0; i < JUGADORES.length; i++) {
  var n = (JUGADORES[i] && JUGADORES[i].nombre) ? String(JUGADORES[i].nombre).trim() : '';
  if (n === '' || /^bot\b/i.test(n)) JUGADORES[i].nombre = 'Jugador ' + (i + 1);
}
var N_JUG = Math.min(JUGADORES.length, 5); // máximo 5

var ESPECIES = [
  { id:'trex', nombre:'T-Rex', img:'img/dinos/trex.png' },
  { id:'triceratops', nombre:'Triceratops', img:'img/dinos/triceratops.png' },
  { id:'brachiosaurio', nombre:'Brachiosaurio', img:'img/dinos/brachiosaurio.png' },
  { id:'estegosaurio', nombre:'Estegosaurio', img:'img/dinos/estegosaurio.png' },
  { id:'raptor', nombre:'Velociraptor', img:'img/dinos/raptor.png' },
  { id:'parasaurolofo', nombre:'Parasaurolofo', img:'img/dinos/parasaurolofo.png' }
];

// Puntuaciones y reglas
var PUNTOS_BOSQUE = [0,0,3,6,10,15,21];
var PUNTOS_PRADO  = [0,0,3,6,10,15];
var BONUS_TREX = 1;
var PUNTOS_RIO = 1;

var RESTRICCIONES = [
  { id:'lado_izq', texto:'Lado IZQUIERDO' },
  { id:'lado_der', texto:'Lado DERECHO' },
  { id:'terreno_bosque', texto:'Terreno BOSQUE' },
  { id:'terreno_rocas', texto:'Terreno ROCAS' },
  { id:'recinto_vacio', texto:'Recinto VACÍO' },
  { id:'sin_trex', texto:'Sin T-Rex en el recinto' }
];

function nuevaOcupacion(){ return { bosque:[], prado:[], amor:[], trio:[], rey:[], isla:[], rio:[] }; }
var ocupaciones = Array.from({length:N_JUG}, nuevaOcupacion);

/* ============================
   BOLSA Y MANOS
============================ */
function crearBolsa(){
  var bolsa = [];
  for (var j=0;j<N_JUG;j++){
    for (var e=0;e<ESPECIES.length;e++) for (var k=0;k<3;k++) bolsa.push(ESPECIES[e].id);
  }
  for (var i=bolsa.length-1;i>0;i--){
    var r=Math.floor(Math.random()*(i+1)); var t=bolsa[i]; bolsa[i]=bolsa[r]; bolsa[r]=t;
  }
  return bolsa;
}
var bolsa = crearBolsa();

function repartirManos(){
  var manos = [];
  for (var j=0;j<N_JUG;j++){ var m=[]; for (var x=0;x<6;x++) m.push(bolsa.pop()); manos.push(m); }
  return manos;
}
var manos = repartirManos();

/* ============================
   VARIABLES DE JUEGO
============================ */
var ronda = 1, turno = 1;
var indiceQuienTira = 0;       // jugador que tira dado este turno
var indiceEnColocacion = null; // a quién le toca colocar AHORA
var colocacionesHechas = 0;
var restriccionActual = null;

// Elementos DOM
var elMano = document.getElementById('mano-dinos');
var elTXTDesk  = document.getElementById('texto-restriccion');
var elTXTMov   = document.getElementById('texto-restriccion-movil');
var elMarco    = document.querySelector('.marco-tablero');
var elTablaFin = document.getElementById('fin-tbody');
var elFin      = document.getElementById('fin-partida-backdrop');
var elFinTitulo= document.getElementById('fin-titulo');
var elFinSub   = document.getElementById('fin-sub');

/* ============================
   LISTENERS BOTONES
============================ */
window.addEventListener('DOMContentLoaded', function(){
  var botonEscritorio = document.getElementById('dado-escritorio');
  var botonMovil = document.getElementById('dado-movil');
  if (botonEscritorio) botonEscritorio.addEventListener('click', tirarDado);
  if (botonMovil) botonMovil.addEventListener('click', tirarDado);
});

/* ============================
   FUNCIONES DE UI
============================ */
function setTextoRestriccion(){
  var txt = restriccionActual ? ('Restricción: ' + restriccionActual.texto) : 'Restricción: —';
  if (elTXTDesk) elTXTDesk.textContent = txt;
  if (elTXTMov)  elTXTMov.textContent  = txt;
}

function mostrarSoloFichasDe(indice){
  var fichas = document.querySelectorAll('.marco-tablero .ficha-dino');
  for (var i = 0; i < fichas.length; i++){
    var dueno = parseInt(fichas[i].getAttribute('data-jugador'), 10);
    fichas[i].style.display = (dueno === indice) ? '' : 'none';
  }
}

function pintarManoDe(idx){
  if (!elMano) return;
  elMano.innerHTML='';
  var mano = manos[idx] || [];
  mano.forEach(function(especie, i){
    var info = ESPECIES.find(function(e){return e.id===especie;});
    var img = document.createElement('img');
    img.src = info.img;
    img.alt = info.nombre;
    img.classList.add('dinosaurio');
    img.setAttribute('data-especie', especie);
    img.setAttribute('data-indice', i);

    // Drag para PC
    img.addEventListener('dragstart', function(ev){
      var data = JSON.stringify({
        especie: this.getAttribute('data-especie'),
        indice: parseInt(this.getAttribute('data-indice'),10)
      });
      ev.dataTransfer.setData('text/plain', data);
    });

    // 👇 Click para pantallas táctiles
    img.addEventListener('click', function(e){
      if (!("ontouchstart" in window || navigator.maxTouchPoints > 0)) return;
      e.preventDefault();
      document.querySelectorAll(".dinosaurio.seleccionado").forEach(d => d.classList.remove("seleccionado"));
      this.classList.add("seleccionado");
      window.dinoSeleccionado = this; // guardo global
    });

    elMano.appendChild(img);
  });
}


/* ============================
   DADO Y AVANCE
============================ */
function tirarDado(){
  // Si ya hay restricción activa, no volver a tirar hasta que coloquen todos.
  if (restriccionActual) return;
  var i = Math.floor(Math.random() * RESTRICCIONES.length);
  restriccionActual = RESTRICCIONES[i];
  setTextoRestriccion();
  indiceEnColocacion = indiceQuienTira;
  colocacionesHechas = 0;
  pintarManoDe(indiceEnColocacion);
  mostrarSoloFichasDe(indiceEnColocacion);
}

function avanzarColocacion(){
  colocacionesHechas++;
  if (colocacionesHechas >= N_JUG){
    // todos colocaron -> rotamos manos y pasa el dado al siguiente
    rotarManosDerecha();
    restriccionActual = null; setTextoRestriccion();
    indiceQuienTira = (indiceQuienTira + 1) % N_JUG;
    indiceEnColocacion = null;
    turno++;

    if (turno > 6){
      if (ronda === 1) { ronda = 2; turno = 1; manos = repartirManos(); }
      else { finDePartida(); return; }
    }
    // mostrar mano del nuevo tirador (sin restricción hasta que presione el botón)
    pintarManoDe(indiceQuienTira);
    mostrarSoloFichasDe(indiceQuienTira);
    return;
  }

  // siguiente jugador (a la derecha)
  indiceEnColocacion = (indiceEnColocacion + 1) % N_JUG;
  pintarManoDe(indiceEnColocacion);
  mostrarSoloFichasDe(indiceEnColocacion);
}

function rotarManosDerecha(){
  if (manos.length<=1) return;
  var ult = manos[manos.length-1];
  for (var i=manos.length-1;i>0;i--) manos[i]=manos[i-1];
  manos[0]=ult;
}

/* ============================
   VALIDACIONES
============================ */
function validaRestriccionHotSeat(idxJugador, recinto, lado, terreno){
  // El que tiró el dado NO tiene restricción
  if (!restriccionActual) return true;
  if (idxJugador === indiceQuienTira) return true;

  var occ = ocupaciones[idxJugador][recinto];
  var r = restriccionActual.id;

  if (r === 'lado_izq')        return (lado === 'izquierda' || lado === 'izq' || lado === 'izqui');
  if (r === 'lado_der')        return (lado === 'derecha'   || lado === 'der');
  if (r === 'terreno_bosque')  return (terreno === 'bosque');
  if (r === 'terreno_rocas')   return (terreno === 'rocas');
  if (r === 'recinto_vacio')   return (occ.length === 0);
  if (r === 'sin_trex')        return !occ.includes('trex');

  return true;
}

function validarPorRecinto(idxJugador, recinto, especie){
  var arr = ocupaciones[idxJugador][recinto].slice();
  arr.push(especie);

  if (recinto==='bosque') return todasIguales(arr);
  if (recinto==='prado')  return todasDistintas(arr);
  if (recinto==='amor')   return true;           // sin restricción de colocación
  if (recinto==='trio')   return arr.length<=3;
  if (recinto==='rey')    return arr.length===1; // solo un dino
  if (recinto==='isla')   return arr.length===1; // solo un dino
  if (recinto==='rio')    return true;           // siempre permitido
  return false;
}

function todasIguales(a){
  for (var i=1;i<a.length;i++) if (a[i]!==a[0]) return false;
  return true;
}
function todasDistintas(a){
  var s={}; for (var i=0;i<a.length;i++){ if (s[a[i]]) return false; s[a[i]]=1; }
  return true;
}

/* ============================
   PUNTOS
============================ */
function contarGlobal(idxJugador, especie){
  var recs = ['bosque','prado','amor','trio','rey','isla','rio'], c=0;
  for (var i=0;i<recs.length;i++){
    var arr = ocupaciones[idxJugador][recs[i]];
    for (var j=0;j<arr.length;j++) if (arr[j]===especie) c++;
  }
  return c;
}

function calcularPuntosJugador(j){
  var o = ocupaciones[j], t=0;

  // Bosque (todas iguales)
  if (o.bosque.length>0 && todasIguales(o.bosque)) {
    var n=o.bosque.length; t += (PUNTOS_BOSQUE[n]||0);
  }
  // Prado (todas distintas, desde 2)
  if (o.prado.length>=2 && todasDistintas(o.prado)) {
    var m=o.prado.length; t += (PUNTOS_PRADO[m]||0);
  }
  // Amor (pares iguales valen 3)
  if (o.amor.length>=2){
    var map={}; o.amor.forEach(function(e){ map[e]=(map[e]||0)+1; });
    for (var k in map) t += Math.floor(map[k]/2)*3;
  }
  // Trío exacto
  if (o.trio.length===3) t += 7;

  // Rey (tener la especie más abundante en mesa entre todos)
  if (o.rey.length===1){
    var esp=o.rey[0], max=0;
    for (var jj=0;jj<N_JUG;jj++){ var c=contarGlobal(jj,esp); if (c>max) max=c; }
    if (contarGlobal(j,esp)===max) t += 7;
  }

  // Isla (único de su especie en TODO tu tablero)
  if (o.isla.length===1){
    var ei=o.isla[0];
    if (contarGlobal(j,ei)===1) t += 7;
  }

  // Río (1 punto cada uno)
  t += o.rio.length * PUNTOS_RIO;

  // Bonus por T-Rex (1 por recinto que lo contenga)
  ['bosque','prado','amor','trio','rey','isla','rio'].forEach(function(r){
    if (o[r].indexOf('trex')!==-1) t += BONUS_TREX;
  });

  return t;
}

function actualizarMarcador(){
  for (var j=0;j<N_JUG;j++){
    var pts = calcularPuntosJugador(j);
    JUGADORES[j].puntos = pts;
    var span = document.getElementById('puntos-'+j);
    if (span) span.textContent = pts+' pts';
  }
}

/* ============================
   DRAG & DROP
============================ */
function prepararZonas(){
  var zonas = document.querySelectorAll('.zona-drop');
  zonas.forEach(function(z){
    z.addEventListener('dragover', function(ev){ ev.preventDefault(); z.classList.add('sobre'); });
    z.addEventListener('dragleave', function(){ z.classList.remove('sobre'); });
    z.addEventListener('drop', function(ev){
      ev.preventDefault(); z.classList.remove('sobre');
      if (restriccionActual==null || indiceEnColocacion==null) return;

      var txt = ev.dataTransfer.getData('text/plain');
      var obj = null; try { obj = JSON.parse(txt); } catch(e){}
      if (!obj || obj.indice==null || !obj.especie) return;

      var lado    = z.dataset.lado;
      var terreno = z.dataset.terreno;
      var recinto = z.dataset.recinto;

      // 1) validar restricción de dado (solo a los que no tiraron)
      if (!validaRestriccionHotSeat(indiceEnColocacion, recinto, lado, terreno)) {
        alert('No cumple la restricción de este turno.');
        return;
      }
      // 2) validar reglas del recinto
      if (!validarPorRecinto(indiceEnColocacion, recinto, obj.especie)) {
        alert('Reglas del recinto no se cumplen para ese dinosaurio.');
        return;
      }

      // 3) colocar
      colocarFicha(indiceEnColocacion, recinto, obj.especie, ev);
      quitarDeMano(indiceEnColocacion, obj.indice);

      // 4) actualizar puntos y pasar turno
      actualizarMarcador();
      avanzarColocacion();
    });
  });
}

function colocarFicha(idxJugador, recinto, especie, ev){
  ocupaciones[idxJugador][recinto].push(especie);
  if (!elMarco) return;
  var r = elMarco.getBoundingClientRect();
  var x = ((ev.clientX - r.left) / r.width) * 100;
  var y = ((ev.clientY - r.top)  / r.height)* 100;
  var info = ESPECIES.find(function(e){return e.id===especie;});
  var img = document.createElement('img');
  img.src = info.img;
  img.alt = info.nombre;
  img.className = 'ficha-dino';
  img.style.left = x+'%';
  img.style.top  = y+'%';
  img.setAttribute('data-jugador', String(idxJugador));
  elMarco.appendChild(img);
}

function quitarDeMano(idxJugador, indice){
  var mano = manos[idxJugador]; if (!mano) return;
  mano.splice(indice, 1);
  pintarManoDe(idxJugador);
}

/* ============================
   FIN DE PARTIDA (guardar)
============================ */
function finDePartida(){
  actualizarMarcador();
  const resultados = JUGADORES.map(function(j, idx){
    return { idx: idx, nombre: j.nombre, puntos: j.puntos, es_bot: j.es_bot || false, usuario_id: j.id || null };
  });

  fetch('php/guardar_partida.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ resultados: resultados, startedAt: window.__partidaInicio || Math.floor(Date.now()/1000) })
  })
  .then(function(r){ return r.json(); })
  .then(function(data){ console.log('Guardado:', data); })
  .catch(function(err){ console.error('Error guardando partida:', err); });

  if (!elFin || !elTablaFin) return;
  elTablaFin.innerHTML = '';
  var ord = resultados.slice().sort(function(a,b){ return b.puntos - a.puntos; });
  ord.forEach(function(r){
    var tr = document.createElement('tr');
    var td1 = document.createElement('td'); td1.textContent = r.nombre;
    var td2 = document.createElement('td'); td2.textContent = r.puntos+' pts'; td2.className='text-end';
    tr.appendChild(td1); tr.appendChild(td2);
    elTablaFin.appendChild(tr);
  });
  if (elFinTitulo){
    if (ord[0].puntos === ord[ord.length-1].puntos) elFinTitulo.textContent='¡EMPATE!';
    else if (ord[0].idx === 0) elFinTitulo.textContent='¡GANASTE!';
    else elFinTitulo.textContent='Fin de la partida';
  }
  if (elFinSub) elFinSub.textContent = '2 rondas × 6 turnos';
  elFin.style.display='flex';
}

/* ============================
   INICIO
============================ */
function iniciar(){
  prepararZonas();
  actualizarMarcador();
  setTextoRestriccion();     // “—” hasta que aprieten el botón
  pintarManoDe(indiceQuienTira);
  mostrarSoloFichasDe(indiceQuienTira);
}
window.addEventListener('DOMContentLoaded', iniciar);
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  var esTactil = "ontouchstart" in window || navigator.maxTouchPoints > 0;
  if (!esTactil) return;

  var zonas = document.querySelectorAll(".zona-drop");
  zonas.forEach(function (zona) {
    zona.addEventListener("click", function (e) {
      e.preventDefault();
      var dino = window.dinoSeleccionado;
      if (!dino) return;

      // mismo comportamiento que el drop
      var lado = zona.dataset.lado;
      var terreno = zona.dataset.terreno;
      var recinto = zona.dataset.recinto;

      if (!validaRestriccionHotSeat(indiceEnColocacion, recinto, lado, terreno)) {
        alert('No cumple la restricción de este turno.');
        return;
      }
      if (!validarPorRecinto(indiceEnColocacion, recinto, dino.dataset.especie)) {
        alert('Reglas del recinto no se cumplen para ese dinosaurio.');
        return;
      }

      // simula el "drop"
      colocarFicha(indiceEnColocacion, recinto, dino.dataset.especie, e);
      quitarDeMano(indiceEnColocacion, parseInt(dino.dataset.indice,10));
      actualizarMarcador();
      avanzarColocacion();

      dino.classList.remove("seleccionado");
      window.dinoSeleccionado = null;
    });
  });
});
</script>




