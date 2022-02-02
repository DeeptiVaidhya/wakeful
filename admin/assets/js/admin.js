(function($) {
    $(function() {

        $('#classForm, #editPageForm').on('click', '.ul-review-practice-type input[type="radio"]',function(){
            var pc = $('.practice-detail-container');
            pc.find('.practice-category').toggleClass('hide',this.value!='practice');
            pc.find('.practice-type-label').text(this.value.charAt(0).toUpperCase() + this.value.slice(1));
            pc.find('input[type="text"]').val('');
        }).trigger('click');
		$(".participant_email_update").hide();
        function get_page(type, course_id) {
            if (type != '' && type != undefined) {
                $.ajax({
                    url: BASE_URL + 'classes/get-page',
                    method: 'POST',
                    data: { 'type': type, 'course_id': course_id },
                    success: function(result) {
                        $(".page_content_div").html(result);
                    },
                    complete: function() {
                        if ($.fn.colorpicker) {
                            $(document).find('.select-color').colorpicker({
                                format: 'hex',
                                autoInputFallback: true
                            });
                        }
                    }
                });
            }
        }

        $('.switch_btn').length && $('.switch_btn').bootstrapSwitch({
            onText: ($('.switch_btn').attr('data-name')) ? "Enabled" : "Active",
            offText: ($('.switch_btn').attr('data-name')) ? "Disabled" : "Inactive",
            size: "mini",
            onColor: 'primary',
            offColor: 'danger'
        });

        if ($.fn.colorpicker) {
            $(document).find('.select-color').length && $(document).find('.select-color').colorpicker({
                format: 'hex',
                autoInputFallback: true
            });
        }
        if ($.fn.dataTable) {
            $.extend(true, $.fn.dataTable.defaults, {
                oLanguage: {
                    sProcessing: "<div class='loader-center'><img height='50' width='50' src='" + BASE_URL + "assets/images/loading.gif'></div>"
                },
                bProcessing: true,
                bServerSide: true,
                ordering: true,
                iDisplayLength: 10,
                responsive: true,
                bSortCellsTop: true,
                aaSorting: [
                    [0, 'asc']
                ],
                bDestroy: true, //!!!--- for remove data table warning.
                aLengthMenu: [
                    [5, 10, 20, -1],
                    [5, 10, 20, 'All']
                ],
                aoColumnDefs: [{
                    bSortable: false,
                    aTargets: [-1]
                }],
                searching: true
            });
            if ($('.data-table').length) {
                $('.data-table').each(function() {
                    var opts = {};
                    var obj = $(this);
                    if ($(this).attr('data-src')) {
                        opts['sAjaxSource'] = $(this).attr('data-src');
                    } else if ($(this).attr('data-opts')) {
                        $.extend(opts, $.parseJSON($(this).attr('data-opts')));
                    }
                    var reorder_url;
                    if ($(this).attr('data-reorder-url')) {
                        reorder_url = $(this).attr('data-reorder-url')
                    }
                    var classes_id = $(this).attr('data-classes_id'),
                        course_id = $(this).attr('data-course_id'),
                        table = $(this).DataTable(opts);
                    table.on('row-reorder', function(e, diff, edit) {
                        var result = 'Reorder started on row: ' + edit.triggerRow.data()[1] + '\n';
                        var json = { data: [], classes_id: classes_id, course_id: course_id };
                        console.log(diff.length);
                        for (var i = 0, ien = diff.length; i < ien; i++) {
                            var rowData = table.row(diff[i].node).data();
                            result += rowData[1] + ' updated to be in position ' +
                                diff[i].newData + ' (was ' + diff[i].oldData + ')+\n\t';
                            json.data[i] = { position: parseInt(diff[i].newPosition), id: $(diff[i].node).find('[name^="page_id"]').val() };
                        }
                        reorder_url && $.ajax({
                            url: BASE_URL + reorder_url, //'classes/reorder-pages',
                            method: 'POST',
                            dataType: 'json',
                            data: json, // must be json
                            success: function(res) {

                                if (res['status'] == 'success') {
                                    toastr.success(res['msg']);
                                } else {
                                    toastr.options = {
                                        "closeButton": true,
                                        "hideDuration": 500,
                                        "onHidden": function() {
                                            window.location.reload();
                                        },
                                        "onCloseClick": function() {
                                            window.location.reload();
                                        }
                                    }
                                    toastr.error(res['msg']);
                                }
                            }
                        });

                        console.log('Event result:\n' + result);
                    });
                });
            }
        }

        // Upload Profile
        $(".upload-button").on('click', function() {
            $(".file-upload").click();
        });

        $(".file-upload").on('change', function() {
            //var file = this.files[0];
            //var fileType = file["type"];
            readURL(this);
        });

        var readURL = function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.box-image').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        };

        $('.step-2-right .list-group a').on('click', function(e) {
            e.preventDefault();
            var type = $(this).attr('data-type');
            var course_id = $(this).closest('.list-group').attr('data-course-id');
            $(this).siblings('a.active').removeClass("active");
            $(this).addClass("active");
            var index = $(this).index();
            $("div.im-tab>div.im-tab-content").removeClass("active");
            $("div.im-tab>div.im-tab-content").eq(index).addClass("active");
            get_page(type, course_id);
        });

        // Add more item functionality
        $('.add-more-container').on('click', '.add-more-items .btn-add-more-item', function() {
            //var obj =  $('.item-details-row').clone();

            //obj.find('.remove_btn_container').html("<button class='pull-right btn btn-danger remove_btn' type='button'><i class='fa fa-trash'> Remove</i></button>");
            //(obj).removeClass('item-details-row').appendTo(".add-more-items");
            var parent = $(this).closest('.add-more-items'),
                removeElem = $("<button class='pull-right btn btn-danger btn-remove-item btn-sm' type='button'><i class='fa fa-trash'></i> Remove</button>"),
                clone = parent.find('.item-details:first').clone(),
                index = ($(this).data('index') || parent.find('.item-details').length - 1) + 1;

            clone.find('[name]').each(function() {
                $(this).attr('name', $(this).attr('name').replace(/\[[0-9]+\]/g, '[' + index + ']'));

            });
            clone.find('label.error,.not-req-elems').remove();
            if ($.fn.colorpicker) {
                clone.find('.select-color').length && clone.find('.select-color').colorpicker({
                    'format': 'hex'
                });
            }
            $(this).data('index', index++);
            clone.find('input[type=text],input[type=hidden],textarea,select,input[type=file]').val('');
            clone.find('.btn-remove-item,div.mce-tinymce').remove();
            clone.prepend(removeElem);
            var name = clone.find('textarea.text-tiny-mce').show().removeAttr('id').attr('name');
            parent.append(clone);
            console.log(name);
            initTinymce('[name="' + name + '"]');

        }).on('click', '.btn-remove-item', function() { // Remove more item functionality
            var that = this,
                msg = $(that).attr('data-msg');
            if ($(that).attr('data-url')) { // If want to remove from server
                var post_data = $.parseJSON($(that).attr('data-params'));
                var delete_id = null;
                delete_id = post_data.id;
                bootbox.confirm({
                    message: "Are you sure you want to delete this " + msg + "?",
                    callback: function(result) {
                        if (result) {
                            $.ajax({
                                url: $(that).attr('data-url'),
                                method: 'POST',
                                dataType: 'json',
                                data: $.parseJSON($(that).attr('data-params')), // must be json
                                success: function(res) {
                                    res && res.status == 'success' && $(that).closest('.item-details').remove();
                                    res && res.status == 'success' && $(that).closest('.audio_box').remove();
                                    res && res.status == 'success' && ($('.file_data_' + delete_id).length) && ($('.file_data_' + delete_id).remove());
                                    if (res.status == 'success') {
                                        toastr.success(res.msg);
                                    }
                                    if (res.status == 'error') {
                                        toastr.error(res.msg);
                                    }

                                }
                            });

                        }
                    }
                });
            } else {
                $(this).closest('.item-details').remove();
            }
        });

        $(document).on('click', '.delete', function() { // Remove more item functionality
            var that = this,
                msg = $(that).attr('data-msg');
            if ($(that).attr('data-url')) { // If want to remove from server
                bootbox.confirm({
                    message: "Are you sure you want to delete this " + msg + "?",
                    callback: function(result) {
                        if (result) {
                            window.location.href = $(that).attr('data-url');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.comm-delete', function() { // Restore more item functionality
            var that = this,
                msg = $(that).attr('data-msg');
            if ($(that).attr('data-url')) { // If want to Restore from server
                bootbox.confirm({
                    message: msg + " this message?",
                    callback: function(result) {
                        if (result) {
                            window.location.href = $(that).attr('data-url');
                        }
                    }
                });
            }
        });

        $(document).on('change', '.checkbox', function() { // Restore more item functionality
            var that = this,
                msg = $(that).attr('data-msg');
                id = $(that).attr('data-id');
                let is_read = (this.checked == true) ? 1 : 0;
            if ($(that).attr('data-url')) { // If want to Restore from server
                bootbox.confirm({
                    message: "Are you sure you want to " + msg + " this community?",
                    callback: function(result) {
                        if (result) {
                            window.location.href = $(that).attr('data-url')+'/'+is_read;
                        }else{
                            $('#check'+id).prop("checked", !that.checked);
                        }
                    }
                });
            }
        });

        $(document).on('click', '.change-status', function() { // Restore more item functionality
            var that = this,
                type = $(that).attr('data-type');
                msg = $(that).attr('data-msg');
                name = $(that).attr('data-name');
                var message ='';
                if(type=='status'){
                    message = "Are you sure you want to " + msg + " account of " + name + "?";
                }else{
                    if(msg == 'mute'){
                        message = "Stop sending reminder emails to " + name + "?";
                    }else{
                        message = "Start sending reminder emails to " + name + "?";
                    }
                }
            if ($(that).attr('data-url')) { // If want to Restore from server
                bootbox.confirm({
                    message: message,
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-danger'
                        }
                    },
                    callback: function(result) {
                        if (result) {
                            window.location.href = $(that).attr('data-url');
                        }
                    },
                });
            }
        });

        $('.page-view').click(function() {
            var type = $(this).attr('data-type');
            $.ajax({
                url: $(this).attr('data-url'),
                method: 'POST',
                //dataType: 'json',
                data: $.parseJSON($(this).attr('data-params')), // must be json
                success: function(res) {
                    $('#view_popup').find('.modal-title').text(type);
                    $('#view_popup').find('.modal-body').html(res);
                }
            });
        });

        // Update class tile image
        $('#update_image').click(function() {
            var form = new FormData();
            var file_data = $('#tile_image').prop('files')[0]; 

            form.append('tile_image',file_data);
            $.ajax({
              type:'POST',
              url: $(this).attr('data-url')+'?img_id='+$(this).attr('data-id')+'&class_id='+$(this).attr('class-id'),
              data: form,
              cache:false,
              contentType: false,
              processData: false,
              success: function(res) {
                var res = $.parseJSON(res);
                if (res.status == 'success') {
                    toastr.success(res.msg);
                    setTimeout(function(){// wait for 1 secs(2)
                           location.reload(); // then reload the page.(3)
                    }, 1000); 
                }
                if (res.status == 'error') {
                    toastr.error(res.msg);
                }
              }
            });
        });

         // Update class title 
         
        $('#update_class').click(function() {
            var class_title = $('#classForm').find('input[name="class_title"]').val();
            if (class_title != '') {
                $.ajax({
                    url: $(this).attr('data-url'),
                    method: 'POST',
                    //dataType: 'json',
                    data: { 'class_title': class_title, 'class_id': $(this).attr('data-id') }, // must be json
                    success: function(res) {
                        var res = $.parseJSON(res);
                        if (res.status == 'success') {
                            toastr.success(res.msg);
                        }
                        if (res.status == 'error') {
                            toastr.error(res.msg);
                        }
                    }
                });
            } else {
                toastr.error('Class title could not be blank');
            }
        });


      



        $('.switch_btn').on('switchChange.bootstrapSwitch', function(event, state) {
            var param = $.parseJSON($(this).attr('data-params'));
            param.is_active = (state) ? 1 : 0;
            $.ajax({
                url: $(this).attr('data-url'),
                method: 'POST',
                //dataType: 'json',
                data: param, // must be json
                success: function(res) {
                    var result = $.parseJSON(res);
                    var status = result.status;
                    toastr.options = { closeButton: true }
                    if (status == 'success') {
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }

                }
            });
        });

        $(document).on('click', '.user_detail', function() {
            var type = $(this).attr('data-type');
            $.ajax({
                url: $(this).attr('data-url'),
                dataType: 'json',
                method: 'POST',
                data: $.parseJSON($(this).attr('data-params')), // must be json
                success: function(result) {
                    if (result.status == 'success') {
                        $('#view_user').find('.modal-title').text(result.data.first_name + ' ' + result.data.last_name);
                        if (result.data.profile_picture != '') {
                            $('#view_user').find('.img-circle').attr('src', result.data.profile_picture);
                        }
                        var content = "<div class='row'>";
						var obj = { unique_id: 'Unique Id', email: 'Email', is_active: 'Active', is_authorized: 'Authorized' };
						var c = '';
                        var nu = '';
						var id = result.data.id;
                        $.each(result.data, function(i, v) {
                            if (obj[i] != undefined) {
                                content += '<div class="row"><div class="control-label col-md-3 col-sm-3 col-xs-12">' +
									'<h5>' + obj[i] + '</h5></div>';
								if(i == 'unique_id'){
									let value = (v != null) ? v : '';
									 c =  '<input data-id="' + id + '" id="unique_id" value="' + value + '">';
								} else {
									 c = '<h5>' + v == 1 || v !=nu ? 'Yes' : 'No'  + '</h5>';
								} 
								content += '<div class = "col-md-9 col-sm-9 col-xs-12">' + c +'</div></div>';
                            }
                        });
                        content += "</div>";
                        $('#view_user').find('.modal-body').html(content);
                    }
                }
            });
		});
		
		$(document).on('blur', '#unique_id', function() {
			var val = $(this).val();
			var id = $(this).attr('data-id');
			$.ajax({
                url: BASE_URL +'user/update-uniqueid',
                dataType: 'json',
                method: 'POST',
                data: {'unique_id': val, 'id': id}, // must be json
                success: function(result) {
                    if (result.status == 'success') {
						toastr.success(result.msg);
						window.location.reload();
                    }
                }
            });
		});
		
		$(document).on('blur', '#user_unique_id', function() {
			var new_access = '';
			var unique_val = $(this).val();
			var access_val = $("#user_access_code").attr('data-access-code');
			new_access = access_val+unique_val;
			$("#user_access_code").val(new_access);
			if(new_access){
				$.ajax({
					url: BASE_URL +'user/check-user-access-code',
					dataType: 'json',
					method: 'POST',
					data: {'register_token': new_access}, // must be json
					success: function(result) {
						if (result.status != 'success') {
							//toastr.error(result.msg);
							$(".access-code").html(result.msg);
							$("#submit-button").prop('disabled', true);
						} else {
							$(".access-code").html('');
							$("#submit-button").prop('disabled', false);
						}
					}
				});	
			}
		});

        $(document).on('click', '.edit_course', function() {
            var type = $(this).attr('data-type');
           var params = $(this).attr('data-params');
            $.ajax({
                url: $(this).attr('data-url'),
                dataType: 'json',
                method: 'POST',
                data: $.parseJSON($(this).attr('data-params')), // must be json
                success: function(result) {
                    var content = '</div><ul style="list-style:none;padding:0">';
                    if (result.status == 'success') {
                        var user_course_list = result.data.user_has_course_list;
                        var checked = '';
                        $.each(result.data.course_list, function(course ,value) {
                            if($.inArray(value.id, user_course_list) !== -1) {
                                checked = 'checked = checked';
                            }  else {
                                checked = '';
                            }
                            content += "<li><h5><input name='course_has_users' value='"+value.id+"' type='checkbox' "+ checked + "/> "+ value.title +"</li></h5>";
                        });
                        content +="</ul></div>";
                        content += "<input id='edit_user_id' type='hidden' data-params='"+ params +"'/>";
                        content += "</div>";
                        $('#edit_course').find('.modal-body').html(content);
                    }
                }
            });
        });
        $(document).on('click', '.reinvite_link', function() {
           var params = $(this).attr('data-params');
            $.ajax({
                url: $(this).attr('data-url'),
                dataType: 'json',
                method: 'POST',
                data: $.parseJSON($(this).attr('data-params')), // must be json
                success: function(result) {
                    console.log(result.status);
                    if (result.status == 'success') {
                        $('#success').css('display','block');
                    }
                }
            });
        });

        $(document).on('click', '.save_course', function() {
            var course_has_users = [];
            $.each($("input[name='course_has_users']:checked"), function(){            
                course_has_users.push($(this).val());
            });
            var params = $.parseJSON($("#edit_user_id").attr('data-params'));
            console.log("params before");
            console.log(params);
            params['course_has_users'] = course_has_users;
            console.log("params after");
            console.log(params);
            $.ajax({
                url: BASE_URL +'Course/user-has-course',
                dataType: 'json',
                method: 'POST',
                data:params, // must be json
                success: function(result) {
                    if (result.status == 'success') {
                        $('#edit_course').modal('hide');;
                    }
                }
            });
         
        });

          $(document).on('click', '.homework-detail', function() {
            var type = $(this).attr('data-type');
            $.ajax({
                url: $(this).attr('data-url'),
               // dataType: 'json',
                method: 'POST',
                data: $.parseJSON($(this).attr('data-params')), // must be json
                success: function(result) {
                        $('#view_homework_excercise').find('.modal-title').text("Homework Excercise");
                        $('#view_homework_excercise').find('.modal-body').html(result);
                    
                }
            });
        });

        function initTinymce(elem) {
            console.log(elem);
            if (typeof(tinymce) != "undefined") {
                tinymce.init({
                    selector: elem,
                    menubar: false,
                    statusbar: false,
                    height: "300",
                    plugins: 'image link hr pagebreak nonbreaking anchor lists textcolor wordcount  imagetools  colorpicker textpattern',
                    toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | bullist outdent indent  | removeformat | fontsizeselect | image',
                    images_upload_url : BASE_URL+'/homework/text-image',
                    fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
                    automatic_uploads : false,
                    image_advtab: true,
                    relative_urls : false,
                    remove_script_host : false,
                    convert_urls : true,
                    images_upload_handler : function(blobInfo, success, failure) {
                        var xhr, formData;
            
                        xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', BASE_URL+'/homework/text-image');
            
                        xhr.onload = function() {
                            var json;
            
                            if (xhr.status != 200) {
                                failure('HTTP Error: ' + xhr.status);
                                return;
                            }
            
                            json = JSON.parse(xhr.responseText);
                            console.log(json);
                            // if (!json || typeof json.location != 'string') {
                            //     failure('Invalid JSON: ' + xhr.responseText);
                            //     return;
                            // }
            
                            success(json.file_path);
                        };
            
                        formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
            
                        xhr.send(formData);
                    },
                    setup: function(editor) {
                        editor.on('change', function(e) {
                            //                            console.log($(this).parent());
                            //
                            //                            $(this).closest('error').html('');
                        });
                    }
                });
            }
		}
		
		$(document).on('click', '.edit_email', function(){
			$(".participant_email").hide();
			$(".participant_email_update").show();
		});
		

        if ($('.text-tiny-mce').length) {
            initTinymce('.text-tiny-mce');
        }

        $(".select-course").on('change', function() {
            let course_id = $(this).children("option:selected").val();
            $.ajax({
                    url: BASE_URL + 'study/get-class',
                    method: 'POST',
                    data: {'course_id': course_id },
                    success: function(result) {
                        let res = JSON.parse(result);
                        let content ='<label class="control-label col-md-3 col-sm-3 col-xs-12" for="class">Classes</label><div class="col-md-6 col-sm-6 col-xs-12">';
                        for (var i = 0, len = res.length; i < len; i++) {
                            content += '<input type="checkbox" id="class'+res[i].id+'" name="class_id[]" value="'+res[i].id+'" >&nbsp;';
                                    
                            content += '<label for="class'+res[i].id+'">'+res[i].title+' </label><br>';  
                        }

                            
                        
                        
                        $('.page_content_div').html(content);
                    }
                });
           //console.log(value);
        });
    });
})(jQuery);

        

