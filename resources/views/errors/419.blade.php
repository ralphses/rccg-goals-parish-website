@include('errors.layout', [
    'statusCode' => 419,
    'title' => 'Your session has expired',
    'message' => request()->is('dashboard*')
        ? 'For security, your dashboard session timed out before the request could finish.'
        : 'Your session expired before the request could be completed.',
    'hint' => 'Please go back, refresh the page, and try the action again.',
    'secondaryLabel' => 'Go Back',
    'secondaryUrl' => url()->previous() ?: (request()->is('dashboard*') && auth()->check() ? route('dashboard') : route('home')),
])
