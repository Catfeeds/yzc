<?php
/**
 * 寻租转租
 * User: Administrator
 * Date: 2018/11/26
 * Time: 10:17
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GetrentSetrents;



class GetrentSetrentsController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';

    public static function obj()
    {
        return new \stdClass();
    }

    /**
     * 获取数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGetrentSetrents()
    {
        $getrentSetrentsObj = new GetrentSetrents();
        $getrentSetrentsInfo = request()->all();
        $info = $this->obj();
        if (isset($getrentSetrentsInfo['id'])) {//取详情--id
            $info = $getrentSetrentsObj->sel_getrent_setrents_info($getrentSetrentsInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            if(empty($getrentSetrentsInfo) ||
                !array_key_exists('type',$getrentSetrentsInfo)){
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }
            if(isset($getrentSetrentsInfo['room_city']))
            {
                if(!isset($getrentSetrentsInfo['now_page'])){
                    $getrentSetrentsInfo['now_page'] = 1;
                }
                //f分页数据
                $info->data = $getrentSetrentsObj->sel_getrent_setrents_list($getrentSetrentsInfo);
                //总页码
                $info->last_page = ceil($getrentSetrentsObj->sel_getrent_setrents_page($getrentSetrentsInfo)/10);
                //当前页码
                $info->current_page = intval($getrentSetrentsInfo['now_page']);
            }
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 设置数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function setGetrentSetrents()
    {
        $getrentSetrentsObj = new GetrentSetrents();
        $getrentSetrentsInfo = request()->all();
        if (empty($getrentSetrentsInfo) ||
            !array_key_exists('type',$getrentSetrentsInfo) ||
            !array_key_exists('acreage',$getrentSetrentsInfo)||
            !array_key_exists('money',$getrentSetrentsInfo)||
            !array_key_exists('room_city',$getrentSetrentsInfo)||
            !array_key_exists('address',$getrentSetrentsInfo)||
            !array_key_exists('start_time',$getrentSetrentsInfo)||
            !array_key_exists('set_money',$getrentSetrentsInfo)||
            !array_key_exists('shops_id',$getrentSetrentsInfo)||
            !array_key_exists('matching',$getrentSetrentsInfo)||
            !array_key_exists('contacts_name',$getrentSetrentsInfo)||
            !array_key_exists('contacts_tel',$getrentSetrentsInfo)||
            !array_key_exists('photo',$getrentSetrentsInfo)||
            !array_key_exists('api_token',$getrentSetrentsInfo)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!tel_preg($getrentSetrentsInfo['contacts_tel'])){
            return msg_err(PassportController::NO_STATUS,'手机号格式不正确');
        }
        $redis = GetAppToken($getrentSetrentsInfo['api_token']);
        if($redis['code'] == 200){
            $getrentSetrentsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $getrentSetrentsInfo = handleData($getrentSetrentsInfo);
        //数据验证通过

        $bool = $getrentSetrentsObj->create($getrentSetrentsInfo);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        set_release_num($getrentSetrentsInfo['users_id']);
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }

    /**
     * 我发布的
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyGetrentSetrents()
    {
        $getrentSetrentsObj = new GetrentSetrents();
        $getrentSetrentsInfo = request()->all();
        $info = $this->obj();
        if (isset($getrentSetrentsInfo['id'])) {//取详情--id
            $info = $getrentSetrentsObj->sel_getrent_setrents_info($getrentSetrentsInfo['id']);
            $info = res_time($info);
        }else {//取列表1时间先后2审核通过，3。城市范围4。数据条数
            $redis = GetAppToken($getrentSetrentsInfo['api_token']);
            if($redis['code'] == 200){
                $getrentSetrentsInfo['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            if (!array_key_exists('f', $getrentSetrentsInfo)) {
                return msg_err(PassportController::NO_STATUS, PassportController::PARAM);
            }
            if (!isset($getrentSetrentsInfo['now_page'])) {
                $getrentSetrentsInfo['now_page'] = 1;
            }
            //f分页数据
            $info->data = $getrentSetrentsObj->sel_getrent_setrents_list($getrentSetrentsInfo);
            //总页码
            $info->last_page = ceil($getrentSetrentsObj->sel_getrent_setrents_page($getrentSetrentsInfo) / 10);
            //当前页码
            $info->current_page = intval($getrentSetrentsInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 删除我发布的
     */
    public function delMyGetrentSetrents()
    {
        $getrentSetrentsObj = new GetrentSetrents();
        $getrentSetrentsInfo = request()->all();
        $redis = GetAppToken($getrentSetrentsInfo['api_token']);
        if($redis['code'] == 200){
            $getrentSetrentsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if (!array_key_exists('id',$getrentSetrentsInfo)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $bool = $getrentSetrentsObj->del_my_data($getrentSetrentsInfo['id']);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,'删除失败',new \stdClass());
        }
        return msg_ok(PassportController::YSE_STATUS,'删除成功',new \stdClass());
    }


}