@include('errors.layout', [
    'statusCode' => 403,
    'title' => 'You do not have access to this page',
    'message' => request()->is('dashboard*')
        ? 'Your account does not currently have permission to open this dashboard page or complete this action.'
        : 'You do not currently have permission to open the page you requested.',
    'hint' => request()->is('dashboard*')
        ? 'Try heading back to your dashboard or signing in with an account that has the right access level.'
        : 'Head back to the homepage or contact the church if you believe this page should be available to you.',
    'secondaryLabel' => request()->is('dashboard*') ? 'Go Home' : 'Contact Us',
    'secondaryUrl' => request()->is('dashboard*') ? route('home') : route('contact'),
])
