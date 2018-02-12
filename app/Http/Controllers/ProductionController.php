<?php

namespace App\Http\Controllers;

use App\Production;
use App\ProductionPrice;
use App\Tag;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ProductionController extends Controller
{
    public function show(Request $request){
        return view("admin.production.index");
    }

    /**
     * 获取产品列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $start = $request->input('iDisplayStart');
        $limit = $request->input('iDisplayLength');


        $categorys = Production::skip($start)->take($limit)->orderby("sort", "desc")->get();
        $data = array();
        $data['recordsFiltered'] = $data['recordsTotal'] = Production::count();
        $data['data'] = $categorys;

        return Response::json($data);
    }

    /**
     * 编辑产品
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request){
        $id = $request->input("id", 0);

        //所有标签
        $tags = Tag::all();

        $data = [];
        //已经设置的标签
        $selected_tag = [];

        if($id){
            $production = Production::withTrashed()->find($id);
            $data['production'] = $production;
            $taged = $production->tags;

            foreach($taged as $tag){
                $selected_tag[$tag->id] = $tag->name;
            }
        }

        $data["tags"] = $tags;
        $data["selected_tag"] = $selected_tag;

        return view("admin.production.edit", $data);
    }


    /**
     * 保存产品
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request){

        $id = $request->input("id", 0);
        $name = $request->input("name");
        $summary = $request->input("summary");
        $main_pic = $request->input("main_pic");
        $origin = $request->input("origin");
        $destination = $request->input("destination");
        $day_info = $request->input("day_info");
        $is_abroad = $request->input("is_abroad", 0);
        $description = $request->input("description");
        $price_info = $request->input("price_info");
        $notice = $request->input("notice");
        $buy_notice = $request->input("buy_notice");
        $schedule = $request->input("schedule");
        $is_online = $request->input("is_online");
        $sort = $request->input("sort");

        $tags = $request->input("tags", []);

        $data = [
            'name' => $name,
            'summary' => $summary,
            'main_pic' => $main_pic,
            'origin' => $origin,
            'destination' => $destination,
            'day_info' => $day_info,
            'price_info' => $price_info,
            'is_abroad' => $is_abroad,
            'description'=>$description,
            'price_info' => $price_info,
            'notice' => $notice,
            'buy_notice' => $buy_notice,
            'schedule'  => $schedule,
            'is_online' => $is_online,
            'sort' => $sort,
        ];

        $production = Production::firstOrNew(['id'=>$id]);
        try{
            DB::transaction(function() use($production, $tags, $data){
                if(!$production->fill($data)->save()){
                    throw new \Exception("保存产品失败");
                }

                if(!$production->tags()->sync($tags)){
                    throw new \Exception("保存标签失败");
                }
            });

            return $this->ajaxSuccess(['id'=> $production->id]);

        }catch(\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

    }

    public function del(Request $request){
        $id = $request->input("id");

        if(Production::where("id", $id)->delete()){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError();
        }

    }


    /**
     * 获取价格
     * @param Request $request
     */
    public function getPrice(Request $request){
        $production_id = $request->input("id");
        $start = $request->input("start");
        $end = $request->input("end");

        $prices = ProductionPrice::where("production_id", $production_id)
            ->where("date", ">=", $start)
            ->where("date", "<=", $end)
            ->get();

        $prices = $prices->toArray();

        return $this->ajaxSuccess($prices);
    }

    /**
     * 获取价格详情
     * @param Request $request
     * @return mixed
     */
    public function getPriceInfo(Request $request){
        $production_id = $request->input("id");
        $date = $request->input("date");

        $price = ProductionPrice::where("production_id", $production_id)
            ->where("date", $date)
            ->first();
        if($price){
            return $this->ajaxSuccess($price);
        }else{
            return $this->ajaxError();
        }
    }

    /**
     * 保存价格
     * @param Request $request
     * @return mixed
     */
    public function savePriceInfo(Request $request){
        $production_id = $request->input("production_id");
        $date = $request->input("date");
        $price = $request->input("price");

        $productionPrice = ProductionPrice::where("production_id", $production_id)
            ->where("date", $date)
            ->first();
        if(!$productionPrice){
            $productionPrice = new ProductionPrice();
        }

        $productionPrice->production_id = $production_id;
        $productionPrice->date = $date;
        $productionPrice->price = $price;

        if($productionPrice->save()){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError();
        }
    }

    /**
     * 删除价格
     * @param Request $request
     * @return mixed
     */
    public function delPriceInfo(Request $request){
        $production_id = $request->input("production_id");
        $date = $request->input("date");
        $price = $request->input("price");

        $ret = ProductionPrice::where("production_id", $production_id)
            ->where("date", $date)
            ->delete();

        if($ret){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError();
        }
    }



}
