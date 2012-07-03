<?php include('header.php'); ?>

<div class="container-fluid">
  <h6 class="row-fluid">Persönliche Einstellungen</h6>
  <form id="user-details" class="form-vertical" method="post" action="<?php print base_url(); ?>einstellungen">
    <div class="row-fluid">
      <div class="span12" id="matrikel-und-sem">
        <div class="well well-small">
          Matrikelnummer: <span class="number"><?php echo $info['Matrikelnummer'] ?></span><br />
          Fachsemester: <span class="number"><?php echo $info['Semester'] ?></span>
        </div>
      </div>
    </div>
    <div class="row-fluid">
      <fieldset class="span4" id="login-details">
        <div class="well well-small">
          <h3 data-toggle="collapse" data-parent=".row-fluid" data-target="#login-details .form-elements">Login-Details</h3>
          <div class="collapse form-elements">
              <label for="login-name">Loginname</label>
              <input type="text" class="input-xxlarge" id="login-name" name="login" placeholder="Loginname"<?php if(isset($info['LoginName'])) echo ' value="' . $info['LoginName'] . '"' ?> />
              <label for="password">Passwort</label>
              <input type="password" class="input-xxlarge" id="password" name="pw" placeholder="Passwort" />
              <label for="confirm-password">Passwort bestätigen</label>
              <input type="password" class="input-xxlarge" id="confirm-password" name="pw2" placeholder="Passwort bestätigen" />
          </div>
        </div>
      </fieldset>
      <fieldset class="span4" id="contact-details">
        <div class="well well-small">
          <h3 data-toggle="collapse" data-parent=".row-fluid" data-target="#contact-details .form-elements">Kontaktinformationen</h3>
          <div class="collapse form-elements">
            <label for="first-name">Vorname</label>
            <input type="text" class="input-xxlarge" id="first-name" name="firstname" placeholder="Vorname"<?php if ( isset($info['Vorname']) ) echo ' value="' . $info['Vorname'] . '"' ?> />
            <label for="last-name">Nachname</label>
            <input type="text" class="input-xxlarge" id="last-name" name="lastname" placeholder="Nachname"<?php if ( isset($info['Nachname']) ) echo ' value="' . $info['Nachname'] . '"' ?> />
            <label for="email">E-Mail-Adresse</label>
            <input type="email" class="input-xxlarge" id="email" name="email" placeholder="E-Mail-Adresse"<?php if ( isset($info['Email']) ) echo ' value="' . $info['Email'] . '"' ?> />
            <label for="private-correspondence" class="checkbox"><input type="checkbox" id="private-correspondence" name="emailflag"<?php if ( isset($info['EmailDarfGezeigtWerden']) && $info['EmailDarfGezeigtWerden'] == 1 ) echo ' checked' ?> /> Dozenten dürfen mich unter dieser Adresse auch persönlich erreichen.</label>
          </div>
        </div>
      </fieldset>
      <fieldset class="span4" id="study-course-details">
        <div class="well well-small">
          <h3 data-toggle="collapse" data-parent=".row-fluid" data-target="#study-course-details .form-elements">Studiengang</h3>
          <div class="collapse form-elements">
            <label for="study-course">Studiengang</label>
            <select name="stgid" id="study-course" class="input-xxlarge">
              
              <?php
                $option = '<option value="%s">%s</option>';

                echo sprintf($option, '0', '--Bitte Wählen--');

                $option = '<option value="%s">%s (PO%s)</option>';
                
                for( $i = 0; $i < count($stgng); $i++ ) {
                  $s = "";
                  if ( $stgng[$i]['StudiengangID'] == $info['StudiengangID'] ) {$s = '<option selected value="%s">%s (PO%s)</option>';}
                  else {$s = $option;}
                  echo sprintf($s, $stgng[$i]['StudiengangID'], $stgng[$i]['StudiengangName'], $stgng[$i]['Pruefungsordnung']);
                }

              ?>
            </select>
            <label for="year">Startjahr</label>
            <input type="text" class="input-xxlarge" id="year" name="year" placeholder="Startjahr"<?php if ( isset($info['StudienbeginnJahr']) ) echo ' value="' . $info['StudienbeginnJahr'] . '"' ?> />
            <label for="start-term-winter" class="radio"><input type="radio" id="start-term-winter" name="semester" value="WS"<?php if ( isset($info['StudienbeginnSemestertyp']) && $info['StudienbeginnSemestertyp'] == 'WS' ) echo ' checked' ?> /> Wintersemester</label>
            <label for="start-term-summer" class="radio"><input type="radio" id="start-term-summer" name="semester" value="SS"<?php if ( isset($info['StudienbeginnSemestertyp']) && $info['StudienbeginnSemestertyp'] == 'SS' ) echo ' checked' ?> /> Sommersemester</label>
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

<?php include('footer.php'); ?>