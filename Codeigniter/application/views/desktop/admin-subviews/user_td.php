<?php

$formdata = array(
    'class' => 'span2',
    'name' => $name,
    'placeholder' => 'kein Eintrag',
    'value' => $value
    );

?>


<td>
	<?php echo form_input($formdata); ?>
</td>