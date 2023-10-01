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

use App\Services\Forge\ForgeService;
use App\Traits\Outputifier;
use Closure;
use Illuminate\Support\Str;

class OrCreateNewSite
{
    use Outputifier;

    public function __invoke(ForgeService $service, Closure $next)
    {
        if (is_null($service->site)) {
            $this->information('Creating a new site.');

            $site = $service->forge->createSite(
                $service->setting->server,
                $this->gatherSiteData($service)
            );

            $service->setSite($site);
            $service->markSiteAsNewlyMade();
        }

        return $next($service);
    }

    private function gatherSiteData(ForgeService $service): array
    {
        $data = [
            'domain' => $service->getFormattedDomainName(),
            'project_type' => $service->setting->projectType,
            'php_version' => $service->setting->phpVersion,
            'directory' => '/public',
        ];

        if ($nginxTemplate = $service->setting->nginxTemplate) {
            $this->information('---> Use the specified Nginx template.');

            $data['nginx_template'] = $nginxTemplate;
        }

        if ($service->setting->siteIsolationRequired) {
            $this->information('---> Enabling site isolation.');

            $data['isolated'] = true;
            $data['username'] = Str::slug($service->setting->branch);
        }

        return $data;
    }
}
