<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Detection\MobileDetect;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EventResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->getDateAttribute(),
            'days' => $this->getDays(),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'hotel' => $this->hotel,
            'address' => $this->address,
            'map' => $this->map,
            'event_status' => $this->event_status,
            'logo' => asset('storage/images/event-' . $this->id . '-' . date('Y', strtotime($this->start_date)) . '/' . $this->logo),
            'banner' => $this->getBanner($this),
            'messages' => EventMessageResource::dataCollection($this->messages()->get())
        ];
    }

    // public function getBanner($obj)
    // {
    //     $detect = new MobileDetect();
    //     if ($detect->isMobile()) {
    //         return asset('storage/images/event-' . $obj->id . '-' . date('Y', strtotime($obj->end_date)) . '/mobile/' . $obj->banner);
    //     } else {
    //         return asset('storage/images/event-' . $obj->id . '-' . date('Y', strtotime($obj->end_date)) . '/' . $obj->banner);
    //     }

    // }

    // public function getBanner($obj)
    // {
    //     $detect = new MobileDetect();
    //     if ($detect->isMobile()) {
    //         return asset('https://res.cloudinary.com/medcon-dam/video/upload/v1769085005/Ehs-2026/4th_EHS_animated_banner_mobile_ixpycs.mp4');
    //     } else {
    //         return asset('https://res.cloudinary.com/medcon-dam/video/upload/v1769071246/Ehs-2026/4th_EHS_animated_banner_tkavjw.mp4');
    //     }

    // }

    public function getBanner()
{
    $detect = new MobileDetect();

    return [
        'desktop' => 'https://res.cloudinary.com/medcon-dam/video/upload/v1771581185/Ehs-2026/4th_EHS_animated_banner_web_s7lidm.mp4',
        'mobile'  => 'https://res.cloudinary.com/medcon-dam/video/upload/v1771829542/Ehs-2026/4th_EHS_animated_banner_mobile_p7voif.mp4',
    ];
}


}
