<?php
/**
 * 大咖推荐
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 15:49
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recommend;

class RecommendsController extends Controller
{
    const SHOW_TREM_NO = '客官,已经没有了';
    const SHOW_TREM_YES = '大咖数据';
    const GETSHOW = '无数据';
    const GETSHOW_STATUS_NO = 0;
    /**
     * 只显示三条,可以左右划，就是翻页，小于3条不显示
     * get
     * num
     */
    public function show()
    {
        $recommendObj = new Recommend();
        $recommend_data = $recommendObj->show();
        return msg_ok(PassportController::YSE_STATUS,self::SHOW_TREM_YES,$recommend_data);
    }

    /**
     * 查看详情
     * id
     * get
     */
    public function getShow()
    {
        $data = request()->all();
        if (empty($data) ||
            !array_key_exists('id',$data)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $recommendObj = new Recommend();
        $recommend_data = $recommendObj->getShow($data['id']);
        return msg_ok(PassportController::YSE_STATUS,self::SHOW_TREM_YES,$recommend_data);
    }
}