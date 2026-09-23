<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PreConferenceResource;
use App\Http\Resources\BaseResource;
use App\Services\Interfaces\PreConferenceDetailsInterface;
use App\Services\Interfaces\PreConferenceInterface;
use App\Services\Interfaces\EventInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreConferenceController extends Controller
{
    private PreConferenceInterface $agenda;
    private PreConferenceDetailsInterface $details;
    private EventInterface $event;

    /**
     * @param PreConferenceInterface $agenda
     * @param PreConferenceDetailsInterface $details
     * @param EventInterface $event
     */
    public function __construct(PreConferenceInterface $agenda, PreConferenceDetailsInterface $details, EventInterface $event)
    {
        $this->agenda = $agenda;
        $this->details = $details;
        $this->event = $event;
    }


    public function index(Request $request)
    {
        try {
            $event = $this->event->getOneByColumns([
                'event_status' => true
            ]);
            if ($event) {
                $res = $this->agenda->getListByColumns([
                    'event_id' => $event->id
                ]);
                return PreConferenceResource::collection($res);
            }
            return PreConferenceResource::collection([]);
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function store(Request $request): JsonResponse|PreConferenceResource
    {
        try {
            DB::beginTransaction();
            $agenda = $this->agenda->store($request);
            if ($agenda) {
                DB::commit();
                return PreConferenceResource::create($agenda);
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function storeDetails(Request $request, $id): JsonResponse|PreConferenceResource
    {
        try {
            DB::beginTransaction();
            $agenda = $this->agenda->getOneByColumns([
                'id' => $id
            ]);
            if ($agenda) {
                $details = $this->details->add($request, $agenda->id);
                if ($details) {
                    DB::commit();
                    return PreConferenceResource::create($agenda);
                }
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function update(Request $request, $id): JsonResponse|PreConferenceResource
    {
        try {
            DB::beginTransaction();
            $agenda = $this->agenda->update($request, $id);
            if ($agenda) {
                DB::commit();
                return PreConferenceResource::create($agenda);
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function updateDetails(Request $request, $id): JsonResponse|PreConferenceResource
    {
        try {
            DB::beginTransaction();
            $details = $this->details->update($request, $id);
            if ($details) {
                DB::commit();
                return PreConferenceResource::create($details->agenda);
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function destroy($id): JsonResponse|BaseResource
    {
        try {
            $details = $this->details->delete($id);
            if ($details) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function destroyDetails($id): JsonResponse|BaseResource
    {
        try {
            $details = $this->details->delete($id);
            if ($details) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }


}
