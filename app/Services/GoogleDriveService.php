<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Madcoda\Youtube\Youtube;
use Tymon\JWTAuth\Contracts\Providers\Auth;

/**
 * Class GoogleDriveService
 * @package App\Services
 */
class GoogleDriveService
{
    /**
     * @var Google Client
     */
    private $google;

    /**
     * GoogleDriveService constructor.
     */
    public function __construct()
    {
        $this->google = new \Google_Client();
        $this->google->setAuthConfig(public_path(env('GOOGLE_CLIENT_CREDENTIALS_PATH')));

        $this->google->setScopes([
            'profile',
            'email',
            'openid',
            'https://www.googleapis.com/auth/drive'
        ]);
        $this->google->setIncludeGrantedScopes(true);
        $this->google->setDeveloperKey(env('GOOGLE_CLIENT_ID'));
        $this->google->setRedirectUri(env('GOOGLE_REDIRECT'));
        $this->google->setApprovalPrompt("force");
        $this->google->setAccessType('offline');
    }

    /**
     *
     * @param $user_id
     * @return string
     */
    public function getAuthUrl($user_id)
    {
        $this->google->setState($user_id);

        return $this->google->createAuthUrl();
    }

    /**
     * @param $code
     * @return array
     */
    public function authenticateToken($code)
    {
        $tokenObject = $this->google->fetchAccessTokenWithAuthCode($code);
        $this->setAccessToken($tokenObject);
        return $tokenObject;
    }

    /**
     * @param $token
     * @return string|null
     */
    public function setAccessToken($token)
    {
        $this->google->setAccessToken($token);
        return $this->refreshAccessToken($token);
    }

    /**
     * @return \Google_Service_Oauth2_Userinfoplus
     */
    public function getUserInfo()
    {
        $userInfo = new \Google_Service_Oauth2($this->google);
        return $userInfo->userinfo->get();
    }

    /**
     * @param $token
     * @return string|null
     */
    public function refreshAccessToken($token)
    {
        if ($this->google->isAccessTokenExpired()) {
            // save refresh token to some variable
            $refreshToken = $this->google->getRefreshToken();
            // update access token
            $newToken = $this->google->fetchAccessTokenWithRefreshToken($refreshToken);
            //$this->google->setAccessToken($newToken);
            return $newToken;
        }
    }

//    /**
//     * @return \Google_Service_YouTube_ChannelListResponse
//     */
//    public function getChannelsList()
//    {
//        $youtube = new \Google_Service_YouTube($this->google);
//        $queryParams = [
//            'mine' => true
//        ];
//        return $youtube->channels->listChannels(
//            'contentOwnerDetails,snippet,contentDetails,statistics',
//            array('mine' => $queryParams)
//        );
//    }
//
//    /**
//     * @return \Google_Service_YouTube_SearchListResponse
//     */
//    public function getListSearch()
//    {
//        $youtube = new \Google_Service_YouTube($this->google);
//        $queryParams = [
//            'maxResults' => 5,
//            'type' => 'video',
//            'forMine' => true,
//        ];
//        return $youtube->search->listSearch('snippet', $queryParams);
//    }
//
//    /**
//     * @param $list
//     * @return \Google_Service_YouTube_VideoListResponse
//     */
//    public function getVideoList($list)
//    {
//        $youtube = new \Google_Service_YouTube($this->google);
//        $queryParams = [
//            'id' => $list
//        ];
//        return $youtube->videos->listVideos('snippet,statistics', $queryParams);
//    }
//    /**
//     * @param $videos
//     * @return mixed
//     */
//    public function videoStats( $videos )
//    {
//        $videos = $videos->items;
//        $listed = $videos;
//        if (!empty($videos)) {
//
//            $videoCollect = collect($videos);
//            $list = $videoCollect->pluck('id.videoId');
//            $list = $list->implode(',');
//            $listed = $this->getVideoList($list); ///// to get statistics
//        }
//
//        $channels = $this->getChannelsList();
//        $channelList = $channels->items;
//
//        $today = Carbon::today()->subDays(280)->format("d-m-Y");
//
//        $filtered = array_filter($videos, function ($item) use ($today) {
//
//            if (strtotime(date("d-m-Y", strtotime($item->snippet->publishedAt))) > strtotime($today)) {
//                return $item;
//            }
//        });
//
//        $val['videos'] = $listed;
//        $val['channelList'] = $channelList;
//        $val['countLatestVideos'] = count($filtered);
//
//        return $val;
//    }

    public function getDrive(){
        $this->ListFolders('root');
    }

    public function ListFolders($id){

        $google = new \Google_Service_Drive($this->google);

        $optParams = array(
            'pageSize' => 100,
            'fields' => 'nextPageToken, files(id, name)'
        );
        $results = $google->files->listFiles($optParams);

        if (count($results->getFiles()) == 0) {
            print "No files found.\n";
        } else {
            print "Files:\n";
            foreach ($results->getFiles() as $file) {
                echo "<pre>";
                dd($file);die;
                var_dump($file->getName(), $file->getID());
                //printf("%s (%s)\n", $file->getName(), $file->getId());
            }
        }

die;

        $query = "mimeType='application/vnd.google-apps.file' and '".$id."' in parents and trashed=false";


        $optParams = [
            'fields' => 'files(id, name)',
            'q' => $query
        ];

        $results = $google->files->listFiles($optParams);
        dd($results->getFiles());
        if (count($results->getFiles()) == 0) {
            print "No files found.\n";
        } else {
            print "Files:\n";
            foreach ($results->getFiles() as $file) {
                dump($file->getName(), $file->getID());
            }
        }
        die;
    }

    function uploadFile(Request $request){
        if($request->isMethod('GET')){
            return view('upload');
        }else{
            $this->createFile($request->file('file'));
        }
    }

    function createStorageFile($storage_path){
        $this->createFile($storage_path);
    }

    function createFile($file, $parent_id = null){
        $name = gettype($file) === 'object' ? $file->getClientOriginalName() : $file;
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $name,
            'parent' => $parent_id ? $parent_id : 'root'
        ]);

        $content = gettype($file) === 'object' ?  File::get($file) : Storage::get($file);
        $mimeType = gettype($file) === 'object' ? File::mimeType($file) : Storage::mimeType($file);

        $file = $this->drive->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        dd($file->id);
    }

    function deleteFileOrFolder($id){
        try {
            $this->drive->files->delete($id);
        } catch (Exception $e) {
            return false;
        }
    }

    function createFolder($folder_name){
        $folder_meta = new Google_Service_Drive_DriveFile(array(
            'name' => $folder_name,
            'mimeType' => 'application/vnd.google-apps.folder'));
        $folder = $this->drive->files->create($folder_meta, array(
            'fields' => 'id'));
        return $folder->id;
    }

}
