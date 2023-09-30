<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\Resource;
use Image;

trait FileHandling
{
    /**
     * Upload image and return image path for storing in database;
     * 
     * @param string $imageFolder Folder name to store the image
     * @param Resource $image The uploaded image file
     * @param string $rootImageFolder
     *
     * @return string Image path
     */
    public function uploadImage($imageFolder, $image, $rootImageFolder = 'images/')
    {
        $imageName = md5($image->getFilename() . time()) . '.' . $image->getClientOriginalExtension();
        $rootImagePath = public_path($rootImageFolder);
        if (!file_exists($rootImagePath)) {
            mkdir($rootImagePath);
        }

        $imagePath = $rootImagePath . $imageFolder . '/';
        if (!file_exists($imagePath)) {
            mkdir($imagePath);
        }

        $image = Image::make($image->getRealPath());
        $image->save($imagePath . $imageName);

        return $rootImageFolder . $imageFolder . '/' . $imageName;
    }

    /**
     * Upload general file and return file path for storing in database.
     *
     * @param string $fileFolder
     * @param Resource $file
     * @param string $rootFileFolder
     *
     * @return string File path
     */
    public function uploadFile($fileFolder, $file, $rootFileFolder = 'documents/')
    {
        $fileName = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
        $rootFilePath = public_path($rootFileFolder);
        if (!file_exists($rootFilePath)) {
            mkdir($rootFilePath);
        }

        $filePath = $rootFilePath . $fileFolder . '/';
        if (!file_exists($filePath)) {
            mkdir($filePath);
        }

        $file->move($filePath, $fileName);
        return $rootFileFolder . $fileFolder . '/' . $fileName;
    }
}
