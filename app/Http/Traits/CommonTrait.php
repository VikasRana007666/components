<?php

namespace App\Http\Traits;

use App\Models\File;

trait CommonTrait
{
    public function upload_multiple_files($request, $request_name, $doc_type, $doc_path, $table_name, $table_name_id)
    {
        foreach ($request->file($request_name) as $file) {
            $extension = $file->extension();
            $fileName = date("j-M-Y-His-a") . "-" . time() . rand(1000000000, 9999999999) . "." . $extension;

            $files = File::create([
                'doc_type' => $doc_type,
                'doc_ext' => $extension,
                // 'doc_path' => $file->store($doc_path, 'public'), //for random
                'doc_path' => $file->storeAs($doc_path, $fileName, "public"), //formated way
                'table_name' => $table_name,
                'table_name_id' => $table_name_id,
                'user_id' => auth()->id()
            ]);
        }
        // upload_multiple_files($request, "doc_image", "image", "posts", "posts", $table_name_id);
    }

    public function upload_single_file($request, $request_name, $doc_type, $doc_path, $table_name, $table_name_id)
    {
        $file = $request->file($request_name);
        $extension = $file->extension();
        $fileName = date("j-M-Y-His-a") . "-" . time() . rand(1000000000, 9999999999) . "." . $extension;

        $file = File::create([
            'doc_type' => $doc_type,
            'doc_ext' => $extension,
            // 'doc_path' => $file->store($doc_path, 'public'), //for random
            'doc_path' => $file->storeAs($doc_path, $fileName, "public"), //formated way
            'table_name' => $table_name,
            'table_name_id' => $table_name_id,
            'user_id' => auth()->id()
        ]);
        // upload_single_file($request, "doc_image", "image", "posts", "posts", $table_name_id);
    }
}


// https://stackoverflow.com/questions/43433350/how-to-use-traits-in-laravel-5-4-18