<?php 
session_start();
include './partials/connections.php';

if (isset($msg)) {
    unset($msg);
}
if (isset($_POST['rollNo'])) {
    $rollNo = $_POST['rollNo'];
    $queryCheck = "select * from students where cardNo='$rollNo'";
    $result = $conn->query($queryCheck);
    if ($result->num_rows == 0) {
        $msg = "Roll Number '$rollNo' Not Found !";
    } else {
        $currentDateTime = date("Y-m-d H:i:s");
        $query = "insert into gateEntry values ('$rollNo', '$currentDateTime')";
        $conn->query($query);
        unset($_POST['rollNo']);
    }
}

$todayDate = date("Y-m-d");
$query = "select * from gateEntry WHERE DATE(inTime) = '$todayDate' order by inTime desc";
$result = $conn->query($query);

$queryV = "select * from visitor where Date(inTime) = '$todayDate' order by inTime desc";
$resultV = $conn->query($queryV);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome To Library</title>
    <link rel="stylesheet" href="./style/gateStyle.css">
</head>
<body>
    <h1 class="pageTitle">
        <img src="./res/nitLogo.png" alt="nitj logo" class="nitIcon">
        Central Library NIT Jalandhar
        <a href="./addStudents.php" class="switchPageBtn">Admin</a>
    </h1>
    <div class="form-wrap">
        <form action="./index.php" method="post">
            <img src="./res/id-card.png" alt="icard logo" class="icardIcon">
            <!-- NOTE!! change the pattern to \d{n} where n is the length of rollnumber, or remove this -->
            <input type="text" pattern="\d{8}" placeholder="Enter Roll Number" name="rollNo">
            <button type="submit">Enter</button>
        </form>
    </div>
    <?php if (isset($msg)) { ?>
        <div class="error"><?= $msg ?></div>
    <?php } ?>
    <div class="table-wrap">
        <table class="studentTable">
            <thead>
                <tr>
                    <th>Sr No</th> <th>Roll No.</th> <th>Name</th> <th>Category</th> <th>In-Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows != 0) { ?>
                    <?php $i = 0;?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { 
                        $queryStdData = "select name, category from students WHERE cardNo='".$row['cardNo']."'";
                        $stdRow = $conn->query($queryStdData)->fetch_assoc();
                        ?>
                        <tr>
                            <td><?= $i++ ?></td> <td><?= $row['cardNo'] ?></td> <td><?= $stdRow['name'] ?></td> <td><?= $stdRow['category'] ?></td> <td><?= $row['inTime'] ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <h2>Visitors <button class="submit" class="showDialogBtn" onclick="showDialog()">Add Visitor</button></h2>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Sr No</th> <th>Visitor Name</th> <th>Visitor Contact</th> <th>In-Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultV->num_rows != 0) { ?>
                    <?php $v = 0;?>
                    <?php while ($row = mysqli_fetch_assoc($resultV)) { 
                        ?>
                        <tr>
                            <td><?= $v++ ?></td> <td><?= $row['name'] ?></td> <td><?= $row['contact'] ?></td> <td><?= $row['inTime'] ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>



    <div class="addVisitorDialog">
        <form action="addVisitor.php" method="post">
            <h2>Add a visitor</h2>
            <input type="text" name="visitorName" placeholder="Visitor Name" required>
            <input type="text" name="visitorContact" placeholder="Visitor Contact" required>
            <input type="email" name="visitorEmail" placeholder="Visitor Email" value="">
            <div class="btns">
                <button type="button" onclick="hideDialog()">Cancel</button>
                <button type="submit">Submit</button>
            </div>
        </form>
    </div>
</body>
<script>
    const dialogBox = document.querySelector(".addVisitorDialog");
    dialogBox.classList.add("hidden");

    function showDialog() {
        dialogBox.classList.remove("hidden");
    }
    function hideDialog() {
        dialogBox.classList.add("hidden");
    }
</script>
</html>