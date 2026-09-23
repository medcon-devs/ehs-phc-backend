<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgendaResource;
use App\Http\Resources\BaseResource;
use App\Http\Resources\PreConferenceResocurce;
use App\Services\Interfaces\AgendaDetailsInterface;
use App\Services\Interfaces\AgendaInterface;
use App\Services\Interfaces\EventInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    private AgendaInterface $agenda;
    private AgendaDetailsInterface $details;
    private EventInterface $event;

    /**
     * @param AgendaInterface $agenda
     * @param AgendaDetailsInterface $details
     * @param EventInterface $event
     */
    public function __construct(AgendaInterface $agenda, AgendaDetailsInterface $details, EventInterface $event)
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
                return AgendaResource::collection($res);
            }
            return AgendaResource::collection([]);
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }
    public function preconfindex(Request $request)
    {
        try {
            $event = $this->event->getOneByColumns([
                'event_status' => true
            ]);
            // if ($event) {
            //     $res = $this->agenda->getListByColumns([
            //         'event_id' => $event->id
            //     ]);
            //     return PreConferenceResocurce::collection($res);
            // }
            if ($event) {
                $ids = $this->agenda->getBuilder([
                    'event_id' => $event->id
                ])->select('id')->get()->pluck('id');
                $res = $this->details->getBuilder(['agenda_type'=> true])->whereIn('agenda_id',$ids)->get();
                return PreConferenceResocurce::collection($res);
            }
            return PreConferenceResocurce::collection([]);
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function store(Request $request): JsonResponse|AgendaResource
    {
        try {
            DB::beginTransaction();
            $agenda = $this->agenda->store($request);
            if ($agenda) {
                DB::commit();
                return AgendaResource::create($agenda);
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function storeDetails(Request $request, $id): JsonResponse|AgendaResource
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
                    return AgendaResource::create($agenda);
                }
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function update(Request $request, $id): JsonResponse|AgendaResource
    {
        try {
            DB::beginTransaction();
            $agenda = $this->agenda->update($request, $id);
            if ($agenda) {
                DB::commit();
                return AgendaResource::create($agenda);
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function updateDetails(Request $request, $id): JsonResponse|AgendaResource
    {
        try {
            DB::beginTransaction();
            $details = $this->details->update($request, $id);
            if ($details) {
                DB::commit();
                return AgendaResource::create($details->agenda);
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
