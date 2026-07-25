@extends('vendor.app')

@push('css_or_js')
    <title>Chat Screen</title>
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
        #local-player {
            width:100%;
            height:250px;
        }

    </style>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-12">
            <div class="chatLists">
                <h3>Chat not started yet</h3>
            </div>
        </div>
        <div class="col-span-12 2xl:col-span-12">
            <form id="join-form">
                <div class="chatMessage">
                    <input type="text" name="chat_message" class="form-control">
                    <button type="button" class="btn btn-primary btn-sm send-message">Send</button>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <input id="chatId" type="hidden" name="chatId" value="{{ $chatId }}">
                    <input id="appid" type="hidden" name="appId" value="{{ $appId }}">
                    <input id="token" type="hidden" name="rtcToken" value="{{ $rtcToken }}">
                    <input id="channel" type="hidden" name="channelName" value="{{ $channelName }}">
                </div>
                <div class="button-group mt-2">
                    <button id="join" type="submit" class="btn btn-primary btn-sm">Join</button>
                    <button id="leave" type="button" class="btn btn-primary btn-sm" disabled>Leave</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/agora/Agora-chat.js') }}"></script>
    {{-- <script src="{{ asset('assets/agora/agora-index.js') }}"></script> --}}

    <script>
        const client = AgoraRTC.createInstance("{{ $appId }}");

        async function loginToRtm() {
            await client.login({
                uid: "user1", 
                token: "{{ $rtcToken }}"
            });
            console.log("RTM Login Success");
        }
        loginToRtm();

        let channel = client.createChannel("{{ $channelName }}");

        async function joinChannel() {
            await channel.join();
            console.log("Joined RTM Channel");
        }
        joinChannel();

    </script>

@endpush