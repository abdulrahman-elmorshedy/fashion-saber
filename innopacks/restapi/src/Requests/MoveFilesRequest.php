<?php
namespace InnoShop\RestAPI\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveFilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files'     => 'required|array|min:1',
            'files.*'   => 'required|string',
            'dest_path' => 'required|string',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'files' => [
                'description' => 'Array of file paths to move or copy.',
                'example'     => ['/images/photo.jpg'],
            ],
            'dest_path' => [
                'description' => 'Destination directory path.',
                'example'     => '/images/archive',
            ],
        ];
    }
}
