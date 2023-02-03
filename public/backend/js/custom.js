// Get & Load Main Category
function load_maincategory(input, main, sub) {
    $.get("/load_maincategory", { mc_id: main, smc_id: sub })
    .done(function( data ) {
        $(input).html(data);
    });
}

// Get & Load Sub Category
function load_subcategory(input, main, sub) {
    $.get("/load_subcategory", { smc_id: main, sb_id: sub })
    .done(function( data ) {
        $(input).html(data);
    });
}

// Get & Load Second Sub Category
function load_secsubcategory(input, main, sub) {
    $.get("/load_secsubcategory", { sb_id: main, ssb_id: sub })
    .done(function( data ) {
        $(input).html(data);
    });
}

// Remove Image Field
function removeDiv(id) {
    var count_id = document.getElementById("count").value;
    document.getElementById('count').value = parseInt(count_id)-1;
    jQuery(id).remove();
}

//Get & Load Size
function load_size(size, sdata) {
    $.get("/load_size", { size_id: size, data: sdata })
    .done(function(data) {
        if (data.status == 1) {
            $('#sizeDiv').css('display', 'block');
            $('#showSize').append('<span class="label" style="font-size:12px;">' + data.size_name + '&nbsp;<input type="checkbox"  name="sizecheckbox[' + data.size_id + ']" checked="checked" value="1"></span>&nbsp;');

            var size_data = $('#si').val();
            if (size_data == '') {
                $('#si').val(data.size_id);
            } else {
                $('#si').val(data.size_id + ',' + size_data);
            }
        } else {
            swal("Sorry!", data.error, "error");
        }
    });
}

//Get & Load Color
function load_color(color, cdata) {
    $.get("/load_color", { color_id: color, data: cdata })
    .done(function(data) {
        if (data.status == 1) {
            $('#colorDiv').css('display', 'block');
            $('#showColor').append('<span class="label col-sm-3" style="font-size:12px; border:1px solid #e5e6e7; background-color:' + data.color_code + ';">' + data.color_name + '&nbsp;<input type="checkbox"  name="colorcheckbox[' + data.color_id + ']" checked="checked" value="1"></span>');

            var color_data = $('#co').val();
            if (color_data == '') {
                $('#co').val(data.color_id);
            } else {
                $('#co').val(data.color_id + ',' + color_data);
            }
        } else {
            swal("Sorry!", data.error, "error");
        }
    });
}

