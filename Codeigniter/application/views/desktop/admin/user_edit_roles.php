<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzerrollen bearbeiten<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<?php
	// definition of basic variables
	$data_formopen = array('class' => 'form-search');

    // prepare data for the role filter dropdown
    $data_role = array();
    $data_role = $all_roles;
    // add "placeholder" as first element
    array_unshift($data_role, 'Bitte w&auml;hlen');
    $data_role_ext = 'class="role_filter_dd" id="role_filter"';

    $searchbox_content = '';
    // if an value for the searchbox is set in flashdata to relocate the dataset
    $searchbox_content = $this->session->flashdata('searchbox');

    $data_search = array(
        'id' => 'user_cr_search',
        'class' => 'search-query',
        'name' => 'search_user',
        'placeholder' => 'Benutzer suchen',
        'value' => $searchbox_content
    );
?>

<div class="row-fluid">
    <div class="span12"><h2>Benutzerrollenverwaltung</h2>
        <p>
            Hier k&ouml;nnen alle Benutzer mit ihren zugeh&ouml;hrigen Rollen bearbeitet werden.
        </p>
    </div>
</div>
<div class="row-fluid">
    <?php echo form_open('', $data_formopen); ?>
    <div class="span4"><h5>Anzahl:</h5><span class="badge badge-success" id="result_counter"></span></div>
    <div class="span4"><h5>Filter</h5><?php echo form_dropdown('role_filter_dd', $data_role, '0', $data_role_ext); ?></div>
    <div class="span4"><h5>Suche</h5><?php echo form_input($data_search); ?></div>
    <?php echo form_close(); ?>
</div>
<hr/>

<div class="row-fluid">
    <div id="list_header">
        <div class="span2"><strong>Loginname</strong></div>
        <div class="span2"><strong>Nachname</strong></div>
        <div class="span2"><strong>Vorname</strong></div>
        <div class="span3"><strong>E-Mail</strong></div>
        <div class="span2"><strong>Rollen</strong></div>
        <div class="span1"><strong>Speichern?</strong></div>
    </div>
</div>
<div class="row-fluid">
    <div id="user_content">
    <!-- content with the single user forms goes here -->
    </div>
</div>

<div id="modalcontent">
    <!-- place for rendering modals -->
</div>

<?php endblock(); ?>

<?php startblock('headJSfiles'); ?>
                {meinfhd_user_search: "<?php print base_url(); ?>resources/js/meinfhd.user_search.js"},
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

                UsersEditAjax.init({
                    roleDropdown : $('#role_filter'),
                    searchInput : $('#user_cr_search'),
                    dataContent : $('div#user_content'),
                    counter : $('#result_counter'),
                    site_url : "<?php print site_url(); ?>",
                    subviewtype : "edit_user_role"
                });

                // recognize if the save button is pressed for any user row and open up a confirmation modal
                $("#user_content").on("click", "input[id^=save_user_roles_]", function() {

                    $(this).attr("data-clicked", "true");
                    // display an modal to verify the action
                    _showModal('Rollen&auml;nderungen speichern', 'Sollen die Rollen&auml;nderungen f&uuml;r den ausgew&auml;hlten Nutzer wirklich gespeichert werden?', true);

                    // prevent default submit behaviour
                    return false;
                });
<?php endblock(); ?>
<?php end_extend(); # end extend main template ?>