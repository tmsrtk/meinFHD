<div class="well">
    
    <h3>Your file was successfully uploaded!</h3>

    <ul>
    <?php foreach ($upload_data as $item => $value):?>
    <li><?php echo $item;?>: <?php echo $value;?></li>
    <?php endforeach; ?>
    </ul>

    <p><?php echo anchor('admin/stdplan_import', 'Weitere Datei hochladen?'); ?></p>
    
</div>
