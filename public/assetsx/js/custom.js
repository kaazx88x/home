function load_maincategory(input,main,sub){$.get("/load_maincategory",{mc_id:main,smc_id:sub}).done(function(data){$(input).html(data);});}
function load_subcategory(input,main,sub){$.get("/load_subcategory",{smc_id:main,sb_id:sub}).done(function(data){$(input).html(data);});}
function load_secsubcategory(input,main,sub){$.get("/load_secsubcategory",{sb_id:main,ssb_id:sub}).done(function(data){$(input).html(data);});}
function removeDiv(id){var count_id=document.getElementById("count").value;document.getElementById('count').value=parseInt(count_id)-1;jQuery(id).remove();}
function load_size(size,sdata){$.get("/load_size",{size_id:size,data:sdata}).done(function(data){if(data.status==1){$('#sizeDiv').css('display','block');$('#showSize').append('<span class="label" style="font-size:12px;">'+ data.size_name+'&nbsp;<input type="checkbox"  name="sizecheckbox['+ data.size_id+']" checked="checked" value="1"></span>&nbsp;');var size_data=$('#si').val();if(size_data==''){$('#si').val(data.size_id);}else{$('#si').val(data.size_id+','+ size_data);}}else{swal("Sorry!",data.error,"error");}});}
function load_color(color,cdata){$.get("/load_color",{color_id:color,data:cdata}).done(function(data){if(data.status==1){$('#colorDiv').css('display','block');$('#showColor').append('<span class="label col-sm-3" style="font-size:12px; border:1px solid #e5e6e7; background-color:'+ data.color_code+';">'+ data.color_name+'&nbsp;<input type="checkbox"  name="colorcheckbox['+ data.color_id+']" checked="checked" value="1"></span>');var color_data=$('#co').val();if(color_data==''){$('#co').val(data.color_id);}else{$('#co').val(data.color_id+','+ color_data);}}else{swal("Sorry!",data.error,"error");}});}
function view_order(this_id){$.get('/view_order/'+ this_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function vcoinlog(this_id){$.get('/vcoinlog/'+ this_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function gplog(this_id){$.get('/gplog/'+ this_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function view_shipment(this_id){$.get('/view_shipment/'+ this_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function update_shipment(this_id){$.get('/update_shipment/'+ this_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function accept_order(this_id){swal({title:window.translations.sure,text:window.translations.confirm,type:"warning",showCancelButton:true,confirmButtonColor:"#5cb85c",confirmButtonText:window.translations.accept,closeOnConfirm:false},function(){var url='/accept_order/'+ this_id+'/2';window.location.href=url;});}
function send_auction_winner(oa_id){$.get('/send_auction_winner/'+ oa_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function load_merchant_store(input,mer_id,store_id){$.get("/load_merchant_store",{mer_id:mer_id,store_id:store_id}).done(function(data){$(input).html(data);});}
function load_city(input,country_id,city_id){$.get("/load_city",{country_id:country_id,city_id:city_id}).done(function(data){$(input).html(data);});}
function check_merchant_email(email){var passemail='email='+ email;var cemail=$('#email');$.ajax({type:'get',data:passemail,url:'/merchant_emailcheck',success:function(responseText){if(responseText){if(responseText==1){swal("Email!",email+" is already registered by another user, \nplease enter different email address","error");cemail.css('border','1px solid red');cemail.val('');cemail.focus();return false;}else{cemail.css('border','');}}}});}
function view_order_offline(this_id,type){$.get('/view_order_offline/'+ this_id+'/'+ type,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function view_merchant_bank_info(this_id){$.get('/get_merchant_bank_info/'+ this_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function approve_fund(this_id){swal({title:"Are you sure?",text:"Confirm to accept this withdrawal?",type:"warning",showCancelButton:true,confirmButtonColor:"#5cb85c",confirmButtonText:"Yes, Accept it!",closeOnConfirm:false},function(){var url='/update_fund_withdraw_status/'+ this_id+'/1';window.location.href=url;});}
function decline_fund(this_id){swal({title:"Are you sure?",text:"Confirm to decline this withdrawal?",type:"warning",showCancelButton:true,confirmButtonColor:"#d9534f",confirmButtonText:"Yes, Decline it!",closeOnConfirm:false},function(){var url='/update_fund_withdraw_status/'+ this_id+'/2';window.location.href=url;});}
function load_state(input,country_id,state_id){$.get("/load_state",{country_id:country_id,state_id:state_id}).done(function(data){$(input).html(data);});}
function edit_product_image(mer_id,this_id){$.get('/edit_product_image/'+ mer_id+'/'+ this_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}
function edit_product_pricing(mer_id,this_id){$.get('/product_pricing/edit/'+ mer_id+'/'+ this_id,function(data){$('#myModal').modal();$('#myModal').on('shown.bs.modal',function(){$('#myModal .load_modal').html(data);});$('#myModal').on('hidden.bs.modal',function(){$('#myModal .modal-body').data('');});});}