/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
document.write('<script type="text/javascript" src="/backend/js/country_state_lib.js"></script>');

function get_countries(country_input, country_id = null, state_input = null, state_id = null)
{
    country_id = !isNaN(parseInt(country_id))? parseInt(country_id) : 0;
    state_id = !isNaN(parseInt(state_id))? parseInt(state_id) : 0;

    $(country_input).html('<option value>'+window.translations.selectCountry+'</option>');
    $.each(myJson.country, function (index, value)
    {
        $(country_input).append($('<option>', {
            value: value.id,
            text: value.name,
        }));

        if(country_id > 0 && country_id == value.id)
        {
            $(country_input).val(value.id).change();
            if(state_id > 0 && state_input && myJson.country[value.id].states != null)
            {
                get_states(state_input, value.id, state_id);
            }
        }
    });
}

function get_states(state_input, country_id, state_id = null)
{
    country_id = !isNaN(parseInt(country_id))? parseInt(country_id) : 0;
    state_id = !isNaN(parseInt(state_id))? parseInt(state_id) : 0;

    if(myJson.country[country_id] == null)
    {
        $(state_input).html('<option value>'+window.translations.selectCountryFirst+'</option>');
        return;
    }

    $(state_input).html('<option value>'+window.translations.selectState+'</option>');
    var states = myJson.country[country_id].states;
    $.each(states, function (index, value)
    {
        $(state_input).append($('<option>', {
            value: value.id,
            text: value.name,
        }));

        if(state_input && state_id == value.id)
        {
            $(state_input).val(value.id).change();
        }
    });
}

function get_phoneAreacode(areacode_input, areacode = null)
{
    areacode = !isNaN(parseInt(areacode))? parseInt(areacode) : 0;

    $.each(myJson.country, function (index, value)
    {
        $(areacode_input).append($('<option>', {
            value: value.phone_areacode,
            text: '+' + value.phone_areacode + ' - ' + value.name,
        }));

        if(areacode > 0 && areacode == value.phone_areacode)
        {
            $(areacode_input).val(value.phone_areacode).change();
        }
        else
        {
            if(value.phone_areacode == 60) {
                $(areacode_input).val(value.phone_areacode).change();
            }
        }
    });
}