@extends('vendor.app')

@push('css_or_js')
    <title>Call History</title>
    <style>
        .remote-player-screen {
            width: 100%;
            height: 100%;
            /* background-color: #b6fde1fa; */
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
            width: 100%;
            height: 500px;
        }
        #time {
            font-size: 17px;
            font-weight: 600;
            color: #0fa3c8;
            margin-bottom: 10px;
        }

    </style>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-12">
            <form action="{{ route('advisor.send-joined-notification') }}" id="join-form">
                <div class="row video-group">
                    <div class="col-lg-12">
                        <span id="time">00:00:00</span>
                    </div>
                    <div class="col-lg-6">
                        <p id="local-player-name1" class="player-name">Advisor</p>
                        <div class="local-player" style="background-image: url('{{ $callRequest->astrologer->profileImage!='' ? asset($callRequest->astrologer->profileImage) : asset('/build/assets/images/videoChatImg.webp') }}');">
                            <div id="local-player" class="player" style="width: 100%;height: 100%; "></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <p id="local-player-name1" class="player-name text-end">Client</p>
                        <div class="remote-playerlist" style="background-image: url('{{ $callRequest->user->profile ? asset($callRequest->user->profile) : asset('/build/assets/images/videoChatImg.webp') }}');">
                            <div id="remote-playerlist"></div>
                        </div>
                    </div>
                    <div class="w-100"></div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    {{ csrf_field() }}
                    <input id="chatId" type="hidden" name="chatId" value="{{ $chatId }}">
                    <input id="appid" type="hidden" name="appId" value="{{ $appId }}">
                    <input id="token" type="hidden" name="rtcToken" value="{{ $callRequest->token }}">
                    <input id="callType" type="hidden" name="callType" value="{{ $callRequest->type }}">
                    <input id="channel" type="hidden" name="channelName" value="{{ $callRequest->channelName }}">
                </div>
                <div class="videoChatButtons">
                    <button id="join" type="submit" class="joinBtn">Join</button>
                    <button type="button" id="btn-mic" class="videoBtn">
                        <img src="{{ asset('/build/assets/images/mic.png') }}" alt="">
                    </button>
                    <button type="button" id="btn-camera" class="videoBtn">
                    <img src="{{ asset('/build/assets/images/video-camera.png') }}"  alt="">
                    </button>
                    <button type="button" id="leave" class="videoBtn" style="background-color: red;">
                    <img src="{{ asset('/build/assets/images/telephone.png') }}"  alt="">
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')

    <script>
        let dashboardUrl = "{{ route('advisor.dashboard') }}"
    </script>
    <script src="{{ asset('assets/agora/AgoraRTC_N-4.24.0.js') }}"></script>
    <script src="{{ asset('assets/agora/agora-index.js') }}"></script>

    <script>
        async function checkAudioDevices() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();

                const microphones = devices.filter(device => device.kind === 'audioinput');
                
                if (microphones.length > 0) {
                    console.log('Headphones/Microphone detected:', microphones);
                    // You can access details of each microphone in the 'microphones' array
                } else {
                    console.log('No headphones/microphone detected.');
                }

            } catch (error) {
                console.error('Error enumerating media devices:', error);
                // This error might occur if the user denies permission to access media devices.
            }
        }

        async function checkVideoDevices() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();                
                const cameras = devices.filter(device => device.kind === 'videoinput');
                if (cameras.length > 0) {
                    console.log('Video camera detected:', cameras);
                    // You can access details of each camera in the 'cameras' array
                } else {
                    console.log('No video camera detected.');
                }

            } catch (error) {
                console.error('Error enumerating media devices:', error);
                // This error might occur if the user denies permission to access media devices.
            }
        }

        async function requestMicrophoneAccess() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                // Microphone access granted, you can now use the stream
                console.log("Microphone access granted.");
            } catch (error) {
                console.error("Error acquiring microphone access:", error);
                // Handle cases where the user denies permission
            }
        }

        // window.onload = function () {
        //     var fiveMinutes = 60 * 5, 
    //          display = document.querySelector('#time');
        //     startTimer(0, display);
        // };


    </script>

@endpush