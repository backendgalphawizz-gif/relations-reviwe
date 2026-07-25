// create Agora client
var client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

var localTracks = {
  videoTrack: null,
  audioTrack: null
};
var remoteUsers = {};
// Agora client options
var options = {
  appid: null,
  channel: null,
  uid: null,
  token: null,
  callType: 'audio'
};

// the demo can auto join channel with params in url
$(() => {
  var urlParams = new URL(location.href).searchParams;
  options.appid = urlParams.get("appid");
  options.channel = urlParams.get("channel");
  options.token = urlParams.get("token");

  options.callType = $("#callType").val()

  if (options.appid && options.channel) {
    $("#appid").val(options.appid);
    $("#token").val(options.token);
    $("#channel").val(options.channel);
    // $("#join-form").submit();
  }
})

$("#join-form").submit(async function (e) {
  e.preventDefault();
  try {
    options.appid = $("#appid").val();
    options.token = $("#token").val();
    options.channel = $("#channel").val();

    let formdata = new FormData(this)

    jQuery.ajax({
      type: "POST",
      url: $(this).attr('action'),
      data: formdata,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        console.log(response)
      }
    });

    await join();
    if (options.token) {
      $("#success-alert-with-token").css("display", "block");
    } else {
      $("#success-alert a").attr("href", `index.html?appid=${options.appid}&channel=${options.channel}&token=${options.token}`);
      $("#success-alert").css("display", "block");
    }
  } catch (error) {
    console.error(error);

    toastr.error(`${error}`)

  } finally {
    $("#join").attr("disabled", true);
    $("#leave").removeAttr("disabled");
  }
})

$("#leave").click(function (e) {
  toastr.success('Call Completed Success')
  leave();
})

async function join() {

  display = document.querySelector('#time');
  startTimer(0, display);

  // add event listener to play remote tracks when remote user publishs.
  client.on("user-published", handleUserPublished);
  client.on("user-unpublished", handleUserUnpublished);

  options.uid = await client.join(
    options.appid,
    options.channel,
    options.token || null
  );

  localTracks.videoTrack = null
  // Always create audio
  localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();

  // Create video ONLY for video call
  if (options.callType === "video") {
    localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();

    localTracks.videoTrack.play("local-player");
    $("#local-player-name").text(`localVideo(${options.uid})`);
    $("#local-player").removeClass("d-none");
  }

  // Publish only existing tracks
  const tracksToPublish = [];
  if (localTracks.audioTrack) tracksToPublish.push(localTracks.audioTrack);
  if (localTracks.videoTrack) tracksToPublish.push(localTracks.videoTrack);

  console.log('tracksToPublish -------- ', tracksToPublish)

  await client.publish(tracksToPublish);


  // // join a channel and create local tracks, we can use Promise.all to run them concurrently
  // [options.uid, localTracks.audioTrack, localTracks.videoTrack] = await Promise.all([
  //   // join the channel
  //   client.join(options.appid, options.channel, options.token || null),
  //   // create local tracks, using microphone and camera
  //   AgoraRTC.createMicrophoneAudioTrack(),
  //   AgoraRTC.createCameraVideoTrack()
  // ]);

  // console.log('localTracks --------------- ', localTracks)

  // if (localTracks.videoTrack) {
  //   // play local video track
  //   localTracks.videoTrack.play("local-player");
  //   $("#local-player-name").text(`localVideo(${options.uid})`);

  //   $('.local-player').removeClass('d-none')

  //   console.log('localTracks --------------- ', options.uid)

  // }

  

  // publish local tracks to channel
  await client.publish(Object.values(localTracks));
  console.log("publish success");
}

async function leave() {
  for (trackName in localTracks) {
    var track = localTracks[trackName];
    if (track) {
      track.stop();
      track.close();
      localTracks[trackName] = undefined;
    }
  }

  // remove remote users and player views
  remoteUsers = {};
  $("#remote-playerlist").html("");
  // $(".remote-playerlist").addClass("d-none");

  // leave the channel
  await client.leave();

  $("#local-player-name").text("");
  $("#local-player").addClass("d-none");
  $("#join").removeAttr("disabled");
  $("#leave").attr("disabled");
  console.log("client leaves channel success");
}

