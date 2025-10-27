document.addEventListener("DOMContentLoaded", function () {
  console.log("✅ style.js loaded successfully");

  // 1️⃣ Fade out all messages with class 'message'
  document.querySelectorAll(".message").forEach(function (msg) {
    setTimeout(function () {
      msg.style.transition = "opacity 1s ease-out";
      msg.style.opacity = "0";
      
      // Remove element after fade completes
      setTimeout(function () {
        msg.remove();
      }, 1000);
    }, 2000);
  });

  // 2️⃣ Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      document.querySelector(this.getAttribute("href")).scrollIntoView({
        behavior: "smooth",
      });
    });
  });

  // 3️⃣ Login form validation
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (event) {
      let username = document.getElementById("username_or_email").value.trim();
      let password = document.getElementById("password").value.trim();

      if (username === "" || password === "") {
        alert("Please fill in all fields.");
        event.preventDefault();
      }
    });
  }

  // 4️⃣ Optional redirect for success messages
  const successMsg = document.getElementById("successMsg");
  if (successMsg) {
    console.log("✅ successMsg detected – fading out and redirecting if needed");

    setTimeout(() => {
      successMsg.style.transition = "opacity 0.5s ease-out";
      successMsg.style.opacity = "0";
      
      // Remove element after fade completes
      setTimeout(() => {
        successMsg.remove();
      }, 500);
    }, 2000);

    setTimeout(() => {
      const path = window.location.pathname;
      if (path.includes("submit_complaint.php")) {
        window.location.href = "dashboard.php";
      } else if (path.includes("update_status.php")) {
        window.location.href = "view_complaints.php";
      }
    }, 2500);
  }

});