<?php include './partials/connections.php'; ?>
<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=library.xls");

$query = $_POST['query'];
$tableResult = $conn->query($query);
?>

<table border="1" style="width: 100%;">
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
                <tr>
                    <td><?= $i++ ?></td> 
                    <td><?= $row['cardNo'] ?></td> 
                    <td><?= $stdRow['name'] ?></td> 
                    <td><?= $stdRow['category'] ?></td> 
                    <td><?= $row['inTime'] ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>