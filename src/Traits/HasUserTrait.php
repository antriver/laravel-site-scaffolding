<?php

namespace Antriver\LaravelSiteUtils\Traits;

use Antriver\LaravelSiteUtils\Users\UserInterface;

trait HasUserTrait
{
    /**
     * @var UserInterface|null
     */
    protected $user;

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     */
    public function setUser(?UserInterface $user)
    {
        $this->user = $user;
    }
}
