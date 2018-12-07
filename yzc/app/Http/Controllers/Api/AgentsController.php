<?php
/**
 * 招商代理
 * User: Administrator
 * Date: 2018/11/26
 * Time: 10:37
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agents;

class AgentsController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';

    public static function obj()
    {
        return new \stdClass();
    }

    public function getAgents()
    {
        $agentsObj = new Agents();
        $agentsInfo = request()->all();
        $info = $this->obj();
        if (isset($agentsInfo['id'])) {//取详情--id
            $info = $agentsObj->sel_agents_info($agentsInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            if(empty($agentsInfo) ||
                !array_key_exists('type',$agentsInfo)){
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }
            if(isset($agentsInfo['room_city']))
            {
                if(!isset($agentsInfo['now_page'])){
                    $agentsInfo['now_page'] = 1;
                }
                //分页数据
                $info->data = $agentsObj->sel_agents_list($agentsInfo);
                //总页码
                $info->last_page = ceil($agentsObj->sel_agents_page($agentsInfo)/10);
                //当前页码
                $info->current_page = intval($agentsInfo['now_page']);
            }
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    public function setAgents()
    {
        $agentsObj = new Agents();
        $agentsInfo = request()->all();
        if (empty($agentsInfo) ||
            !array_key_exists('type',$agentsInfo) ||
            !array_key_exists('company',$agentsInfo)||
            !array_key_exists('brand',$agentsInfo)||
            !array_key_exists('room_city',$agentsInfo)||
            !array_key_exists('acreage',$agentsInfo)||
            !array_key_exists('policy',$agentsInfo)||
            !array_key_exists('photo',$agentsInfo)||
            !array_key_exists('api_token',$agentsInfo)

        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($agentsInfo['api_token']);
        if($redis['code'] == 200){
            $agentsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $agentsInfo = handleData($agentsInfo);
        //数据验证通过
        $bool = $agentsObj->create($agentsInfo);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        set_release_num($agentsInfo['users_id']);
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }
    /**
     * 我发布的招商代理
     */
    public function getMyAgents()
    {
        $agentsObj = new Agents();
        $agentsInfo = request()->all();
        $info = $this->obj();
        if (isset($agentsInfo['id'])) {//取详情--id
            $info = $agentsObj->sel_agents_info($agentsInfo['id']);
            $info = res_time($info);
            $info = isset($info)?$info:$this->obj();
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            $redis = GetAppToken($agentsInfo['api_token']);
            if($redis['code'] == 200){
                $agentsInfo['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            if (!array_key_exists('f', $agentsInfo)) {
                return msg_err(PassportController::NO_STATUS, PassportController::PARAM);
            }
            if(!isset($agentsInfo['now_page'])){
                $agentsInfo['now_page'] = 1;
            }
            //分页数据
            $info->data = $agentsObj->sel_agents_list($agentsInfo);
            //总页码
            $info->last_page = ceil($agentsObj->sel_agents_page($agentsInfo)/10);
            //当前页码
            $info->current_page = intval($agentsInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 删除我发布的
     */
    public function delMyAgents()
    {
        $agentsObj = new Agents();
        $agentsInfo = request()->all();
        $redis = GetAppToken($agentsInfo['api_token']);
        if($redis['code'] == 200){
            $agentsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if (!array_key_exists('id',$agentsInfo)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $bool = $agentsObj->del_my_data($agentsInfo['id']);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,'删除失败',new \stdClass());
        }
        return msg_ok(PassportController::YSE_STATUS,'删除成功',new \stdClass());
    }

}