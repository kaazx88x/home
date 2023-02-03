<?php

namespace App\Repositories;
use DB;
use App\Models\State;


class StateRepo
{
    public static function get_states()
    {
        $states = State::where('status', '=', 1)->get();

        return $states;
    }

    public static function get_states_by_country_id($country_id)
    {
        $states = State::where('country_id', '=', $country_id)->where('status', '=', 1)->get();
        return $states;
    }

    public static function all_by_country_id($id)
    {
        $states = State::where('nm_state.country_id','=', $id)
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_state.country_id')
        ->get();

        return $states;
    }

    public static function add($data)
    {
        $state = State::create([
            'name' => $data['name'],
            'country_id' => $data['country'],
            'status' => $data['status'],
        ]);

        return $state;
    }

    public static function find($id)
    {
        return State::find($id);
    }

    public static function update($id,$data)
    {
        $state = State::findOrFail($id);
        $state->name = $data['name'];
        $state->country_id = $data['country'];
        $state->status = $data['status'];
        $state->save();

        return $state;
    }

    public static function delete($id)
    {
        $state = State::findOrFail($id);
        $state->delete();
    }
    
    public static function get_states_by_country_id_name($country_id)
    {
        $states = State::where('country_id', '=', $country_id)->where('status', '=', 1)->select('id', 'name')->get();
        return $states;
        //return response()->json($states);
    }
}