(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* Jam di taskbar */
  function clock() {
    var el = $('#clock'); if (!el) return;
    var d = new Date(); el.textContent = ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
  }
  clock(); setInterval(clock, 30000);

  /* Slider foto dulu vs sekarang */
  $$('[data-cmp]').forEach(function (r) {
    r.addEventListener('input', function () {
      var top = r.previousElementSibling.querySelector('.top');
      if (top) top.style.clipPath = 'inset(0 ' + (100 - r.value) + '% 0 0)';
    });
  });

  /* Hitung mundur acara */
  function cd(ms) {
    if (ms <= 0) return 'Sudah lewat';
    var d = Math.floor(ms / 864e5), h = Math.floor(ms % 864e5 / 36e5), m = Math.floor(ms % 36e5 / 6e4), s = Math.floor(ms % 6e4 / 1e3);
    return d + ' hari ' + ('0' + h).slice(-2) + ':' + ('0' + m).slice(-2) + ':' + ('0' + s).slice(-2);
  }
  function tick() { $$('[data-at]').forEach(function (el) { el.textContent = cd(new Date(el.getAttribute('data-at')).getTime() - Date.now()); }); }
  tick(); setInterval(tick, 1000);

  /* Konfirmasi dua klik untuk tombol berbahaya (tanpa dialog bawaan browser) */
  $$('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      var b = f.querySelector('button[type=submit]');
      if (!b.dataset.armed) {
        e.preventDefault(); b.dataset.armed = '1'; var old = b.textContent; b.textContent = 'Yakin? Klik lagi';
        setTimeout(function () { delete b.dataset.armed; b.textContent = old; }, 3000);
      }
    });
  });

  /* Kuis */
  var qz = $('#quiz');
  if (qz) {
    var GENG = { K: ['Geng Kantin', 'Kamu paling betah nongkrong di kantin: gorengan, es teh, dan gosip hangat. Semua orang kenal kamu!'], P: ['Geng Perpus', 'Kamu si rajin yang hafal letak buku dan jadi tujuan nyontek PR. Diam-diam paling sukses!'], B: ['Geng Anak Band', 'Gitar di tas, poster band di dinding. Kamu bintang pensi sekolah!'], L: ['Geng Lapangan', 'Bel istirahat belum bunyi, kamu sudah di lapangan. Basket, futsal, atau kasti, kamu juaranya!'] };
    var Q1 = [['Bel istirahat bunyi, kamu langsung...', [['Lari ke kantin', 'K'], ['Ke perpustakaan', 'P'], ['Ke ruang musik', 'B'], ['Ke lapangan', 'L']]], ['Hadiah ulang tahun idamanmu?', [['Voucher makan', 'K'], ['Buku & novel', 'P'], ['CD musik / gitar', 'B'], ['Bola / sepatu olahraga', 'L']]], ['Pelajaran favorit?', [['Ekonomi (hitung uang jajan)', 'K'], ['Fisika / Kimia', 'P'], ['Seni Budaya', 'B'], ['Olahraga', 'L']]], ['Acara sekolah paling ditunggu?', [['Bazar', 'K'], ['Cerdas cermat', 'P'], ['Pentas seni', 'B'], ['Class meeting', 'L']]], ['Tempat nongkrong sepulang sekolah?', [['Warung depan sekolah', 'K'], ['Rumah teman buat belajar', 'P'], ['Studio / rental band', 'B'], ['Lapangan kampung', 'L']]]];
    var Q2 = [['Gadget pemutar musik pribadi sebelum smartphone?', ['Walkman / MP3 player', 'Smartwatch', 'Tablet', 'Speaker bluetooth']], ['Game bawaan Nokia 3310 yang terkenal?', ['Snake', 'Candy Crush', 'Angry Birds', 'Tetris Blast']], ['Dulu, tarif warnet biasanya dihitung per...', ['Jam', 'Gigabyte', 'Bulan', 'Follower']], ['Telepon umum jaman dulu memakai...', ['Koin atau kartu telepon', 'QR code', 'Sidik jari', 'Kuota']], ['Situs jejaring sosial paling ngetren sebelum Facebook?', ['Friendster', 'Instagram', 'TikTok', 'Threads']]];
    var st = { q: 0, step: 0, ans: [] };
    var esc = function (s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; };
    var render = function () {
      if (!st.q) { qz.innerHTML = '<h3>Pilih Kuis</h3><div class="acts"><button class="btn" data-q="1">🎒 Kamu anak geng apa waktu SMA?</button><button class="btn" data-q="2">📟 Seberapa jadul kamu?</button></div>'; return; }
      var Q = st.q === 1 ? Q1 : Q2;
      if (st.step >= Q.length) {
        var h = '';
        if (st.q === 1) { var c = {}; st.ans.forEach(function (a) { c[a] = (c[a] || 0) + 1; }); var k = Object.keys(c).sort(function (a, b) { return c[b] - c[a]; })[0]; h = '<h3>' + esc(GENG[k][0]) + '!</h3><p>' + esc(GENG[k][1]) + '</p>'; }
        else { var sc = st.ans.filter(Boolean).length; h = '<h3>Skor: ' + sc + '/' + Q.length + '</h3><p>' + (sc >= 4 ? 'Anak 2000-an sejati! Kamu pasti hafal semua lagu di MP3 player itu.' : sc >= 2 ? 'Lumayan jadul. Masih ada kenangan yang tersisa.' : 'Anak zaman now nyasar ke warnet. Ayo belajar sejarah lagi!') + '</p>'; }
        qz.innerHTML = h + '<div class="acts"><button class="btn" data-reset="1">Main Lagi</button></div>'; return;
      }
      var q = Q[st.step];
      qz.innerHTML = '<p class="small">Pertanyaan ' + (st.step + 1) + ' dari ' + Q.length + '</p><h3>' + esc(q[0]) + '</h3><div style="display:grid;gap:6px">' + q[1].map(function (o, i) { return '<button class="btn" style="text-align:left" data-i="' + i + '">' + esc(Array.isArray(o) ? o[0] : o) + '</button>'; }).join('') + '</div>';
    };
    qz.addEventListener('click', function (e) {
      var b = e.target.closest('button'); if (!b) return;
      if (b.dataset.q) { st = { q: +b.dataset.q, step: 0, ans: [] }; }
      else if (b.dataset.reset) { st = { q: 0, step: 0, ans: [] }; }
      else if (b.dataset.i !== undefined) { var i = +b.dataset.i; st.ans.push(st.q === 1 ? Q1[st.step][1][i][1] : i === 0); st.step++; }
      render();
    });
    render();
  }

  /* Snake ala Nokia */
  var cv = $('#snake-canvas');
  if (cv) {
    var cx = cv.getContext('2d'), C = 12, COLS = 20, ROWS = 14, sc = $('#snake-score');
    var g = { snake: [], dir: { x: 1, y: 0 }, nd: { x: 1, y: 0 }, food: { x: 10, y: 7 }, score: 0, timer: null, running: false };
    var rnd = function () { var f; do { f = { x: Math.floor(Math.random() * COLS), y: Math.floor(Math.random() * ROWS) }; } while (g.snake.some(function (s) { return s.x === f.x && s.y === f.y; })); return f; };
    var draw = function () { cx.fillStyle = '#c7f0d8'; cx.fillRect(0, 0, 240, 168); cx.fillStyle = '#43523d'; g.snake.forEach(function (s) { cx.fillRect(s.x * C + 1, s.y * C + 1, C - 2, C - 2); }); cx.fillRect(g.food.x * C + 3, g.food.y * C + 3, C - 6, C - 6); };
    var tickG = function () {
      g.dir = g.nd; var h = { x: g.snake[0].x + g.dir.x, y: g.snake[0].y + g.dir.y };
      if (h.x < 0 || h.y < 0 || h.x >= COLS || h.y >= ROWS || g.snake.some(function (s) { return s.x === h.x && s.y === h.y; })) { clearInterval(g.timer); g.running = false; sc.textContent = 'Game over! Skor: ' + g.score + '. Tekan Mulai untuk ulang.'; return; }
      g.snake.unshift(h);
      if (h.x === g.food.x && h.y === g.food.y) { g.score++; sc.textContent = 'Skor: ' + g.score; g.food = rnd(); } else g.snake.pop();
      draw();
    };
    var start = function () { clearInterval(g.timer); g.snake = [{ x: 6, y: 7 }, { x: 5, y: 7 }, { x: 4, y: 7 }]; g.dir = { x: 1, y: 0 }; g.nd = { x: 1, y: 0 }; g.score = 0; g.running = true; g.food = rnd(); sc.textContent = 'Skor: 0'; g.timer = setInterval(tickG, 120); draw(); };
    var D = { U: { x: 0, y: -1 }, D: { x: 0, y: 1 }, L: { x: -1, y: 0 }, R: { x: 1, y: 0 } };
    var setDir = function (d) { if (!g.running) return; if (d.x === -g.dir.x && d.y === -g.dir.y) return; g.nd = d; };
    document.addEventListener('click', function (e) {
      var b = e.target.closest('[data-sdir],[data-sstart]'); if (!b) return;
      if (b.dataset.sstart !== undefined) start(); else setDir(D[b.dataset.sdir]);
    });
    document.addEventListener('keydown', function (e) {
      var m = { ArrowUp: 'U', ArrowDown: 'D', ArrowLeft: 'L', ArrowRight: 'R' }[e.key];
      if (m && g.running) { e.preventDefault(); setDir(D[m]); }
    });
    g.snake = [{ x: 6, y: 7 }, { x: 5, y: 7 }, { x: 4, y: 7 }]; draw();
  }

  /* Tutup / buka lagi jendela beranda */
  (function () {
    var desk = document.getElementById('desk'), x = document.getElementById('closeWin'),
        back = document.getElementById('restoreWin'), hint = document.getElementById('deskHint');
    if (!desk || !x || !back) return;
    function setClosed(c) {
      desk.classList.toggle('closed', c);
      back.hidden = !c;
      if (hint) hint.hidden = !c;
    }
    x.addEventListener('click', function () { setClosed(true); });
    back.addEventListener('click', function () { setClosed(false); });
  })();
})();
