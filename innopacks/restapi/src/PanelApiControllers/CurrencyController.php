<?php
namespace InnoShop\RestAPI\PanelApiControllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoShop\Common\Models\Currency;
use InnoShop\Common\Repositories\CurrencyRepo;
use InnoShop\Common\Resources\CurrencyItem;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Panel - Currencies')]
class CurrencyController extends BaseController
{
    /**
     * Get all enabled currencies for calculator
     *
     * @return JsonResponse
     */
    #[Endpoint('List currencies')]
    public function index(): JsonResponse
    {
        $currencies = CurrencyRepo::getInstance()->withActive()->builder()->get();

        return json_success('Success', CurrencyItem::collection($currencies)->toArray(request()));
    }

    #[Endpoint('Create currency')]
    public function store(Request $request): mixed
    {
        try {
            $currency = Currency::query()->create($request->all());

            return create_json_success($currency);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    #[Endpoint('Update currency')]
    #[UrlParam('id', 'integer', description: 'Currency ID')]
    public function update(Request $request, int $id): mixed
    {
        try {
            $currency = Currency::query()->findOrFail($id);
            $currency->update($request->all());

            return update_json_success(new CurrencyItem($currency));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
