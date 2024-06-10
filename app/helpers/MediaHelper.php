<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use App\Models\Media;

class MediaHelper{

    /**
     * this function is used to store the media
     *
     * @param Request $request
     * @return Response
     */
    public static function uploadImage(Request $request){

        $validator = Validator::make($request->all(), [
            'media_image' => 'required|image|max:1024',
        ],[
            'media_image.max' => 'The media image field must not be greater than 1 MB.',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $image = $request->file('media_image');
        $extension = $image->getClientOriginalExtension();
        $fullName = $image->getClientOriginalName();
        $imageSize = $image->getSize();
        $onlyName = explode('.'.$extension, $fullName);
        $imageName = str_replace(" ","-",$onlyName[0]).'-'.time().'.webp';
        $imageWebp  = ImageManagerStatic::make($request->file('media_image'))->encode('webp');

        $data = [
            'media_name' => $onlyName[0],
            'media_path' => 'media/'.$imageName,
            'media_type' => 'webp',
            'media_size' => $imageSize,
        ];

        $media = Media::create($data);
        $storage = Storage::disk('public')->put('media/' . $imageName, $imageWebp);

        if ($media && $storage) {

            return true;

        } else {

            return false;
        }
    }

    /**
     * this function is used to get all the images
     *
     * @return Response
     */
    public static function getAllImages(){

        $media = Media::orderByDesc('media_id')->get();

        foreach($media as $key => $value) {
            $media[$key]['media_path'] = Storage::url($value['media_path']);
        }

        return response()->json([
                                'data' => $media ?? [],
                                'status' => true,
                                ],200);
    }

    /**
     * Update the specified media in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $media_id
     * @return \Illuminate\Http\Response
     */
    public static function updateImage(Request $request, string $name){

        $media = Media::whereMedia_name($name)->first();

        if(!$media){
            // Media not found
            return false;
        }

        $validator = Validator::make($request->all(), [
            'media_image' => 'required|image|max:1024',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        // Handle file update
        if ($request->hasFile('media_image')) {

            $image = $request->file('media_image');
            $extension = $image->getClientOriginalExtension();
            $fullName = $image->getClientOriginalName();
            $imageSize = $image->getSize();
            $onlyName = explode('.'.$extension, $fullName);
            $imageName = str_replace(" ","-",$onlyName[0]).'-'.time().'.webp';
            $imageWebp  = ImageManagerStatic::make($request->file('media_image'))->encode('webp');

            // Update media data
            $media->media_name = $onlyName[0];
            $media->media_path = 'media/'.$imageName;
            $media->media_type = 'webp';
            $media->media_size = $imageSize;

            // Save updated media data
            $media->save();

            // Store the updated image
            $storage = Storage::disk('public')->put('media/' . $imageName, $imageWebp);

            if ($storage) {

                return true;
            } else {

                // Error in storing the updated image
                return false;
            }
        } else {
            // No file to update, return false
            return false;
        }
    }

    /**
     * this function is used to delete the media
     *
     * @param string $id
     * @return Response
     */
    public static function deleteMedia(string $id){

        $media = Media::find($id);

        if ($media) {

            Storage::disk('public')->delete($media->media_path);
            $media->delete();

            return true;
        }else{

            return false;
        }
    }

     /**
     * this function is used to return the media path
     *
     * @param string $id
     * @return Response
     */
    public static function getMediaPath(string $id = null){

        $media = Media::find($id);

        if ($media) {

            return  Storage::url($media->media_path);
        }else{

            return null;
        }
    }

    /**
     * this function is used to return the media
     *
     * @param string $name
     * @return Response
     */
    public static function getMediaByName(string $name = null){

        $name = 'media/'.$name;

        $media = Media::Where('media_path', $name)->first();

        if ($media) {

            return $media;
        }else{

            return null;
        }
    }

     /**
     * this function is used to return the media
     *
     * @param string $name
     * @return Response
     */
    public static function getMediaByMediaName(string $mediaName = null){

        $media = Media::Where('media_name', $mediaName)->first();

        if ($media) {

            return $media;
        }else{

            return null;
        }
    }
}
?>
