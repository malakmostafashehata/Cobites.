<?php
require "auth.php";
require "db.php";

$full_nameErr = $emailErr = $passwordErr = $phoneErr = $addressErr = $roleErr = "";
$errorMessage = "";

if (isset($_GET["error"])) {

    switch ($_GET["error"]) {

        case "wrong_password":
            $errorMessage = "Wrong password ";
            break;

        case "user_not_found":
            $errorMessage = "User not found ";
            break;

        case "empty":
            $errorMessage = "Please fill all fields ";
            break;

        case "pending":
            $errorMessage = "You can't login yet. Admin is still reviewing your account ";
            break;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? '');
    $email = strtolower(trim($_POST["email"] ?? ''));
    $password_raw = $_POST["password"] ?? '';
    $phone = trim($_POST["phone"] ?? '');
    $address = trim($_POST["address"] ?? '');
    $role = $_POST["role"] ?? '';
    $volunteer_type = $_POST["volunteer_type"] ?? null;

    $valid = true;


    if ($full_name === '' || !preg_match('/^[a-zA-Z0-9 ]+$/', $full_name)) {
        $full_nameErr = "Invalid full name";
        $valid = false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email";
        $valid = false;
    }

    if (strlen($password_raw) < 6) {
        $passwordErr = "Password must be at least 6 characters";
        $valid = false;
    }

    if (!preg_match('/^[0-9]{11}$/', $phone)) {
        $phoneErr = "Invalid phone";
        $valid = false;
    }

    if ($address === '') {
        $addressErr = "Address required";
        $valid = false;
    }

    $allowed_roles = ["volunteer", "charity", "delivery", "admin"];

    if (!in_array($role, $allowed_roles)) {
        $roleErr = "Invalid role";
        $valid = false;
    }

    if ($valid) {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $emailErr = "Email already exists";
            $valid = false;
        }
    }

    $uploaded_file = null;
