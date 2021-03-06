<?php

namespace App\Http\Controllers\Api;

use App\Models\ProjectCooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Log;
class ProjectCooperationControlerodel extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'room_city' => 'required|numeric',

            ],[],[
                'room_city' => '城市id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $where['status'] = 1;
        $where['room_city'] = $request->get('room_city');
        $pagenum = $request->get('pagenum')?:10;
        $list = ProjectCooperation::select('id','title','room_city as room_name','content as contents','address','ctime as ctimes','images')
            ->orderBy('utime','DESC')
            ->where($where)->paginate($pagenum);
        $list2 = obj_array($list);
        return msg_ok(200,'success',$list2);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'content' => 'required|max:10000',
                'room_city'=>'required|numeric',
                'address'=>'required',

            ],[],[
                'title' => '项目名称',
                'content' => '项目描述',
                'room_city' => '所在地区',
                'address' => '详细地址',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = handleData($request->all());
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $where ['title'] = $data['title'];
        $where ['content'] = $data['content'];
        $where ['u_id'] = $data['u_id'];
        $where ['room_city'] = $data['room_city'];
        $where ['address'] = $data['address'];
        $single_num = ProjectCooperation::where($where)->count();
        if($single_num){
            return msg_err(401,'已提交过相同的项目合作，操作失败');
        }
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }

        $data['images'] = $request->get('js_images');
        unset($data['api_token']);
        unset($data['js_images']);
        $r = ProjectCooperation::create($data);
        if($r->id){
            if(!set_release_num($data['u_id'])){
                Log::info('用户: '.$data['u_id'].'添加项目合作资讯，发布数量自增加失败');
            }
            return msg_ok(200,'提交成功');
        }else{
            return msg_err(401,'提交失败');
        }
    }
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',

            ],[],[
                'g_id' => '详情id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }

        $where['id'] = $request->get('g_id');
        $where['status'] = 1;
        $info = ProjectCooperation::where($where)->select('id','title','content','u_id','address','ctime as ctimes','images','room_city as room_name')->first();
        $info = $info->toArray();
        if($info){
            $info['newtime'] = time();
            return msg_ok(200,'success',$info);
        }else{
            return msg_err(401,'数据不存在或已删除');
        }
    }

}
