@include('errors.layout', [
    'statusCode' => 503,
    'title' => 'We are temporarily unavailable',
    'message' => request()->is('dashboard*')
        ? 'The dashboard is temporarily unavailable while the system recovers or receives maintenance.'
        : 'This part of the website is temporarily unavailable while the system recovers or receives maintenance.',
    'hint' => 'Please wait a little and try again shortly.',
    'secondaryLabel' => request()->is('dashboard*') ? 'Go Home' : 'Contact Us',
    'secondaryUrl' => request()->is('dashboard*') ? route('home') : route('contact'),
])
