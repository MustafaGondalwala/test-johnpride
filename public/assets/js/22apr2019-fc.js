var xhr;

window.onload = function () {

};


jQuery(document).ready(function () {

  var resizeTimer;
  //general on resize actions
  jQuery(window).resize(function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      FC.do_resize_actions();
    }, 30);
  });

  jQuery(document).keyup(function (e) {
    if (e.keyCode === 27) { // escape key maps to keycode `27`
      if (xhr !== null && xhr != undefined) {
        xhr.abort();
      }

    }
  });

  FC.init();

});


var FC = {

  filename_id: '',

  image_size: {},
  image_ppi: 0,
  image_rotate: 0,

  current_ppi: 0,
  current_scale: 100,
  current_img_size: {},
  current_bg_size: {},

  layout: 'tile',
  cut: '',
  fabric: '',
  quantity: 1,

  author_commision: 0,

  fabric_width: 0,
  fabric_height: 0,
  // Added GS
  fabric_weight: 0,
  total_weight: 0,
  // End Added GS

  default_ruler_width: 10,
  default_ruler_height: 10,
  ruler_width: 0,
  ruler_height: 0,

  swatch_price: 10,

  ajax_upload_handler: false,

  ajax_processing: false,

  //Artwork
  artwork_id: '',

  cart_item_id: '',

  image_type: [
    'center',
    'tile',
    'mirror',
    'vert',
    'brick'
  ],

  pixels_per_inch: 25,
  //pixels_per_inch :   85,

  // Added GS
  weight: function () {
    var cut_value = jQuery('#fc-option-cut').val();
    var fabric_width = jQuery('#fc-option-fabric').find('option:selected').attr('data-width');
    var fabric_weight = jQuery('#fc-option-fabric').find('option:selected').attr('data-weight');

    if (fabric_width < 1) {
      fabric_width = 0;
    }

    if (cut_value === 'swatch') {
      FC.fabric_weight = parseInt(fabric_weight / 25);
    }
    else if (cut_value === 'linear_metre') {
      FC.fabric_weight = parseInt(( ( ( fabric_width * 10 ) + 10 ) / 100) * fabric_weight);
    }
    else {
      FC.fabric_weight = 0;
    }
    FC.total_weight = FC.fabric_weight * FC.quantity;
  },
  // End Added GS

  do_resize_actions: function () {

    jQuery(document).trigger("resize_actions");


    FC.set_ruler_dimensions();
    FC.update_ruler();
    FC.calculate_pixels_per_inch();
    FC.ppi_update();
    FC.update_layers_bg_size();

  },

  init: function () {

    FC.upload_init();

    //open the file select dialog
    jQuery("#fc-upload-form input#userImage").change(function () {

      FC.user_image_change();

    });


    //expand / collapse data block
    jQuery('#fc-edit .data-area h2').click(function (event) {

      var element = jQuery(this);

      if (event.currentTarget !== 'span') {
        element = jQuery(this).find('span');
      }

      FC.data_block_slide(element);
    });


    jQuery('.tooltip_element').tooltipster({
      animation: 'fade',
      delay: 200,
      trigger: 'hover',
      theme: 'tooltipster-noir'
    });

    //init the slider range
    FC.range_slider_init();
    FC.range_slider_adjust();

    //init the layout selection
    FC.layout_init();
    FC.layout_change(FC.layout);

    //init the select select
    FC.cut_init();

    //init the fabric select
    FC.fabric_init();

    //init the spinner
    FC.spinner_init();

    //init the icheck-boxes
    FC.icheck_init();

    //tooltips init
    FC.tooltips_init();

    jQuery(document).trigger('FC_initialized');

  },

  upload_init: function () {

    //on form submit action
    jQuery('#fc-upload-form').submit(function (e) {
      if (jQuery('#userImage').val()) {
        e.preventDefault();

        //reset the progress
        jQuery("#progress-bar").width('0%');
        FC.show_upload_progress();

        var form = jQuery(this).ajaxSubmit({

          dataType: 'json',
          data: { 'action': 'fc_upload' },

          beforeSubmit: function () {

          },
          uploadProgress: function (event, position, total, percentComplete) {
            jQuery("#progress-bar").width(percentComplete + '%');
            jQuery("#progress-bar").html('<div id="progress-status">' + percentComplete + ' %</div>')

            //show a processing image until server complete the images generator
            if (percentComplete > 99) {
              FC.show_image_processing();
            }
          },
          success: function (data) {

            //hide the processing image
            FC.hide_image_processing();

            //hide the progress bar
            FC.hide_upload_progress();

            FC.process_data(data, true);

            xhr = null;

          },
          error: function (data) {
            //there's been an error

            //hide the processing image
            FC.hide_image_processing();

            //hide the progress bar
            FC.hide_upload_progress();

            //output the error
            if (data.statusText == 'abort')
              FC.show_upload_error('Upload aborted.');
            else
              FC.show_upload_error('Server error..');

            //reset xhr
            xhr = null;
          },
          resetForm: true
        });

        xhr = form.data('jqxhr');

        return false;
      }
    });

  },

  process_init: function () {

    FC.ajax_processing = true;

    var queryString = { "action": "fc_image_rotate", "filename_id": FC.filename_id, "rotate": FC.image_rotate };
    //send the data through ajax
    jQuery.ajax({
      type: 'POST',
      url: FabricCreator.ajax_url,
      data: queryString,
      cache: false,
      dataType: "json",
      success: function (data) {

        FC.hide_image_processing();

        FC.ajax_processing = false;
        FC.process_data(data, false);

      },
      error: function (html) {
        FC.ajax_processing = false;
      }
    });


  },

  user_image_change: function () {

    //hide any previous errors
    FC.hide_upload_error();

    //check for valid file type
    // get the file name, possibly with path (depends on browser)
    var filename = jQuery("#fc-upload-form input#userImage").val();

    // Use a regular expression to trim everything before final dot
    var extension = filename.replace(/^.*\./, '');

    // Iff there is no dot anywhere in filename, we would have extension == filename,
    // so we account for this possibility now
    if (extension == filename) {
      extension = '';
    }
    else {
      // if there is an extension, we convert to lower case
      // (N.B. this conversion will not effect the value of the extension
      // on the file upload.)
      extension = extension.toLowerCase();
    }

    switch (extension) {
      case 'jpg':
      case 'jpeg':
      // case 'png':
      // case 'gif':
      case 'tiff':
      case 'tif':
      // case 'bmp':

        //we got the right file type, submit the form
        jQuery('#fc-upload-form').trigger('submit');

        break;

      default:
        FC.show_upload_error('Invalid file type');
        return false;
    }

  },

  choose_file: function () {

    jQuery("#userImage").click();

  },

  show_upload_progress: function () {

    jQuery('#fc-upload-form #progress-div').show();

  },

  hide_upload_progress: function () {

    jQuery('#fc-upload-form #progress-div').hide();

  },

  /**
   * Show the AJAX image processing
   *
   */
  show_image_processing: function () {

    jQuery('#ajax_image_processing').addClass('visible');

  },

  /**
   * Hide the AJAX image processing
   *
   */
  hide_image_processing: function () {

    jQuery('#ajax_image_processing').removeClass('visible');
  },

  reset_variables: function (data, hard_reset) {

    FC.filename_id = data.filename_id;

    FC.image_size.width = data.original_image_width;
    FC.image_size.height = data.original_image_height;

    if (hard_reset === true) {
      FC.image_ppi = data.original_image_resolution;
      FC.current_ppi = data.original_image_resolution;

      FC.image_rotate = 0;
      FC.current_scale = 100;

      FC.layout = 'tile';


      FC.fabric_height = FC.default_ruler_height;

    }

    FC.current_img_size.width = data.original_image_width;
    FC.current_img_size.height = data.original_image_height;

  },

  process_data: function (data, reset) {

    //check for response status
    if (data.status == 'error') {
      //output the error
      FC.show_upload_error(data.message);

      return false;
    }

    //reset class variables
    FC.reset_variables(data, reset);

    if (reset === true) {

      //reset all fields in edit area
      FC.editor_reset();
    }


    //add the thumbnail
    jQuery('#fce-status .fc-thumbnail img').attr('src', data.img_set.thumbnail);

    //add the containers, img set
    for (var set_type in data.img_set) {
      var img_src = data.img_set[set_type];
      var element = jQuery('#render-container div.container-type-' + set_type);

      if (jQuery(element).length < 1)
        continue;

      jQuery(element).attr('data-src', img_src);
      jQuery(element).css('background-image', 'url(' + img_src + ')');

    }

    FC.layout_change(FC.layout);

    FC.ppi_update();
    FC.update_layers_bg_size();

    FC.set_ruler_dimensions();
    FC.update_ruler();

    FC.editor_show();

    //reinit custom selects to adjust width and reset to default option
    if (reset === true) {
      setTimeout(function () {
        FC.cut_init();
        FC.fabric_init();

      }, 250);
    }
  },

  update_layers_bg_size: function () {

    FC.current_bg_size.width = FC.calculate_ar(FC.current_img_size.width);
    FC.current_bg_size.height = FC.calculate_ar(FC.current_img_size.height);

    for (var index in FC.image_type) {
      var element = jQuery('#render-container div.container-type-' + FC.image_type[index]);

      if (jQuery(element).length < 1)
        continue;

      jQuery(element).css('background-size', ( FC.current_bg_size.width * 2 ) + 'px ' + ( FC.current_bg_size.height * 2 ) + 'px');

    }

  },

  set_ruler_dimensions: function () {

    if (FC.fabric_width > 0)
      FC.ruler_width = Math.ceil(FC.fabric_width);
    else
      FC.ruler_width = FC.default_ruler_width;

    if (FC.fabric_height > 0)
      FC.ruler_height = Math.ceil(FC.fabric_height);
    else
      FC.ruler_height = FC.default_ruler_height;

  },


  update_ruler: function () {


    //draw the rules

    //top
    for (i = 0; i <= FC.ruler_width; i++) {
      if (jQuery('#fc-view .ruler-top ul li').eq(i).length < 1)
        jQuery('#fc-view .ruler-top ul').append('<li><span>' + (i * 10) + '</span></li>');
    }
    if (jQuery('#fc-view .ruler-top ul li').length > FC.ruler_width)
      jQuery('#fc-view .ruler-top ul li:gt(' + FC.ruler_width + ')').remove();

    //left
    for (i = 0; i <= FC.ruler_height; i++) {
      if (jQuery('#fc-view .ruler-left ul li').eq(i).length < 1)
        jQuery('#fc-view .ruler-left ul').append('<li><span>' + (i * 10) + '</span></li>');
    }
    if (jQuery('#fc-view .ruler-left ul li').length > FC.ruler_height)
      jQuery('#fc-view .ruler-left ul li:gt(' + FC.ruler_height + ')').remove();


    //adjust the rule elements dimensions
    var available_width = jQuery('#fc-view .ruler-width').width();


    //substract any margin for top ruler
    //available_width =   available_width - parseInt(jQuery('#fc-view .ruler-width').css('margin-left')) - parseInt(jQuery('#fc-view .ruler-width').css('margin-right')) - parseInt(jQuery('#fc-view .ruler-width').css('padding-left')) - parseInt(jQuery('#fc-view .ruler-width').css('padding-right'));
    var single_rule_size = available_width / (FC.ruler_width);

    jQuery('#fc-view .ruler-width li').css('width', single_rule_size);
    jQuery('#fc-view .ruler-height li').css('height', single_rule_size)

    //adjust the #render-container to match fraction of rules  e.g. 138cm
    if (FC.fabric_width > 0) {

      var render_container_spacing = 70;

      var decimal = FC.fabric_width - Math.floor(FC.fabric_width)
      decimal = parseInt(decimal * 10);

      if (decimal > 0) {
        var spacing = parseInt(single_rule_size * decimal / 10);
        render_container_spacing += single_rule_size - spacing;
      }

      jQuery('#render-container').css("width", "calc(100% - " + render_container_spacing + "px)");
    }

    //adjust the render-container height
    //wait for animation
    setTimeout(function () {
      jQuery('#render-container').height(jQuery('#fc-view .ruler-left').height() - 29);
    }, 300)

  },

  interface_updated: function () {

    //update the fabrix_width
    var cut_value = jQuery('#fc-option-cut').val();
    var fabric_width = jQuery('#fc-option-fabric').find('option:selected').attr('data-width');

    if (fabric_width < 1)
      fabric_width = 0;

    if (cut_value == 'swatch') {

      FC.fabric_width = 2;
      FC.fabric_height = 2;

    }
    else if (cut_value == 'linear_metre') {

      FC.fabric_width = fabric_width;
      //FC.fabric_height    =   Math.ceil( FC.quantity ) * 10;
      FC.fabric_height = ( FC.quantity ) * 10;

      if (FC.fabric_height < 10)
        FC.fabric_height = 10;

    }
    else {
      FC.fabric_width = fabric_width;
      FC.fabric_height = 0;
    }

  },

  /**
   * Update the Price based on other elements
   *
   */
  price_update: function () {

    var fabric_cut = jQuery('#fc-option-cut').val();
    var fabric_id = jQuery('#fc-option-fabric').val();
    var quantity = jQuery('#fc-option-qty').val();

    var fabric_unit_price = 0;
    if (fabric_cut == 'swatch') {
      fabric_unit_price = FC.swatch_price;
    }
    else
      fabric_unit_price = jQuery('#fc-option-fabric option[value="' + fabric_id + '"]').attr('data-price');

    var price = fabric_unit_price * quantity;

    if (isNaN(price))
      price = 0;

    var author_commision = 0;
    if (FC.author_commision > 0) {
      author_commision = ( FC.author_commision * price / 100 );
      author_commision = author_commision.toFixed(2);
      price = price + ( FC.author_commision * price / 100 );
    }

    jQuery('#fc-price span').html(price.toFixed(2));

    if (price > 0 && FC.author_commision > 0) {
      jQuery('#author_commission span').html(author_commision);
      jQuery('#author_commission').show();
    }
    else {
      jQuery('#author_commission').hide();
    }

  },

  show_upload_error: function (message) {

    jQuery('#fc-upload-form #fc-upload-error').html(message).slideDown();

  },

  hide_upload_error: function () {
    jQuery('#fc-upload-form #fc-upload-error').html('').slideUp();
  },

  //create a new instance, reset, allow user to upload a new image.
  new_creator: function () {

    //reveal the upload
    jQuery('#fc-upload').slideDown('fast');

    //hide the edit area
    jQuery('#fc-edit').slideUp('fast');

    FC.reset_render_container();

    FC.fabric_width = FC.default_ruler_width;
    FC.fabric_height = FC.default_ruler_height;

    FC.set_ruler_dimensions();
    FC.update_ruler();

    jQuery('#fc-view').attr('class', '');

  },

  //reset fields and inputs
  editor_reset: function () {

    //reset the size
    FC.range_slider_reset();

    //arrangemenet
    jQuery('#fc-arrangement li').removeClass('selected');
    jQuery('#fc-arrangement li').eq(1).addClass('selected');

    //cut select reset
    FC.cut_destroy();

    //fabric select reset
    FC.fabric_destroy();

    //splinner reset
    FC.spinner_reset();

    FC.price_update();

  },

  //reset the render container
  reset_render_container: function () {

    jQuery('#render-container .layer.image').each(function () {

      jQuery(this).attr('data-src', '');
      jQuery(this).attr('style', '');

    })

  },

  //reveal the editor area and close the upload
  editor_show: function () {
    //reveal the edit area
    jQuery('#fc-edit').slideDown('fast');

    //close the upload
    jQuery('#fc-upload').slideUp('fast');
  },

  //expand and close edit blocks
  data_block_slide: function (el_click) {

    var holder = jQuery(el_click).closest('.data-area');

    if (jQuery(holder).hasClass('expanded')) {
      //data block is expanded, close it
      jQuery(holder).find('.data-block').slideUp('fast');
      jQuery(holder).removeClass('expanded');
      jQuery(el_click).html('+');

    }
    else {
      //data block is closed, expand it
      jQuery(holder).find('.data-block').slideDown('fast');
      jQuery(holder).addClass('expanded');
      jQuery(el_click).html('-');
    }
  },

  //Start the Range Slider
  range_slider_init: function () {
    var rangeSlider = document.getElementById('slider-range');

    noUiSlider.create(rangeSlider, {
      start: [100],
      connect: 'lower',
      range: {
        'min': [1],
        'max': [300]
      },
      pips: {
        mode: 'values',
        density: 50,
        values: [0, 100, 200, 300],
        format: wNumb({
          decimals: 0,
          postfix: '%'
        })
      }

    });

    rangeSlider.noUiSlider.on('update', function (values, handle) {

      var scale = parseInt(values[handle]);

      jQuery('#fc-option-scale').val(scale);
      FC.current_scale = scale;

      //calculate current ppi
      FC.calculate_PPI();
      FC.ppi_update();
      FC.update_layers_bg_size();
    });

  },

  //make further adjustments on the slider to match the requried design
  range_slider_adjust: function () {
    jQuery('#fc-print-size .noUi-pips .noUi-value').first().css('left', '12px');
    jQuery('#fc-print-size .noUi-pips .noUi-value').last().css('left', 'auto').css('right', '-22px');
  },

  range_slider_update: function (value) {
    var rangeSlider = document.getElementById('slider-range');
    rangeSlider.noUiSlider.set(value);
  },

  range_slider_reset: function () {
    var rangeSlider = document.getElementById('slider-range');
    rangeSlider.noUiSlider.set(100);
  },

  //layout init
  layout_init: function () {

    jQuery('#fc-arrangement li').on('click', function () {

      var el_type = jQuery(this).attr('data-type');

      FC.layout = el_type;

      FC.layout_change(el_type);

    })

  },

  layout_change: function (new_layout) {

    jQuery('#fc-option-layout li').removeClass('selected');
    jQuery('#fc-option-layout li[data-type="' + new_layout + '"]').addClass('selected');

    //further processing
    jQuery('#render-container > div').removeClass('show');
    jQuery('#render-container > div.container-type-' + new_layout).addClass('show');

  },

  cut_init: function () {

    jQuery("#fc-option-cut").selectBoxIt({
      autoWidth: false
    });


    jQuery("#fc-option-cut").on('change', function () {

      FC.cut_on_change(jQuery(this));

    });

    //adjust the width
    var available_width = jQuery("#fc-option-cut").closest('.col').width();
    jQuery("#fc-option-cut").closest('.col').find('#fc-option-cutSelectBoxIt').width(available_width);

  },

  cut_on_change: function (element) {

    var el_value = jQuery(element).val();

    FC.cut = el_value;

    //update the price field for round / fraction values
    if (el_value == 'swatch') {
      jQuery("#fc-option-qty").trigger("touchspin.updatesettings", { step: 1, decimals: 0 });
      jQuery('#fc-view').attr('class', '');
      jQuery('#fc-view').addClass('swatch');

      FC.pixels_per_inch = 85;

    }
    else if (el_value == 'linear_metre') {
      jQuery("#fc-option-qty").trigger("touchspin.updatesettings", { step: 0.1, decimals: 1 });
      jQuery('#fc-view').attr('class', '');
      jQuery('#fc-view').addClass('linear');

      FC.pixels_per_inch = 25;

    }
    else {

      jQuery("#fc-option-qty").trigger("touchspin.updatesettings", { step: 1, decimals: 0 });
      jQuery('#fc-view').attr('class', '');

      FC.pixels_per_inch = 25;

    }

    FC.interface_updated();

    FC.set_ruler_dimensions();
    FC.update_ruler();

    FC.calculate_pixels_per_inch();
    FC.ppi_update();
    FC.update_layers_bg_size();

    FC.price_update();

  },

  //reset the cut select
  cut_destroy: function () {

    var selectBox = jQuery("#fc-option-cut").data("selectBox-selectBoxIt");
    selectBox.destroy();

    jQuery("#fc-option-cut option:first").attr('selected', 'selected');

  },

  fabric_init: function () {

    jQuery("#fc-option-fabric").selectBoxIt({
      autoWidth: false,
    });

    jQuery("#fc-option-fabric").on('change', function () {

      FC.fabric_on_change(jQuery(this));

    });

    //adjust the width
    var available_width = jQuery("#fc-option-fabric").closest('.col').width();
    jQuery("#fc-option-fabric").closest('.col').find('#fc-option-fabricSelectBoxIt').width(available_width);

  },

  fabric_on_change: function (element) {

    FC.fabric = jQuery(element).val();

    FC.interface_updated();

    //further actions
    FC.price_update();

    //update rulers
    FC.set_ruler_dimensions();
    FC.update_ruler();

    FC.calculate_pixels_per_inch();
    FC.ppi_update();
    FC.update_layers_bg_size();

  },

  //reset the fabric select
  fabric_destroy: function () {

    var selectBox = jQuery("#fc-option-fabric").data("selectBox-selectBoxIt");
    selectBox.destroy();

    jQuery("#fc-option-fabric option:first").attr('selected', 'selected');
  },

  //spinner init
  spinner_init: function () {

    jQuery("#fc-option-qty").TouchSpin({
      verticalbuttons: true,
      min: 1,
    });


    jQuery("#fc-option-qty").on("change", function () {

      FC.spinner_on_change(jQuery(this));
    })

  },

  spinner_on_change: function (element) {

    FC.quantity = jQuery(element).val();

    FC.interface_updated();

    FC.price_update();

    //update rulers
    FC.set_ruler_dimensions();
    FC.update_ruler();

    FC.calculate_pixels_per_inch();

  },

  //reset the spinner
  spinner_reset: function () {

    jQuery('#fc-option-qty').val(1);
  },

  //init the iCheck
  icheck_init: function () {

    jQuery('input[type="checkbox"]').iCheck({
      checkboxClass: 'icheckbox_square-grey',
      radioClass: 'iradio_square-grey'
    });
    jQuery('input[type="checkbox"]').on('ifChanged', function () {
      //close the tip if opened
      jQuery(this).tooltipster('close');
    });

  },

  tooltips_init: function () {

    jQuery('.tooltip').tooltipster({
      triggerClose: {
        click: true,
        scroll: true
      }
    });

    //close any tips on key press
    jQuery(window).keypress(function () {
      jQuery('.tooltip').tooltipster('close');
    });
    jQuery(window).mousedown(function () {
      jQuery('.tooltip').tooltipster('close');
    });

  },

  //rotate the image
  rotate: function () {

    //check if artwork
    if (FC.artwork_id != '') {
      FC_Artwork.rotate();
      return;
    }

    if (FC.ajax_processing === true)
      return false;

    FC.show_image_processing();

    FC.image_rotate = FC.image_rotate + 90;
    if (FC.image_rotate >= 360)
      FC.image_rotate = 0;

    //request the new images
    FC.process_init();
  },


  ppi_update: function () {

    var current_ppi = FC.current_ppi;
    var box_class = '';
    var box_message = ''

    //update the PPI value
    jQuery('#fce-status .fc-ppi h4 span').html(current_ppi);

    if (current_ppi < 72) {
      box_class = 'red';
      box_message = 'Poor print reproduction';
    }
    else if (current_ppi >= 72 && current_ppi < 150) {
      box_class = 'orange';
      box_message = 'Acceptable print reproduction';
    }
    else {
      box_class = 'green';
      box_message = 'Excellent print reproduction';
    }

    jQuery('#fce-status .fc-ppi').attr('class', 'fc-ppi');
    jQuery('#fce-status .fc-ppi').addClass(box_class);
    jQuery('#fce-status .fc-ppi p ').html(box_message);

  },

  calculate_ar: function (value) {

    var size = 0;

    var size_inch = value / FC.current_ppi;

    //size    =   ( size_inch * FC.pixels_per_inch ) ;
    size = parseInt(size_inch * FC.pixels_per_inch);

    return size;

  },

  calculate_PPI: function () {

    //default width size inches
    var default_w_inch = FC.image_size.width / FC.image_ppi;

    var scaled_size_inch = FC.current_scale * default_w_inch / 100;

    FC.current_ppi = parseInt(FC.image_size.width / scaled_size_inch);

  },

  calculate_pixels_per_inch: function () {

    //available with / rule size
    var available_width = jQuery('#fc-view .ruler-width').width();

    var single_rule_size = available_width / (FC.ruler_width);

    //FC.pixels_per_inch  =   single_rule_size;
    FC.pixels_per_inch = ( single_rule_size / 4 );

  },

  /**
   * Add to cart
   */
  add_to_cart: function () {
    FC.weight();

    var found_errors = false;

    //check if fields are populated corectlly

    //fabric cut
    if (jQuery('#fc-option-cut').val() == '') {
      //open the tab if empty
      if (!jQuery('#fc-arrangement').hasClass('expanded')) {
        jQuery('#fc-arrangement > h2').click();
      }

      setTimeout(function () {
        jQuery('#fc-option-cut-tooltip').tooltipster('content', 'Choose your Cut').tooltipster('open');
      }, 150);

      found_errors = true;
    }

    if (jQuery('#fc-option-fabric').val() == '') {
      //open the tab if empty
      if (!jQuery('#fc-fabric').hasClass('expanded')) {
        jQuery('#fc-fabric > h2').click();
      }

      setTimeout(function () {
        jQuery('#fc-option-fabric-tooltip').tooltipster('content', 'Choose your Fabric').tooltipster('open');
      }, 150);

      found_errors = true;
    }

    //quantity require
    if (jQuery('#fc-option-qty').val() == '' || jQuery('#fc-option-qty').val() < 0.1) {
      //open the tab if empty
      if (!jQuery('#fc-quantity-price').hasClass('expanded')) {
        jQuery('#fc-quantity-price > h2').click();
      }

      setTimeout(function () {
        jQuery('#fc-option-qty-tooltip').tooltipster('content', 'Increase Quantity').tooltipster('open');
      }, 150);

      found_errors = true;
    }

    //terms
    if (jQuery('#fc-option-toc:checked').length < 1) {

      setTimeout(function () {
        jQuery('#fc-option-toc').tooltipster('content', 'You must agree to the terms').tooltipster('open');
      }, 150);

      found_errors = true;
    }

    //return if errors faound
    if (found_errors === true)
      return false;

    if (FC.ajax_processing === true)
      return false;

    //add to cart
    FC.ajax_processing = true;

    FC.cart_processing_show();

    var queryString = {
      "action": "fc_add_to_cart",
      "cart_item_id": FC.cart_item_id,
      "artwork_id": FC.artwork_id,
      "filename_id": FC.filename_id,
      "scale": FC.current_scale,
      "layout": FC.layout,
      "cut": FC.cut,
      "rotate": FC.image_rotate,
      "fabric": FC.fabric,
      "weight": FC.total_weight,
      "quantity": FC.quantity
    }; // Added GS "weight": FC.fabric_weight,
    //send the data through ajax
    jQuery.ajax({
      type: 'POST',
      url: FabricCreator.ajax_url,
      data: queryString,
      cache: false,
      dataType: "json",
      success: function (data) {

        FC.ajax_processing = false;

        if (data.status != 'complete')
          return false;

        //FC.cart_item_id   =   data.cart_item_id;

        jQuery('html, body').animate({
          scrollTop: jQuery("#woocart").offset().top - 40
        }, 500);

        jQuery('#woocart').html(data.cart_html);

        if (jQuery('#fabric-creator').parent().find(' > .woocommerce-message').length > 0)
          jQuery('#fabric-creator').parent().find(' > .woocommerce-message').remove();

        jQuery('#fabric-creator').parent().prepend(data.woo_message_html);

        //window.location.replace( data.redirect_url );

        FC.cart_processing_hide();

      },
      error: function (html) {
        FC.ajax_processing = false;
        FC.cart_processing_hide();
      }
    });

  },

  cart_processing_show: function () {

    jQuery('#add_to_cart a').addClass('processing');
    jQuery('#add_to_cart .loading').show();

  },

  cart_processing_hide: function () {

    jQuery('#add_to_cart a').removeClass('processing');
    jQuery('#add_to_cart .loading').hide();

  }

}
    
    
    