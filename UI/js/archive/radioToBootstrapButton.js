/*

Erzeugt aus bestehenden Radiobuttons Twitter-Bootstrap-Buttons

*/
function radioToBootstrapButton(buttonClasses) {
  var radios = $('input:radio'),
      buttons = $('button[data-toggle="radio"]'),
      btnGroup = $('<div></div>', {
        'class': 'btn-group',
        'data-toggle': 'buttons-radio'
      }),
      hiddenField = $('<input />' {
        'name': radios.attr('name')
      });
  
  radios.each(function(){
    var $this = $(this),
        button = $('<button></button>', {
          'data-name': $this.attr('name'),
          'data-id': $this.attr('id'),
          'data-value': $this.val(),
          'data-toggle': 'radio',
          'id': 'btn-' + $this.attr('id'),
          'type': 'button',
          'class': buttonClasses
        }),
        parent = $this.parent(),
        lastButton = ( ( buttons.length > 0 ) ? buttons[buttons.length-1] : false ),
        label;

    if(parent.is('label')) {
      $this.remove();
      
      if(!lastButton) {
        $this.insertAfter(parent);
      }
      else {
        $this.insertAfter(lastButton);
      }

      label = parent;
    }
    else{
      label = $('label[for="%%%%"]'.replace('%%%', $this.attr('id')));
    }

    label.remove();

    $this.replaceWith(button.text(jQuery.trim(label.text())));
    buttons = buttons.add(button);
  });

  buttons.wrapAll(btnGroup);

  buttons.on('click', function(event){
    console.log('bla');
  });

}