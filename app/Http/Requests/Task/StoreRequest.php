<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // dont' forget to set this as true
        return true;
    }

    public function rules(): array
    {
        // make all of the fields required, set info file to accept only files
        return [
            'content' => 'required|string|min:3|max:255', // minimum length is 3 characters, maximum length is 255 characters
            'info_file' => 'nullable|file|max:1024|mimes:pdf,docx,doc,xlsx,xls', // optional, file only, max size is 1024 KB, with some allowed mime types
        ];
    }
}
