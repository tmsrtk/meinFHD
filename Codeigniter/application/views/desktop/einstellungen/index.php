<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Tag<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<div class="container-fluid">
  <h6 class="row-fluid">Persönliche Einstellungen</h6>
  <form id="user-details" class="form-vertical" method="post" action="<?php print base_url(); ?>einstellungen">

    <div class="row-fluid">
      <div class="span12" id="basisinfos">
        <div class="alert alert-block alert-info">
          <h3>Basisinfos</h3>

    <?php 

    if($info['MatrikelnummerFlag'] != 1) {

    ?>
        <p>Bitte geben Sie zunächst ihre Matrikelnummer an. Diese Eingabe ist einmalig und wichtig für Ihre Zuordnung zu Kursen.</p>

        <div class="form-elements" style="margin: 1em 0;">
          <label for="matrikelnummer">Matrikelnummer</label>
          <input type="number" class="input-xxxlarge input-block-level" id="matrikelnummer" name="matrikel" placeholder="Matrikelnummer" />
        </div>
        <ul style="list-style: none; margin:0; padding 0;">    
    <?php 

    } else {

    ?>
          <ul style="list-style: none; margin:0; padding 0;">
            <li>Matrikelnummer: <?php echo $info['Matrikelnummer'] ?></li>
    <?php 

    }

    ?>
            <li>Studiengang: <?php echo $info['StudiengangName'] ?> <a href="<?php print base_url(); ?>einstellungen/studiengangWechseln">(ändern)</a></li>
            <li>Studienbeginn: <?php echo $info['StudienbeginnSemestertyp'] . " " . $info['StudienbeginnJahr']; ?></li>
            <li>Fachsemester: <?php echo $info['Semester'] ?></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="row-fluid">
      <fieldset class="span6" id="login-details">
        <div class="well well-small">
          <h3>Login-Details</h3>
          <div class="form-elements">
              <label for="login-name">Loginname</label>
              <input type="text" class="input-xxxlarge input-block-level" id="login-name" name="login" placeholder="Loginname"<?php if(isset($info['LoginName'])) echo ' value="' . $info['LoginName'] . '"' ?> />
              <label for="password">Passwort</label>
              <input type="password" class="input-xxxlarge input-block-level" id="password" name="pw" placeholder="Passwort" />
              <label for="confirm-password">Passwort bestätigen</label>
              <input type="password" class="input-xxxlarge input-block-level" id="confirm-password" name="pw2" placeholder="Passwort bestätigen" />
          </div>
        </div>
      </fieldset>
      <fieldset class="span6" id="contact-details">
        <div class="well well-small">
          <h3>Kontaktinformationen</h3>
          <div class="form-elements">
            <label for="first-name">Vorname</label>
            <input type="text" class="input-xxxlarge input-block-level" id="first-name" name="firstname" placeholder="Vorname"<?php if ( isset($info['Vorname']) ) echo ' value="' . $info['Vorname'] . '"' ?> />
            <label for="last-name">Nachname</label>
            <input type="text" class="input-xxxlarge input-block-level" id="last-name" name="lastname" placeholder="Nachname"<?php if ( isset($info['Nachname']) ) echo ' value="' . $info['Nachname'] . '"' ?> />
            <label for="email">E-Mail-Adresse</label>
            <input type="email" class="input-xxxlarge input-block-level" id="email" name="email" placeholder="E-Mail-Adresse"<?php if ( isset($info['Email']) ) echo ' value="' . $info['Email'] . '"' ?> />
            <label for="private-correspondence" class="checkbox"><input type="checkbox" id="private-correspondence" name="emailflag"<?php if ( isset($info['EmailDarfGezeigtWerden']) && $info['EmailDarfGezeigtWerden'] == 1 ) echo ' checked' ?> /> Dozenten dürfen mich unter dieser Adresse auch persönlich erreichen.</label>
          </div>
        </div>
      </fieldset>
    </div>

    <div class="row-fluid">
      <div class="alert alert-info clearfix">
        <button type="submit" class="btn btn-large" value="save"><i class="icon-hdd"></i> Speichern</button>
      </div>
    </div>
  </form>
</div>

<?php endblock(); ?>

<?php startblock('headJSfiles'); ?>
	{meinfhd_radiobuttons: "<?php print base_url(); ?>resources/js/meinfhd.radiobuttons.js"},
	{meinfhd_checkboxes: "<?php print base_url(); ?>resources/js/meinfhd.checkboxes.js"},
<?php endblock(); ?>

<?php end_extend(); ?>