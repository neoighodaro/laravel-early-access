<?php

namespace Neo\EarlyAccess\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Neo\EarlyAccess\Facades\EarlyAccess;
use Symfony\Component\HttpFoundation\IpUtils;

class CheckForEarlyAccessMode
{
    /**
     * The URIs that should be accessible while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [];

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * CheckForEarlyAccessMode constructor.
     */
    public function __construct()
    {
        $this->baseUrl = config('early-access.url');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (EarlyAccess::isEnabled()) {
            $data = EarlyAccess::getBeaconDetails();

            if (isset($data['allowed']) && IpUtils::checkIp($request->ip(), (array) $data['allowed'])) {
                return $next($request);
            }

            if ($this->inExceptArray($request)) {
                return $next($request);
            }

            return redirect(config('early-access.url'));
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should be accessible in maintenance mode.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        $defaultExceptions = [
            $this->baseUrl,
            $this->baseUrl . '/*',
            config('early-access.login_url'),
        ];

        $defaultExceptions = array_filter($defaultExceptions, function ($item) {
            return trim($item, '/') !== '*';
        });

        array_push($this->except, ...$defaultExceptions);

        foreach (array_unique($this->except) as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
