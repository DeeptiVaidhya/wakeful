(function($) {
    var formLoadingMessage = 'Please wait...';
    // Additional rule for check extension of file
    $.validator.addMethod("extension", function(value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
        return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
    }, $.validator.format("Please enter a valid file."));




    // Additional rule for check color code
    $.validator.addMethod("color", function(value, element) { //^[a-z0-9_\s-']+$
        return this.optional(element) || /^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/.test(value); //
    }, "Invalid color code.");

    // Additional rule for check course slug
    $.validator.addMethod("slug_regx", function(value, element) { //^[a-z0-9_\s-']+$
        return this.optional(element) || /^[a-z0-9-]+$/.test(value); //
	}, "Only lowercase characters, digits, dash allowed."); 
	
	// Additional rule for check participant unique id
    $.validator.addMethod("unique_id_regx", function(value, element) { //^[a-z0-9_\s-']+$
        return this.optional(element) || /^[0-9]{3}$/.test(value); //
	}, "Only 3 digits unique code allowed.");
	
	// Additional rule for check participant unique id
    $.validator.addMethod("course_access_regx", function(value, element) { //^[a-z0-9_\s-']+$
        return this.optional(element) || /^[a-zA-Z]{4}$/.test(value); //
	}, "Only 4 character access allowed.");
	
	// Additional rule for check participant unique id
    $.validator.addMethod("course_regx", function(value, element) { //^[a-z0-9_\s-']+$
        return this.optional(element) || /^[^-\s][a-zA-Z0-9_\s-]+$/.test(value); //
    }, "Please enter valid course name");




    /* ------- Organization form script for validation-------- */
    $("#organizationForm").validate({
        rules: { title: { required: true } }
    });

    $("#siteSettingForm").validate({
        rules: { value: { required: true,course_access_regx:true } }
    });
    /* ------- Course form script for validation-------- */
    $("#courseForm").validate({
       
        rules: {
            title: { required: true, course_regx:  true },
            //slug: { required: true, slug_regx: true },
            organizations_id: { required: true },
            bell_audio_file: { required: true, extension: "mp3" }
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
    });

    $("#courseForm .btn-save").click(function() {
        $('.closing_file').each(function(i) {
            $(this).rules('add', {
                required: !i,
                extension: 'mp3'

            });
        });
    });
    $("#editCourseForm").validate({
        rules: {
            title: { required: true },
            //slug: { required: true, slug_regx: true },
            organizations_id: { required: true },
            bell_audio_file: {
                extension: "mp3",
                required: function() {
                    var idVal = $('[name^=previous_bell_file_id]').val();
                    return (idVal > 0) ? false : true;
                }
            }
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
    });

    $("#editCourseForm .btn-save").click(function() {
        $('.closing_file').each(function(i) {
            $(this).rules('add', {
                extension: 'mp3'
            });
        });


        $('#closing_file_1').rules('add', {
            required: function() {
                var idVal = $("#previous_closing_audio_file_1").val();
                return (idVal > 0) ? false : true;
            }
        });

    });

      /* ------- Add Admin form script for validation-------- */
      $("#addAdminForm").validate({
        rules: {
            first_name: { required: true },
            last_name: { required: true },
            username: { required: true },
            email: { required: true, email: true },
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
    });

    /* ------- Basic information form script for validation-------- */
    $("#basicInfoForm").validate({
        rules: { first_name: { required: true }, last_name: { required: true }}
    });
    /* ------- Login  information form script for validation-------- */
    $("#loginDetailForm").validate({
        rules: {
            username: { required: true },
            email: { required: true, email: true },
            confirm_password: {
                equalTo: "#password",
                required: function() {
                    return $("#password").val().length > 0;
                }
            }
        }
    });
    /* ------- Class form script for validation-------- */
    $("#classForm").validate({
        rules: {
            class_title: { required: true },
            title: { required: true },            
            header: { required: true },
            content: { required: true },
            button_text: { required: true },
            script: { required: true },
            audio_text: { required: true },
            pretext: { required: true },
            post_text: { required: true },
            question_number: { required: true },
            question_color: { required: true, color: true },
            question_text: { required: true },
            intro_text: { required: true },
            image: { required: true, extension: "gif|png|jpg|jpeg" },
            audio: { required: true, extension: "mp3" },
            closing_file: { required: true },
            poem: { extension: "mp3" },
            video: { required: true, extension: "mp4|ogg|webm" },
            tile_image: { extension: "gif|png|jpg|jpeg"  },            
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
    });
    $("#classForm .btn-save").click(function() {
        $('.add-more-container .topic_title,.topic_text,.name,.quote').each(function() {
            $(this).rules('add', {
                required: true
            });
        });

        $('.add-more-container .topic_color').each(function() {
            $(this).rules('add', {
                required: true,
                color: true
            });
        });

        $('.add-more-container .photo').each(function() {
            $(this).rules('add', {
                required: true,
                media: "mp3|mp4|ogg|webm"
            });
        });
    });
    /* ------- End Script-------- */

    //Priyanka
    $("#courseHomeworkExercise").validate({
        rules: {
            title: { required: true },
            tip: { required: true },
            audio: {
                required: function() {
                    return $("[name='previous_audio_id']").val() == "";
                },
                extension: "mp3"
            },
//            closing_file: {
//                required: function() {
//                    return $("[name='previous_closing_id']").val() == "";
//                }
//            },
            poem: { extension: "mp3" },
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
    });

    //priyanka

    /* ------- Class form script for validation-------- */
    $("#editPageForm").validate({
        rules: {
            class_title: { required: true },
            title: { required: true },
            header: { required: true },
            content: { required: true },
            button_text: { required: true },
            script: { required: true },
            audio_text: { required: true },
            pretext: { required: true },
            post_text: { required: true },
            question_number: { required: true },
            question_color: { required: true },
            question_text: { required: true },
            intro_text: { required: true },
            image: { extension: "gif|png|jpg|jpeg" },
            audio: { extension: "mp3" },
            video: { extension: "mp4|ogg|webm" },
            poem: { extension: "mp3" },
            closing_file: { required: true },
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
    });

    $("#editPageForm .btn-save").click(function() {
        $('.add-more-container .topic_title,.topic_text,.name,.quote').each(function() {
            $(this).rules('add', {
                required: true
            });
        });

        $('.add-more-container .topic_color').each(function() {
            $(this).rules('add', {
                required: true,
                color: true
            });
        });

        $('.add-more-container .photo').each(function() {
            var idVal = $(this).closest('.item-details').find('[name^=sub_id]').val();
            $(this).rules('add', { required: !idVal, extension: "gif|png|jpg|jpeg" });
        });

    });



    /* ------- Review form script for validation-------- */
    $("#reviewForm").validate({
        rules: {
            title: { required: true },
            //button_text: {required: true},
            intro_text: { required: true },
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
    });
    $("#reviewForm .btn-save").click(function() {
        $('.add-more-container .pretext').each(function() {
            $(this).rules('add', {
                required: true
            });
        });
    });
    /* ------- End Script-------- */


    /* ------- Podcast form script for validation-------- */
    $("#podcastForm").validate({
        rules: {
            title: { required: true },
            intro_text: { required: true },
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
    });
    $("#podcastForm .btn-save").click(function() {
        $('.add-more-container .podcast_title,.podcast_author,.podcast_script').each(function() {
            $(this).rules('add', {
                required: true
            });
        });

        $('.add-more-container .podcast_link').each(function() {
            var idVal = $(this).closest('.item-details').find('[name^=previous_file_id]').val();
            $(this).rules('add', { required: !idVal, extension: "mp3" });
        });
    });
    /* ------- End Script-------- */



    /* ------- Reading form script for validation-------- */
    $("#readingForm").validate({
        ignore: [],
        rules: {
            title: { required: true },
            intro_text: { required: true },
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        },
        errorPlacement: function(error, element) {
            if (element.hasClass('reading_detail')) {
                $(element).next('.error').html(error);
            } else {
                error.insertAfter(element);
            }
        }

    });
    $("#readingForm .btn-save").click(function() {
        $('.add-more-container .reading_title,.reading_author').each(function() {
            $(this).rules('add', {
                required: true
            });
        });

        $('.add-more-container .reading_detail').each(function() {
            $(this).rules('add', { required: true });
        });
    });
    /* ------- End Script-------- */

    /* -----------  Clear Error message and all input in  form ------------*/
    $(document).on('click', '.btn-reset', function() {
        var formId = $(this).parents('form').attr('id');
        var validator = $("#" + formId).validate();
        $("#" + formId)[0].reset();
        validator.resetForm();
	});
	
	/*----------- Add participant ----------------*/
	$("#addUserForm").validate({
       
        rules: {
            unique_id: { required: true, unique_id_regx: true },
            email: { required: true },
        },
        submitHandler: function(form) {
            var frmOverlay = $('body').children('.formOverlay');
            if (!frmOverlay.length) {
                $('body').append('<div class="formOverlay">' + formLoadingMessage + '</div>');
            } else {
                frmOverlay.hide();
            }
            form.submit();
        }
	});
/*----------- Add participant ----------------*/

})(jQuery);
