<?php
namespace InnoShop\RestAPI\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteFilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'path'    => 'required|string',
            'files'   => 'required|array|min:1',
            'files.*' => 'required|string',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'path' => [
                'description' => 'Base directory path.',
                'example'     => '/images',
            ],
            'files' => [
                'description' => 'Array of filenames to delete.',
                'example'     => ['photo.jpg'],
            ],
        ];
    }
}
