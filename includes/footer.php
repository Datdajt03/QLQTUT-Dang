<?php
// includes/footer.php
?>
</main><!-- /.main -->
</div><!-- /.layout -->

<script>
// ── Sidebar toggle (mobile) ────────────────────────────────────────────────
document.getElementById('menuToggle').addEventListener('click', function() {
  document.getElementById('sidebar').classList.toggle('open');
});
document.addEventListener('click', function(e) {
  var sb  = document.getElementById('sidebar');
  var btn = document.getElementById('menuToggle');
  if (window.innerWidth <= 768 && !sb.contains(e.target) && e.target !== btn) {
    sb.classList.remove('open');
  }
});

// ── Global search ──────────────────────────────────────────────────────────
document.getElementById('globalSearch').addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && this.value.trim()) {
    window.location = '<?= BASE_URL ?>Chucnang/danh_sach.php?search=' + encodeURIComponent(this.value.trim());
  }
});

// ── Flash auto-dismiss ─────────────────────────────────────────────────────
var flash = document.querySelector('.flash');
if (flash) setTimeout(function(){ flash.style.transition='opacity 0.5s'; flash.style.opacity='0'; }, 4000);

// ── Tab phụ accordion ──────────────────────────────────────────────────────
function toggleTabPhu() {
  var toggle = document.getElementById('tabphuToggle');
  var body   = document.getElementById('tabphuBody');
  var isOpen = body.classList.contains('open');

  body.classList.toggle('open', !isOpen);
  toggle.classList.toggle('open', !isOpen);
  toggle.querySelector('.accordion-arrow').textContent = isOpen ? '▼' : '▲';

  // Persist state
  try { localStorage.setItem('tabphuOpen', !isOpen); } catch(e){}
}

// Restore state on load (unless auto-opened by PHP)
(function() {
  var body = document.getElementById('tabphuBody');
  if (!body.classList.contains('open')) {
    try {
      var saved = localStorage.getItem('tabphuOpen');
      if (saved === 'true') {
        body.classList.add('open');
        document.getElementById('tabphuToggle').classList.add('open');
        document.querySelector('.accordion-arrow').textContent = '▲';
      }
    } catch(e){}
  }
})();
</script>
</body>
</html>
