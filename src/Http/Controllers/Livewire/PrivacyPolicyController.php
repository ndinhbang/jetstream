<?php

namespace Ndinhbang\Jetstream\Http\Controllers\Livewire;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Ndinhbang\Jetstream\Jetstream;

class PrivacyPolicyController extends Controller
{
    /**
     * Show the privacy policy for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $policyFile = Jetstream::localizedMarkdownPath('policy.md');

        return view('policy', [
            'policy' => Str::markdown(file_get_contents($policyFile)),
        ]);
    }
}
