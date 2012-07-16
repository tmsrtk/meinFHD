<?php

$formdata = array(
    'class' => 'span2',
    'name' => $name,
    'placeholder' => 'kein Eintrag',
    'value' => $value
    );

?>


<div class="span2">
	<?php echo form_input($formdata); ?>
</div>