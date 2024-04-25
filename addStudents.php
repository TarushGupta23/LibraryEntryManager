<?php 
include './partials/connections.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
?>
<?php
session_start();

if(isset($_POST['addExcel'])) {
    $inputFileNamePath = $_FILES['addDataFile']['tmp_name'];
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
    $data = $spreadsheet->getActiveSheet()->toArray();
    $count = "1";

    foreach($data as $row) {
        if ($count == "1") {
            $count = "2";
            continue;
        }
        $CardNo = $row['0'];
        $Name = $row['1'];
        $Category = $row['2'];
        $Branch = $row['3'];
        $Email = $row['4'];
        
        $queryCheck = "select * from students where cardNo='$CardNo'";
        $result = $conn->query($queryCheck);
        if ($CardNo != "") {
            if ($result->num_rows != 0) {
                $queryDelete = "DELETE FROM students WHERE cardNo = '$CardNo'";
                $conn->query($queryDelete);
            }
            $query = "insert into students values ('$CardNo', '$Name', '$Category', '$Branch', '$Email')";
            $conn->query($query);
        }
    }
    unset($_POST['addExcel']);
}

if (isset($_POST['deleteStudent'])) {
    $delCard = $_POST['deteleCard'];
    $delDate = $_POST['deleteDate'];
    $deleteStdQuery = "DELETE FROM gateEntry WHERE cardNo = '".$delCard."' and inTime = '".$delDate."'";
    $conn->query($deleteStdQuery);
    unset($_POST['deleteStudent']);
}
if (isset($_POST['deleteVisitor'])) {
    $delCard = $_POST['deteleCard'];
    $delDate = $_POST['deleteDate'];
    $deleteStdQuery = "DELETE FROM visitor WHERE contact = '".$delCard."' and inTime = '".$delDate."'";
    $conn->query($deleteStdQuery);
    unset($_POST['deleteVisitor']);
}

$conditionSet = false;
if (isset($_POST['cardNum']) && $_POST['cardNum'] != "") {
    $c1 = "cardNo = '".$_POST['cardNum']."'";
    $c3 = "contact = '".$_POST['cardNum']."'";
    $conditionSet = true;
} else {
    $c1 = "1";
    $c3 = '1';
}
if (isset($_POST['dateTime']) && $_POST['dateTime'] != "") {
    $date = $_POST['dateTime'];
    $c2 = "DATE(inTime) = '$date'";
    $conditionSet = true;
} else {
    $c2 = "1";
}
$tableQuery = "select * from gateEntry where ".$c1." and ".$c2." order by inTime desc";
$tableQuery2 = "select * from visitor where ".$c3." and ".$c2." order by inTime desc";
if ($conditionSet != true) {
    $tableQuery = "select * from gateEntry where 0";
    $tableQuery2 = "select * from visitor where 0";
}
$tableResult = $conn->query($tableQuery);
$tableResult2 = $conn->query($tableQuery2);
?>

<link rel="stylesheet" href="./style/adminPanel.css">
<body>
<h1 class="pageTitle">
    <img src="./res/nitLogo.png" alt="nitj logo" class="nitIcon">
    Central Library NIT Jalandhar
    <a href="./index.php" class="switchPageBtn">Home</a>
