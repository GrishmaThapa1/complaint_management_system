document.addEventListener("DOMContentLoaded", function () {
  console.log("✅ script.js loaded successfully");

  /* ===== 1️⃣ Fade out messages ===== */
  document.querySelectorAll(".message, .success, .error").forEach((msg) => {
    setTimeout(() => {
      msg.style.transition = "opacity 1s ease-out";
      msg.style.opacity = "0";
      setTimeout(() => msg.remove(), 1000);
    }, 2000);
  });

  /* ===== 2️⃣ Smooth scroll for anchor links ===== */
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) target.scrollIntoView({ behavior: "smooth" });
    });
  });

  /* ===== 3️⃣ Login form validation ===== */
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      const username = document
        .getElementById("username_or_email")
        ?.value.trim();
      const password = document.getElementById("password")?.value.trim();
      if (!username || !password) {
        alert("Please fill in all fields.");
        e.preventDefault();
      }
    });
  }

  /* ===== 4️⃣ Register form validation ===== */
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      const password = document.getElementById("password")?.value;
      const confirmPassword =
        document.getElementById("confirm_password")?.value;
      if (password !== confirmPassword) {
        alert("Passwords do not match!");
        e.preventDefault();
      }
    });
  }

  /* ===== 5️⃣ Password toggle (FIXED) ===== */
  document.querySelectorAll(".password-toggle").forEach((icon) => {
    icon.addEventListener("click", function (e) {
      e.stopPropagation();
      const wrapper = this.parentElement;
      const input = wrapper.querySelector("input");

      if (input) {
        input.type = input.type === "password" ? "text" : "password";
        this.classList.toggle("fa-eye-slash");
        this.classList.toggle("fa-eye");
      }
    });
  });

  /* ===== 6️⃣ Profile page fade & redirect ===== */
  const profileMsg = document.getElementById("profileMessage");
  if (profileMsg) {
    setTimeout(() => {
      profileMsg.style.transition = "opacity 1s ease-out";
      profileMsg.style.opacity = "0";
      setTimeout(() => profileMsg.remove(), 1000);
    }, 2000);

    if (profileMsg.classList.contains("success")) {
      setTimeout(() => (window.location.href = "profile.php"), 2500);
    }
  }

  /* ===== 7️⃣ Other success messages & optional redirect ===== */
  const successMsg = document.getElementById("successMsg");
  if (successMsg) {
    setTimeout(() => {
      successMsg.style.transition = "opacity 0.5s ease-out";
      successMsg.style.opacity = "0";
      setTimeout(() => successMsg.remove(), 500);
    }, 2000);

    setTimeout(() => {
      const path = window.location.pathname;
      if (path.includes("submit_complaint.php"))
        window.location.href = "dashboard.php";
      if (path.includes("update_status.php"))
        window.location.href = "view_complaints.php";
    }, 2500);
  }

  /* ===== 8️⃣ Header dropdown toggle ===== */
  document.querySelectorAll(".user-dropdown").forEach((dd) => {
    const icon = dd.querySelector("i");
    const content = dd.querySelector(".dropdown-content");
    if (icon && content) {
      const newIcon = icon.cloneNode(true);
      icon.replaceWith(newIcon);
      newIcon.addEventListener("click", function (e) {
        e.stopPropagation();
        document.querySelectorAll(".dropdown-content.show").forEach((d) => {
          if (d !== content) d.classList.remove("show");
        });
        content.classList.toggle("show");
      });
    }
  });

  document.addEventListener("click", () => {
    document
      .querySelectorAll(".dropdown-content.show")
      .forEach((d) => d.classList.remove("show"));
  });

  /* ===== 9️⃣ Confirm logout ===== */
  document.querySelectorAll(".logout-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = this.href;
      }
    });
  });
});
