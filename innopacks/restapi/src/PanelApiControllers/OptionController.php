<?php
namespace InnoShop\RestAPI\PanelApiControllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoShop\Common\Repositories\OptionRepo;
use InnoShop\Common\Repositories\OptionValueRepo;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Panel - Options')]
class OptionController extends BaseController
{
    /**
     * Get available options list
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Endpoint('Get available options')]
    public function available(Request $request): JsonResponse
    {
        $filters           = $request->all();
        $filters['active'] = 1; // Only get active options

        $options = OptionRepo::getInstance()->list($filters);

        // Format data, including option values count
        $formattedOptions = [];
        foreach ($options as $option) {
            $formattedOptions[] = [
                'id'                  => $option->id,
                'name'                => $option->getLocalizedName(),
                'description'         => $option->getLocalizedDescription(),
                'type'                => $option->type,
                'sort_order'          => $option->sort_order,
                'active'              => $option->active,
                'option_values_count' => $option->optionValues->count(),
                'created_at'          => $option->created_at,
                'updated_at'          => $option->updated_at,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => ['options' => $formattedOptions],
        ]);
    }

    /**
     * Get option values list by option ID
     *
     * @param  Request  $request
     * @param  int  $optionId
     * @return JsonResponse
     * @throws Exception
     */
    #[Endpoint('Get option values')]
    #[UrlParam('optionId', type: 'integer', description: 'Option ID')]
    public function values(Request $request, int $optionId): JsonResponse
    {
        $filters              = $request->all();
        $filters['option_id'] = $optionId;
        $filters['active']    = 1; // Only get active option values

        $optionValues = OptionValueRepo::getInstance()->list($filters);

        // Format data
        $formattedValues = [];
        foreach ($optionValues as $optionValue) {
            $formattedValues[] = [
                'id'         => $optionValue->id,
                'option_id'  => $optionValue->option_id,
                'name'       => $optionValue->getLocalizedName(),
                'image'      => $optionValue->image,
                'image_url'  => $optionValue->image ? $optionValue->getImageUrl() : null,
                'sort_order' => $optionValue->sort_order,
                'active'     => $optionValue->active,
                'created_at' => $optionValue->created_at,
                'updated_at' => $optionValue->updated_at,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => ['option_values' => $formattedValues],
        ]);
    }
}
