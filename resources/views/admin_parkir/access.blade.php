<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Akses Parkir</title>
    <style>
        *{box-sizing:border-box}
        body{
            margin:0;
            font-family:Arial,sans-serif;
            background:#f3f4f6;
            color:#111827;
        }
        .container{
            max-width:1100px;
            margin:30px auto;
            padding:20px;
        }
        .header{
            background:white;
            padding:20px;
            border-radius:14px;
            margin-bottom:20px;
            box-shadow:0 2px 8px rgba(0,0,0,.08);
        }
        .header h1{
            margin:0 0 8px;
            font-size:25px;
        }
        .header p{
            margin:0;
            color:#6b7280;
        }
        .grid{
            display:grid;
            grid-template-columns:2fr 1fr;
            gap:20px;
        }
        .card{
            background:white;
            padding:20px;
            border-radius:14px;
            box-shadow:0 2px 8px rgba(0,0,0,.08);
        }
        .card h2{
            margin-top:0;
            font-size:19px;
        }
        video{
            width:100%;
            border-radius:12px;
            background:#111;
            display:block;
            aspect-ratio:16/9;
            object-fit:cover;
        }
        .status{
            padding:16px;
            border-radius:10px;
            background:#f3f4f6;
            margin-bottom:20px;
            font-weight:bold;
        }
        .status.waiting{
            background:#fef3c7;
            color:#92400e;
        }
        .status.processing{
            background:#dbeafe;
            color:#1e40af;
        }
        .status.success{
            background:#dcfce7;
            color:#166534;
        }
        .status.failed{
            background:#fee2e2;
            color:#991b1b;
        }
        .info{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:10px;
        }
        .info-item{
            background:#f9fafb;
            padding:12px;
            border-radius:8px;
        }
        .label{
            display:block;
            color:#6b7280;
            font-size:12px;
            margin-bottom:4px;
        }
        .value{
            font-weight:bold;
        }
        button{
            border:0;
            padding:12px 18px;
            border-radius:8px;
            cursor:pointer;
            background:#2563eb;
            color:white;
            font-weight:bold;
        }
        button:disabled{
            background:#9ca3af;
            cursor:not-allowed;
        }
        .hidden{
            display:none!important;
        }
        .result{
            margin-top:20px;
            padding:15px;
            border-radius:10px;
            background:#f9fafb;
        }
        @media(max-width:800px){
            .grid{
                grid-template-columns:1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Verifikasi Akses Parkir</h1>
        <p>Sistem otomatis verifikasi wajah dan plat kendaraan.</p>
    </div>

    <div id="statusBox" class="status">
        Menunggu kendaraan...
    </div>

    <div class="grid">

        <div class="card">
            <h2>Kamera Laptop</h2>

            <video id="camera" autoplay playsinline muted></video>

            <canvas id="canvas" class="hidden"></canvas>

            <br>

            <button id="cameraButton" onclick="startCamera()">
                Aktifkan Kamera
            </button>
        </div>

        <div class="card">
            <h2>Status Verifikasi</h2>

            <div class="info">

                <div class="info-item">
                    <span class="label">Gate</span>
                    <span class="value" id="gateValue">gate_utama</span>
                </div>

                <div class="info-item">
                    <span class="label">Status Gate</span>
                    <span class="value" id="gateStatus">SIAGA</span>
                </div>

                <div class="info-item">
                    <span class="label">Plat</span>
                    <span class="value" id="plateValue">-</span>
                </div>

                <div class="info-item">
                    <span class="label">Member</span>
                    <span class="value" id="memberValue">-</span>
                </div>

                <div class="info-item">
                    <span class="label">Face Distance</span>
                    <span class="value" id="faceValue">-</span>
                </div>

                <div class="info-item">
                    <span class="label">Hasil</span>
                    <span class="value" id="resultValue">-</span>
                </div>

            </div>

            <div id="resultBox" class="result hidden"></div>
        </div>

    </div>

</div>

<script>
const video=document.getElementById('camera');
const canvas=document.getElementById('canvas');
const statusBox=document.getElementById('statusBox');
const cameraButton=document.getElementById('cameraButton');

const gateValue=document.getElementById('gateValue');
const gateStatus=document.getElementById('gateStatus');
const plateValue=document.getElementById('plateValue');
const memberValue=document.getElementById('memberValue');
const faceValue=document.getElementById('faceValue');
const resultValue=document.getElementById('resultValue');
const resultBox=document.getElementById('resultBox');

const gate='gate_utama';

let cameraStream=null;
let checking=false;
let verificationRunning=false;
let lastGateStatus='SIAGA';

function setStatus(message,type=''){
    statusBox.innerText=message;
    statusBox.className='status';

    if(type){
        statusBox.classList.add(type);
    }
}

async function startCamera(){
    if(cameraStream){
        return true;
    }

    if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
        setStatus(
            'Browser tidak mendukung akses kamera.',
            'failed'
        );
        return false;
    }

    try{
        cameraStream=await navigator.mediaDevices.getUserMedia({
            video:{
                width:{ideal:1280},
                height:{ideal:720},
                facingMode:'user'
            },
            audio:false
        });

        video.srcObject=cameraStream;

        await video.play();

        cameraButton.innerText='Kamera Aktif';
        cameraButton.disabled=true;

        return true;

    }catch(error){
        console.error('CAMERA ERROR:',error);

        setStatus(
            'Kamera gagal dibuka. Izinkan akses kamera browser.',
            'failed'
        );

        return false;
    }
}

function captureFace(){
    if(!video.videoWidth || !video.videoHeight){
        return null;
    }

    canvas.width=video.videoWidth;
    canvas.height=video.videoHeight;

    const context=canvas.getContext('2d');

    context.drawImage(
        video,
        0,
        0,
        canvas.width,
        canvas.height
    );

    return canvas.toDataURL('image/jpeg',0.9);
}

async function getGateStatus(){
    try{
        const response=await fetch(
            '/api/parking/verification-status?gate='+encodeURIComponent(gate),
            {
                method:'GET',
                cache:'no-store',
                headers:{
                    'Accept':'application/json'
                }
            }
        );

        if(!response.ok){
            throw new Error('HTTP '+response.status);
        }

        const result=await response.json();

        if(!result.success){
            return null;
        }

        return result.data;

    }catch(error){
        console.error('GATE STATUS ERROR:',error);
        return null;
    }
}

async function checkGate(){
    if(checking){
        return;
    }

    checking=true;

    const state=await getGateStatus();

    checking=false;

    if(!state){
        return;
    }

    gateValue.innerText=state.gate;
    gateStatus.innerText=state.status;

    /*
    |--------------------------------------------------------------------------
    | MENUNGGU
    |--------------------------------------------------------------------------
    */

    if(state.izin==='MENUNGGU'){

        if(lastGateStatus!=='MENUNGGU'){
            setStatus(
                'Kendaraan terdeteksi. Menyalakan kamera...',
                'waiting'
            );
        }

        lastGateStatus='MENUNGGU';

        const cameraReady=await startCamera();

        if(
            cameraReady &&
            !verificationRunning
        ){
            await new Promise(resolve=>setTimeout(resolve,1000));

            await verifyAccess();
        }

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | BUKA
    |--------------------------------------------------------------------------
    */

    if(state.izin==='BUKA'){
        lastGateStatus='BUKA';

        setStatus(
            'AKSES DITERIMA. PALANG MEMBUKA.',
            'success'
        );

        resultValue.innerText='BUKA';

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | TOLAK
    |--------------------------------------------------------------------------
    */

    if(state.izin==='TOLAK'){
        lastGateStatus='TOLAK';

        setStatus(
            'AKSES DITOLAK.',
            'failed'
        );

        resultValue.innerText='TOLAK';

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | SIAGA
    |--------------------------------------------------------------------------
    */

    if(state.izin==='SIAGA'){
        lastGateStatus='SIAGA';

        if(!verificationRunning){
            setStatus('Menunggu kendaraan...');
        }
    }
}

async function verifyAccess(){

    if(verificationRunning){
        return;
    }

    verificationRunning=true;

    try{

        setStatus(
            'Mengambil gambar wajah...',
            'processing'
        );

        await new Promise(resolve=>setTimeout(resolve,500));

        const faceImage=captureFace();

        if(!faceImage){
            setStatus(
                'Kamera belum siap mengambil gambar.',
                'failed'
            );

            verificationRunning=false;
            return;
        }

        setStatus(
            'Mengirim wajah ke AI...',
            'processing'
        );

        const csrfToken=document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        const response=await fetch(
            '{{ route("parkir.verify-access") }}',
            {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':csrfToken
                },
                body:JSON.stringify({
                    gate:gate,
                    face_image:faceImage
                })
            }
        );

        const result=await response.json();

        console.log('VERIFY RESULT:',result);

        if(result.plate_text){
            plateValue.innerText=result.plate_text;
        }

        if(result.member){
            memberValue.innerText=result.member.name;
        }

        if(result.face_distance!==undefined &&
           result.face_distance!==null){

            faceValue.innerText=
                Number(result.face_distance).toFixed(4);
        }

        resultValue.innerText=
            result.status || '-';

        if(result.success){

            resultBox.classList.remove('hidden');

            let html='';

            html+=`<strong>${result.message}</strong>`;

            if(result.plate_text){
                html+=`<br>Plat: ${result.plate_text}`;
            }

            if(result.member){
                html+=`<br>Member: ${result.member.name}`;
            }

            if(result.face_distance!==undefined &&
               result.face_distance!==null){

                html+=`<br>Face distance: ${
                    Number(result.face_distance).toFixed(4)
                }`;
            }

            resultBox.innerHTML=html;

            if(result.status==='auto_approved'){
                setStatus(
                    'AKSES DITERIMA. PALANG MEMBUKA.',
                    'success'
                );
            }else{
                setStatus(
                    result.message || 'Akses ditolak.',
                    'failed'
                );
            }

        }else{

            setStatus(
                result.message || 'Verifikasi gagal.',
                'failed'
            );

        }

    }catch(error){

        console.error('VERIFY ERROR:',error);

        setStatus(
            'Terjadi kesalahan saat verifikasi.',
            'failed'
        );

    }finally{

        verificationRunning=false;
    }
}

/*
|--------------------------------------------------------------------------
| Polling gate setiap 1 detik
|--------------------------------------------------------------------------
*/

setInterval(checkGate,1000);

checkGate();
</script>

</body>
</html>