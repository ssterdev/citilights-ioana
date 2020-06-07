jQuery(document).ready(function($) {
  // image upload 
  $(document).on('click', '.noo-wpmedia', function(e) {
    e.preventDefault();
    var $this = $(this);
    var custom_uploader = wp.media({
        title: 'Select Image',
        button: {
            text: 'Insert image'
        },
        multiple: false  // Set this to true to allow multiple files to be selected
    })
    .on('select', function() {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
        $this.val(attachment.id).change();
        
    })
    .open();
  });

  $('.parent-control').change(function() {
    var $this = $(this);
    var parent_active = false;
    var parent_type = $this.attr('type');
    var parent_id   = $this.attr('id');
    if(parent_type == 'text') {
      parent_active = ($this.val() !== '');
    } else if(parent_type == 'checkbox') {
      parent_active = ($this.is(':checked'));
    }

    if(parent_active) {
      $('.' + parent_id + '-child').show().find('input.parent-control').change();
    } else {
      $('.' + parent_id + '-child').hide().find('input.parent-control').change();
    }
  });

  $('.noo-slider').each(function() {
    var $this = $(this);

    var $slider = $('<div>', {id: $this.attr("id") + "-slider"}).insertAfter($this);
    $slider.slider(
    {
      range: "min",
      value: $this.val() || $this.data('min') || 0,
      min: $this.data('min') || 0,
      max: $this.data('max') || 100,
      step: $this.data('step') || 1,
      slide: function(event, ui) {
        $this.val(ui.value).attr('value', ui.value).change();
      }
    }
    );

    $this.change(function() {
      $slider.slider( "option", "value", $this.val() );
    });
  });
  /**
   * Process when click element help
   */
    if ( $('.noo-help').length > 0 ) {

      $('.noo-help').each(function(index, el) {
        
        $(this).on('click', function(event) {
          event.preventDefault();
          
          /**
           * VAR
           */
            var $$       = $(this),
              class_wrap = $$.data('class-wrap');

          /**
           * Process
           */
            $('.' + class_wrap).toggle('slow');

        });

      });

    }
    // ("#noo_additional_details").sortable({
    // revert: 100,
    // placeholder: "detail-placeholder",
    // handle: ".sort-additional-row",
    // cursor: "move"
    // });
  
    $('.add-additional-row').click(function (e) {
    e.preventDefault();

    var numVal = $(this).data("key") + 1;
    $(this).data('key', numVal);
    $(this).attr({
        "key": numVal
    });

    var newAdditionalDetail = '<tr>' +
        '<td class="action-field">' +
        '<span class="sort-additional-row"><i class="fa fa-navicon"></i></span>' +
        '</td>' +
        '<td class="field-title">' +
        '<input class="noo-ft-field" type="text" name="additional_features[' + numVal + '][additional_feature_label]" id="additional_feature_label_' + numVal + '" value="">' +
        '</td>' +
        '<td>' +
        '<input class="noo-ft-field" type="text" name="additional_features[' + numVal + '][additional_feature_value]" id="additional_feature_value_' + numVal + '" value="">' +
        '</td>' +
        '<td class="action-field">' +
        '<span data-remove="' + numVal + '" class="remove-additional-row"><i class="fa fa-remove"></i></span>' +
        '</td>' +
        '</tr>';

    $('#noo_additional_details').append(newAdditionalDetail);
    removeAdditionalDetails();
    });

    var removeAdditionalDetails = function () {

        $('.remove-additional-row').click(function (event) {
            event.preventDefault();
            var $this = $(this);
            $this.closest('tr').remove();
        });
    }
    removeAdditionalDetails();

    //Sub properties
        
    var clone_sub_properties = function () {

        $('#rp-item-sub_property_wrap-wrap .rp-clone-sub-property').on('click', '.add-sub-property', function ( event ) {
            event.preventDefault();
            var btn_clone     = $(this),
                total         = btn_clone.data('total'),
                content_clone = $('#clone_element1').clone(true).html();

            content_clone = content_clone.replace(/\[0\]/g, '[' + (total + 1) + ']');
            $('.content-clone1').append('<div class="rp-sub-property-wrap rp-md-12">' + content_clone + '</div>');
            btn_clone.data('total', total + 1);

        });

    }
    clone_sub_properties();

    var remove_sub_properties = function () {

        $('body').on('click', '.remove-sub-property', function ( event ) {
            event.preventDefault();
            $(this).closest('.rp-sub-property-wrap').remove();
        });

    }
    remove_sub_properties();
    
});