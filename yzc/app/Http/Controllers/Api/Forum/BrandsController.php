<?php
/**
 * 品牌论坛
 * User: Administrator
 * Date: 2018/11/23
 * Time: 14:59
 */
namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Api\PassportController;
use App\Http\Controllers\Controller;
use App\Models\Brand;

class BrandsController extends Controller
{
    /**
     * 品牌论坛列表选择接口
     * @return int
     */
    public function brandForum()
    {
        $brandObj = new Brand();
        $info = $brandObj->sel_data();
        return msg_ok(PassportController::YSE_STATUS,'品牌论坛列表',$info);
    }

}