async function subscribe(user, mediaType) {
  const uid = user.uid;
  await client.subscribe(user, mediaType);
  console.log("subscribe success:", mediaType);

  // VIDEO
  if (mediaType === "video") {
    const player = $(`
      <div id="player-wrapper-${uid}" class="player-call-screen">
        <p class="player-name">remoteUser(${uid})</p>
        <div id="player-${uid}" class="player remote-player-screen"></div>
      </div>
    `);

    $("#remote-playerlist").append(player);
    user.videoTrack.play(`player-${uid}`);
    $(".remote-playerlist").removeClass("d-none");
  }

  // AUDIO
  // if (mediaType === "audio") {
    user.audioTrack.play(); // no UI needed
  // }

  toastr.success('User Joined Call')

}

function handleUserPublished(user, mediaType) {
  const id = user.uid;
  remoteUsers[id] = user;
  subscribe(user, mediaType);
}

function handleUserUnpublished(user) {
  const id = user.uid;
  delete remoteUsers[id];
  $(`#player-wrapper-${id}`).remove();
  $(`#player-wrapper-${id}`).addClass();
}

// function handleUserUnpublished(user, mediaType) {
//   const id = user.uid;

//   if (mediaType === "video") {
//     $(`#player-wrapper-${id}`).remove();
//   }
// }

let isMicMuted = false;

$("#btn-mic").click(async () => {
  if (!localTracks.audioTrack) return;

  isMicMuted = !isMicMuted;
  await localTracks.audioTrack.setEnabled(!isMicMuted);

  // $("#btn-mic").text(isMicMuted ? "Unmute" : "Mute");

  const micImg = $("#btn-mic img");

  micImg.attr(
    "src",
    isMicMuted
      ? "/build/assets/images/microphone.png"
      : "/build/assets/images/mic.png"
  );
  
});

let isCameraOff = false;
$("#btn-camera").click(async () => {
  if (!localTracks.videoTrack) return;

  navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
      stream.getTracks().forEach(t => t.stop());
      console.log("Camera available");
    })
    .catch(() => {
      toastr.error("Camera in use or blocked");
    });

  isCameraOff = !isCameraOff;
  let response = await localTracks.videoTrack.setEnabled(!isCameraOff);

  if(isCameraOff) {
    $('#local-player').hide()
  } else {
    $('#local-player').show()
  }

  // $("#btn-camera").text(isCameraOff ? "Camera On" : "Camera Off");
  const vidImg = $("#btn-camera img");

  vidImg.attr(
    "src",
    isCameraOff
      ? "/build/assets/images/videocam.png"
      : "/build/assets/images/video-camera.png"
  );
});


async function switchToVideo() {
  if (localTracks.videoTrack) return;

  localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();
  await client.publish(localTracks.videoTrack);

  localTracks.videoTrack.play("local-player");
  $("#local-player").removeClass("d-none");

  options.callType = "video";
}

async function switchToAudio() {
  if (!localTracks.videoTrack) return;

  await client.unpublish(localTracks.videoTrack);

  localTracks.videoTrack.stop();
  localTracks.videoTrack.close();
  localTracks.videoTrack = null;

  $("#local-player").addClass("d-none");

  options.callType = "audio";
}

function startTimer(duration, display) {
  var timer = duration, minutes, seconds;
  setInterval(function () {
      minutes = parseInt(timer / 60, 10);
      seconds = parseInt(timer % 60, 10);

      minutes = minutes < 10 ? "0" + minutes : minutes;
      seconds = seconds < 10 ? "0" + seconds : seconds;

      display.textContent = minutes + ":" + seconds;
      ++timer
  }, 1000);
}