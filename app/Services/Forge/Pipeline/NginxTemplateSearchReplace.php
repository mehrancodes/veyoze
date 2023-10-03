<?php

declare(strict_types=1);

/**
 * This file is part of Harbor CLI.
 *
 * (c) Mehran Rasulian <mehran.rasulian@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Services\Forge\Pipeline;

use App\Actions\SearchReplaceKeysInText;
use App\Services\Forge\ForgeService;
use App\Traits\Outputifier;
use Closure;

class NginxTemplateSearchReplace
{
    use Outputifier;

    public function __invoke(ForgeService $service, Closure $next)
    {
        if (empty($service->setting->nginxSubstitute) || ! $service->siteNewlyMade) {
            return $next($service);
        }

        $template = $service->forge->siteNginxFile(
            $service->setting->server,
            $service->site->id
        );

        $service->forge->updateSiteNginxFile(
            $service->setting->server,
            $service->site->id,
            SearchReplaceKeysInText::run(
                $service->setting->nginxSubstitute,
                $template
            )
        );

        return $next($service);
    }
}