<?php

namespace App\Http\Controllers;

use App\Contracts\CardAttachmentRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Class CardAttachmentsController.
 *
 * @package namespace App\Http\Controllers;
 */
class CardAttachmentsController extends Controller
{
    /**
     * @var CardAttachmentRepository
     */
    protected $repository;

    /**
     * CardAttachmentsController constructor.
     *
     * @param CardAttachmentRepository $repository
     */
    public function __construct(CardAttachmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save(Request $request){

        $resp = [
            'status'    => true,
            'message'   => 'Attachments uploaded successfully',
            'data'      => []
        ];

        $attachments = [];
        try {
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                foreach($files as $file){
                    $name = $file->getClientOriginalName();
                    $path = 'card_attachments/'.time().'_'.$name;
                    Storage::cloud()->put($path, file_get_contents($file));

                    $data = ['card_id' => $request->get('card_id'), 'user_id' => auth()->user()->id, 'path' => $path, 'name' => $name];
                    $attachments[] = $this->repository->create($data);
                }
                $resp['data'] = $attachments;
            }

        } catch (\Exception $e) {
            $resp['status'] = false;
            $resp['message'] = $e->getMessage();
        }

        return response()->json($resp);

    }

    public function download(Request $request) {
        $attachment = $this->repository
            ->findByField('name', $request->route('name'))->first();

        try{
            $file_url = $attachment->path;
            $file_name  = $attachment->name;

            $mime = Storage::cloud()->getDriver()->getMimetype($file_url);
            $size = Storage::cloud()->getDriver()->getSize($file_url);

            $headers =  [
                'Content-Type' => $mime,
                'Content-Length' => $size,
                'Content-Description' => 'File Transfer',
                'Content-Disposition' => "attachment; filename={$file_name}",
                'Content-Transfer-Encoding' => 'binary',
            ];

            ob_end_clean(); //---------This is important

            return \Response::make(Storage::cloud()->get($file_url), 200, $headers);
        }
        catch(Exception $e){
            return response()->json(
                [
                    'error-code' => $e->getCode(),
                    'message' => $e->getMessage()
                ]);
        }

    }

    public function delete(Request $request){

        $resp = [
            'status'    => true,
            'message'   => 'Attachment deleted successfully',
        ];

        $attachment = $this->repository
            ->find( $request->route('id'));

        try {
            $file_url = $attachment->path;
            $attachment->delete();
            Storage::cloud()->delete($file_url);

        }catch(Exception $e){
            $resp = [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($resp);

    }

}