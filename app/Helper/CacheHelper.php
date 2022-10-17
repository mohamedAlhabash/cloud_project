<?php

declare(strict_types=1);

namespace App\Helper;

use InvalidArgumentException;
use Illuminate\Support\Facades\File;


class CacheHelper implements CacheInterface
{
    public array $items = [];
    public int $size;
    public int $items_size = 0;
    public float $hitCount = 0;
    public float $missCount = 0;
    public int $requestCount = 0;

    public $replacment_policy;

    public function __construct(int $size)
    {
        if ($size < 0) {
            throw new InvalidArgumentException('Cache size must be greater than 0');

        }

        $this->size = $size;
    }

    public function add($key, $item, $item_size): void
    {

        //increase current size
        $this->items_size += $item_size;

        //no capacity
        while($this->size < $this->items_size) {
            $this->replacementPolicies();
        }

        // already on the list
        if (isset($this->items[$key])) {
            $old = $this->items[$key];
            $oldSize = File::size(public_path('uploads/' . $old));

            $this->items[$key] = $item;
            $this->moveToFront($key);

            $this->items_size -= $oldSize;

            return;
        }

        $this->items[$key] = $item;
    }

    public function get($key)
    {
        if (false === isset($this->items[$key])) {
            return null;
        }

        if(count($this->items) == 0) {
            $this->items_size = 0;
        }

        $this->moveToFront($key);

        return $this->items[$key];
    }

    private function moveToFront(string $key): void
    {
        $cachedItem = $this->items[$key];

        unset($this->items[$key]);

        $this->items[$key] = $cachedItem;
    }

    private function replacementPolicies()
    {
        switch ($this->replacment_policy) {
            case 'least recently used':
                reset($this->items);

                $oldItem = $this->items[key($this->items)];
                $oldItemSize = File::size(public_path('uploads/'. $oldItem));

                //remove the size of the element was deleted
                $this->items_size -= $oldItemSize;

                unset($this->items[key($this->items)]);

                break;

            case 'random replacement':
                $replacment_key = array_rand($this->items);
                $oldItem = $this->items[$replacment_key];

                $oldItemSize = File::size(public_path('uploads/'. $oldItem));

                //remove the size of the element was deleted
                $this->items_size -= $oldItemSize;

                unset($this->items[$replacment_key]);

                break;

            default:
                # code...
                break;
        }
    }

    public function clearCache()
    {
        $this->items_size = 0;
        $this->replacment_policy = null;
        $this->size = 0;
        $this->hitCount = 0;
        $this->missCount = 0;
        $this->requestCount = 0;
        foreach ($this->items as $key => $value) {
            unset($this->items[$key]);
        }

        session()->forget('cache');
    }

    public function hitRate()
    {
        return ($this->hitCount / $this->requestCount) * 100.0;
    }

    public function missRate()
    {
        return ($this->missCount / $this->requestCount) * 100.0;
    }
}
