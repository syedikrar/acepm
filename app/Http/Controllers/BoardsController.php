<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardTemplate;
use App\Models\BoardUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Contracts\BoardRepository;
use App\Validators\BoardValidator;
use Illuminate\Support\Facades\Storage;

/**
 * Class BoardsController.
 *
 * @package namespace App\Http\Controllers;
 */
class BoardsController extends Controller
{
    /**
     * @var BoardRepository
     */
    protected $repository;

    /**
     * BoardTemplatesController constructor.
     *
     * @param BoardRepository $repository
     */
    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * @param $field
     *
     * @return array|mixed
     */
    public function byField($field)
    {
        try {
            return auth()->user()->boards()
                ->with('fieldType')
                ->with('cards')
                ->with('cards.subTasks')
                ->where('shop_id', \request()->cookie('ace-shop-id', 0))
                ->whereHas('fieldType', function ($query) use ($field) {
                    $query->where('field_types.slug', $field);
                })->get();

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @return array
     */
    public function all(){
        try {
            return auth()->user()->boards()
                ->with('cards')
                ->where('shop_id', \request()->cookie('ace-shop-id', 0))
                ->get();

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function save(Request $request)
    {
        $resp = [
            'status'    => true,
            'message'   => 'Board Updated.',
            'board'     => null
        ];

        try {
            $data = $request->all();
            $id = [ 'id' => $data['id']];
            unset($data['id']); unset($data['background_image']);
            $resp['board'] = $this->repository->updateOrCreate($id, $data);

            if($id['id'] == null) $resp['board']->creator()->attach(auth()->user(), ['role' => 'creator']);
            else if(isset($data['selectedMembers'])) {
                $resp['board']->members()->wherePivot('role', '!=', 'creator')->detach();
                $index = array_search($data['creator'][0]['id'], $data['selectedMembers']);
                unset($data['selectedMembers'][$index]);
                $resp['board']->members()->attach($data['selectedMembers']);
            }
        } catch (\Exception $e) {
            $resp['status'] = false;
            $resp['message'] = $e->getMessage();
        }

        return response()->json($resp);
    }

    public function fromTemplate(Request $request)
    {
        $board = $this->moveBackgroundImage($this->repository->fromTemplate($request));
        $board->creator()->attach(auth()->user(), ['role' => 'creator']);

        return $board->load(['cards', 'creator', 'fieldType', 'members']);
    }

    public function delete($id)
    {
        try {
            $this->repository->delete($id);
            BoardUser::where(['board_id' => $id])->update(['deleted_at' => Carbon::now()]);

            return ['status' => 'success', 'message' => 'Board deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function restore($id)
    {
        try {
            $board = $this->repository->withTrashed()->find($id);

            if($board && $board->trashed()) {
                $board->restore();
                $board->load('fieldType', 'cards', 'cards.subTasks');
                BoardUser::where(['board_id' => $id])->update(['deleted_at' => null]);

                return ['status' => 'success', 'board' => $board, 'message' => 'Board restored successfully'];
            }else{
                return ['status' => 'error', 'message' => 'Board not found'];
            }

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function uploadBackgroundImage(Request $request){
        $resp = [
            'status'            => 'success',
            'message'           => 'Image uploaded successfully',
            'background_image'        => ''
        ];

        try {
            if ($request->hasFile('background_image')) {
                $board_id = $request->get('board_id');
                $file = $request->file('background_image');
                $name = $file->getClientOriginalName();
                $path = preg_replace("/\s+/", "", 'images/boards/'. $board_id .'/'.time().'_'.$name);
                Storage::disk('public_uploads')->put($path, file_get_contents($file));
                $board = $this->repository->find($board_id);
                $this->deleteBackgroundImage(null, $board);
                $board->background_image = $path;
                $board->save();
                $resp['background_image'] = $path;
            }

        } catch (\Exception $e) {
            $resp['status'] = 'error';
            $resp['message'] = $e->getMessage();
        }
        return response()->json($resp);
    }

    public function deleteBackgroundImage($id, Board $board){
        if($id != null) $board = $this->repository->find($id);
        $resp = [
            'status'    => 'success',
            'message'   => 'Image deleted successfully',
        ];

        try {
            $file_url =  $board->background_image;
            $board->background_image = '';
            Storage::disk('public_uploads')->delete($file_url);
            $board->save();

        }catch(Exception $e){
            $resp = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return response()->json($resp);
    }

    public function get($id)
    {
        return $this->repository->with([
            'cards', 'cards.subTasks', 'cards.comments', 'cards.comments.author', 'cards.shopifyIntegrations'
        ])->find($id);
    }

    private function moveBackgroundImage(Board $board){
        $fromPath = $board->background_image;
        if(empty($fromPath)) return $board;

        $toPath  = 'images/boards/'. $board->id .'/'.time().'_'.basename($fromPath);
        Storage::disk('public_uploads')->copy($fromPath, $toPath);
        $board->background_image = $toPath;
        $board->save();
        return $board;
    }
}
