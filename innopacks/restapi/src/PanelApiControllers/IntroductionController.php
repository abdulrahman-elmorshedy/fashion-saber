<?php
namespace InnoShop\RestAPI\PanelApiControllers;

use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Unauthenticated;

#[Group('Panel - Introduction')]
class IntroductionController extends BaseController
{
    #[Endpoint('Panel API base info')]
    #[Unauthenticated]
    public function index(): string
    {
        return 'This is Panel Restful APIs for '.innoshop_version();
    }
}
