<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>QR Code Scanner</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.card {
    width: 400px;
    max-width: 95vw;
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.scanner-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1/1;
    overflow: hidden;
    border-radius: 16px;
    background: black;
}

#reader {
    width: 100%;
    height: 100%;
}

/* Hide html5-qrcode default UI */
#reader__dashboard,
#reader__header_message {
    display: none !important;
}

#reader__scan_region {
    border: none !important;
}

#reader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

/* Overlay */
.overlay {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

/* Scan box */
.scan-box {
    position: absolute;
    width: 260px;
    height: 260px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Dark mask */
.scan-box::before {
    content: "";
    position: absolute;
    inset: -1000px;
    border: 1000px solid rgba(0,0,0,.45);
}

/* Corners */
.corner {
    position: absolute;
    width: 28px;
    height: 28px;
    border: 4px solid #00e676;
}

.top-left {
    top: -4px;
    left: -4px;
    border-right: none;
    border-bottom: none;
}

.top-right {
    top: -4px;
    right: -4px;
    border-left: none;
    border-bottom: none;
}

.bottom-left {
    bottom: -4px;
    left: -4px;
    border-right: none;
    border-top: none;
}

.bottom-right {
    bottom: -4px;
    right: -4px;
    border-left: none;
    border-top: none;
}

/* Scan line */
.scan-line {
    position: absolute;
    left: 0;
    width: 100%;
    height: 3px;
    background: #00e676;
    box-shadow: 0 0 10px #00e676;
    animation: scan 2s linear infinite;
}

@keyframes scan {
    0% {
        top: 0;
    }
    100% {
        top: calc(100% - 3px);
    }
}

.title {
    text-align: center;
    margin-top: 16px;
    font-weight: bold;
    color: #374151;
}

.subtitle {
    text-align: center;
    color: #6b7280;
    font-size: 14px;
    margin-top: 6px;
}

#result {
    margin-top: 15px;
    padding: 12px;
    background: #ecfdf5;
    border: 1px solid #10b981;
    border-radius: 8px;
    display: none;
    word-break: break-all;
}

#debug {
    margin-top: 10px;
    font-size: 12px;
    color: #666;
}
</style>
</head>
<body>

<div class="card">

    <div class="scanner-container">

        <div id="reader"></div>

        <div class="overlay">
            <div class="scan-box">
                <div class="corner top-left"></div>
                <div class="corner top-right"></div>
                <div class="corner bottom-left"></div>
                <div class="corner bottom-right"></div>

                <div class="scan-line"></div>
            </div>
        </div>

    </div>

    <div class="title">
        Point your camera at the QR Code
    </div>

    <div class="subtitle">
        Ensure sufficient lighting and keep the QR Code inside the frame
    </div>

    <div id="debug">
        Initializing camera...
    </div>

    <div id="result"></div>

</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>

let lastResult = null;
let html5QrCode = null;

function onScanSuccess(decodedText) {

    if (decodedText === lastResult) {
        return;
    }

    lastResult = decodedText;

    console.log("QR Result:", decodedText);

    const resultDiv =
        document.getElementById("result");

    resultDiv.style.display = "block";

    resultDiv.innerHTML = `
        <strong>QR Detected:</strong><br>
        ${decodedText}
    `;

    // Uncomment if you want scanner to stop
    // html5QrCode.stop();
}

async function startScanner() {

    try {

        document.getElementById("debug")
            .innerText = "Loading cameras...";

        html5QrCode =
            new Html5Qrcode("reader");

        const devices =
            await Html5Qrcode.getCameras();

        console.log(devices);

        if (!devices.length) {

            document.getElementById("debug")
                .innerText = "No camera found";

            return;
        }

        document.getElementById("debug")
            .innerText =
            `Found ${devices.length} camera(s)`;

        const rearCamera =
            devices.find(device =>
                device.label.toLowerCase()
                .includes("back")
            ) ||
            devices.find(device =>
                device.label.toLowerCase()
                .includes("rear")
            ) ||
            devices[0];

        console.log("Using:", rearCamera);

        await html5QrCode.start(
            rearCamera.id,
            {
                fps: 10,
                qrbox: {
                    width: 260,
                    height: 260
                }
            },
            onScanSuccess
        );

        document.getElementById("debug")
            .innerText =
            "Camera started successfully";

    } catch (err) {

        console.error(err);

        document.getElementById("debug")
            .innerText =
            "ERROR: " + err;

    }
}

startScanner();

</script>

</body>
</html>