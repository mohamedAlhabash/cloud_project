<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\CacheConfig;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function index(){
        return view('backend.index');
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'key'=>'required|string',
            'value'=>'required|mimes:jpeg,png|max:2048'
        ]);

        $attachment = Attachment::wherekey($request->key)->first();
        if($attachment){
            if($image = $request->file('value')){
                if($attachment->value != null && File::exists('uploads/'. $attachment->value)){
                    unlink('uploads/'. $attachment->value);
                }
                $image_name = $attachment->key.".".$image->getClientOriginalExtension();
                $image->move(public_path('uploads/'), $image_name);
                $input['value'] = $image_name;
            }
            $attachment->update($input);

        }else{
            $input['key'] = $request->key;
            if($image = $request->file('value')){
                $image_name = $request->key.".".$image->getClientOriginalExtension();
                $image->move(public_path('uploads/'), $image_name);
                $input['value'] = $image_name;
            }
            Attachment::create($input);
        }
        return redirect(route('index'));
    }
    public function image(){
        $attachment = null;
        return view('backend.images', compact('attachment'));
    }

    public function getImage(Request $request){
        $request->validate([
            'key' => 'required|exists:attachments,key'
        ]);
        $attachment = Attachment::whereKey($request->key)->first();
        return view('backend.images', compact('attachment'));
    }
    public function keys(){
        $attachments = Attachment::all();
        return view('backend.keys',compact('attachments'));
    }
    public function cacheConfig(){
        return view('backend.cache-setting');
    }
    public function storeCacheConfig(Request $request){
        $request->validate([
            'policy'=>'required',
            'capacity'=>'required'
        ]);
        CacheConfig::create([
            'replacment_policy'=>$request->policy,
            'capacity'=>$request->capacity
        ]);
        return view('backend.cache-setting');
    }
    public function cacheStatus(){
        return view('backend.statistics');
    }

    public function clearCache(Request $request)
    {
        # code...
    }
}
