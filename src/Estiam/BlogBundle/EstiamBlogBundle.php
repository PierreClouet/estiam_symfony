<?php

namespace Estiam\BlogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EstiamBlogBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
