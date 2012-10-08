/*
  @author Nils Röhrig
*/
// run self invoking anonymous function
(function($) {
  /*
    figures out if labels respective formfield is of type select or comes with 
    a placeholder. if true, label got hid. this function should only be called 
    when placeholders are supported by the current browser.
    @returns undefined if used in wrong context (aka "this" not referring a label-tag)
    @returns true if label got hid
    @retuns false if label didn't change visibility.
  */
  function hideIfAppropriate() {
    var $this = $(this),
          formField = $('#' + $this.attr('for'));
    if($this.is('label')) {
      // when formField is a dropdown list or comes with a placeholder, label got hid
      if ( formField.is('select') || formField.attr('placeholder') !== undefined ) {
        $this.hide();
      }
      return this;
    }
    else {
      return undefined;
    }
  }

  // get all labels and hide them if appropriate
  if ( Modernizr.input.placeholder ) {
    $('label').each(hideIfAppropriate);
  }
})(jQuery);