<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzer bearbeiten<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<?php
	// needed vars
	$data_formopen = array('class' => 'form-search', 'id' => 'edit_user');
	$data_role = array();
	$data_role = $all_roles;
	// add as first element
	array_unshift($data_role, 'Bitte w&auml;hlen');
	$data_role_ext = 'class="user_change_rolle_dd" id="user_cr_role"';

	$searchbox_content = '';
	$searchbox_content = $this->session->flashdata('searchbox');

	$data_search = array(
		'id' => 'user_cr_search',
		'class' => 'search-query',
		'name' => 'search_user',
		'placeholder' => 'Benutzer suchen',
		'value' => $searchbox_content
		);
	//--------------------------------------------------------------------------
?>
	<div class="row-fluid">
		<?php echo form_open('', $data_formopen); ?>
		<div class="span4"><h2>Benutzer bearbeiten</h2>Anzahl: <span class="badge badge-success" id="result_counter"></span></div>
		<div class="span4"><h5>Filter</h5><?php echo form_dropdown('user_change_rolle_dd', $data_role, '0', $data_role_ext); ?></div>
		<div class="span4"><h5>Suche</h5><?php echo form_input($data_search); ?></div>
		<?php echo form_close(); ?>
	</div>
	<hr/>

	<?php echo validation_errors(); // validation errors or empty string otherwise ?>
    <div class="row-fluid">
        <div id="user_overview">
            <div class="span2"><strong>Loginname</strong></div>
            <div class="span2"><strong>Nachname</strong></div>
            <div class="span2"><strong>Vorname</strong></div>
            <div class="span2"><strong>E-Mail</strong></div>
            <div class="span2"><strong>Funktion</strong></div>
            <div class="span2"><strong>Ausf&uuml;hren?</strong></div>
        </div>
    </div>
    <div id="user_content">
        <!-- User Data -->
    </div>
	<div id="modalcontent"></div>

<?php endblock(); ?>

<?php startblock('headJSfiles'); ?>
                {meinfhd_user_search: "<?php print base_url(); ?>resources/js/meinfhd.user_search.js"},
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

	UsersEditAjax.init({
		roleDropdown : $('#user_cr_role'),
		searchInput : $('#user_cr_search'),
		dataContent : $('div#user_content'),
		counter : $('#result_counter'),
        site_url : "<?php print site_url(); ?>",
        subviewtype : "edit_user_information"
	});


	// live click listener (because of ajax and new content) to override default submit button function
	// to open the prompt dialog 
	$("#user_content").on("click", "input#save", function() {
		// determine which function was selected from the dropdown
		// 0 = speichern, 1 = pw resetten, 2 = Studienplan resetten, 3 = Als..anmelden, 4 = Benutzer loeschen
		var user_function =  $(this).parents('form[id^="edit_user_row_"]').find("[id^=user_function]").val();

    	if (user_function === '0') {
			$(this).attr("data-clicked", "true");
            _showModal('&Auml;nderungen speichern', 'Sollen die &Auml;nderungen wirklich gespeichert werden?', true);
		}
        else if (user_function === '1') {
			$(this).attr("data-clicked", "true");
            _showModal('Passwort resetten', 'M&ouml;chtest du das Passwort f&uuml;r diesen Benutzer wirklich zur&uuml;cksetzen?', true);
		}
        else if (user_function === '2') {
			$(this).attr("data-clicked", "true");
		    _showModal('Studienplan resetten', 'M&ouml;chtest du den Studienplan f&uuml;r diesen Benutzer wirklich zur&uuml;cksetzen?', true);
        }
        // login as
        else if (user_function === '3') {
			$(this).attr("data-clicked", "true");
            // if we do not use the modal box pass the information of the choosen user to the controller
            $("input[type=submit][data-clicked=true]").parents("form[id^=edit_user_row_]").submit();
            console.log($("input[type=submit][data-clicked=true]").parents("form[id^=edit_user_row_]"));
		}
        else if (user_function === '4') {
            $(this).attr("data-clicked", "true");
            _showModal('Benutzer l&ouml;schen', 'M&ouml;chtest du den Benutzer wirklich l&ouml;schen?', true);
		}
        else {

        }

		// prevent default submit behaviour
		return false;
	});

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>