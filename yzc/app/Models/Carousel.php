<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    //
    public function show($data)
    {
        $res = \DB::table('carousels')
            ->select(['id','img','link','description'])
            ->where('type','=',$data)
            ->get();
        $carousel_data = array();
        foreach ($res as $k=>$v) {
            $carousel_data[$k]['id'] = $v->id;
            $carousel_data[$k]['img'] = $v->img;
            $carousel_data[$k]['link'] = $v->link;
            $carousel_data[$k]['description'] = $v->description;
        }
        $carousel_count = count($carousel_data);
        $carousel = array(
            'carousel_data'=>$carousel_data,
            'carousel_count'=>$carousel_count,
        );
        return $carousel;
    }
}
