</div> <!-- end container -->

<footer class="text-center py-4 mt-5 border-top">
  <p class="mb-0">© <?php echo date('Y'); ?> Traction Ideas — Built for Innovation 💡</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===== DARK MODE SCRIPT =====
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("darkModeToggle");
  const body = document.body;

  if (!toggleBtn) return;

  // Load saved mode
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark") {
    body.classList.add("dark-mode");
    toggleBtn.textContent = "☀️";
  }

  toggleBtn.addEventListener("click", () => {
    body.classList.toggle("dark-mode");
    const dark = body.classList.contains("dark-mode");
    toggleBtn.textContent = dark ? "☀️" : "🌙";
    localStorage.setItem("theme", dark ? "dark" : "light");
  });
});
</script>

</body>
</html>
