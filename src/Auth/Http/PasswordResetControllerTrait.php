<?php

namespace Antriver\LaravelSiteUtils\Auth\Http;

use Antriver\LaravelSiteUtils\Auth\ApiAuthResponseFactory;
use Antriver\LaravelSiteUtils\Auth\Forgot\PasswordResetTokenRepository;
use Antriver\LaravelSiteUtils\Auth\UserAuthenticator;
use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserPresenter;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Antriver\LaravelSiteUtils\Users\UserService;
use Antriver\LaravelSiteUtils\Users\ValidatesUserCredentialsTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait PasswordResetControllerTrait
{
    use ValidatesUserCredentialsTrait;

    /**
     * @api {get} /password-resets/:token Validate Password Reset Token
     *
     * @param $resetToken
     * @param Request $request
     * @param UserPresenter $userPresenter
     * @param UserRepository $userRepository
     * @param PasswordResetTokenRepository $passwordResetTokenRepository
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestHttpException
     */
    public function show(
        $resetToken,
        Request $request,
        UserPresenter $userPresenter,
        UserRepository $userRepository,
        PasswordResetTokenRepository $passwordResetTokenRepository
    ) {
        // Purposely introducing the delay to slow an attempt to brute force a reset token.
        sleep(1);

        $this->validate(
            $request,
            [
                'userId' => 'required',
            ]
        );

        $user = $userRepository->findOrFail($request->input('userId'));

        $tokenValid = $passwordResetTokenRepository->exists($user, $resetToken);

        if (!$tokenValid) {
            throw new BadRequestHttpException('This password reset link has expired.');
        }

        return $this->response(
            [
                'user' => $userPresenter->present($user),
                'resetToken' => $resetToken,
                'resetTokenValid' => $tokenValid,
            ]
        );
    }

    /**
     * @api {patch} /users/:userId/password Reset User's Password
     *
     * @param User $user
     * @param ApiAuthResponseFactory $apiAuthResponseFactory
     * @param PasswordResetTokenRepository $passwordResetTokenRepository
     * @param Request $request
     * @param UserAuthenticator $userAuthenticator
     * @param UserRepository $userRepository
     *
     * @param UserService $userService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(
        User $user,
        ApiAuthResponseFactory $apiAuthResponseFactory,
        PasswordResetTokenRepository $passwordResetTokenRepository,
        Request $request,
        UserAuthenticator $userAuthenticator,
        UserRepository $userRepository,
        UserService $userService
    ) {
        // Purposely introducing the delay to slow an attempt to brute force a reset token.
        sleep(1);

        $this->validate(
            $request,
            [
                'resetToken' => 'required',
                'password' => $this->getPasswordValidationRules(true),
            ]
        );

        $resetToken = $request->input('resetToken');

        $tokenValid = $passwordResetTokenRepository->exists($user, $resetToken);
        if (!$tokenValid) {
            throw new BadRequestHttpException('This password reset link has expired.');
        }

        $userService->setUserPassword($user, $request->input('password'));

        $userRepository->persist($user);

        // Delete all reset tokens for the user.
        $passwordResetTokenRepository->delete($user);

        // Ensure banned users can't login.
        $userAuthenticator->ensureAccountCanLogin(
            $user,
            config('auth.allow_unverified_user_login', false)
        );

        $response = $apiAuthResponseFactory->make(
            $request,
            $user
        );

        $response['success'] = true;

        return $this->response($response);
    }
}
