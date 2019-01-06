<?php

namespace Neo\EarlyAccess\Http\Controllers;

use Illuminate\Http\Request;
use Neo\EarlyAccess\Subscriber;
use Illuminate\Routing\Controller as BaseController;
use Neo\EarlyAccess\Traits\InteractsWithEarlyAccess;

class EarlyAccessController extends BaseController
{
    use InteractsWithEarlyAccess;

    /**
     * Show the early access page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = $this->getBeaconDetails();

        return view(config('early-access.view'), compact('details'));
    }

    /**
     * Subscribe.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'name' => 'string|between:3,100',
        ]);

        if (! $subscriber = Subscriber::make()->findByEmail($data['email'])) {
            $subscriber = Subscriber::make($data);
            $subscriber->subscribe();
        }

        return redirect()->route('early-access.index')->withSuccess(true);
    }

    /**
     * Unsubscribe.
     *
     * @param \Neo\EarlyAccess\Subscriber $subscriber
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Subscriber $subscriber, Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $unsubscribed = with($subscriber->findByEmail($data['email']), function ($user) {
            return $user ? $user->unsubscribe() : false;
        });

        return redirect()->route('early-access.index')->with([
            ($unsubscribed ? "success" : "error") => true,
        ]);
    }

    /**
     * Share to twitter.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shareOnTwitter()
    {
        if ($handle = config('early-access.twitter_handle')) {
            $shareText = rawurlencode(
                trans('early-access::messages.twitter_share_text', [
                    'handle' => $handle,
                    'url' => route('early-access.index'),
                ])
            );

            $url = "https://twitter.com/intent/tweet?text={$shareText}&related={$handle}&handle={$handle}";
        }

        return redirect($url ?? route('early-access.index'));
    }
}