if ($valid && isset($_FILES["charity_file"]) && $_FILES["charity_file"]["error"] === 0) {

    $dir = __DIR__ . "/uploads/charity_file/";

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["charity_file"]["name"]);
    $path = $dir . $fileName;

    if (move_uploaded_file($_FILES["charity_file"]["tmp_name"], $path)) {
        $uploaded_file = "uploads/charity_file/" . $fileName;
    }
}
    $status = ($role === "charity") ? "pending" : "accepted";

    if ($valid) {

        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users
            (full_name, email, password, phone, address, role, volunteer_type, charity_file, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->bind_param(
            "sssssssss",
            $full_name,
            $email,
            $password,
            $phone,
            $address,
            $role,
            $volunteer_type,
            $uploaded_file,
            $status
        );if ($stmt->execute()) {

    $_SESSION["user_id"] = $stmt->insert_id;
    $_SESSION["role"] = $role;
    $_SESSION["full_name"] = $full_name;

    if ($role === "charity") {
        $_SESSION["toast_message"] = "Your request is pending approval 🎉";
    } else {
        $_SESSION["toast_message"] = "Welcome 🎉";
        header("Location: homepage.php");
        exit;
    }

}}}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Login</title>

    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial;
      }

      body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #102f15);
      }
      
      input{
          width:80%;
          padding:10px;
          margin:10px 0;
          border:2px solid #102f15;
          border-radius:6px;
      }

      .remember {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #102f15;
        cursor: pointer;
        white-space: nowrap; 

      }
      .remember input {
        width: 16px;
        height: 16px;
        margin: 0;
        accent-color: #102f15;
      } 
      .error {
        color:red;
        font-weight:bold;
        font size: 10px;
        margin-top:0px;
      }

      .forgot {
        text-decoration: none;
        color: #102f15;
        font-size: 13px;
        transition: 0.3s;
      }

      .forgot:hover {
        text-decoration: underline;
        color: #2e5a2e;
      }

      a{
        font-weight:bold;
        color:#102f15;
        text-decoration:underline;
      }

      .container {
        width: 700px;
        height: 590px;
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        background: #eaf1b1;
        backdrop-filter: blur(10px);
      }

      .form-container {
        position: absolute;
        width: 50%;
        height: 100%;
        padding: 40px;
        transition: 0.6s ease-in-out;
        text-align: center;
      }

      .login {
        left: 0;
      }

      .register {
        left: 100%;
      }

      .container.active .login {
        transform: translateX(-100%);
      }

      .container.active .register {
        transform: translateX(-100%);
      }

      .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        z-index: 9999;
      }

      .modal-content {
        background: #eaf1b1;
        padding: 20px;
        border-radius: 10px;
        width: 300px;
        text-align: center;
      }

      .modal-content input {
        width: 100%;
        margin: 10px 0;
        padding: 8px;
        border: 2px solid #102f15;
      }

      .modal-content button {
        margin: 5px;
      }

      .cancel {
        background: red;
      }

      input:hover {
        background-color: #eaf1b1;
      }

      input {
        width: 100%;
        padding: 5px;
        margin: 10px 0;
        border: 2px solid #102f15;
        background: white;
        color: black;
        outline: none;
      }

      select {
        width: 100%;
        padding: 5px;
        margin-top: 10px;
        border: 2px solid #020617;
        background: white;
        color: black;
        outline: none;
      }

      h1 {
        color: #102f15;
        margin-bottom: 20px;
        font-size: bold;
      }

      button {
        padding: 10px 20px;
        margin-top: 10px;
        border: none;
        background: #102f15;
        color: white;
        border-radius: 15px;
        cursor: pointer;
      }

      .overlay {
        position: fixed;
        width: 50%;
        height: 100%;
        right: 0;
        background: linear-gradient(135deg, #728c5a);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        transition: 0.5s;
        z-index: 1;
      }

      .container.active .overlay {
        transform: translateX(-100%);
      }
.toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #1f7a1f;
  color: white;
  padding: 12px 18px;
  border-radius: 8px;
  opacity: 0;
  transform: translateY(20px);
  transition: 0.4s ease;
  z-index: 99999;
  font-size: 14px;
  max-width: 280px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.toast.show {
  opacity: 1;
  transform: translateY(0);
}
    </style>
  </head>

  <body>
    <?php if (!empty($_SESSION["toast_message"])): ?>
<script>
window.addEventListener("load", function () {
    showToast("<?= $_SESSION["toast_message"] ?>");
});
</script>
<?php unset($_SESSION["toast_message"]); endif; ?>
    <div class="container <?php if($_SERVER['REQUEST_METHOD'] == 'POST') echo 'active'; ?>" id="container">

      <form method="POST" action="login.php">
        <div class="form-container login">

          <h1>Login</h1>

          <br><br><br>

          <input type="email" name="email" placeholder="Email" required>
          <input type="password" name="password" placeholder="Password" required>
          <?php if (!empty($errorMessage)): ?>
           <h5 class="error"><?= $errorMessage ?></h5>
            <?php endif; ?>


            <label class="remember">
              <input type="checkbox" name="remember">
              <span>Remember me</span>
            </label><br>

          <a href="#" class="forgot" onclick="openModal()">Forgot password?</a><br>

          <button type="submit">Login</button>

          <p style="color: black; margin-top: 10px;">
            Don’t have an account?<br>
            <a href="#" id="showRegister" style="color:#102f15; font-weight:700;">
              Register here
            </a>
          </p>

        </div>
      </form>

      <div id="forgotModal" class="modal">
        <div class="modal-content">

          <p>Enter your email address</p>

          <input type="email" id="resetEmail" placeholder="Email">

          <button onclick="sendReset()">Send</button>
          <button onclick="closeModal()" class="cancel">Cancel</button>
        </div>
      </div>

      <div id="toast" class="toast"></div>
          <form method="POST" action="" enctype="multipart/form-data">
          <div class="form-container register">
          <h2>Register</h2>

          <input type="text" name="full_name" placeholder="Full Name" />
          <h5 class="error"><?= $full_nameErr ?></h5>

          <input type="email" name="email" placeholder="Email" />
          <h5 class="error"><?= $emailErr ?></h5>
          <input type="password" name="password" placeholder="Password" />
          <h5 class="error"><?= $passwordErr ?></h5>
          <input type="text" name="address" placeholder="Address" />
          <h5 class="error"><?= $addressErr ?></h5>
          
          <input type="text" name="phone" placeholder="Phone Number" />
          <h5 class="error"><?= $phoneErr ?></h5>

          <select name="role" id="roleSelect" onchange="showVolunteerOptions()">
          <option value="">Select Role</option>
          <option value="volunteer">Volunteer</option>
          <option value="charity">Charity</option>
          <option value="delivery">Delivery</option>
          </select>

          <h5 class="error"><?= $roleErr ?></h5>

          <select name="volunteer_type" id="volunteerOptions" style="display:none">
              <option value="">Select Type</option>
              <option value="individual">Individual</option>
              <option value="hotel">Hotel</option>
              <option value="restaurant">Restaurant</option>
          </select>

          <input type="file" name="charity_file" id="charityFile" style="display:none">

          <button>Register</button>

          <p style="color: black; margin-top: 10px">
            Already have an account?
            <a href="#" id="showLogin" style="color: #102f15">Login</a>
          </p>
        </div>

        <div class="overlay">
          <div>
            <h1>Welcome!</h1>
          </div>
        </div>
      </form>
    </div>


    <script>
      const container = document.getElementById("container");

      document.getElementById("showRegister").onclick = () => {
        container.classList.add("active");
      };

      document.getElementById("showLogin").onclick = () => {
        container.classList.remove("active");
      };
function showVolunteerOptions() {
  const role = document.getElementById("roleSelect").value;
  const volunteerDropdown = document.getElementById("volunteerOptions");
  const charityInput = document.getElementById("charityFile");

  volunteerDropdown.value = "";

  if (role === "volunteer") {
    volunteerDropdown.style.display = "block";
    volunteerDropdown.required = true;

    charityInput.style.display = "none";
    charityInput.value = "";
    charityInput.required = false;
  } 
  else if (role === "charity") {
    volunteerDropdown.style.display = "none";
    volunteerDropdown.required = false;

    charityInput.style.display = "block";
    charityInput.required = true;
  } 
  else {
    volunteerDropdown.style.display = "none";
    volunteerDropdown.required = false;

    charityInput.style.display = "none";
    charityInput.required = false;

    charityInput.value = "";
  }
}

      function openModal() {
        document.getElementById("forgotModal").style.display = "flex";
      }

      function closeModal() {
        document.getElementById("forgotModal").style.display = "none";
      }



      function sendReset() {
        const email = document.getElementById("resetEmail").value;

        if (email === "") {
          showToast("Please enter your email ⚠️");
          return;
        }

        showToast("Password sent successfully ✅");

        closeModal();
      }
      window.onload = function () {
    showVolunteerOptions();
};
document.addEventListener("DOMContentLoaded", function(){

    showVolunteerOptions();

});
    </script>
<div id="toast" class="toast"></div>

<script>
function showToast(message){
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}
</script>

<?php if(!empty($successMessage)): ?>
<script>
window.addEventListener("load", function(){
    showToast("<?= $successMessage ?>");
});
</script>
<?php endif; ?>
  </body>
</html>