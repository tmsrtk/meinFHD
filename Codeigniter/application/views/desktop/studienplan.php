<?php include('header.php'); ?>

<h1>Desktop Stundenplan View</h1>

<table>
    <tr><th>Semester</th><th>Kursname</th><th>Hoeren</th><th>Schreiben</th><th>Notenpunkte</th></tr>
    
    <?php 
        $count = 0;
        foreach($studienplan as $plan): 
    ?>
    <tr><td><?php echo $plan[$count]['Semester']; ?></td>
        <td><?php echo $plan[$count]['Kursname']; ?></td>
        <td><?php echo $plan[$count]['Hoeren']; ?></td>
        <td><?php echo $plan[$count]['Schreiben']; ?></td>
        <td><?php echo $plan[$count]['Notenpunkte']; ?></td>
    <?php endforeach; ?>
</table>

<?php include('footer.php'); ?>