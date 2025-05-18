document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ DOM fully loaded. Loading models...");
    loadModels();
});

async function loadModels() {
    await faceapi.nets.tinyFaceDetector.loadFromUri("/models");
    await faceapi.nets.faceLandmark68Net.loadFromUri("/models");
    await faceapi.nets.faceRecognitionNet.loadFromUri("/models");

    console.log("✅ Models loaded. Starting webcam...");
    startVideo();
}

function startVideo() {
    const video = document.getElementById("video");

    if (!video) {
        console.error("❌ Video element not found. Check your HTML.");
        return;
    }

    navigator.mediaDevices.getUserMedia({ video: {} })
        .then(stream => {
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                console.log("✅ Webcam started successfully.");
                detectFace(video); // Start face detection when webcam is ready
            };
        })
        .catch(err => console.error("❌ Error accessing webcam:", err));
}

// ✅ Detect faces only when needed
async function detectFace(video) {
    if (!video || video.readyState !== 4) {
        console.error("❌ Video not ready yet.");
        setTimeout(() => detectFace(video), 5000); // Retry after 1 second
        return;
    }

    const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!detections) {
        console.log("❌ No face detected. Retrying...");
        setTimeout(() => detectFace(video), 5000); // Retry after 1 second
        return;
    }

    console.log("✅ Face detected. Sending to backend...");
    sendFaceToBackend(JSON.stringify(detections.descriptor), video);
}

// ✅ Only refresh when a face is processed
async function sendFaceToBackend(descriptor, video) {
    try {
        const response = await fetch("verify_face.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ descriptor })
        });

        const data = await response.json();
        console.log("📩 Server Response:", data);

        document.getElementById("status").innerText = data.message;

        if (data.status === "confirm" && data.confirm_needed) {
            let userConfirmed = confirm(data.message);
            if (userConfirmed) {
                const confirmResponse = await fetch("verify_face.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ descriptor, confirmTimeout: true })
                });

                const confirmData = await confirmResponse.json();
                document.getElementById("status").innerText = confirmData.message;
            }
        }

        setTimeout(() => detectFace(video), 5000);
    } catch (error) {
        console.error("❌ Error:", error);
        setTimeout(() => detectFace(video), 5000);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ DOM is fully loaded");

    function updateClock() {
        const now = new Date();
        const formattedTime = now.toLocaleTimeString("en-US", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true
        });

        const formattedDate = now.toLocaleDateString("en-US", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        });

        const dateTimeSpan = document.getElementById("dateTime");

        if (dateTimeSpan) {
            dateTimeSpan.textContent = `${formattedDate} | ${formattedTime}`;
        } else {
            console.error("❌ <span id='dateTime'> NOT found in the DOM!");
        }
    }

    // Run clock immediately & update every second
    updateClock();
    setInterval(updateClock, 1000);
});
