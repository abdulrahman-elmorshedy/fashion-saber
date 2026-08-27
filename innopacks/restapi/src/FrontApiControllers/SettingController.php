<?php
namespace InnoShop\RestAPI\FrontApiControllers;

use Exception;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Unauthenticated;

#[Group('Front - Settings')]
class SettingController extends BaseController
{
    /**
     * @return mixed
     * @throws Exception
     */
    #[Endpoint('Get system settings')]
    #[Unauthenticated]
    public function index(): mixed
    {
        $settings = setting('system');

        $settings['locales']    = locales()->select(['name', 'code']);
        $settings['currencies'] = currencies()->select(['name', 'code']);

        return read_json_success($settings);
    }
}
