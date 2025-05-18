//REGISTER FACE
document.addEventListener("DOMContentLoaded", async () => {
    const video = document.getElementById("camera-stream");
    const captureBtn = document.getElementById("capture-btn");
    const retryBtn = document.getElementById("retry-btn");
    const canvas = document.getElementById("capture-canvas");
    const photoPreview = document.getElementById("photo-preview");
    const faceDescriptorInput = document.getElementById("face-descriptor"); // hidden input to store descriptor

    let modelsLoaded = false;


    // load models
    async function loadFaceAPIModels() {
        if (!window.faceapi) {
            console.error("face-api.js not loaded!");
            return;
        }

        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
            ]);
            console.log("Face API models loaded successfully");
            modelsLoaded = true;
        } catch (error) {
            console.error("Error loading face-api.js models:", error);
        }
    }

    // start webcam
    async function startWebcam() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (error) {
            console.error("Error accessing webcam:", error);
            alert("Could not access webcam. Please check your camera settings.");
        }
    }

    // process at capture ng face
    async function captureFace() {
        if (!modelsLoaded) {
            alert("Face detection models are still loading. Please wait.");
            return;
        }

        const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!detections) {
            alert("No face detected. Please try again.");
            return;
        }

        const ctx = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // image to base64
        const faceBase64 = canvas.toDataURL("image/png");
        photoPreview.src = faceBase64;

        // json(descriptor ng image)
        faceDescriptorInput.value = JSON.stringify(detections.descriptor);

        console.log("Face Descriptor Captured:", faceDescriptorInput.value); // Debugging Log

        alert("Face captured successfully!");
    }

    function resetCapture() {
        photoPreview.src = "";
        faceDescriptorInput.value = "";
        canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
    }

    captureBtn.addEventListener("click", captureFace);
    retryBtn.addEventListener("click", resetCapture);

    await loadFaceAPIModels();
    await startWebcam();
});
