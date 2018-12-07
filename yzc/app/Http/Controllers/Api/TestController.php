<?php
/**
 * Created by PhpStorm.
 * User: liule
 * Date: 2018/10/7
 * Time: 16:08
 */

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\Request;

class TestController
{


    /**

     * 测试

     */

    public function test()

    {
        $a = tel_preg('15210217123');
        if(!$a){
            echo 0;
        }else{
            echo 1;
        }

    }


}