(function($){
  function cb2cs( checkboxes, labels ) {
  var $checkboxes = ( checkboxes instanceof jQuery ) ? checkboxes : $(checkboxes),
      labels = (labels != undefined) ? labels : { on: 'on', off: 'off' };

  if( !$checkboxes.is('input:checkbox') ) return undefined;

  $checkboxes.each( function() {
    var $this = $(this),
        $parent = $this.parent(),
        $label;

    if ( $parent.is('label') ) {
      $label = $parent;
      $this.insertAfter($parent);
    }
    else {
      $label = $('label[for="' + $this.attr('id') + '"]');
    }

    $label.remove();
    
    var $switch = $('<div></div>', {
          'class': ( $this.is(':checked') ) ? 'switch active' : 'switch'
        }),
        $text = $('<span></span>', {
          'text': $label.text(),
          'class': 'text'
        }),
        $options = $('<span></span>', {
          'class': 'options'
        }),
        $on = $('<span></span>', {
          'class': 'on',
          'text': (labels.on != undefined) ? labels.on : 'on'
        }),
        $off = $('<span></span>', {
          'class': 'off',
          'text': (labels.off != undefined) ? labels.off : 'off'
        }),
        $slider = $('<span></span>', {
          'class': 'slider',
          'html': '&nbsp;'
        });

    $switch
      .append($text)
      .append($options);

    $options
      .append($off)
      .append($on)
      .append($slider);

    $switch.insertAfter($this);
    $switch.append($this.remove().hide());

  });

  $(document).on('click', '.switch', function( event ) {
    var $this = $(this),
        $cb = $this.find('input:checkbox');

    if( $cb.is(':checked') ) {
      $cb.removeAttr('checked');
    }
    else  {
      $cb.attr('checked', 'checked');
    }
    $this.toggleClass('active');
  });
}

cb2cs($('input:checkbox'));
})(jQuery);