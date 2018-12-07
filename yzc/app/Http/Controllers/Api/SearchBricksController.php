<?php
/**
 * 广播找砖
 * User: HWJ
 * Date: 2018/11/14
 * Time: 13:29
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SearchBricks;
class SearchBricksController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';

    public static function obj()
    {
        return new \stdClass();
    }

    public function setSearchBricks()
    {
        $searchBricksObj = new SearchBricks();
        $searchBricksInfo = request()->all();
        if (empty($searchBricksInfo) ||
            !array_key_exists('specifications',$searchBricksInfo) ||
            !array_key_exists('category',$searchBricksInfo)||
            !array_key_exists('style',$searchBricksInfo)||
            !array_key_exists('brands_id',$searchBricksInfo)||
            !array_key_exists('sketch',$searchBricksInfo)||
            !array_key_exists('photo',$searchBricksInfo)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($searchBricksInfo['api_token']);
        if($redis['code'] == 200){
            $searchBricksInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $searchBricksInfo = handleData($searchBricksInfo);
        //数据验证通过
        $bool = $searchBricksObj->create($searchBricksInfo);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        set_release_num($searchBricksInfo['users_id']);
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }
    public function getSearchBricks()
    {
        $searchBricksObj = new SearchBricks();
        $searchBricksInfo = request()->all();
        $info = $this->obj();
        if (isset($searchBricksInfo['id'])) {//取详情--id
            $info = $searchBricksObj->sel_search_bricks_info($searchBricksInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，4。数据条数
                if(!isset($searchBricksInfo['now_page'])){
                    $searchBricksInfo['now_page'] = 1;
                }
                $info->data = $searchBricksObj->sel_search_bricks_list($searchBricksInfo);
                $info->last_page = ceil($searchBricksObj->sel_search_bricks_page()/10);
                $info->current_page = intval($searchBricksInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 我发布的
     */
    public function getMySearchBricks()
    {
        $searchBricksObj = new SearchBricks();
        $searchBricksInfo = request()->all();
        $info = $this->obj();
        if (isset($searchBricksInfo['id'])) {//取详情--id
            $info = $searchBricksObj->sel_search_bricks_info($searchBricksInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，4。数据条数
            $redis = GetAppToken($searchBricksInfo['api_token']);
            if($redis['code'] == 200){
                $searchBricksInfo['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            if(!isset($searchBricksInfo['now_page'])){
                $searchBricksInfo['now_page'] = 1;
            }
            $info->data = $searchBricksObj->sel_search_bricks_list($searchBricksInfo);
            $info->last_page = ceil($searchBricksObj->sel_search_bricks_page()/10);
            $info->current_page = intval($searchBricksInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 删除我发布
     */
    public function delMySearchBricks()
    {
        $searchBricksObj = new SearchBricks();
        $searchBricksInfo = request()->all();
        $redis = GetAppToken($searchBricksInfo['api_token']);
        if($redis['code'] == 200){
            $searchBricksInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if (!array_key_exists('id',$searchBricksInfo)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $bool = $searchBricksObj->del_my_data($searchBricksInfo['id']);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,'删除失败',new \stdClass());
        }
        return msg_ok(PassportController::YSE_STATUS,'删除成功',new \stdClass());
    }

}