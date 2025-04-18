<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{
    //

    public function create()
    {
        $redis = Redis::connection();
        $redis->set('producto:1', 'Camisa');
        $redis->set('producto:2', 'Pantalón');
        $redis->set('producto:3', 'Zapatos');
        return "Registros creados en Redis";
    }

    public function read()
    {
        $redis = Redis::connection();
        $productos = [];
        foreach (range(1, 3) as $id) {
            $producto = $redis->get('producto:' . $id);
            $productos[] = $producto;
        }
        return $productos;
    }

    public function update()
    {
        $redis = Redis::connection();
        $redis->set('producto:1', 'Camisa actualizada');
        return "Registro actualizado en Redis";
    }

    public function delete()
    {
        $redis = Redis::connection();
        $redis->del(['producto:1', 'producto:2', 'producto:3']);
        return "Registros eliminados de Redis";
    }
}
