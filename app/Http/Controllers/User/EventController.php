<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\SponsorResource;
use App\Services\Interfaces\EventInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class EventController extends Controller
{

    private EventInterface $event;

    /**
     * @param EventInterface $event
     */
    public function __construct(EventInterface $event)
    {
        $this->event = $event;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $events = $this->event->getListByColumns([]);
            return EventResource::paginable($events);
        } catch (Exception $exception) {
            return EventResource::collection([]);
        }
    }

    // public function gallery(Request $request)
    // {
    //     try {
    //         $event = $this->event->gallery($request);
    //         if ($event) {
    //             return GalleryResource::create($event);
    //         }
    //         return BaseResource::return();
    //     } catch (Exception $exception) {
    //         return BaseResource::return();
    //     }
    // }

   public function gallery(Request $request)
{
    try {
        $event = $this->event->gallery($request);

        if (!$event) {
            return BaseResource::return();
        }

        return response()->json([
    'status' => true,
    'status_code' => 200,
    'message' => 'Success!',
    'data' => (new GalleryResource($event))->toArray($request),
], 200);

    } catch (\Exception $exception) {
        return response()->json([
            'status' => false,
            'status_code' => 500,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ], 500);
    }
}





    public function sponsors(Request $request)
    {
        try {
            $event = $this->event->getOneByColumns([
                'event_status' => true
            ]);
            if ($event) {
                $sponsors = $event->sponsors()->get();
                return SponsorResource::collection($sponsors);
            }
            return SponsorResource::collection([]);
        } catch (Exception $exception) {
            return BaseResource::return();

        }
    }

    public function live(Request $request)
    {
        try {
            $event = $this->event->getOneByColumns([
                'event_status' => true
            ]);
            if ($event) {
                return EventResource::create($event);
            }
            return EventResource::return();
        } catch (Exception $exception) {
            return EventResource::return();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return EventResource|JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $event = $this->event->store($request);
            if ($event) {
                return EventResource::create($event);
            }
            return EventResource::return();
        } catch (Exception $exception) {
            return EventResource::return();
        }
    }

    public function setLive($id)
    {
        try {

            $this->event->getBuilder([])->update([
                'event_status' => false
            ]);
            $event = $this->event->getOneByColumns([
                'id' => $id
            ]);
            if ($event) {
                $event->update([
                    'event_status' => false
                ]);
                return BaseResource::ok();
            }
            return EventResource::return();
        } catch (Exception $exception) {
            return EventResource::return();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return EventResource|JsonResponse
     */
    public function show($id)
    {
        try {
            $event = $this->event->getOneByColumns([
                'id' => $id
            ]);
            if ($event) {
                return EventResource::create($event);
            }
            return EventResource::return();
        } catch (Exception $exception) {
            return EventResource::return();
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return EventResource|JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $event = $this->event->update($request, $id);
            if ($event) {
                return EventResource::create($event);
            }
            return EventResource::return();
        } catch (Exception $exception) {
            return EventResource::return();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
