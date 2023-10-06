@php use http\Env\Request; @endphp
<!DOCTYPE html>
<html>
<head>
    <title>Chunked Video Upload</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        #container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #fileInput {
            display: block;
            margin-bottom: 10px;
        }

        #startUploadButton {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        #progressBarContainer {
            margin-top: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            height: 20px;
            width: 100%;
        }

        #progressBar {
            background-color: #4caf50;
            border-radius: 5px;
            height: 100%;
            width: 0;
        }
    </style>
</head>
<body>

<div id="container">
    <h1>Загрузка Видео Частями</h1>
    <input type="file" id="fileInput">
    <input type="date" id="dateSelector">
    <button id="startUploadButton">Загрузить</button>
    <div id="progressBarContainer">
        <div id="progressBar"></div>
    </div>
</div>

<script>
    async function uploadFileChunk(file, start, end, chunkIndex, totalChunks, date) {
        const chunk = file.slice(start, end);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const formData = new FormData();
        formData.append('file', chunk);
        formData.append('fileName', file.name);
        formData.append('chunkIndex', chunkIndex);
        formData.append('totalChunks', totalChunks);
        formData.append('date', date);
        formData.append('type', file.type);
        formData.append('size', file.size);

        const response = await fetch('/upload-video', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            throw new Error(`Ошибка загрузки с кодом ${response.status}`);
        }

        return await response.text();
    }

    async function uploadFile(file, chunkSize, onProgress, date) {
        const totalChunks = Math.ceil(file.size / chunkSize);

        let start = 0;
        let end = Math.min(chunkSize, file.size);
        let chunkIndex = 0;

        while (start < file.size) {
            await uploadFileChunk(file, start, end, chunkIndex, totalChunks, date);
            start = end;
            end = Math.min(start + chunkSize, file.size);
            chunkIndex++;

            const progress = (chunkIndex / totalChunks) * 100;
            onProgress(progress);
        }
    }

    const inputFile = document.getElementById('fileInput');
    const startButton = document.getElementById('startUploadButton');
    const progressBar = document.getElementById('progressBar');
    const dateSelector = document.getElementById('dateSelector');

    inputFile.addEventListener('change', () => {
        startButton.disabled = false;
    });

    startButton.addEventListener('click', async () => {
        const selectedFile = inputFile.files[0];
        const selectedDate = dateSelector.value;
        if (selectedFile) {
            startButton.disabled = true;

            await uploadFile(selectedFile, 10 * 1024 * 1024, (progress) => {
                progressBar.style.width = `${progress}%`;
            }, selectedDate);

            progressBar.style.width = '0';
            startButton.disabled = false;
            console.log('Загрузка завершена');
        }
    });
</script>
</body>
</html>