</h1>
<div class="wrap">
    <form action="addStudents.php" method="post" enctype="multipart/form-data">
        <h2>Add Students Excel Data</h2>
        <h4>rollno | name | category | branch | email</h4>
        <input type="file" id="addDataFile" name="addDataFile" accept=".xls, .xlsx" required>
        <input type="submit" value="upload" class="submitBtn" name="addExcel">
    </form>

    <form action="addStudents.php" method="post">
        <h2>History</h2>
        <input type="text" name="cardNum" placeholder="Enter Roll Number or contact">
        <input type="text" name="dateTime" pattern="\d{4}-\d{2}-\d{2}" placeholder="Enter Date [yyyy-mm-dd]">
        <input type="submit" value="search" class="submitBtn" name="execute">
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Sr No</th> <th>Roll No.</th> <th>Name</th> <th>Category</th> <th>In-Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tableResult->num_rows != 0) { ?>
                    <?php $i = 0;?>
                    <?php while ($row = mysqli_fetch_assoc($tableResult)) { 
                        $queryStdData = "select name, category from students WHERE cardNo='".$row['cardNo']."'";
                        $stdRow = $conn->query($queryStdData)->fetch_assoc();
                        ?>
                        <tr onclick="showPopup('<?= $row['cardNo'] ?>', '<?= $row['inTime'] ?>')">
                            <td><?= $i++ ?></td> <td><?= $row['cardNo'] ?></td> <td><?= $stdRow['name'] ?></td> <td><?= $stdRow['category'] ?></td> <td><?= $row['inTime'] ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        </div>
        
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Sr No</th> <th>Visitor Name</th> <th>Visitor Contact</th> <th>Email</th> <th>In-Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tableResult2->num_rows != 0) { ?>
                    <?php $i = 0;?>
                    <?php while ($row = mysqli_fetch_assoc($tableResult2)) { ?>
                        <tr onclick="showPopup2('<?= $row['contact'] ?>', '<?= $row['inTime'] ?>')">
                            <td><?= $i++ ?></td> <td><?= $row['name'] ?></td> <td><?= $row['contact'] ?></td> <td><?= $row['email'] ?></td> <td><?= $row['inTime'] ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </form>
    <form action="extractData.php" method="post">
        <input type="text" name="query" value="<?= $tableQuery ?>" style="display: none;">
        <?php if ($conditionSet) { ?>
            <input type="submit" value="extract students" class="submitBtn" name="extract">
        <?php } ?>
    </form>
    <form action="extractVisitors.php" method="post">
        <input type="text" name="query" value="<?= $tableQuery2 ?>" style="display: none;">
        <?php if ($conditionSet) { ?>
            <input type="submit" value="extract visitors" class="submitBtn" name="extract">
        <?php } ?>
    </form>

</div>
<div id="popup" class="popup">
    <form action="addStudents.php" method="post">
        <input type="text" name="deteleCard" id="popCardNum">
        <input type="text" name="deleteDate" id="popCardDate">
        <h3>Do you want to delete this entry?</h3>
        <div class="form-group btns">
            <input type="submit" value="Confirm" name="deleteStudent" class="submitBtn">
            <input type="button" value="Cancel" onclick="closePopup()" class="submitBtn">
        </div>
    </form>
</div>
<div id="popup2" class="popup">
    <form action="addStudents.php" method="post">
        <input type="text" name="deteleCard" id="popCardNum2">
        <input type="text" name="deleteDate" id="popCardDate2">
        <h3>Do you want to delete this entry?</h3>
        <div class="form-group btns">
            <input type="submit" value="Confirm" name="deleteVisitor" class="submitBtn">
            <input type="button" value="Cancel" onclick="closePopup2()" class="submitBtn">
        </div>
    </form>
</div>

<script>
    const popup = document.getElementById("popup");
    const cardNumInp = document.getElementById("popCardNum");
    const dateInp = document.getElementById("popCardDate");
    
    const popup2 = document.getElementById("popup2");
    const cardNumInp2 = document.getElementById("popCardNum2");
    const dateInp2 = document.getElementById("popCardDate2");

    function showPopup(cardNo, date) {
        popup.style.display = "block";
        cardNumInp.value = cardNo;
        dateInp.value = date;
    }
    function closePopup(params) {
        popup.style.display = "none";
    }

    function showPopup2(cardNo, date) {
        popup2.style.display = "block";
        cardNumInp2.value = cardNo;
        dateInp2.value = date;
    }
    function closePopup2(params) {
        popup2.style.display = "none";
    }
</script>
</body>