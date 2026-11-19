<?php
namespace InnoShop\Front\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoShop\Common\Repositories\ProductRepo;
use InnoShop\Common\Services\EventTrackingService;
use InnoShop\Front\Repositories\HomeRepo;

class HomeController extends Controller
{
    private const HOME_PRODUCTS_PER_PAGE = 12;

    /**
     * @return mixed
     * @throws \Exception
     */
    public function index(): mixed
    {
        $maxLimit    = $this->getHomeProductsMaxLimit();
        $productRepo = ProductRepo::getInstance();
        $bestSeller  = $productRepo->paginateHomeTabProducts('bestseller', self::HOME_PRODUCTS_PER_PAGE, 1, $maxLimit);
        $newArrivals = $productRepo->paginateHomeTabProducts('new_arrival', self::HOME_PRODUCTS_PER_PAGE, 1, $maxLimit);
        $tabProducts = [
            [
                'key'          => 'bestseller',
                'tab_title'    => trans('front/home.bestseller'),
                'products'     => $bestSeller['items'],
                'current_page' => $bestSeller['current_page'],
                'has_more'     => $bestSeller['has_more'],
            ],
            [
                'key'          => 'new_arrival',
                'tab_title'    => trans('front/home.new_arrival'),
                'products'     => $newArrivals['items'],
                'current_page' => $newArrivals['current_page'],
                'has_more'     => $newArrivals['has_more'],
            ],
        ];

        $news = HomeRepo::getInstance()->getHomeArticles();
        $data = [
            'slideshow'       => HomeRepo::getInstance()->getSlideShow(),
            'tab_products'    => $tabProducts,
            'news'            => $news,
            'hot_products'    => $this->getHotProducts(),
            'home_categories' => HomeRepo::getInstance()->getHomeCategories(),
        ];

        $data = fire_hook_filter('home.index.data', $data);

        // Track home page view event
        $eventService = new EventTrackingService;
        $eventService->trackHomeView(request());

        return inno_view('home', $data);
    }

    /**
     * Load paginated products for home page tabs via AJAX (infinite scroll).
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function tabProducts(Request $request): JsonResponse
    {
        $tab  = $request->input('tab') === 'new_arrival' ? 'new_arrival' : 'bestseller';
        $page = max(1, (int) $request->input('page', 1));

        $result = ProductRepo::getInstance()->paginateHomeTabProducts(
            $tab,
            self::HOME_PRODUCTS_PER_PAGE,
            $page,
            $this->getHomeProductsMaxLimit()
        );

        $html = inno_view('home._tab_products', [
            'products' => $result['items'],
        ])->render();

        return response()->json([
            'html'         => $html,
            'current_page' => $result['current_page'],
            'has_more'     => $result['has_more'],
            'next_page'    => $result['has_more'] ? $result['current_page'] + 1 : null,
        ]);
    }

    /**
     * Get maximum total product count for home tabs from admin panel settings.
     *
     * @return int
     */
    private function getHomeProductsMaxLimit(): int
    {
        $limit = (int) system_setting('home_product_limit', self::HOME_PRODUCTS_PER_PAGE);

        return $limit > 0 ? $limit : self::HOME_PRODUCTS_PER_PAGE;
    }

    /**
     * Get hot products from settings, organized by floors.
     *
     * @return array Array of floors: [['name' => 'xxx', 'subtitle' => 'xxx', 'products' => [...]], ...]
     */
    private function getHotProducts(): array
    {
        $hotProductsSetting = system_setting('home_hot_products', '{}');

        if (is_array($hotProductsSetting)) {
            $hotProductsData = $hotProductsSetting;
        } else {
            $hotProductsData = json_decode($hotProductsSetting, true) ?: [];
        }

        if (empty($hotProductsData) || empty($hotProductsData['floors'])) {
            return [];
        }

        $groups = $hotProductsData['floors'];

        $result = [];

        try {
            $allProductIds = HomeRepo::getInstance()->getHomeHotProductIdsOrdered();

            if (empty($allProductIds)) {
                return [];
            }

            $products = ProductRepo::getInstance()->builder(['active' => true])
                ->whereIn('products.id', $allProductIds)
                ->with(['masterSku', 'skus', 'translation'])
                ->get();

            foreach ($groups as $group) {
                if (! isset($group['products']) || ! is_array($group['products']) || empty($group['products'])) {
                    continue;
                }

                $floorProducts = [];
                foreach ($group['products'] as $productId) {
                    $product = $products->firstWhere('id', $productId);
                    if ($product) {
                        $floorProducts[] = $product;
                    }
                }

                if (! empty($floorProducts)) {
                    $result[] = [
                        'name'     => $this->resolveLocaleValue($group['name'] ?? ''),
                        'subtitle' => $this->resolveLocaleValue($group['subtitle'] ?? ''),
                        'products' => $floorProducts,
                    ];
                }
            }

            return [
                'display_mode' => $hotProductsData['display_mode'] ?? 'flat',
                'title_align'  => $hotProductsData['title_align'] ?? 'left',
                'floors'       => $result,
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Resolve a locale-aware value: if array (locale→value map), pick current locale;
     * if string, return as-is (backward compatibility).
     */
    private function resolveLocaleValue(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            $currentLocale  = front_locale_code();
            $fallbackLocale = setting_locale_code() ?? 'en';

            return $value[$currentLocale] ?? $value[$fallbackLocale] ?? reset($value) ?: '';
        }

        return '';
    }
}
