<?php

namespace App\Http\Controllers;

use App\Helper\CacheHelper;
use App\Models\Attachment;
use App\Models\CacheConfig;
use App\Models\Policy;
use App\Models\Statistics;
use Dotenv\Store\FileStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public $cache;
    public $capacity;
    public $replacment_policy;

    public function __construct()
    {
        $this->capacity = CacheConfig::query()->latest('id')->pluck('capacity')->first();

        $replacment_policy_id = CacheConfig::query()->latest('id')->pluck('policy_id')->first();
        $this->replacment_policy = Policy::find($replacment_policy_id);

        $this->cache = new CacheHelper( (int) $this->capacity);

        $this->cache->replacment_policy = $this->replacment_policy->policy_name;
        session()->put('cache', $this->cache);

    }


    public function index()
    {
        // dd(session()->get('cache'));
        return view('backend.index');
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'key'=>'required|string',
            'value'=>'required|mimes:jpeg,png|max:2048'
        ]);

        $attachment = Attachment::wherekey($request->key)->first();

        // attachment exists in db
        if($attachment){
            $size = 0;
            $image_name = '';
            if($image = $request->file('value')){
                if($attachment->value != null && File::exists('uploads/'. $attachment->value)){
                    unlink('uploads/'. $attachment->value);
                }
                $image_name = $attachment->key.".".$image->getClientOriginalExtension();
                $size = $image->getSize();
                $image->move(public_path('uploads/'), $image_name);
                $input['value'] = $image_name;
            }

            $attachment->update($input);

            $cachedItem = session()->get('cache');
            $cachedItem->add($request->key, $image_name, $size);
            session()->put('cahce', $cachedItem);

        }else{

            $input['key'] = $request->key;
            $size = 0;
            $image_name = '';
            if($image = $request->file('value')) {
                $image_name = $request->key . "." . $image->getClientOriginalExtension();

                $size = $image->getSize();
                $image->move(public_path('uploads/'), $image_name);
                $input['value'] = $image_name;
            }

            Attachment::create($input);

            //add to cache
            $cachedItem = session()->get('cache');
            $cachedItem->add($request->key, $image_name, $size);
            session()->put('cahce', $cachedItem);



        }
        return redirect(route('index'));
    }


    public function image(){
        $attachment = null;
        $source = '';

        return view('backend.images', compact('attachment','source'));

    }

    public function getImage(Request $request){
        $request->validate([
            'key' => 'required|exists:attachments,key'
        ]);

        $source = 'DB';

        $cachedItem = session()->get('cache');
        $attachment = $cachedItem->get($request->key);
        $cachedItem->requestCount++;

        if ($attachment) {
            $cachedItem->hitCount++;
            $source = 'Cache';

        } else {
            $cachedItem->missCount++;
            $attachment = Attachment::whereKey($request->key)->pluck('value')->first();
            // dd($cachedItem);
            $size = File::size(public_path('uploads/' . $attachment));
            $cachedItem->add($request->key, $attachment, $size);
            session()->put('cache', $cachedItem);

        }
        return view('backend.images', compact('attachment', 'source'));

    }


    public function keys(){
        $attachments = Attachment::all();
        return view('backend.keys',compact('attachments'));

    }


    public function cacheConfig(){
        $policies = Policy::query()->get(['id', 'policy_name']);
        return view('backend.cache-setting', compact('policies'));

    }

    public function storeCacheConfig(Request $request){
        $request->validate([
            'policy_id'=>'required',
            'capacity'=>'required'
        ]);

        $newConfigration = CacheConfig::create([
            'policy_id' => $request->policy_id,
            'capacity' => $request->capacity * 1000000
        ]);

        $cachedItem = session()->get('cache');

        $cachedItem->size = $newConfigration->capacity;
        $this->replacment_policy = Policy::find($newConfigration->policy_id);
        $cachedItem->replacment_policy = $this->replacment_policy->policy_name;

        session()->put('cache', $cachedItem);

        return redirect()->route('cache-config');

    }


    public function cacheStatus(){
        $cachedItem = Statistics::latest('id')->first();
        return view('backend.statistics', [
            'num_items' => $cachedItem->num_items,
            'hit_rate'  => $cachedItem->hit_rate,
            'miss_rate'  => $cachedItem->miss_rate,
            'current_capacity' => $cachedItem->current_capacity,
            'replacment_policy' => $this->replacment_policy->policy_name,
        ]);
    }

    public function storeCacheStatus()
    {
        $cachedItem = session()->get('cache');

        $statiscts = Statistics::create([
            'num_items' => count($cachedItem->items),
            'current_capacity' => $cachedItem->items_size,
            'requests_number' => $cachedItem->requestCount,
            'miss_rate'  => $cachedItem->missRate(),
            'hit_rate'  => $cachedItem->hitRate(),
        ]);


        return $statiscts;
    }


    public function clearCache()
    {
        $cachedItem = session()->get('cache');
        $cachedItem->clearCache();
        session()->put('cache', $cachedItem);


        return ['message' => 'Cache is cleared'];

    }
}

