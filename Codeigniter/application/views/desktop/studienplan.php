<?php include('header.php'); ?>

<h1>Desktop Studienplan View</h1>

<style>
    #tablestyle{
        margin-top: 5%;
        border-collapse: collapse;
        border: 1px dotted black;
    }
    
    .coloumnstyle{
        float: left;
    }
    
    #box_border_1{
        display: block; 
        border-top: 1px solid black;
        border-left: 1px solid black;
        border-right: 1px solid black;
    }
    
    #box_border_2{
        display: block; 
        border-bottom: 1px solid black;
        border-left: 1px solid black;
        border-right: 1px solid black;
    }
    
</style>

<table id="tablestyle">
    <tr>
        
    <?php foreach($studienplan as $semester): ?>
        <?php $count = 0;
        foreach($semester as $modul): ?>
        <td class="coloumnstyle"> <?php echo 'Semester '.$count ;?>
                <?php foreach($modul as $data): ?>

                <div id="box_border_1"><?php echo $data['Kurzname']; ?></div>
                <div id="box_border_2"><?php echo "Notenpunkte :".$data['Notenpunkte']; ?></div>

                <?php endforeach; ?>
            </td>
        <?php $count++;
        endforeach; ?>
    <?php endforeach; ?>
                    
    </tr>
</table>

<?php include('footer.php'); ?>