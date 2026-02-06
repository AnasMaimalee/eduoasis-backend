<?php

namespace App\Http\Controllers\Api\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Stevebauman\Location\Facades\Location;

class VisitorController extends Controller
{
    public function index()
    {
        $visitors = Visistor::all();

        return respose->json([
            'message' => 'Visitors Fetched',
            'data' => $visitors
        ]);

    }
    public function store(Request $request)
    {
        $agent = $request->userAgent();
        $ip = request()->ip();

        if ($ip === '127.0.0.1') {
            $ip = request()->header('X-Forwarded-For');
            $ip = explode(',', $ip)[0];
        }

        $location = Location::get($ip);
        
        Visitor::create([
            'ip' => $ip,
            'browser' => $this->getBrowser($agent),
            'os' => $this->getOS($agent),
            'device' => $this->isMobile($agent) ? 'Mobile' : 'Desktop',
            'referrer' => $request->headers->get('referer'),
            'country' => is_object($location) ? $location->countryName : null,
            'city' => is_object($location) ? $location->cityName : null,
        ]);

        return response()->json(['success' => true]);
    }

    private function isMobile($agent): bool
    {
        return (bool) preg_match('/Mobile|Android|iPhone|iPad/i', $agent);
    }

    private function getBrowser($agent): string
    {
        if (str_contains($agent, 'Chrome')) return 'Chrome';
        if (str_contains($agent, 'Firefox')) return 'Firefox';
        if (str_contains($agent, 'Safari')) return 'Safari';
        return 'Unknown';
    }

    private function getOS($agent): string
    {
        if (str_contains($agent, 'Windows')) return 'Windows';
        if (str_contains($agent, 'Mac')) return 'MacOS';
        if (str_contains($agent, 'Linux')) return 'Linux';
        if (str_contains($agent, 'Android')) return 'Android';
        if (str_contains($agent, 'iPhone')) return 'iOS';
        return 'Unknown';
    }
}
