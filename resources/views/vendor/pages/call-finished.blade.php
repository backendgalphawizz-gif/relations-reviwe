@extends('vendor.app')

@push('css_or_js')
    <title>Call Completed</title>
    <style>
        .remote-player-screen {
            width: 100%;
            height: 100%;
            background-color: #b6fde1fa;
        }

        .player-call-screen {
            width: 100%;
            height: 100%;
            border-radius: 25px;
        }

        .agora_video_player {
            width: 100%;
            height: 100%;
            border-radius: 25px;
            border: 2px solid #e4fff3;
        }

        #remote-playerlist{
            width: 150px;
            height: 500px;
            background-color: #b6fde1fa;
        }

        .local-player {
            background-color: #e4fff3;
            border-radius: 10px;
            height: 500px;
            background-image: url('https://relationship-revive-2.developmentalphawizz.com/storage/images/AdminLogo1743598139.png');
        }

    </style>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-12">
           <h1>Call Finished</h1>
        </div>
    </div>
@endsection

@push('script')

@endpush