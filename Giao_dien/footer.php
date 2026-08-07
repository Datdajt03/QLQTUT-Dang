<?php
// Giao_dien/footer.php
?>
</main><!-- /.main -->
</div><!-- /.layout -->

<script>
// ── Sidebar toggle (desktop collapse & mobile slide-out) ───────────────────
document.getElementById('menuToggle').addEventListener('click', function(e) {
  e.stopPropagation();
  var layout = document.getElementById('mainLayout');
  var sidebar = document.getElementById('sidebar');
  
  if (window.innerWidth > 768) {
    // Desktop: collapse / expand sidebar
    layout.classList.toggle('collapsed');
    var isCollapsed = layout.classList.contains('collapsed');
    try { localStorage.setItem('sidebarCollapsed', isCollapsed); } catch(ex){}
  } else {
    // Mobile: open / close drawer
    sidebar.classList.toggle('open');
  }
});

document.addEventListener('click', function(e) {
  var sb  = document.getElementById('sidebar');
  var btn = document.getElementById('menuToggle');
  if (window.innerWidth <= 768 && !sb.contains(e.target) && e.target !== btn) {
    sb.classList.remove('open');
  }
});

// ── Global search ──────────────────────────────────────────────────────────
var globSearch = document.getElementById('globalSearch');
if (globSearch) {
  globSearch.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) {
      window.location = '<?= BASE_URL ?>Quan_ly_doi_tuong/danh_sach.php?search=' + encodeURIComponent(this.value.trim());
    }
  });
}

// ── Flash auto-dismiss ─────────────────────────────────────────────────────
var flash = document.querySelector('.flash');
if (flash) setTimeout(function(){ flash.style.transition='opacity 0.5s'; flash.style.opacity='0'; }, 4000);

// ── Tab phụ accordion ──────────────────────────────────────────────────────
var tabphuToggle = document.getElementById('tabphuToggle');
if (tabphuToggle) {
  tabphuToggle.addEventListener('click', toggleTabPhu);
}

function toggleTabPhu() {
  var toggle = document.getElementById('tabphuToggle');
  var body   = document.getElementById('tabphuBody');
  if (!toggle || !body) return;
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
  if (body && !body.classList.contains('open')) {
    try {
      var saved = localStorage.getItem('tabphuOpen');
      if (saved === 'true') {
        body.classList.add('open');
        var toggle = document.getElementById('tabphuToggle');
        if (toggle) {
          toggle.classList.add('open');
          var arrow = toggle.querySelector('.accordion-arrow');
          if (arrow) arrow.textContent = '▲';
        }
      }
    } catch(e){}
  }
})();
</script>
</body>
</html>