// View Order Detail
function view_order(this_id) {
    $.get( '/view_order/' + this_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

//view vcoin log
function vcoinlog(this_id) {
    $.get( '/vcoinlog/' + this_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

//view gp log
function gplog(this_id) {
    $.get( '/gplog/' + this_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

// View Shipment Detail
function view_shipment(this_id) {
    $.get( '/view_shipment/' + this_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

// Update Shipment Detail
function update_shipment(this_id) {
    $.get( '/update_shipment/' + this_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

// Accept Order
function accept_order(this_id) {
    swal({
        title: window.translations.sure,
        text: window.translations.confirm,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#5cb85c",
        confirmButtonText: window.translations.accept,
        closeOnConfirm: true
    }, function(){
        $('#spinner').show();
        var url = '/accept_order/' + this_id + '/2';
        window.location.href = url;
    });
}

function send_auction_winner(oa_id) {
    $.get( '/send_auction_winner/' + oa_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

// Get & Load Merchant Store
function load_merchant_store(input, mer_id, store_id) {
    $.get("/load_merchant_store", { mer_id: mer_id, store_id: store_id })
    .done(function( data ) {
        $(input).html(data);
    });
}

//Get & Load City
function load_city(input, country_id, city_id) {
    $.get("/load_city", { country_id: country_id, city_id: city_id })
    .done(function( data ) {
        $(input).html(data);
    });
}

//Check Existing Merchant Email
function check_merchant_email(email){
    var passemail = 'email=' + email;
    var cemail = $('#email');
    $.ajax({
        type: 'get',
        data: passemail,
        url: '/merchant_emailcheck',
        success: function (responseText) {
            if (responseText) {
                if (responseText == 1) {
                    swal({
                        title: window.translations.error,
                        text: email+window.translations.is_taken,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#d9534f",
                        confirmButtonText: window.translations.ok,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                    });
                    cemail.css('border', '1px solid red');
                    cemail.val('');
                    cemail.focus();
                    return false;
                } else {
                    cemail.css('border', '');
                }
            }
        }
    });
}

// View Order Offline Detail
function view_order_offline(this_id,type) {
    $.get( '/view_order_offline/' + this_id + '/' + type, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

//view merchant bank info
function view_merchant_bank_info(this_id) {
    $.get( '/get_merchant_bank_info/' + this_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

//update fund withdraw
function approve_fund(this_id) {
    swal({
        title: window.translations.sure,
        text: window.translations.accept_fund_request+"?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#5cb85c",
        confirmButtonText: window.translations.yes,
        cancelButtonText: window.translations.cancel,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function(){
        var url = '/update_fund_withdraw_status/' + this_id + '/1';
        window.location.href = url;
    });
}

function decline_fund(this_id) {
    swal({
        title: window.translations.sure,
        text: window.translations.decline_fund_request+"?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9534f",
        confirmButtonText: window.translations.yes,
        cancelButtonText: window.translations.cancel,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function(){
        var url = '/update_fund_withdraw_status/' + this_id + '/2';
        window.location.href = url;
    });
}

//Get & Load State
function load_state(input, country_id, state_id) {
    $.get("/load_state", { country_id: country_id, state_id: state_id })
    .done(function( data ) {
        $(input).html(data);
    });
}

// Edit image
function edit_product_image(mer_id,this_id) {
    $.get( '/edit_product_image/' + mer_id + '/' + this_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

// Edit Product Pricing
function edit_product_pricing(mer_id,this_id) {
    $.get( '/product_pricing/edit/' + mer_id + '/' + this_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

//Edit Product Pricing Attribute
function edit_pricing_attribute(mer_id, pro_id ,pricing_id) {
    $.get( '/pricing_attribute_quantity/edit/' + mer_id + '/' + pro_id + '/' + pricing_id, function( data ) {
        $('#myModal').modal();
        $('#myModal').on('shown.bs.modal', function(){
            $('#myModal .load_modal').html(data);
        });
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

// Check Merchant Username
function check_merchant_username(inputName, username, merchant_id, form) {
    var usernameReg = /^[a-zA-Z0-9]*$/;

    if (username.length < 4) {
        swal({
            title: window.translations.error,
            text: window.translations.username_validation_error,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#d9534f",
            confirmButtonText: window.translations.ok,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        });
        $(inputName).css('border', '1px solid red').focus();
        form.preventDefault();
        return false;
    } else if (!usernameReg.test($(inputName).val())) {
        swal({
            title: window.translations.error,
            text: window.translations.username_invalid_character,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#d9534f",
            confirmButtonText: window.translations.ok,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        });
        $(inputName).css('border', '1px solid red').focus();
        form.preventDefault();
        return false;
    } else {
        $.get( '/merchant/usernamecheck?username=' + username + '&merid=' + merchant_id, function( data ) {
            if (data > 0) {
                swal({
                    title: window.translations.error,
                    text: username+window.translations.is_taken,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#d9534f",
                    confirmButtonText: window.translations.ok,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                });
                $(inputName).css('border', '1px solid red').focus();
                $(inputName).val('');
                form.preventDefault();
                return false;
            } else {
                $(inputName).css('border', '');
                return true;
            }
        });
    }
}

// Check Merchant Email
function check_merchant_email(inputName, email, merchant_id, form) {
     var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    if(!emailReg.test(email)) {
        swal({
            title: window.translations.error,
            text: window.translations.email_validation_error,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#d9534f",
            confirmButtonText: window.translations.ok,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        });
        $(inputName).css('border', '1px solid red').focus();
        form.preventDefault();
        return false;
    } else {
        $.get( '/merchant/emailcheck?email=' + email + '&merid=' + merchant_id, function( data ) {
            if (data > 0) {
                swal({
                    title: window.translations.error,
                    text: email+window.translations.is_taken,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#d9534f",
                    confirmButtonText: window.translations.ok,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                });
                $(inputName).css('border', '1px solid red').focus();
                $(inputName).val('');
                form.preventDefault();
                return false;
            } else {
                $(inputName).css('border', '');
                return true;
            }
        });
    }
}

// Check Merchant Username
function check_storeuser_username(inputName, username, id, form) {
    var usernameReg = /^[a-zA-Z0-9]*$/;

    if (username.length < 4) {
        swal({
            title: window.translations.error,
            text: window.translations.username_validation_error,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#d9534f",
            confirmButtonText: window.translations.ok,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        });
        $(inputName).css('border', '1px solid red').focus();
        form.preventDefault();
        return false;
    } else if (!usernameReg.test($(inputName).val())) {
            swal({
                title: window.translations.error,
                text: window.translations.username_invalid_character,
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#d9534f",
                confirmButtonText: window.translations.ok,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            });
            $(inputName).css('border', '1px solid red').focus();
            form.preventDefault();
            return false;
    } else {
        $.get( '/store_usernamecheck?username=' + username + '&id=' + id, function( data ) {
            if (data > 0) {
                swal({
                    title: window.translations.error,
                    text: username+window.translations.is_taken,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#d9534f",
                    confirmButtonText: window.translations.ok,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                });
                $(inputName).css('border', '1px solid red').focus();
                $(inputName).val('');
                form.preventDefault();
                return false;
            } else {
                $(inputName).css('border', '');
                return true;
            }
        });
    }
}

// Check Merchant Email
function check_storeuser_email(inputName, email, id, form) {
     var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    if(!emailReg.test(email)) {
        swal({
            title: window.translations.error,
            text: window.translations.email_validation_error,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#d9534f",
            confirmButtonText: window.translations.ok,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        });
        $(inputName).css('border', '1px solid red').focus();
        form.preventDefault();
        return false;
    } else {
        $.get( '/store_emailcheck?email=' + email + '&id=' + id, function( data ) {
            if (data > 0) {
                swal({
                    title: window.translations.error,
                    text: email+window.translations.is_taken,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#d9534f",
                    confirmButtonText: window.translations.ok,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                });
                $(inputName).css('border', '1px solid red').focus();
                $(inputName).val('');
                form.preventDefault();
                return false;
            } else {
                $(inputName).css('border', '');
                return true;
            }
        });
    }
}

// Edit product attribute
function edit_product_attribute(attribute_id, pro_id, mer_id) {
    $.get( '/edit_product_attribute/' + attribute_id + '/' + pro_id + '/' + mer_id, function( data ) {
        $('#myModal-static').modal();
        $('#myModal-static').on('shown.bs.modal', function(){
            $('#myModal-static .load_modal').html(data);
        });
        $('#myModal-static').on('hidden.bs.modal', function(){
            $('#myModal-static .modal-body').data('');
        });
    });
}

function paid_fund(this_id) {
    swal({
        title: window.translations.sure,
        text: window.translations.set_paid_fund_request,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#5cb85c",
        confirmButtonText: window.translations.yes,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function(){
        var url = '/update_fund_withdraw_status/' + this_id + '/3';
        window.location.href = url;
    });
}

function check_member_username(inputName, username, id, form) {
    var usernameReg = /^[a-zA-Z0-9]*$/;

    if (username.length < 4) {
        swal({
            title: window.translations.error,
            text: window.translations.username_validation_error,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#d9534f",
            confirmButtonText: window.translations.ok,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        });
        $(inputName).css('border', '1px solid red').focus();
        form.preventDefault();
        return false;
    } else if (!usernameReg.test($(inputName).val())) {
            swal({
                title: window.translations.error,
                text: window.translations.username_invalid_character,
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#d9534f",
                confirmButtonText: window.translations.ok,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            });
            $(inputName).css('border', '1px solid red').focus();
            form.preventDefault();
            return false;
    } else {
        $.get( '/member_usernamecheck?username=' + username + '&id=' + id, function( data ) {
            if (data > 0) {
                swal({
                    title: window.translations.error,
                    text: username+window.translations.is_taken,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#d9534f",
                    confirmButtonText: window.translations.ok,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                });
                $(inputName).css('border', '1px solid red').focus();
                $(inputName).val('');
                form.preventDefault();
                return false;
            } else {
                $(inputName).css('border', '');
                return true;
            }
        });
    }
}

function check_member_email(inputName, email, id, form) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    if(!emailReg.test(email)) {
        // swal("Error!", "Please enter a valid email address", "error");
        swal({
            title: window.translations.error,
            text: window.translations.email_validation_error,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#d9534f",
            confirmButtonText: window.translations.ok,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        });
        $(inputName).css('border', '1px solid red').focus();
        form.preventDefault();
        return false;
    } else {
        $.get( '/member_emailcheck?email=' + email + '&id=' + id, function( data ) {
            if (data > 0) {
                // swal("Error!", "Email already used.", "error");
                swal({
                    title: window.translations.error,
                    text: window.translations.email_exist,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#d9534f",
                    confirmButtonText: window.translations.ok,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                });
                $(inputName).css('border', '1px solid red').focus();
                $(inputName).val('');
                form.preventDefault();
                return false;
            } else {
                $(inputName).css('border', '');
                return true;
            }
        });
    }
}

function check_member_phone(inputName, phone, id, form) {
    $.get( '/member_phone_check?phone=' + phone + '&id=' + id, function( data ) {
        if (data > 0) {
            // swal("Error!", "Phone number is already taken", "error");
            swal({
                title: window.translations.error,
                text: window.translations.phone_exist,
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#d9534f",
                confirmButtonText: window.translations.ok,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            });
            $(inputName).css('border', '1px solid red').focus();
            $(inputName).val('');
            form.preventDefault();
            return false;
        } else {
            $(inputName).css('border', '');
            return true;
        }
    });
}

function GetServerDate()
{
    var svserverDate;
    $.ajax({
        async: false,
        url: '/getDate',
        dataType: 'json',
        type: 'get',
        success: function (res) {
            svserverDate = res.dateData;
            $('#serverdate').val(svserverDate);
        }
    });
}

function countdown(expired_date, button_name = null)
{
    GetServerDate();
    var serverDate = $('#serverdate').val();
    var nowx = new Date(serverDate).getTime();
    var expiredDate = new Date(expired_date).getTime();
    var dateDiffTime = expiredDate - nowx;
    var fiveMinTime = (5 * 60 * 1000);

    var expired = new Date().getTime() + dateDiffTime;

    if (dateDiffTime > fiveMinTime)
        expired = new Date().getTime() + fiveMinTime;

    $(button_name).siblings().hide();
    var btn_text = $(button_name).text();

    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = expired - now;
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        $(button_name).html('<i class="fa fa-clock-o" aria-hidden="true"></i> ' + minutes + "m " + seconds + "s "+window.translations.time_left).prop('disabled', true);

        if (distance < 0) {
            clearInterval(x);
            $(button_name).html(btn_text).prop('disabled', false);
            $(button_name).siblings().show();
        }
    }, 1000);
}

//view order coupon list
function get_code_number_listing(this_id, user_type, product_type) {

    var url = '/get_code_number_listing/' + this_id + '/' + user_type + '/' + product_type;

    $.get( url, function( data ) {
        if(data == 0) {
            swal("Error!", "Invalid Operation!", "error");
        } else {
            $('#myModal').modal();
            $('#myModal').on('shown.bs.modal', function(){
                $('#myModal .load_modal').html(data);
            });
        }

        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

$('#sdate').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    format: "dd/mm/yyyy",
    minDate: 0,
}).on('changeDate', function(){
    var minDate = $(this).datepicker('getDate');
    $('#edate').datepicker('setDate', minDate);
    $('#edate').datepicker('setStartDate', minDate);
});

$('#edate').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    format: "dd/mm/yyyy",
}).on('changeDate', function(){
    var minDate = $(this).datepicker('getDate');
    $('#sdate').datepicker('setEndDate', minDate);
});

function load_user_country(input, u_country) {
    $.get("/load_user_country", { u_country: u_country})
    .done(function( data ) {
        $(input).html(data);
    });
}

function sendFile(file, textarea_id,mer_id) {

    data = new FormData();
    data.append("file", file);
    data.append("mer_id", mer_id);
    $.ajax({
        data: data,
        type: "POST",
        url: "/description",
        cache: false,
        contentType: false,
        processData: false,
        success: function(url) {
            $('#' + textarea_id).summernote('insertImage',url);
        }
    });
}

// View withdraw statement
function get_fund_withdraw_statement(fund_id, mer_id) {
    $.get( '/get_fund_withdraw_statement', {mer_id: mer_id, fund_id: fund_id})
    .done(function(data) {
        if(data == 0) {
            swal("Error!", "Invalid Operation!", "error");
        } else {
            $('#myModal .modal-dialog').addClass('modal-lg');
            $('#myModal').modal();
            $('#myModal').on('shown.bs.modal', function(){
                $('#myModal .load_modal').html(data);
            });
        }

        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-body').data('');
        });
    });
}

function redeem_ecard(url) {

    swal({
        title: window.translations.sure,
        text: window.translations.ecard_redeem_confirm,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#5cb85c",
        confirmButtonText: window.translations.yes,
        cancelButtonText: window.translations.cancel,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function(){
        $('#spinner').show();
        window.location.href = url;
    });

}

function delete_ecard(url) {

    swal({
        title: window.translations.sure,
        text: window.translations.ecard_delete_confirm,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9534f",
        confirmButtonText: window.translations.yes,
        cancelButtonText: window.translations.cancel,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function(){
        $('#spinner').show();
        window.location.href = url;
    });

}
