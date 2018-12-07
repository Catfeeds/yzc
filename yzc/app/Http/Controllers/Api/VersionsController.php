<?php
/**
 * 版本控制--关于我们
 * User: hwj
 * Date: 2018/11/24
 * Time: 10:28
 */
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Versions;

class VersionsController extends Controller
{
    /**
     * 关于我们
     * @return int  1:安卓 0：ios
     */
    public function aboutUs()
    {
        $versionData = request()->all();
        if (empty($versionData) ||
            !array_key_exists('num',$versionData)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $versionObj = new Versions();
        return msg_ok(PassportController::YSE_STATUS,'关于我们',arr($versionObj->sel_data($versionData['num'])));
    }
    /**
     * 版本控制
     */
    public function version()
    {
        $versionData = request()->all();
        if (empty($versionData) ||
            !array_key_exists('num',$versionData)||
            !array_key_exists('version',$versionData)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $versionObj = new Versions();
        $res = arr($versionObj->sel_data($versionData['num']));
        $bool = 1;
        if($versionData['num'] == 0)//ios版本修改
        {
            if($res->now_version_i != $versionData['version']){//修改版本ios
                $bool = $versionObj->up_data($versionData);
            }
        }elseif ($versionData['num'] == 1){//安卓版本修改
            if($res->now_version_a != $versionData['version']){//修改版本安卓
                $bool = $versionObj->up_data($versionData);
            }
        }
        if(!$bool)
        {
            return msg_err(PassportController::YSE_STATUS,'修改失败',new \stdClass());
        }
        return msg_ok(PassportController::YSE_STATUS,'修改成功',new \stdClass());

    }
}