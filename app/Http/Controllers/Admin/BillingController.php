<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\BillingService;
use App\Http\Requests\BillingStripePaymentMethodRequest;

class BillingController extends Controller
{
    protected $repo;

    public function __construct(BillingService $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        if ($res = $this->repo->index($request)) {
            return response()->json($res, 200);
        }

        return response()->json('Failed to retrieve billing data.', 500);
    }

    public function setupPaymentMethod(BillingStripePaymentMethodRequest $request)
    {
        // return response()->json($this->repo->setupPaymentMethod($request), 500);

        if ($res = $this->repo->setupPaymentMethod($request)) {
            return response()->json($res, 200);
        }

        return response()->json('Failed to update payment method.', 500);
    }
}
