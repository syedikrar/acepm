<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Contracts\TimeTrackerRepository;

/**
 * Class TimeTrackersController.
 *
 * @package namespace App\Http\Controllers;
 */
class TimeTrackersController extends Controller
{
    /**
     * @var TimeTrackerRepository
     */
    protected $repository;

    /**
     * TimeTrackersController constructor.
     *
     * @param TimeTrackerRepository $repository
     */
    public function __construct(TimeTrackerRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        try {
            $data = $request->all();
            $data['date_started'] = Carbon::create($data['date_started']);
            $data['user_id'] = auth()->user()->id;
            $timeTracker = $this->repository->create($data);

            $response = [
                'status'   => true,
                'message' => 'Time logged successfully',
                'data'    => $timeTracker->toArray(),
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status'   => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $data = $request->all();
            $timeTracker = $this->repository->update($data, $data['id']);

            $response = [
                'status'   => true,
                'message' => 'TimeTracker updated.',
                'data'    => $timeTracker->toArray(),
            ];

            return response()->json($response);

        } catch (\Exception $e) {
                return response()->json([
                    'status'   => false,
                    'message' => $e->getMessage()
                ]);
        }
    }

}
