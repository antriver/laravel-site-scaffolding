<?php

namespace Antriver\LaravelSiteUtils\Http\Validators;

use Antriver\LaravelSiteUtils\Models\Image;
use Antriver\LaravelSiteUtils\Repositories\ImageRepository;

class UserImageValidator
{
    public function validate($attribute, $value, $parameters)
    {
        /** @var Image $image */
        $image = app(ImageRepository::class)->find($value);
        if (!$image) {
            return false;
        }

        if (isset($parameters[0])) {
            $userId = $parameters[0];
            if ($image->userId != $userId) {
                return false;
            }
        }

        return true;
    }
}