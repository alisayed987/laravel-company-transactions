<?php

namespace App\Traits;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Http\FormRequest;

trait AbilityTrait
{
    /**
     * Checks if request sent from admin
     *
     * @param string $fullToken
     * @return boolean
     */
    private function checkIfAdmin($fullToken)
    {
        [$bearer_id, $token] = explode('|', $fullToken, 2);

        $tokenId = explode(' ', $bearer_id, 2)[1];
        /**
         * @var User
         */
        $user = User::select(['*', 'personal_access_tokens.id as p_id', 'users.id as id'])
            ->join('personal_access_tokens', 'users.id', 'personal_access_tokens.tokenable_id')
            ->firstWhere('personal_access_tokens.id', $tokenId);

        return $user->hasRole('admin');
    }

    /**
     * Validate user role
     *
     * @param FormRequest $request
     * @param User $authUser
     * @return void
     */
    protected function validateTokenAndUserRole(FormRequest $request, $authUser = null)
    {
        if ($authUser) {
            $isAdmin = $authUser->hasRole('admin');
        } else {
            $fullToken = $request->header('Authorization') ?? null;

            if (!$fullToken) throw new Exception('No token provided.');

            $isAdmin = $this->checkIfAdmin($fullToken);

            if (!$isAdmin) throw new Exception('Admins only can add transactions');
        }
    }
}
