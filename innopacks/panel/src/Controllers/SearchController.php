<?php
namespace InnoShop\Panel\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoShop\Panel\Repositories\MenuSearchRepo;

class SearchController extends BaseController
{
    /**
     * Search panel menus by keyword.
     */
    public function menus(Request $request): JsonResponse
    {
        $keyword = mb_substr(trim($request->get('keyword', '')), 0, 100);
        $results = MenuSearchRepo::getInstance()->search($keyword);

        return response()->json($results);
    }
}
