/* 
  transforms the passed set of checkboxes into one fully functional twitter 
  bootstrap button group. affected ckeckboxes are hidden and their respective 
  labels are removed from the dom.
  @param jQuery Object checkboxes: the checkboxes that are transformed into 
  button group
  @param String buttonClasses: contains the classes of the future buttons in 
  typical HTML-notation. i.e. "btn btn-info"
  @returns undefined: when checkboxes is empty
  @returns jQuery Object: finalised button group
*/ 
function checkbox2BSButtons (checkboxes, buttonClasses) {
  // when checkboxes' length is lower than or equal zero, there are no checkboxes in passed set, so return undefined  
  if(checkboxes.length <= 0) return undefined;

  // creating a new button group conforming to bootstraps rules
  var btnGroup = $('<div></div>', {
        'class': 'btn-group',
        'data-toggle': (checkboxes.length > 1) ? 'buttons-checkbox' : 'button'
      });

  // figure out if last checkbox is inside a label.
  // if so, insert button group container after that label,
  // if not, insert it behind checkbox
  var last = checkboxes.last();
  if(last.parent().is('label')) {
    btnGroup.insertAfter(last.parent());
  }
  else {
    btnGroup.insertAfter('label[for="' + last.attr('id') + '"]');
  }
  
  // create a bootstrap button for each checkbox, add it to the button group, move original checkbox after button group, remove label from dom
  checkboxes.each(function(){ 
    var $this = $(this),
        label = $('label[for="' + $this.attr('id') + '"]')
        button = $('<button></button>', {
          'type': 'button',
          // use labeltext as buttontext
          'text': label.text(),
          'data-value': $this.val(),
          'data-id': $this.attr('id'),
          // figure out if current checkbox is checked and set class appropriately following bootstraps rules
          'class': ((this.checked) ? buttonClasses + ' active' : buttonClasses)
        });
    btnGroup.append(button);
    $this.insertAfter(btnGroup).hide();
    label.remove();
  });

  // create event handler for click events on buttons inside respective group checking respective checkboxes
  return btnGroup.on('click', 'button', function(event) {
    var checkbox = $( '#' + $(this).data('id'));
    if( checkbox.is(':checked') ) {
      checkbox.removeAttr('checked');
    }
    else {
      checkbox.attr('checked', 'checked');
    }
  });
}