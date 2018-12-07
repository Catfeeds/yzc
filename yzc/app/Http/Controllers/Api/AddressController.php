<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AddressController extends Controller
{


      /**
     * 城市列表.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $db = \DB::table('cities');
        //列表
        $data['list'] = $db
                ->select(['id','area_name','area_code','initial'])
                ->orderBy('initial')
                ->get();
        foreach ($data['list'] as &$v) {
            $v->initial = strtoupper($v->initial);
        }
        //热门城市
        $data['hot_city'] = $db
                ->select(['id','area_name','area_code','initial'])
                ->where('status','=',1)
                ->get();
        return msg_ok(PassportController::YSE_STATUS,'success',$data);
    }

    /**
     * 根据城市名获取城市信息
     */
    public function citiesInfo()
    {
        $cities = request()->all();
        if (empty($cities) ||
            !array_key_exists('city_name',$cities)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $list = \DB::table('cities')
            ->select(['id','area_name','area_code','initial'])
            ->where('area_name', 'like', '%'.$cities['city_name'].'%')
            ->get();
        foreach ($list as &$v) {
            $v->initial = strtoupper($v->initial);
        }
        $list = arr($list);
        return msg_ok(PassportController::YSE_STATUS,'城市信息',$list);
    }


    /**
     * 获取图片基地址
     */
    public function get_base_url(){
        return msg_ok(PassportController::YSE_STATUS,'success',[getenv('APP_URL').'/uploads/']);
    }

}
