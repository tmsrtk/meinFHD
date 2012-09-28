<?php include('header.php'); ?>

<div class="container-fluid">
  <h6 class="row-fluid">Persönliche Einstellungen</h6>
  <form id="user-details" class="form-vertical" method="post" action="<?php print base_url(); ?>einstellungen">
      
      <!-- the hidden-input fields are kinda a workaround -->
      <input type="hidden" name="login" <?php if(isset($info['LoginName'])) echo ' value="' . $info['LoginName'] . '"' ?> >
      <input type="hidden" name="email" <?php if(isset($info['Email'])) echo ' value="' . $info['Email'] . '"' ?> >
      <input type="hidden" name="firstname" <?php if(isset($info['Vorname'])) echo ' value="' . $info['Vorname'] . '"' ?> >
      <input type="hidden" name="lastname" <?php if(isset($info['Nachname'])) echo ' value="' . $info['Nachname'] . '"' ?> >
      <input type="hidden" name="year" <?php if(isset($info['StudienbeginnJahr'])) echo ' value="' . $info['StudienbeginnJahr'] . '"' ?> >
      <input type="hidden" name="semester" <?php if(isset($info['StudienbeginnSemestertyp'])) echo ' value="' . $info['StudienbeginnSemestertyp'] . '"' ?> >
      <input type="hidden" name="emailflag" <?php if(isset($info['EmailDarfGezeigtWerden'])) echo ' value="' . $info['EmailDarfGezeigtWerden'] . '"' ?> >
    <div class="row-fluid">
      <div class="span12" id="matrikel-und-sem">
        <div class="well well-small">
          Blablabla ändern des Studiengangs blablabla LINK: <a href="<?php echo '../'.$filepath; ?> ">Link</a>
          
        </div>
      </div>
    </div>
    <div class="row-fluid"> 
      <fieldset class="span4" id="study-course-details">
        <div class="well well-small">
          <h3 >Studiengang</h3>
          <div class="form-elements">
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