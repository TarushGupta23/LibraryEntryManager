<?php include './partials/connections.php'; ?>
<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=library.xls");

$query = $_POST['query'];
$resultV = $conn->query($query);
?>

<table border="1" style="width: 100%;">
            <thead>
                <tr>
                    <th>Visitor Name</th> <th>Visitor Contact</th> <th>Visitor Email</th> <th>In-Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultV->num_rows != 0) { ?>
                    <?php $v = 0;?>
                    <?php while ($row = mysqli_fetch_assoc($resultV)) { ?>
                        <tr>
                            <td><?= $row['name'] ?></td> <td><?= $row['contact'] ?></td> <td><?= $row['email']?></td> <td><?= $row['inTime'] ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
</table>