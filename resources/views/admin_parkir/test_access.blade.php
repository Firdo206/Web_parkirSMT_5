<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Test Access</title>
    <style>
        body{font-family:Arial,sans-serif;max-width:700px;margin:40px auto;padding:20px}
        input,button{padding:10px;margin-top:10px}
        button{cursor:pointer}
        pre{background:#f3f3f3;padding:15px;overflow:auto}
    </style>
</head>
<body>
    <h2>Test Parkir Access</h2>

    <form method="POST" action="{{ route('parkir.detect-access') }}" enctype="multipart/form-data">
        @csrf

        <label>Foto kendaraan + wajah</label><br>
        <input type="file" name="photo" accept="image/*" required>

        <br>
        <button type="submit">Tes Akses</button>
    </form>

    @if($errors->any())
        <h3>Error</h3>
        <pre>{{ $errors->toJson(JSON_PRETTY_PRINT) }}</pre>
    @endif
</body>
</html>