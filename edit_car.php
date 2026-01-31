<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$car_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

/* Fetch car data */
$stmt = $conn->prepare("SELECT * FROM cars WHERE id=? AND owner_id=?");
$stmt->bind_param("ii", $car_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    die("Car not found");
}

/* Update car */
if (isset($_POST['update_car'])) {

    $car_name = $_POST['car_name'];
    $car_model = $_POST['car_model'];
    $driver_name = $_POST['driver_name'];
    $gps_device_id = $_POST['gps_device_id'];
    $status = $_POST['status'];

    $update = $conn->prepare(
        "UPDATE cars 
         SET car_name=?, car_model=?, driver_name=?, gps_device_id=?, status=? 
         WHERE id=? AND owner_id=?"
    );

    $update->bind_param(
        "sssssii",
        $car_name,
        $car_model,
        $driver_name,
        $gps_device_id,
        $status,
        $car_id,
        $user_id
    );

    $update->execute();
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Car</title>

<style>
/* ===== RESET ===== */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', sans-serif;
}

/* ===== BACKGROUND ===== */
body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
}

/* ===== CARD ===== */
.card{
    width:420px;
    padding:30px;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(12px);
    border-radius:16px;
    box-shadow:0 20px 40px rgba(0,0,0,0.4);
    color:#fff;
}

/* ===== TITLE ===== */
.card h2{
    text-align:center;
    margin-bottom:25px;
    letter-spacing:1px;
}

/* ===== INPUTS ===== */
.card input,
.card select{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:10px;
    border:none;
    outline:none;
    background:rgba(255,255,255,0.2);
    color:#fff;
    font-size:14px;
}

.card input::placeholder{
    color:#ddd;
}

/* ===== SELECT ===== */
.card select option{
    color:#000;
}

/* ===== BUTTON ===== */
.card button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#00c6ff,#0072ff);
    color:#fff;
    font-size:15px;
    cursor:pointer;
    transition:0.3s;
}

.card button:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,0.3);
}

/* ===== CANCEL LINK ===== */
.cancel{
    display:block;
    text-align:center;
    margin-top:15px;
    color:#ffb3b3;
    text-decoration:none;
}

.cancel:hover{
    text-decoration:underline;
}
</style>

</head>
<body>

<div class="card">
    <h2>Edit Vehicle</h2>

    <form method="post">
        <input type="text" name="car_name" value="<?php echo $car['car_name']; ?>" placeholder="Car Name" required>
        <input type="text" name="car_model" value="<?php echo $car['car_model']; ?>" placeholder="Car Model" required>
        <input type="text" name="driver_name" value="<?php echo $car['driver_name']; ?>" placeholder="Driver Name" required>
        <input type="text" name="gps_device_id" value="<?php echo $car['gps_device_id']; ?>" placeholder="GPS Device ID" required>

        <select name="status">
            <option value="Online" <?php if($car['status']=="Online") echo "selected"; ?>>Online</option>
            <option value="Offline" <?php if($car['status']=="Offline") echo "selected"; ?>>Offline</option>
        </select>

        <button type="submit" name="update_car">Update Vehicle</button>
    </form>

    <a href="dashboard.php" class="cancel">Cancel</a>
</div>

</body>
</html>
