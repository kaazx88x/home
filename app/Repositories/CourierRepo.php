<?php

namespace App\Repositories;
use App\Models\Courier;
use Auth;

class CourierRepo
{
    public static function get_couriers()
    {
        return Courier::where('status', '=', 1)->get();
    }

    public static function all()
    {
        return Courier::all();
    }

    public static function add($data)
    {
        $courier = Courier::create([
            'name' => $data['name'],
            'link' => $data['link'],
            'status' => $data['status'],
        ]);

        return $courier;
    }

    public static function find($id)
    {
        return Courier::find($id);
    }

    public static function update($id,$data)
    {
        $courier = Courier::find($id);
        $courier->name = $data['name'];
        $courier->link = $data['link'];
        $courier->status = $data['status'];
        $courier->save();

        return $courier;
    }

    public static function delete($id)
    {
        $courier = Courier::find($id);
        $courier->delete();

        return $courier;
    }
}
