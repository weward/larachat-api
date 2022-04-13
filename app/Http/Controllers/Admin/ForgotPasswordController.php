<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ForgotPasswordRequest;
use App\Services\Admin\ForgotPasswordService;

class ForgotPasswordController extends Controller
{
    protected $repo;

    public function __construct(ForgotPasswordService $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Send User An Email Upon Request
     * 
     * @param  object $req 
     * @return boolean
     */
    public function handle(ForgotPasswordRequest $request)
    {
        if ($this->repo->handle($request)) {
            return response()->json('A password reset link was sent to the specified email.', 200);
        }

        return response()->json('Failed request.', 500);
    }

    /**
     * Verify Link From Email And Redirect To Frontend
     * 
     * @param  int $id User ID
     * @param string $hash Random Hash
     * @return boolean
     */
    public function resetLink($id, $hash)
    {
        if ($this->repo->resetLink($id, $hash)) {
            return redirect()->away(env('FRONTEND_APP_URL') . "/change-password/{$id}/{$hash}");
        }

        return redirect()->away(env('FRONTEND_APP_URL') . "/404");
    }
}
