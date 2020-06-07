(function($) {
    $.fn.noo_upload_form = function(options) {
        var noo_default = {
            max_file_size: '10mb',
            runtimes: 'html5,flash,silverlight,html4',
            multi_upload: true,
            browse_button: 'pickfiles',
            container: 'noo-upload-wrap',
            name: 'noo-upload-form',
            allow_format: 'jpg,jpeg,gif,png',
            multi_input: false,
            drop_element: '',
            set_featured: false,
            slider: true,
        }
        options = $.extend(noo_default, options);
        var $_$ = $(this);
        var uploader = new plupload.Uploader({
            runtimes: options.runtimes,
            browse_button: options.browse_button,
            container: document.getElementById(options.container),
            multi_upload: options.multi_upload,
            url: NooUpload.ajax_url,
            file_data_name: 'noo-upload-form',
            filters: {
                max_file_size: options.max_file_size,
                mime_types: [{
                    title: '',
                    extensions: options.allow_format
                }]
            },
            views: {
                list: true,
                thumbs: true,
                active: 'thumbs'
            },
            dragdrop: true,
            multiple_queues: true,
            urlstream_upload: true,
            multipart: true,
            multi_selection: false,
            flash_swf_url: NooUpload.flash_swf_url,
            silverlight_xap_url: NooUpload.silverlight_xap_url,
            multipart_params: {
                'security': NooUpload.security,
                'action': 'noo_upload_form'
            },
            drop_element: options.drop_element,
            init: {
                PostInit: function() {
                    if (options.slider) {
                        $('#' + options.container).find('.process-upload-media').html('');
                        var ListImage = $('#' + options.container).find('.noo-list-image').owlCarousel({
                            items: 3,
                            navigation: true,
                            pagination: false,
                            mouseDrag: false,
                            touchDrag: false,
                            navigationText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                        })
                        $('#' + options.container).on('click', '.noo-upload-image', function(event) {
                            uploader.start();
                            return false;
                        });
                        var total_item = $('#' + options.container).find('.noo-list-image').find('.owl-item').length;
                        if (total_item > 3) {
                            $('#' + options.container).find('.upload-show-more').show();
                            noo_upload_more_item('#' + options.container);
                        } else {
                            $('#' + options.container).find('.upload-show-more').hide();
                        }
                        $('#' + options.container).on('click', '.remove-item', function(event) {
                            event.preventDefault();
                            $(this).closest('.owl-item').remove();
                            if (total_item < 1) {
                                $('#' + options.container).find('.noo-drop-file').show();
                            } else {
                                $('#' + options.container).find('.noo-drop-file').hide();
                            }
                            var $$ = $(this),
                                id_img = parseInt($$.data('id')),
                                id_featured = parseInt($('body').find('#set_featured').val());
                            if (typeof id_img !== 'undefined' && id_img !== '') {
                                var data_image = {
                                    action: 'noo_remove_media',
                                    security: NooUpload.security,
                                    id: id_img
                                }
                                $.post(NooUpload.ajax_url, data_image, function(data) {
                                    if (data.status === 'success') {
                                        $('#' + options.container).find('input.value-image-' + id_img).remove();
                                        if (typeof id_featured !== 'undefined' && id_featured == id_img) {
                                            $('body').find('#set_featured').val('');
                                        }
                                    }
                                    $('#' + options.container).find('.process-upload-media').html(data.msg);
                                    setTimeout(function() {
                                        $('#' + options.container).find('.process-upload-media').html('').hide('400');
                                    }, 2000);
                                });
                            }
                        });
                        $('#' + options.container).on('click', '.set-featured', function(event) {
                            event.preventDefault();
                            var $$ = $(this),
                                id_img = parseInt($$.data('id')),
                                id_featured = parseInt($('body').find('#set_featured').val()),
                                icon_featured = '<i class="item-featured fa fa-star"></i>';
                            $$.addClass('active');
                            $('#' + options.container).find('.item-featured').remove();
                            $$.closest('.item-image').append(icon_featured);
                            if (typeof id_featured !== 'undefined' && id_featured !== id_img) {
                                $('body').find('#set_featured').val(id_img);
                            }
                        });
                        $('.noo-list-image').sortable({
                            cursor: 'move',
                            items: '.owl-item',
                            stop: uploader.updateOrder
                        });
                    } else {}
                },
                FilesAdded: function(up, files) {
                    if (options.slider) {

                        plupload.each(files, function(file) {

                            var file_type = file['type'];

                            var url_img = noo_img_upload.file_ext_thumbnail;
                            // console.log('url_img');

                            if ((file_type === 'image/png') || (file_type === 'image/jpeg')) {

                                var img_preview = new mOxie.Image();

                                img_preview.onload = function() {
                                    $('#' + options.container).find('.preview').empty();
                                    this.embed($('#' + options.container).find('.preview').get(0), {
                                        width: 150,
                                        height: 150
                                    });
                                };

                                img_preview.onembedded = function() {
                                    this.destroy();
                                };
                                img_preview.onerror = function() {
                                    this.destroy();
                                };

                                img_preview.load(file.getSource());


                                img_preview.onembedded = function() {

                                    var url_img = $('#' + options.container).find('.preview').find('canvas')[0].toDataURL();


                                    if (typeof url_img !== 'undefined' || url !== '') {
                                        $('#' + options.container).find('.noo-drop-file').hide();
                                    } else {
                                        $('#' + options.container).find('.noo-drop-file').show();
                                    }
                                    var content_img = '<div class="item-image" id="item-image-' + id_img + '"><img src="' + url_img + '"><i class="remove-item fa fa-trash-o"></i></div>';

                                    $('#' + options.container).find('.noo-list-image').data('owlCarousel').addItem(content_img, 0);

                                    $('#' + options.container).on('click', '.remove-item', function(event) {

                                        event.preventDefault();
                                        up.removeFile(file);
                                        $(this).closest('.owl-item').remove();
                                        var total_item = $('#' + options.container).find('.noo-list-image').find('.owl-item').length;
                                        if (total_item < 1) {
                                            $('#' + options.container).find('.noo-drop-file').show();
                                        } else {
                                            $('#' + options.container).find('.noo-drop-file').hide();
                                        }
                                        var $$ = $(this),
                                            id_img = parseInt($$.data('id')),
                                            id_featured = parseInt($('body').find('#set_featured').val());
                                        if (typeof id_img !== 'undefined' && id_img !== '') {
                                            var data_image = {
                                                action: 'noo_remove_media',
                                                security: NooUpload.security,
                                                id: id_img
                                            }
                                            $.post(NooUpload.ajax_url, data_image, function(data) {
                                                if (data.status === 'success') {
                                                    $('#' + options.container).find('input.value-image-' + id_img).remove();
                                                    if (typeof id_featured !== 'undefined' && id_featured == id_img) {
                                                        $('body').find('#set_featured').val('');
                                                    }
                                                }
                                                $('#' + options.container).find('.process-upload-media').html(data.msg);
                                                setTimeout(function() {
                                                    $('#' + options.container).find('.process-upload-media').html('').hide('400');
                                                }, 2000);
                                            });
                                        }
                                    });


                                }
                            } else {

                                if (typeof url_img !== 'undefined' || url !== '') {
                                    $('#' + options.container).find('.noo-drop-file').hide();
                                } else {
                                    $('#' + options.container).find('.noo-drop-file').show();
                                }
                                var content_img = '<div class="item-image" id="item-image-' + file.id + '"><img src="' + url_img + '"><i class="remove-item fa fa-trash-o"></i></div>';

                                $('#' + options.container).find('.noo-list-image').data('owlCarousel').addItem(content_img, 0);
                            }
                            var id_img = file.id;

                            $('#' + options.container).on('click', '.set-featured', function(event) {
                                event.preventDefault();
                                var $$ = $(this),
                                    id_img = parseInt($$.data('id')),
                                    id_featured = parseInt($('body').find('#set_featured').val()),
                                    icon_featured = '<i class="item-featured fa fa-star"></i>';
                                $$.toggleClass('active');
                                $('#' + options.container).find('.item-featured').remove();
                                $$.closest('.item-image').append(icon_featured);
                                if (typeof id_featured !== 'undefined' && id_featured !== id_img) {
                                    $('body').find('#set_featured').val(id_img);
                                }
                            });

                            uploader.start();

                        });

                    } else {
                        uploader.start();
                    }
                },
                UploadProgress: function(up, file) {
                    if (options.slider) {
                        var total_file_upload = up.total.uploaded,
                            total_upload_failed = up.total.failed,
                            total_file = up.files.length,
                            filesPending = total_file - (total_file_upload + total_upload_failed),
                            maxCount = up.getOption('filters').max_file_count || 0;
                        $('#' + options.container).find('.process-upload-media').show().html('Uploaded ' + total_file_upload + '/' + total_file + ' files - ( ' + plupload.formatSize(file.size) + ' )');
                    }
                },
                UploadComplete: function(up, file) {
                    if (options.slider) {
                        $('#' + options.container).find('.process-upload-media').html('Upload Success!!!');
                        setTimeout(function() {
                            $('#' + options.container).find('.process-upload-media').html('').hide('400');
                        }, 2000);
                    }
                },
                Error: function(up, err) {
                    $('#' + options.container).find('.process-upload-media').html("\nError #" + err.code + ": " + err.message);
                },
                FileUploaded: function(up, file, response) {

                    if (options.slider) {
                        $('#' + options.container).find('.process-upload-media').html('');
                        var data = $.parseJSON(response.response),
                            html_results = '',
                            list_input = (options.name).split('|');
                        var id_img = file.id,
                            id_image_wrap = 'item-image-' + id_img;
                            // console.log(data.id);
                        if (options.multi_input) {
                            $.each(list_input, function(index) {
                                if (options.multi_upload) {
                                    html_results += '<input type="hidden" name="' + list_input[index] + '[]" value="' + data.id + '" class="value-image-' + data.id + '" />';
                                } else {
                                    html_results += '<input type="hidden" name="' + list_input[index] + '" id="' + list_input + '" value="' + data.id + '" class="value-image-' + data.id + '" />';
                                }
                            });
                        } else if (options.multi_upload) {
                            html_results += '<input type="hidden" class="' + options.name + ' value-image-' + data.id + '" name="' + options.name + '[]"  value="' + data.id + '" />';
                        } else {
                            html_results += '<input type="hidden" class="' + options.name + ' value-image-' + data.id + '" name="' + options.name + '"  value="' + data.id + '"/>';
                        }
                        if (data.status == 'success') {
                            var icon_set_featured = '',
                                class_featured = '';
                            var url_img = noo_img_upload.file_ext_thumbnail;
                            var file_type = file['type'];
                            if (options.set_featured) {
                                icon_set_featured = '<i class="set-featured fa fa-star" data-id="' + data.id + '"></i>';
                                class_featured = 'featured';
                            }
                            var icon_remove = '<i class="remove-item fa fa-trash-o" data-id="' + data.id + '"></i>';
                            if ($('#' + options.container).find('#' + id_image_wrap).length > 0) {
                                    if ((file_type === 'image/jpeg') || (file_type === 'image/png')) {
                                        $('#' + options.container).find('#' + id_image_wrap).addClass(class_featured).html('<i class="success fa fa-check-circle-o"></i>' +
                                    '<img src="' + data.url + '">' + icon_set_featured + icon_remove + html_results);
                                    }else {
                                      $('#' + options.container).find('#' + id_image_wrap).addClass(class_featured).html('<i class="success fa fa-check-circle-o"></i>' +
                                    '<img src="' + url_img + '">' + icon_set_featured + icon_remove + html_results);  
                                    }
                            }
                            console.log(data.url);
                            if (options.multi_upload) {

                            } else {

                            }

                        } else {
                            $('#' + options.container).find('.noo-thumbnail-process').html(data.msg);
                        }
                        $('.noo-list-image').sortable({
                            cursor: 'move',
                            items: '.owl-item',
                            stop: uploader.updateOrder
                        });
                        noo_upload_more_item('#' + options.container);
                    } 
                    else {
                        var data = $.parseJSON(response.response);
                        if (data.status == 'success') {
                            $('#' + options.container).find('.noo-upload-thumbnail').html('<img src="' + data.url + '" />' +
                                '<input type="hidden" class="' + options.name + ' value-image-' + data.id + '" name="' + options.name + '"  value="' + data.id + '"/>');
                        }
                    }
                }
            }
        });
        uploader.init();

        function noo_upload_more_item(class_wrap) {
            var class_wrap = $(class_wrap);
            class_wrap.find('.upload-show-more').on('click', function(event) {
                event.preventDefault();
                $('body').addClass('noo-cover-body');
                class_wrap.find('.noo-upload-left').addClass('upload-show-box-more');
                class_wrap.find('.owl-wrapper').append('<i class="upload-close-more fa fa-times"></i>');
                class_wrap.on('click', '.upload-close-more', function(event) {
                    event.preventDefault();
                    $('body').removeClass('noo-cover-body');
                    class_wrap.find('.noo-upload-left').removeClass('upload-show-box-more');
                    $(this).remove();
                });
            });
        }
    }
})(jQuery);