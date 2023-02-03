<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">{{trans('localize.bank_information')}}</h4>
</div>
<div class="modal-body">
    <div class="row form-horizontal">
        <div class="form-group">
            <label class="col-sm-5 control-label">{{trans('localize.account_holder_name')}}</label>
            <div class="col-sm-7">
                    <p class="form-control-static">{{ $merchant->bank_acc_name}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-5 control-label">{{trans('localize.account_number')}}</label>
            <div class="col-sm-7">
                    <p class="form-control-static">{{ $merchant->bank_acc_no}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-5 control-label">{{trans('localize.name_of_bank')}}</label>
            <div class="col-sm-7">
                    <p class="form-control-static">{{ $merchant->bank_name}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-5 control-label">{{trans('localize.country_of_bank')}}</label>
            <div class="col-sm-7">
                    <p class="form-control-static">{{ $merchant->co_name}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-5 control-label">{{trans('localize.address_of_bank')}}</label>
            <div class="col-sm-7">
                    <p class="form-control-static">{{ $merchant->bank_address}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-5 control-label">{{trans('localize.Bank_swift_code_BIC_ABA')}}</label>
            <div class="col-sm-7">
                    <p class="form-control-static">{{ $merchant->bank_swift}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-5 control-label">{{trans('localize.if_europe_bank_IBAN')}}</label>
            <div class="col-sm-7">
                    <p class="form-control-static">{{ $merchant->bank_europe}}</p>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('localize.closebtn')}}</button>
</div>