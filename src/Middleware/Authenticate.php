<?php


namespace ChromeExtension\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Class Authenticate
 * @package ChromeExtension\Middleware
 */
class Authenticate
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $this->checkToken($request['token'])) {
            abort(403);
        }

        return $next($request);
    }

    /**
     * @param string $token
     * @return bool
     */
    private function checkToken(string $token)
    {
        $response = $response = Http::withHeaders([
            'X-CSRF-TOKEN' => csrf_token(),
        ])->get(env('AUTH_ROUTE', 'https://') . $token);

        $this->checkResponseStatus($response->status());

        $result = json_decode((string)$response->body(), true);

        return 200 === $result['status'];
    }

    /**
     * @param int $responseCode
     */
    private function checkResponseStatus(int $responseCode)
    {
        if (200 !== $responseCode) {
            abort($responseCode);
        }
    }

}
