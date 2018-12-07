<?php
/**
 * 清仓特卖
 * User: Administrator
 * Date: 2018/11/26
 * Time: 8:55
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClearingHouses;



class ClearingHousesController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';

    public static function obj()
    {
        return new \stdClass();
    }

    /**
     * 获取清仓数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClearingHouses() {
        $clearingHousesObj = new ClearingHouses();
        $clearingHousesInfo = request()->all();
        $info = $this->obj();
        if (isset($clearingHousesInfo['id'])) {//取详情--id
            $info = $clearingHousesObj->sel_clearing_houses_info($clearingHousesInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            if(isset($clearingHousesInfo['room_city']))
            {
                //默认10条+默认页码1
                if(!isset($clearingHousesInfo['now_page'])){
                    $clearingHousesInfo['now_page'] = 1;
                }
                //分页数据
                $info->data = $clearingHousesObj->sel_clearing_houses_list($clearingHousesInfo);
                //总页码
                $info->last_page = ceil($clearingHousesObj->sel_clearing_houses_page($clearingHousesInfo)/10);
                //当前页码
                $info->current_page = intval($clearingHousesInfo['now_page']);
            }
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 设置清仓入库数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function setClearingHouses() {
        $clearingHousesObj = new ClearingHouses();
        $clearingHousesInfo = request()->all();
        if (empty($clearingHousesInfo) ||
            !array_key_exists('univalent',$clearingHousesInfo) ||
            !array_key_exists('unit',$clearingHousesInfo)||
            !array_key_exists('number',$clearingHousesInfo)||
            !array_key_exists('company',$clearingHousesInfo)||
            !array_key_exists('mode',$clearingHousesInfo)||
            !array_key_exists('invoice',$clearingHousesInfo)||
            !array_key_exists('room_city',$clearingHousesInfo)||
            !array_key_exists('address',$clearingHousesInfo)||
            !array_key_exists('remarks',$clearingHousesInfo)||
            !array_key_exists('contacts_name',$clearingHousesInfo)||
            !array_key_exists('contacts_tel',$clearingHousesInfo)||
            !array_key_exists('photo',$clearingHousesInfo)||
            !array_key_exists('api_token',$clearingHousesInfo)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!tel_preg($clearingHousesInfo['contacts_tel'])){
            return msg_err(PassportController::NO_STATUS,'手机号格式不正确');
        }
        $redis = GetAppToken($clearingHousesInfo['api_token']);
        if($redis['code'] == 200){
            $clearingHousesInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }

        $clearingHousesInfo = handleData($clearingHousesInfo);
        //数据验证通过
        $bool = $clearingHousesObj->create($clearingHousesInfo);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        set_release_num($clearingHousesInfo['users_id']);
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }

    /**
     * 获取我发的清仓特卖列表以及详情
     * api_token
     */
    public function getMyClearingHouses()
    {
        $clearingHousesObj = new ClearingHouses();
        $clearingHousesInfo = request()->all();
        $info = $this->obj();
        if (isset($clearingHousesInfo['id'])) {//取详情--id
            $info = $clearingHousesObj->sel_clearing_houses_info($clearingHousesInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，4。数据条数
                //默认10条+默认页码1
            $redis = GetAppToken($clearingHousesInfo['api_token']);
            if($redis['code'] == 200){
                $clearingHousesInfo['users_id'] = $redis['u_id'];
             }else{
                return msg_err($redis['code'],$redis['msg']);
             }
            if(!isset($clearingHousesInfo['now_page'])){
                $clearingHousesInfo['now_page'] = 1;
            }
            if (!array_key_exists('f',$clearingHousesInfo)) {
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }
            //分页数据
            $info->data = $clearingHousesObj->sel_clearing_houses_list($clearingHousesInfo);
            //总页码
            $info->last_page = ceil($clearingHousesObj->sel_clearing_houses_page($clearingHousesInfo)/10);
            //当前页码
            $info->current_page = intval($clearingHousesInfo['now_page']);

        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 删除我的
     */
    public function delMyClearingHouses()
    {
        $clearingHousesObj = new ClearingHouses();
        $clearingHousesInfo = request()->all();
        $redis = GetAppToken($clearingHousesInfo['api_token']);
        if($redis['code'] == 200){
            $clearingHousesInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if (!array_key_exists('id',$clearingHousesInfo)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $bool = $clearingHousesObj->delMyData($clearingHousesInfo['id']);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,'删除失败',new \stdClass());
        }
        return msg_ok(PassportController::YSE_STATUS,'删除成功',new \stdClass());
    }
}