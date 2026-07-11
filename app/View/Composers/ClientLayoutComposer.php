<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ClientLayoutComposer
 *
 * Automatically shares layout-selection variables with all user.* views.
 * This centralises the "which layout to render?" logic so that individual
 * view files never need to duplicate the @php $isClient = ... @endphp block.
 *
 * Variables injected into every user.* view:
 *  - $isClient      (bool)   – true when the authenticated user is a Client
 *  - $layout        (string) – 'layouts.client' | 'layouts.user'
 *  - $contentSection(string) – 'client_content' | 'content'
 */
class ClientLayoutComposer
{
    public function compose(View $view): void
    {
        $isClient = Auth::check()
            && Auth::user()->user_type === User::TYPE_CLIENT;

        $view->with([
            'isClient'       => $isClient,
            'layout'         => $isClient ? 'layouts.client' : 'layouts.user',
            'contentSection' => $isClient ? 'client_content' : 'content',
        ]);
    }
}
