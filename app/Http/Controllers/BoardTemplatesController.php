<?php

namespace App\Http\Controllers;

use App\Contracts\FieldTypeRepository;
use App\Models\BoardTemplate;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BoardTemplateCreateRequest;
use App\Http\Requests\BoardTemplateUpdateRequest;
use App\Contracts\BoardTemplateRepository;
use App\Validators\BoardTemplateValidator;

/**
 * Class BoardTemplatesController.
 *
 * @package namespace App\Http\Controllers;
 */
class BoardTemplatesController extends Controller
{
    /**
     * @var BoardTemplateRepository
     */
    protected $repository;

    /**
     * @var \App\Contracts\FieldTypeRepository
     */
    protected $categoryRepository;

    /**
     * BoardTemplatesController constructor.
     *
     * @param BoardTemplateRepository            $repository
     * @param \App\Contracts\FieldTypeRepository $categoryRepository
     */
    public function __construct(BoardTemplateRepository $repository, FieldTypeRepository $categoryRepository)
    {
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
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
            return auth()->user()->templates()
                ->with('fieldType')
                ->with('user')
                ->whereHas('fieldType', function ($query) use ($field) {
                    $query->where('field_types.slug', $field);
                    $query->where('approved_at', '!=', null);
                })
                ->get();

        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @return array
     */
    public function all(){
        try {
            return auth()->user()->templates()->get();
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
            'status' => true,
            'message'=> 'Template Added.'
        ];

        try {
            $data = $request->all();
            $id = [ 'id' => $data['id']];
            unset($data['id']);
            if(!isset($data['background_image'])) unset($data['background_image']);

            $this->moveBackgroundImage($this->repository->updateOrCreate($id, $data));

        } catch (\Exception $e) {
            $resp['status'] = false;
            $resp['message'] = $e->getMessage();
        }

        return response()->json($resp);
    }

    public function delete($id, Request $request)
    {
        try {
            $this->repository->delete($id);
            return ['status' => 'success', 'message' => 'Deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    private function moveBackgroundImage(BoardTemplate $boardTemplate){
        $fromPath = $boardTemplate->background_image;
        if(empty($fromPath)) return;

        $toPath  = 'images/templates/'. $boardTemplate->id .'/'.time().'_'.basename($fromPath);
        Storage::disk('public_uploads')->copy($fromPath, $toPath);
        $boardTemplate->background_image = $toPath;
        $boardTemplate->save();
    }

}