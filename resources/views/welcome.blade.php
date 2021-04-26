<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="google-site-verification" content="9T7CDwviMokVrafNoI5wbxAXWki_mX7ttswWlKoHg_M" />

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>

          <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
          <script>
        
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;
        
            // var pusher = new Pusher('30b40ac3acc26d1a0504', {
            //   cluster: 'eu',
            //   auth: {
            //     headers: {
            //     'X-CSRF-Token': "{{ csrf_token() }}"
            //     }
            //   }
            // });

            var pusher = new Pusher('30b40ac3acc26d1a0504', {
                cluster: 'eu',
                authTransport: 'jsonp',
                authEndpoint: 'https://hahyv.herokuapp.com/pusher/auth'
            });

 
        
            // var channel = pusher.subscribe('channel-name');
            // channel.bind('LikeComment', function(data) {
            //   alert(JSON.stringify(data));
            // });
            var pchannel = pusher.subscribe('private-notification-101');
            pchannel.bind('App\\Events\\PostNotificationEvent', function(data) {
              alert(JSON.stringify(data));
            });


            // var pchannelChat = pusher.subscribe('private-chat-1');
            // pchannelChat.bind('App\\Events\\MessageEvent', function(data) {
            //   alert(JSON.stringify(data));
            });
          </script>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Hahyv Backend
                </div>

                <div class="links">
                    <a href="https://documenter.getpostman.com/view/8806253/TVsrF97w">API-Docs1</a>
                    <a href="https://documenter.getpostman.com/view/8806253/TW74jR5o">API-Docs2</a>
                    {{-- <a href="https://documenter.getpostman.com/view/8806253/TVzPnJpe#e86af4f4-d855-4b14-b57b-a140a1cbfc15">API-Docs2</a> --}}
                </div>
            </div>
        </div>
        <div>
            <h1>Pusher Test</h1>
            <p>
              Try publishing an event to channel <code>my-channel</code>
              with event name <code>my-event</code>.
            </p>
        </div>
    </body>
</html>
