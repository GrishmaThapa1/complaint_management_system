
document.addEventListener("DOMContentLoaded", function () {
    // Fade out messages after 3 seconds
    document.querySelectorAll(".message").forEach(function (msg) {
        setTimeout(function () {
            msg.style.transition = "opacity 1s ease-out";
            msg.style.opacity = "0";
        }, 3000);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute("href")).scrollIntoView({
                behavior: "smooth"
            });
        });
    });

  })
document.addEventListener("DOMContentLoaded", function () {
  document
    .querySelector('select[name="role"]')
    .addEventListener("change", function () {
      document.getElementById("admin-fields").style.display =
        this.value === "admin" ? "block" : "none";
      document.getElementById("user-fields").style.display =
        this.value === "user" ? "block" : "none";
    });
});

document
  .getElementById("loginForm")
  .addEventListener("submit", function (event) {
    let role = document.getElementById("role").value;
    let username = document.getElementById("username_or_email").value.trim();
    let password = document.getElementById("password").value.trim();

    if (role === "" || username === "" || password === "") {
      alert("Please fill in all fields.");
      event.preventDefault();
    }
  });
