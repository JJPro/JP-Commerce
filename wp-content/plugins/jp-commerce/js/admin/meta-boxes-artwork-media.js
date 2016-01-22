/**
 * Created by jjpro on 11/25/15.
 *
 * Handles Ajax of the artwork-media meta box to upload media files to the server
 */
jQuery(function ($){
    var $notices_div = $('#cover-image-notices');

    function coverNotice(msg) {
        $notices_div.append('<div class="notice error is-dismissible inline">' +
            '<p>' + msg + '</p>' +
            '<button type="button" class="notice-dismiss">' +
            '<span class="screen-reader-text">Dismiss this notice.</span>' +
            '</button>' +
            '</div>')
            .on('click', 'div.is-dismissible button.notice-dismiss', function() {
                $(this).parent().slideUp(function() {
                    $(this).remove();
                });
            });
    }

    function clearCoverNotice() {
        $notices_div.empty();
    }

    // reject submission if cover image is not set
    $('form').on('submit', function(){

        if ( ! $('#cover-preview').attr('src') )
        {


            // display notice
            coverNotice('A Cover Image is required!');


            // scroll to notice
            $('html, body').animate( {
                scrollTop: $notices_div.offset().top - 60
            }, 500);

            $notices_div.effect('bounce', { times: 3}, 'slow');
            return false;
        }
    });


    // disable dropping file into document default behavior.
    // This avoids situation where user mistakenly dragged a file onto the page, and the browser opens that file
    $(window).on("dragover", function(e){e = e||event;e.preventDefault();});
    $(window).on("drop", function(e){e = e||event;e.preventDefault();});


    /*************** COVER IMAGE ***************/
    /**
     * Note: I used Jcrop jQuery plugin to make the interface of the image cropping part.
     *       Therefore, you may need to get a basic understanding of Jcrop to make sense of the code in this section.
     *
     *       For introduction and documentation about Jcrop:
     *       http://deepliquid.com/content/Jcrop.html
     *
     *
     * Passed through variables from PHP:
     *
     * @property artwork     int     Id of current artwork
     * @property artist      int     Id of artist
     * @property cover_thumb string  Cover thumbnail url
     */
    var $preview_container = $("#cover-image-upload"),
        $preview = $('#cover-preview'),
        $wechat_container = $('#wechat-preview-container'),
        $wechat  = $('#wechat-preview'),

        // wechat image size
        xsize = $wechat_container.width(),
        ysize = $wechat_container.height(),

        // stores image size in the box
        boundx,
        boundy,

        // scale rate actual size to img box size
        scale,

        // cache the api
        jcrop_api;

    /**
     * read image file and init jcrop when 'update cover image' button is clicked.
     */
    $("#btn-update-cover").on('click', function(e){
        e.preventDefault();

        // something else is also bind to the change event, so the event is triggered twice somehow, used unbind() to remove other listeners.
        $('#cover-input').trigger('click').unbind('change').on('change', function(e){

            if (this.value != '') {


                var file   = this.files[0];
                var reader = new FileReader(); // HTML5 FileReader

                //debugger;

                clearCoverNotice();

                // set up file reader onloadend event to preview the image
                reader.onloadend = function(theFile) {

                    /*
                            ====================
                                Size Check > 1500px
                            ====================
                     */
                    var image = new Image();
                    image.src = theFile.target.result;

                    image.onload = function() {
                        // access image size here. this.width, this.height
                        console.log('(' + this.width + ', ' + this.height + ')');
                        if ( this.width >= 1500 && this.height >= 1500 ) {
                            /*
                                     ====================
                                         Passed Size Check, read the image in
                                     ====================
                             */

                            var newImage = reader.result;

                            $wechat.attr('src', newImage);

                            if (jcrop_api) {
                                jcrop_api.destroy();

                                $preview.remove();

                                $preview = $('<img id="cover-preview" style="width:100%; height:auto;" />');
                                $preview.attr('src', reader.result);
                                $preview.appendTo($preview_container);

                                initJcrop();

                            } else {
                                $preview.attr('src', newImage);
                                $preview.css('display', 'initial');
                                $wechat.css('display', 'initial');

                                // init jcrop
                                initJcrop();
                            }
                        }
                        else {

                            /*
                                     ====================
                                         Failed Size Check, alert user
                                     ====================
                             */
                            coverNotice(
                                "<p>" +
                                "<strong>Please re-upload a image with the smallest side being at least 1500 pixels.</strong> " +
                                "</p>" +
                                "<p><strong>Please select a different file.</strong></p>");

                        }

                    }

                }

                //debugger;

                reader.readAsDataURL(file);
            }
        });
    });

    /**
     * Updates wechat preview to show part specified by given coordinates.
     * @param c Object {x, y, w, h} Coordinates
     */
    function updateWechatPreview(c) {
        var rx = xsize / c.w;
        var ry = ysize / c.h;

        $wechat.css({
            width: Math.round(rx * boundx) + 'px',
            height: Math.round(ry * boundy) + 'px',
            marginLeft: '-' + Math.round(rx * c.x) + 'px',
            marginTop: '-' + Math.round(ry * c.y) + 'px'
        });

        // update the coord inputs for saving coord  info
        $('input[name="coord[x]"]').val(c.x * scale);
        $('input[name="coord[y]"]').val(c.y * scale);
        $('input[name="coord[w]"]').val(c.w * scale);
        $('input[name="coord[h]"]').val(c.h * scale);
    }

    /**
     * Hook up jcrop and setup event handlers.
     */
    function initJcrop() {
        $preview.Jcrop({
            onSelect: updateWechatPreview,
            onChange: updateWechatPreview,

            aspectRatio: 1
        }, function() {
            jcrop_api = this;
            jcrop_api.animateTo([42, 100, 192, 250]);
            var bounds = jcrop_api.getBounds();
            boundx = bounds[0];
            boundy = bounds[1];

            scale = $preview[0].naturalWidth / boundx;
        });
    }


    /*************** OTHER IMAGES ******************/
    /**
     * Note: You need to understand Dropzone JS to understand the code in this section.
     *       For introduction and documentation of DropzoneJS, read at:
     *       http://dropzonejs.com
     *
     *
     * Passed through variables from PHP:
     *
     * @property jc_data          Object {ajaxurl}
     * @property artwork          int    Id of current artwork
     * @property other_thumbnails array  Array of objects {id, post_id, path, url, order}
     */
    var $dropdiv        = $("#media-upload-wrap"),
        $errorslist      = $("#media-upload-errors ul"),
        nonce           = $dropdiv.attr("data-nonce"),
        $file_processing_modal   = $('#file-processing-modal'),
        $progress_bar   = $('#progress-bar'),
        $progress_text  = $('#progress-text'),
        $dropzone_local_errors = $('#dropzone-local-errors'),

        ajaxurl         = jc_data.ajaxurl,

        // ajax action names
        add_action      = "jp_commerce_add_artwork_file",
        update_action   = "jp_commerce_update_artwork_file_order",
        delete_action   = "jp_commerce_delete_artwork_file",

        max_files       = 5,
        dropzone_api    = null,

        /**
         * The Working Theory for Exchanging Information with server about Reordering and Changes to Images upon submission:
         *
         *  When a file is added, do nothing.
         *  When a file is deleted && it has a file_id, insert deleted file_id into the change log.
         *  When file order is changed, do nothing
         *
         *  On submission, we collect order data and compare with the order info attached to those files.
         *      If it doesn't have an order, assign it the order and upload as new file
         *      If the order doesn't match, assign it the new order and call update with the file_id
         *      Else do nothing.
         */
        // changes to existing files.
        changes         = {
                            added_files   : [],
                            updated_files : [],// [{file_id, order}]
                            deleted_files : [] // [file_id]
                          },

        errors          = [],
        no_errors       = false;


    config_dropzone();
    init_dropzone();
    register_form_submission_handler();

    /**
     * DO NOT CHANGE
     * This function alters default dropzone behaviors.
     */
    function config_dropzone(){

        // disable auto discover
        Dropzone.autoDiscover = false;

        // Alter addFile prototype function to avoid duplicate files be added.
        Dropzone.prototype.addFile = function(file) {

            // Check for duplicates
            if (this.files.length) {
                var _i, _len;
                for (_i = 0, _len = this.files.length; _i < _len; _i++) {
                    if(this.files[_i].name === file.name && this.files[_i].size === file.size){
                        temp_error_alert('File with the same name already exists.');
                        return false;
                    }
                }
            }

            // Source code from the original addFile prototype function.
            file.upload = {
                progress: 0,
                total: file.size,
                bytesSent: 0
            };
            this.files.push(file);
            file.status = Dropzone.ADDED;
            this.emit("addedfile", file);
            this._enqueueThumbnail(file);
            return this.accept(file, (function(_this) {
                return function(error) {
                    if (error) {
                        file.accepted = false;
                        _this._errorProcessing([file], error);
                    } else {
                        file.accepted = true;
                        if (_this.options.autoQueue) {
                            _this.enqueueFile(file);
                        }
                    }
                    return _this._updateMaxFilesReachedClass();
                };
            })(this));
        };
    }

    function init_dropzone() {

        // initialize dropzone with options
        $dropdiv.dropzone(
            {
                url                    :ajaxurl,
                method                 :"post",
                maxFiles               :max_files,
                parallelUploads        :max_files + 1,
                acceptedFiles          :"image/*",
                autoProcessQueue       :false,
                uploadMultiple         :false,
                previewTemplate        :'' +
                                        '<div class="dz-preview dz-file-preview">' +
                                        '   <div class="dz-details">' +
                                        '       <img data-dz-thumbnail />' +
                                        '   </div>' +
                                        '   <div class="controls">' +
                                        '       <i class="dz-remove icon-x-circle" data-dz-remove title="Delete"></i>' +
                                        '   </div>' +
                                        '</div> ',
                clickable              :".dz-clickable",
                dictInvalidFileType    :"Only images are allowed.",
                dictResponseError      :"Upload Failed. \nError Code: {{statusCode}}.",
                dictMaxFilesExceeded   :"Exceeded number of files allowed.",

                init                   :function() {
                                        dropzone_api = this;
                                        window.dropzone_api = this;

                                        attach_event_handlers_to_dropzone();
                                        load_existing_files();

                                        /******** Make Dropzone Preview Elements Sortable ***********/
                                        var move = {};
                                        $dropdiv.sortable( {
                                            items: '.dz-preview',
                                            containment: 'parent',
                                            distance: 20,
                                            tolerance: 'pointer',
                                            start: function(e, ui) {
                                                move.from = ui.item.index('.dz-preview');
                                            },
                                            update: function(e, ui) {
                                                move.to = ui.item.index('.dz-preview');
                                                dropzone_api.files.move(move.from, move.to);
                                            }
                                        });


                                        /****
                                         * Add move function to Array prototype
                                         */
                                        Array.prototype.move = function( from, to) {
                                            this.splice(to, 0, this.splice(from, 1)[0]);
                                        }
                                    }
            }
        );
    }

    function attach_event_handlers_to_dropzone(){

        dropzone_api.on("addedfile", function(file) {
            // Note: duplicates have been handled by changing the prototype addFile function.
            // move indicator div to the end
            $dropdiv.append($("#upload-indicator-wrap"));
        });

        dropzone_api.on("removedfile", function(file) {

            if (dropzone_api.options.maxFiles !== max_files){
                dropzone_api.options.maxFiles++;
            }

            // re-enable dropzone if it is out of the "max file reached" situation
            if (this.files.length == 4){
                $dropdiv.removeClass('dz-max-files-reached');
                this.enable();
            }

            // add change into change log if necessary
            if (file.hasOwnProperty('file_id')) {
                changes.deleted_files.push(file.file_id);
            }

        });

        // Update total progress bar
        dropzone_api.on("totaluploadprogress", function(progress) {
            var progress = parseInt(progress) + "%";
            $progress_bar.css('width', progress);
            $progress_text.text(progress);
        });

        dropzone_api.on("maxfilesexceeded", function(file) {
            this.removeFile(file);
        });

        // before sending file
        dropzone_api.on("sending", function(file, xhr, data){
            debugger;
            var i,
                updated_files = changes.updated_files,
                updated_len = updated_files.length,
                current_file;
            if (file.hasOwnProperty('file_id')){ // this is an update to existing file
                for(i=0; i<updated_len; i++){
                    current_file = updated_files[i];
                    if (current_file.file_id === file.file_id) {
                        data.append('action', update_action);
                        data.append("artwork", artwork);
                        data.append("file_id", current_file.file_id);
                        data.append("file_order", current_file.order);
                        data.append("nonce", nonce);
                    }
                }

            } else { // this is a new file
                data.append('action', add_action);
                data.append("artwork", artwork);
                data.append("file_order", file.order);
                data.append("nonce", nonce);
            }
        });

        // all files are uploaded, check for errors and resubmit
        dropzone_api.on("queuecomplete", function() {
            // if there are errors:
            if (errors.length !== 0) {
                var err;
                no_errors = false;
                // show errors in media-upload-errors div
                errors.map(function(i){
                    err = errors[i];
                    add_to_errorlist(err);
                });
            } else {
                // trigger form submit again
                no_errors = true;
                $file_processing_modal.dialog('close');
                $('form').trigger('submit');
            }
        });

        // collect errors
        dropzone_api.on("success", function(file, response){
            if (!response.success) {
                //debugger;
                no_errors = false;
                errors.push(response.data);
            }
        });
        dropzone_api.on("error", function(file, err) {
            //debugger;
            temp_error_alert(err);
        });
    }

    function register_form_submission_handler(){
        $('form').on('submit', function (e) {

            //debugger;

            // collect changes
            collect_changes();

            // if no changes or upload completed with no errors, return true to finish submission
            if (no_errors || no_changes()) {
                return true;
            }

            // if there are changes,
            //   show modal dialog and start processing files.
            if ( ! no_changes() ) {

                // show modal window
                $file_processing_modal.dialog({
                    modal: true,
                    dialogClass: 'noTitleStuff fixed-dialog'
                });

                // process deleted files
                process_deleted_files();

                // process reordered existing files
                // this is a special case that we have to do manually, because dropzone won't process queue if there is no new file or removal occuring
                process_reordered_existing_files();

                // process other files and collect errors along the way
                dropzone_api.processQueue();

            }

            return false;
        });
    }

    /***
     * Load existing images
     */
    function load_existing_files() {
        var existing_file_count = other_thumbnails.length,
            thumbnail,
            mockFile;

        dropzone_api.options.maxFiles -= existing_file_count;
        for ( var i= 0, len=existing_file_count; i<len; i++ ) {
            thumbnail = other_thumbnails[i];


            mockFile = {
                        name    : thumbnail.id,
                        size    : 0,
                        file_id : thumbnail.id,
                        order   : thumbnail.order
                        };

            dropzone_api.files.push(mockFile);

            dropzone_api.emit("addedfile", mockFile);

            dropzone_api.emit("thumbnail", mockFile, thumbnail.url);

        }

        // hide the + indicator if existing_file_count == max_files
        if (dropzone_api.options.maxFiles === 0) {
            $dropdiv.addClass('dz-max-files-reached');
        }
    }

    // collect changes for submissoin
    function collect_changes() {
        var files = dropzone_api.files,
            file;

        for (var i= 0, len=files.length; i < len; i++){

            file = files[i];

            if (!file.hasOwnProperty('order')) { // this is a new file
                // assign i as its order
                file.order = i;
                changes.added_files.push(i);
            } else if (file.hasOwnProperty('order') && i !== file.order) { // this is an update to a existing file
                // assign i as its order and add to changes.updated_files
                file.order = i; // NOTE: don't try to simplify this with the line in if statement
                changes.updated_files.push({file_id: file.file_id, order: i});
            }
        }
    }

    function temp_error_alert(err) {
        var $errItem = $("<li>Error: "+err+"</li>");
        $errItem.appendTo($dropzone_local_errors).effect("shake", {direction: 'right'}).delay(8000).fadeOut();
    }

    function add_to_errorlist(err_msg) {
        //debugger;
        var $errItem = $("<li>Error: "+err_msg+"</li>");
        $errItem.appendTo($errorslist).effect("shake", {direction: 'right'});
    }

    function no_changes() {
        return changes.added_files.length === 0 && changes.updated_files.length === 0 && changes.deleted_files.length === 0;
    }

    function process_deleted_files() {
        //debugger;

        var deleted_files = changes.deleted_files;
        var i, len=deleted_files.length;
        var file_id;

        if (len === 0) {
            return;
        }

        for (i=0; i<len; i++){
            file_id = deleted_files[i];
            $.ajax(ajaxurl, {
                data: {
                    'artwork': artwork,
                    'file_id': file_id,
                    'nonce'  : nonce,
                    'action' : delete_action
                },
                type: 'POST',
                error: function(xhr, status, error) {
                    no_errors = false;
                    errors.push(error);
                    add_to_errorlist(error);
                },
                success: function(result, status, xhr) {
                    if (! result.success ) {
                        //debugger;
                        var err_msg = result.data;
                        no_errors = false;
                        errors.push(err_msg);
                        add_to_errorlist(err_msg);
                    }
                }
            });
        }

        // empty deleted files record
        changes.deleted_files = [];

        if (errors.length === 0){
            no_errors = true;
        }

        if (no_errors && no_changes()) {
            $file_processing_modal.dialog('close');
        }
    }

    function process_reordered_existing_files() {
        var reordered_files = changes.updated_files,
            i, len = reordered_files.length,
            file_id,
            new_order;

        if (len === 0) {
            return;
        }

        for (i=0; i<len; i++) {
            file_id = reordered_files[i].file_id;
            new_order = reordered_files[i].order;

            $.ajax(ajaxurl, {
                data: {
                    'action' : update_action,
                    'artwork': artwork,
                    'file_id': file_id,
                    'file_order': new_order,
                    'nonce'  : nonce
                },
                type: 'POST',
                error: function(xhr, status, error) {
                    no_errors = false;
                    errors.push(error);
                    add_to_errorlist(error);
                },
                success: function(result, status, xhr) {
                    if (! result.success ) {
                        //debugger;
                        var err_msg = result.data;
                        no_errors = false;
                        errors.push(err_msg);
                        add_to_errorlist(err_msg);
                    }

                }
            });
        }

        // clear updated files record
        changes.updated_files = [];

        if (errors.length === 0){
            no_errors = true;
        }

        if (no_errors && no_changes()) {
            $file_processing_modal.dialog('close');
        }
    }

});