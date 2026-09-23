<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\FacultyResource;
use App\Services\Facades\EventFacade;
use App\Services\Interfaces\EventInterface;
use App\Services\Interfaces\FacultyInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    private FacultyInterface $faculty;
    private EventFacade $event;

    /**
     * @param FacultyInterface $faculty
     * @param EventInterface $event
     */
    public function __construct(FacultyInterface $faculty, EventInterface $event)
    {
        $this->faculty = $faculty;
        $this->event = $event;
    }

    public function index(Request $request)
    {
        try {
            $event = $this->event->getOneByColumns([
                'event_status' => true
            ]);
            if ($event) {
                $res = $event->faculties()->where([
                    'vip' => false
                ])->orderBy('order','asc')->get();
                return FacultyResource::collection($res);
            }
            return FacultyResource::collection([]);
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function scientific(Request $request)
    {
        try {
            $event = $this->event->getOneByColumns([
                'event_status' => true
            ]);
            if ($event) {
                $res = $event->faculties()->where([
                    'vip' => true
                ])->orderBy('order','asc')->get();
                return FacultyResource::collection($res);
            }
            return FacultyResource::collection([]);
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function store(Request $request): FacultyResource|JsonResponse
    {
        try {
            $faculty = $this->faculty->store($request);
            if ($faculty) {
                return FacultyResource::create($faculty);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function update(Request $request, $id): FacultyResource|JsonResponse
    {
        try {
            $faculty = $this->faculty->update($request, $id);
            if ($faculty) {
                return FacultyResource::create($faculty);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function destroy($id): JsonResponse|BaseResource
    {
        try {
            $faculty = $this->faculty->delete($id);
            if ($faculty) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }


}
